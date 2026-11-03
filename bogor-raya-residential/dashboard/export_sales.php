<?php
session_start();
require_once '../includes/auth_helper.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

checkUserStatus();

if (!isset($_SESSION['nama'])) {
    header("Location: ../login.php");
    exit();
}

$permissions = json_decode($_SESSION['permissions'], true);
if (!isset($permissions['sales']['view']) || $permissions['sales']['view'] != 1) {
    header("Location: ../dashboard.php");
    exit();
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "db_bogor");

// Modified query to include all follow-ups
$query = "SELECT s.*, src.nama as sumber_nama, u.nama as user_nama,
          GROUP_CONCAT(
            CONCAT(
                fu.tgl_follow_up, '|',
                fu.media, '|',
                COALESCE(fu.hasil, ''), '|',
                COALESCE(un.name, ''), '|',
                u2.nama
            ) ORDER BY fu.tgl_follow_up DESC
          ) as follow_ups
          FROM tbl_sales s 
          LEFT JOIN tbl_sumber src ON s.id_sumber = src.id
          LEFT JOIN tbl_users u ON s.id_user = u.id
          LEFT JOIN tbl_follow_up fu ON s.id = fu.id_sales
          LEFT JOIN tbl_unit un ON fu.id_unit = un.id
          LEFT JOIN tbl_users u2 ON fu.id_user = u2.id
          GROUP BY s.id
          ORDER BY s.tgl_create DESC";

$result = mysqli_query($conn, $query);

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();

// First worksheet - Sales Data
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Data Calon Pembeli');

// Add title
$sheet->mergeCells('A1:J1');
$sheet->setCellValue('A1', 'DATA CALON PEMBELI BOGOR RAYA RESIDENTIAL');
$sheet->getStyle('A1')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 16,
        'color' => ['rgb' => '000000']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'C5E0B4']
    ]
]);
$sheet->getRowDimension(1)->setRowHeight(30);

// Add date
$sheet->mergeCells('A2:J2');
$sheet->setCellValue('A2', 'Tanggal Export: ' . date('d F Y'));
$sheet->getStyle('A2')->applyFromArray([
    'font' => ['italic' => true],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'E2EFDA']
    ]
]);
$sheet->getRowDimension(2)->setRowHeight(20);

// Simplified headers for main sheet
$headers = ['No', 'Nama', 'Sumber', 'Pekerjaan', 'No HP', 'Email', 'Dibuat Oleh', 'Tanggal Dibuat', 'Follow Up Terakhir', 'Hasil Terakhir'];
$sheet->fromArray([$headers], NULL, 'A4');

// Style headers
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4472C4']
    ]
];
$sheet->getStyle("A4:J4")->applyFromArray($headerStyle);
$sheet->getRowDimension(4)->setRowHeight(25);

// Process data
$followupData = [];
while($data = mysqli_fetch_assoc($result)) {
    if (!empty($data['follow_ups'])) {
        $followups = array_map(function($fu) {
            return explode('|', $fu);
        }, explode(',', $data['follow_ups']));
        $followupData[] = ['data' => $data, 'followups' => $followups];
    } else {
        $followupData[] = ['data' => $data, 'followups' => []];
    }
}

// Add main data
$row = 5;
foreach($followupData as $index => $item) {
    $data = $item['data'];
    $lastFollowup = !empty($item['followups']) ? $item['followups'][0] : null;
    
    $sheet->setCellValue('A'.$row, $index + 1);
    $sheet->setCellValue('B'.$row, $data['nama']);
    $sheet->setCellValue('C'.$row, $data['sumber_nama']);
    $sheet->setCellValue('D'.$row, $data['pekerjaan']);
    $sheet->setCellValue('E'.$row, $data['no_hp']);
    $sheet->setCellValue('F'.$row, $data['email']);
    $sheet->setCellValue('G'.$row, $data['user_nama']);
    $sheet->setCellValue('H'.$row, date('d/m/Y', strtotime($data['tgl_create'])));
    $sheet->setCellValue('I'.$row, $lastFollowup ? date('d/m/Y', strtotime($lastFollowup[0])) : 'Belum ada');
    $sheet->setCellValue('J'.$row, $lastFollowup ? $lastFollowup[2] : 'Belum ada');
    
    // Apply row styling
    $rowStyle = [
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => ($row % 2 == 0) ? 'F5F5F5' : 'FFFFFF']
        ]
    ];
    $sheet->getStyle("A$row:J$row")->applyFromArray($rowStyle);
    
    $row++;
}

// Add totals row for main sheet
$sheet->mergeCells("A$row:J$row");
$sheet->setCellValue("A$row", "Total Calon Pembeli: " . count($followupData));
$totalStyle = [
    'font' => ['bold' => true],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_RIGHT
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'E2EFDA']
    ]
];
$sheet->getStyle("A$row:J$row")->applyFromArray($totalStyle);

// Create second worksheet - Follow Up History
$followupSheet = $spreadsheet->createSheet();
$followupSheet->setTitle('Riwayat Follow Up');

// Set follow-up sheet headers
$followupHeaders = ['No', 'Nama Calon Pembeli', 'Tanggal Follow Up', 'Media', 'Hasil', 'Unit', 'Sales'];
$followupSheet->fromArray([$followupHeaders], NULL, 'A1');

// Style follow-up headers
$followupSheet->getStyle('A1:G1')->applyFromArray($headerStyle);

// Add follow-up data
$row = 2;
$no = 1;
foreach($followupData as $item) {
    $data = $item['data'];
    foreach($item['followups'] as $fu) {
        $followupSheet->setCellValue('A'.$row, $no++);
        $followupSheet->setCellValue('B'.$row, $data['nama']);
        $followupSheet->setCellValue('C'.$row, date('d/m/Y', strtotime($fu[0])));
        $followupSheet->setCellValue('D'.$row, $fu[1]);
        $followupSheet->setCellValue('E'.$row, $fu[2]);
        $followupSheet->setCellValue('F'.$row, $fu[3]);
        $followupSheet->setCellValue('G'.$row, $fu[4]);
        
        // Color code hasil
        $hasilColor = 'FFFFFF';
        if (stripos($fu[2], 'tertarik') !== false) {
            $hasilColor = 'C6EFCE';
        } elseif (stripos($fu[2], 'pikir') !== false) {
            $hasilColor = 'FFEB9C';
        } elseif (stripos($fu[2], 'tidak') !== false) {
            $hasilColor = 'FFC7CE';
        }
        $followupSheet->getStyle("E$row")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color($hasilColor));
        
        // Apply row styling
        $rowStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => ($row % 2 == 0) ? 'F5F5F5' : 'FFFFFF']
            ]
        ];
        $followupSheet->getStyle("A$row:G$row")->applyFromArray($rowStyle);
        
        $row++;
    }
}

// Auto-size columns for follow-up sheet
foreach(range('A', 'G') as $col) {
    $followupSheet->getColumnDimension($col)->setAutoSize(true);
}

// Set active sheet to first sheet
$spreadsheet->setActiveSheetIndex(0);

// Set the header for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Data_Calon_Pembeli_'.date('Y-m-d').'.xlsx"');
header('Cache-Control: max-age=0');

// Save file
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
