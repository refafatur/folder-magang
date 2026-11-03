<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan tidak ada output sebelumnya
if (ob_get_level()) ob_end_clean();

session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['nama'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit('Unauthorized access');
}

require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

try {
    // Create new spreadsheet
    $spreadsheet = new Spreadsheet();
    
    // Database connection dengan timeout
    $conn = new mysqli("localhost", "root", "", "db_bogor");
    $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Get filter parameters from GET request
    $period = $_GET['period'] ?? 'daily';
    $startDate = $_GET['startDate'] ?? date('Y-m-d');
    $endDate = $_GET['endDate'] ?? date('Y-m-d');

    if ($period === 'daily') {
        // Create daily report with single sheet
        $dailySheet = $spreadsheet->getActiveSheet();
        $dailySheet->setTitle('Daily_Traffic');

        // Set report title
        $dailySheet->setCellValue('A1', 'Daily Traffic Report');
        $periodText = sprintf('Period: %s to %s', 
            date('d F Y', strtotime($startDate)),
            date('d F Y', strtotime($endDate))
        );
        $dailySheet->setCellValue('A2', $periodText);

        // Set column headers
        $dailySheet->setCellValue('A4', 'Date');
        $dailySheet->setCellValue('B4', 'Visitors');

        // Get daily data
        $sql = "SELECT 
            visit_date,
            total_visitors 
            FROM view_daily_visitors 
            WHERE visit_date BETWEEN ? AND ?
            ORDER BY visit_date";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();

        // Populate data
        $row = 5;
        $total_visitors = 0;
        $chartLabels = [];
        while($data = $result->fetch_assoc()) {
            $formatted_date = date('l, d F Y', strtotime($data['visit_date']));
            $chartLabel = date('d M', strtotime($data['visit_date']));
            
            $dailySheet->setCellValue('A' . $row, $formatted_date);
            $dailySheet->setCellValue('B' . $row, (int)$data['total_visitors']);
            $chartLabels[] = $chartLabel;
            $total_visitors += (int)$data['total_visitors'];
            $row++;
        }

        // Add total row
        $totalRow = $row;
        $dailySheet->setCellValue('A' . $totalRow, 'Total');
        $dailySheet->setCellValue('B' . $totalRow, $total_visitors);

        // Style the sheet
        $dailySheet->getStyle('A1:B1')->getFont()->setBold(true)->setSize(14);
        $dailySheet->getStyle('A2')->getFont()->setItalic(true);
        $dailySheet->getStyle('A4:B4')->getFont()->setBold(true);
        $dailySheet->getStyle('A' . $totalRow . ':B' . $totalRow)->getFont()->setBold(true);
        $dailySheet->getStyle('A' . $totalRow . ':B' . $totalRow)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E3E3E3');

        // Add chart
        createTrafficChart($dailySheet, 'Daily Visitors', [
            [
                'range' => 'B5:B' . ($row-1),
                'title' => 'Visitors',
                'color' => '6366f1',
                'labels' => $chartLabels
            ]
        ], 'line', 'daily', 'D2', 'K20');

        // Auto-size columns
        foreach(['A', 'B'] as $col) {
            $dailySheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = sprintf('Traffic_Report_Daily_%s_to_%s.xlsx',
            date('d_F_Y', strtotime($startDate)),
            date('d_F_Y', strtotime($endDate))
        );

    } else {
        // Monthly report with two sheets
        // Create monthly summary sheet first
        $summarySheet = $spreadsheet->getActiveSheet();
        $summarySheet->setTitle('Monthly_Summary');

        // Create details sheet immediately after
        $detailsSheet = $spreadsheet->createSheet();
        $detailsSheet->setTitle('Daily_Details');

        // Monthly Summary Sheet
        $summarySheet->setCellValue('A1', 'Monthly Traffic Summary');
        $periodText = sprintf('Period: %s to %s', 
            date('F Y', strtotime($startDate)),
            date('F Y', strtotime($endDate))
        );
        $summarySheet->setCellValue('A2', $periodText);
        $summarySheet->setCellValue('A4', 'Month');
        $summarySheet->setCellValue('B4', 'Visitors');

        // Get and populate monthly summary data
        $sql_summary = "SELECT 
            DATE_FORMAT(visit_date, '%Y-%m') as period_date,
            DATE_FORMAT(visit_date, '%Y-%m-01') as visit_date,
            SUM(total_visitors) as total_visitors 
            FROM view_daily_visitors 
            WHERE DATE_FORMAT(visit_date, '%Y-%m') BETWEEN DATE_FORMAT(?, '%Y-%m') AND DATE_FORMAT(?, '%Y-%m')
            GROUP BY period_date
            ORDER BY period_date";
            
        $stmt = $conn->prepare($sql_summary);
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result_summary = $stmt->get_result();

        $row = 5;
        $total_visitors = 0;
        $chartLabels = [];
        while($data = $result_summary->fetch_assoc()) {
            $formatted_date = date('F Y', strtotime($data['visit_date']));
            $chartLabel = date('F', strtotime($data['visit_date']));
            
            $summarySheet->setCellValue('A' . $row, $formatted_date);
            $summarySheet->setCellValue('B' . $row, (int)$data['total_visitors']);
            $chartLabels[] = $chartLabel;
            $total_visitors += (int)$data['total_visitors'];
            $row++;
        }

        // Add total and styling to summary sheet
        $totalRow = $row;
        $summarySheet->setCellValue('A' . $totalRow, 'Total');
        $summarySheet->setCellValue('B' . $totalRow, $total_visitors);

        // Style summary sheet
        $summarySheet->getStyle('A1:B1')->getFont()->setBold(true)->setSize(14);
        $summarySheet->getStyle('A2')->getFont()->setItalic(true);
        $summarySheet->getStyle('A4:B4')->getFont()->setBold(true);
        $summarySheet->getStyle('A' . $totalRow . ':B' . $totalRow)->getFont()->setBold(true);
        $summarySheet->getStyle('A' . $totalRow . ':B' . $totalRow)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E3E3E3');

        // Add chart to summary sheet
        createTrafficChart($summarySheet, 'Monthly Visitors Summary', [
            [
                'range' => 'B5:B' . ($row-1),
                'title' => 'Monthly Visitors',
                'color' => '6366f1',
                'labels' => $chartLabels
            ]
        ], 'line', 'monthly', 'D2', 'K20');

        // Daily Details Sheet
        $detailsSheet->setCellValue('A1', 'Traffic Details');
        $detailsSheet->setCellValue('A2', $periodText);
        $detailsSheet->setCellValue('A4', 'Date');
        $detailsSheet->setCellValue('B4', 'Visitors');

        // Get and populate daily details data
        $sql_details = "SELECT 
            visit_date,
            total_visitors 
            FROM view_daily_visitors 
            WHERE DATE_FORMAT(visit_date, '%Y-%m') BETWEEN DATE_FORMAT(?, '%Y-%m') AND DATE_FORMAT(?, '%Y-%m')
            ORDER BY visit_date";

        $stmt = $conn->prepare($sql_details);
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result_details = $stmt->get_result();

        $row = 5;
        $total_detail_visitors = 0;
        $detailChartLabels = [];
        while($data = $result_details->fetch_assoc()) {
            $formatted_date = date('l, d F Y', strtotime($data['visit_date']));
            $chartLabel = date('d M', strtotime($data['visit_date']));
            
            $detailsSheet->setCellValue('A' . $row, $formatted_date);
            $detailsSheet->setCellValue('B' . $row, (int)$data['total_visitors']);
            $detailChartLabels[] = $chartLabel;
            $total_detail_visitors += (int)$data['total_visitors'];
            $row++;
        }

        // Add total and styling to details sheet
        $detailsTotalRow = $row;
        $detailsSheet->setCellValue('A' . $detailsTotalRow, 'Total');
        $detailsSheet->setCellValue('B' . $detailsTotalRow, $total_detail_visitors);

        // Style details sheet
        $detailsSheet->getStyle('A1:B1')->getFont()->setBold(true)->setSize(14);
        $detailsSheet->getStyle('A2')->getFont()->setItalic(true);
        $detailsSheet->getStyle('A4:B4')->getFont()->setBold(true);
        $detailsSheet->getStyle('A' . $detailsTotalRow . ':B' . $detailsTotalRow)->getFont()->setBold(true);
        $detailsSheet->getStyle('A' . $detailsTotalRow . ':B' . $detailsTotalRow)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E3E3E3');

        // Add chart to details sheet
        createTrafficChart($detailsSheet, 'Daily Visitors Detail', [
            [
                'range' => 'B5:B' . ($row-1),
                'title' => 'Daily Visitors',
                'color' => '4cc9f0',
                'labels' => $detailChartLabels
            ]
        ], 'line', 'daily', 'D2', 'K20');

        // Auto-size columns for both sheets
        foreach(['A', 'B'] as $col) {
            $summarySheet->getColumnDimension($col)->setAutoSize(true);
            $detailsSheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = sprintf('Traffic_Report_Monthly_%s_to_%s.xlsx',
            date('F_Y', strtotime($startDate)),
            date('F_Y', strtotime($endDate))
        );
    }

    // Verifikasi temporary directory
    $tempDir = sys_get_temp_dir();
    if (!is_writable($tempDir)) {
        throw new Exception("Temporary directory is not writable");
    }

    // Save dengan proper error handling
    $tempFile = tempnam($tempDir, 'excel');
    if ($tempFile === false) {
        throw new Exception("Cannot create temporary file");
    }

    $writer = new Xlsx($spreadsheet);
    $writer->setIncludeCharts(true);
    
    // Set proper headers sebelum output apapun
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    try {
        $writer->save($tempFile);
        
        if (!file_exists($tempFile)) {
            throw new Exception("Failed to create Excel file");
        }
        
        readfile($tempFile);
        unlink($tempFile);
    } catch (Exception $e) {
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
        throw $e;
    }
    
    // Clean up and exit
    $conn->close();
    exit();

} catch (Exception $e) {
    // Log error dengan detail
    error_log("[" . date('Y-m-d H:i:s') . "] Excel generation error: " . $e->getMessage());
    
    // Return proper error response
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => "Error generating report: " . $e->getMessage()
    ]);
    exit();
}

