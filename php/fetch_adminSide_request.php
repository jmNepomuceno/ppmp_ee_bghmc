<?php
include('../session.php');
include('../assets/connection/sqlconnection.php');

header('Content-Type: application/json');

$sql = "SELECT 
            itemReqID,
            request_date,
            request_itemName,
            request_itemLink,
            request_itemImage,
            request_itemReason,
            request_userName,
            request_userSection
        FROM item_order_request 
        ORDER BY request_date DESC";

$stmt = $pdo->query($sql);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// // Inject fake requestor name for now if not stored
// foreach ($requests as &$req) {
//     $req['requestor_name'] = 'John Doe'; // replace if you have actual user data
// }

echo json_encode($requests);
