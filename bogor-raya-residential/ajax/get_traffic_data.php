<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['nama'])) {
    http_response_code(401);
    exit(json_encode(['error' => true, 'message' => 'Unauthorized']));
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $period = $data['period'] ?? 'daily';
    $startDate = $data['startDate'] ?? date('Y-m-d');
    $endDate = $data['endDate'] ?? date('Y-m-d');

    $conn = new mysqli("localhost", "root", "", "db_bogor");
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed");
    }

    if ($period === 'monthly') {
        $sql = "SELECT 
                DATE_FORMAT(visit_date, '%Y-%m') as period_date,
                DATE_FORMAT(visit_date, '%Y-%m-01') as visit_date,
                SUM(total_visitors) as total_visitors 
                FROM view_daily_visitors 
                WHERE DATE_FORMAT(visit_date, '%Y-%m') BETWEEN DATE_FORMAT(?, '%Y-%m') AND DATE_FORMAT(?, '%Y-%m')
                GROUP BY period_date
                ORDER BY period_date";
    } else {
        $sql = "SELECT 
                visit_date,
                SUM(total_visitors) as total_visitors 
                FROM view_daily_visitors 
                WHERE visit_date BETWEEN ? AND ?
                GROUP BY DATE(visit_date)
                ORDER BY visit_date";
    }
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $labels = [];
    $values = [];

    while ($row = $result->fetch_assoc()) {
        if ($period === 'monthly') {
            $labels[] = date('F', strtotime($row['visit_date']));
        } else {
            $labels[] = date('l, d F Y', strtotime($row['visit_date']));
        }
        $values[] = intval($row['total_visitors']);
    }

    echo json_encode([
        'error' => false,
        'labels' => $labels,
        'values' => $values,
        'label' => $period === 'monthly' ? 'Monthly Visitors' : 'Daily Visitors'
    ]);

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>
