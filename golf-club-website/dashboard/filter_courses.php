<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once '../config/database.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$length = isset($_GET['length']) ? $_GET['length'] : '';
$par = isset($_GET['par']) ? $_GET['par'] : '';
$handicap = isset($_GET['handicap']) ? $_GET['handicap'] : '';

$where_conditions = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where_conditions[] = "(name LIKE ? OR description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ss';
}

if (!empty($length)) {
    switch($length) {
        case '5000-':
            $where_conditions[] = "length < 5000";
            break;
        case '5000-6000':
            $where_conditions[] = "length BETWEEN 5000 AND 6000";
            break;
        case '6000+':
            $where_conditions[] = "length > 6000";
            break;
    }
}

if (!empty($par)) {
    switch($par) {
        case '70-':
            $where_conditions[] = "par < 70";
            break;
        case '70-72':
            $where_conditions[] = "par BETWEEN 70 AND 72";
            break;
        case '72+':
            $where_conditions[] = "par > 72";
            break;
    }
}

if (!empty($handicap)) {
    switch($handicap) {
        case '10-':
            $where_conditions[] = "handicap < 10";
            break;
        case '10-20':
            $where_conditions[] = "handicap BETWEEN 10 AND 20";
            break;
        case '20+':
            $where_conditions[] = "handicap > 20";
            break;
    }
}

$sql = "SELECT * FROM tbl_courses";
if (!empty($where_conditions)) {
    $sql .= " WHERE " . implode(" AND ", $where_conditions);
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$courses = [];

while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

header('Content-Type: application/json');
echo json_encode($courses);
