<?php
include('../session.php');
include('../assets/connection/sqlconnection.php');

header('Content-Type: application/json');

// Fetch user-specific item requests (filter by user if needed)
$sql = "SELECT request_itemName, request_date FROM item_order_request ORDER BY request_date DESC";
$stmt = $pdo->query($sql);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Compute time ago
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) return "just now";
    if ($diff < 3600) return floor($diff / 60) . " minute(s) ago";
    if ($diff < 86400) return floor($diff / 3600) . " hour(s) ago";
    if ($diff < 604800) return floor($diff / 86400) . " day(s) ago";
    return date("F j, Y", $timestamp);
}

// Format response
foreach ($requests as &$row) {
    $row['formattedDate'] = date("F j, Y", strtotime($row['request_date']));
    $row['timeAgo'] = timeAgo($row['request_date']);
}

echo json_encode($requests);
?>
