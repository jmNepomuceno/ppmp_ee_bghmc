<?php
include('../session.php');
include('../assets/connection/sqlconnection.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['itemReqID'])) {
    $itemReqID = $_POST['itemReqID'];

    $sql = "SELECT request_itemName, request_itemLink, request_itemImage, request_itemReason
            FROM item_order_request
            WHERE itemReqID = :itemReqID";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':itemReqID' => $itemReqID]);

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Item not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