// Helper function to create traffic charts
function createTrafficChart($sheet, $title, $dataSeries, $chartType = 'line', $period = 'daily', $topLeft = 'D2', $bottomRight = 'K20') {
    $lastRow = $sheet->getHighestRow();
    $xRange = 'A5:A' . ($lastRow - 1);

    // Modern color palette - removed # prefix
    $colors = [
        '4361ee', // Primary blue
        '3a0ca3', // Deep purple
        '7209b7', // Bright purple
        'f72585', // Pink
        '4cc9f0'  // Light blue
    ];

    // Create custom labels for the chart
    $customLabels = isset($dataSeries[0]['labels']) ? $dataSeries[0]['labels'] : [];
    $xAxisTickValues = [
        new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues(
            'String',
            null,
            null,
            count($customLabels),
            $customLabels
        )
    ];

    $dataSeriesLabels = [];
    $dataSeriesValues = [];

    foreach ($dataSeries as $index => $series) {
        // Add series label
        $dataSeriesLabels[] = new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues(
            'String',
            null,
            null,
            1,
            [$series['title']]
        );

        // Add series values with color
        $seriesValues = new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues(
            'Number',
            $sheet->getTitle() . '!' . $series['range'],
            null,
            $lastRow - 5
        );
        
        $seriesValues->setFillColor($series['color'] ?? $colors[$index]);
        $dataSeriesValues[] = $seriesValues;
    }

    // Set chart type
    $chartType = strtolower($chartType) === 'line' 
        ? \PhpOffice\PhpSpreadsheet\Chart\DataSeries::TYPE_LINECHART 
        : \PhpOffice\PhpSpreadsheet\Chart\DataSeries::TYPE_BARCHART;

    $grouping = strtolower($chartType) === 'line'
        ? \PhpOffice\PhpSpreadsheet\Chart\DataSeries::GROUPING_STANDARD
        : \PhpOffice\PhpSpreadsheet\Chart\DataSeries::GROUPING_CLUSTERED;

    // Create the data series
    $series = new \PhpOffice\PhpSpreadsheet\Chart\DataSeries(
        $chartType,
        $grouping,
        range(0, count($dataSeriesValues) - 1),
        $dataSeriesLabels,
        $xAxisTickValues,
        $dataSeriesValues
    );

    if ($chartType === \PhpOffice\PhpSpreadsheet\Chart\DataSeries::TYPE_BARCHART) {
        $series->setPlotDirection(\PhpOffice\PhpSpreadsheet\Chart\DataSeries::DIRECTION_COL);
    }

    // Configure modern layout
    $layout = new \PhpOffice\PhpSpreadsheet\Chart\Layout();
    $layout->setShowVal(false);
    $layout->setShowCatName(true);
    $layout->setShowLeaderLines(false);
    $layout->setShowPercent(false);
    $layout->setShowBubbleSize(false);
    
    // Create plot area with gradient background
    $plotArea = new \PhpOffice\PhpSpreadsheet\Chart\PlotArea($layout, [$series]);
    
    // Configure legend
    $legend = new \PhpOffice\PhpSpreadsheet\Chart\Legend(
        \PhpOffice\PhpSpreadsheet\Chart\Legend::POSITION_TOP,
        null,
        false
    );

    // Create chart title with styling
    $chartTitle = new \PhpOffice\PhpSpreadsheet\Chart\Title($title);
    
    // Create and configure the chart
    $chart = new \PhpOffice\PhpSpreadsheet\Chart\Chart(
        'chart_' . $sheet->getTitle(),
        $chartTitle,
        $legend,
        $plotArea
    );

    // Adjust chart size based on data points
    $width = min(25, max(15, ceil($lastRow / 4))); // Dynamic width based on data points
    $chart->setTopLeftPosition($topLeft);
    $chart->setBottomRightPosition($bottomRight);

    $sheet->addChart($chart);
}
?>