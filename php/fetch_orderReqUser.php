<?php
include ('../session.php');
include('../assets/connection/sqlconnection.php');

$filter = $_POST['filter'];

try {
    if($filter == 'All'){
        $sql = "SELECT * FROM ppmp_request WHERE order_by=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([(int)$_SESSION['user']]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    }else{
        $sql = "SELECT * FROM ppmp_request WHERE order_by=? AND order_status=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([(int)$_SESSION['user'] , $filter]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    for ($i = 0; $i < count($data); $i++) {
        $data[$i]['order_item'] = !empty($data[$i]['order_item']) ? json_decode($data[$i]['order_item'], true) : [];

        // Fetch order_by fullName
        $sql = "SELECT fullName FROM user_cart WHERE bioID=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([(int)$data[$i]['order_by']]);
        $data[$i]['order_by_name'] = $stmt->fetchColumn();

        // Fetch sectionName
        $sql = "SELECT sectionName FROM pgsSection WHERE sectionID=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([(int)$data[$i]['order_by_section']]);
        $data[$i]['order_by_sectionName'] = $stmt->fetchColumn();

        // Fetch request history
        // 3374, 3858, 2514
        $sql = "SELECT * FROM request_history WHERE orderID=? AND (edit_by=3374 OR edit_by=3858 OR edit_by=2514)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$data[$i]['orderID']]);
        $history_orderID = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Collect all itemIDs from previousOrder and updatedOrder
        $allItemIDs = [];
        foreach ($history_orderID as &$history) {
            foreach (['previousOrder', 'updatedOrder'] as $orderKey) {
                if (!empty($history[$orderKey])) {
                    $decodedCart = json_decode($history[$orderKey], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        foreach ($decodedCart as $orderItem) {
                            if (!empty($orderItem['itemID'])) {
                                $allItemIDs[] = (int)$orderItem['itemID'];
                            }
                        }
                        $history[$orderKey] = $decodedCart;
                    } else {
                        $history[$orderKey] = [];
                    }
                }
            }
        }

        // Remove duplicate itemIDs and fetch item names in one query
        if (!empty($allItemIDs)) {
            $placeholders = implode(',', array_fill(0, count($allItemIDs), '?'));
            $sql = "SELECT itemID, itemName, itemImage FROM imiss_inventory WHERE itemID IN ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($allItemIDs);
            $itemsMap = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch as associative array (itemID => [itemName, itemImage])

            // Create a map to easily access itemName and itemImage by itemID
            $itemsMap = array_column($itemsMap, null, 'itemID');
        } else {
            $itemsMap = [];
        }

        // Attach item names and item images to previousOrder and updatedOrder
        foreach ($history_orderID as &$history) {
            foreach (['previousOrder', 'updatedOrder'] as $orderKey) {
                foreach ($history[$orderKey] as &$orderItem) {
                    $itemID = $orderItem['itemID'];
                    // Attach item name and item image from the map
                    $orderItem['itemName'] = $itemsMap[$itemID]['itemName'] ?? "Unknown Item";
                    $orderItem['itemImage'] = !empty($itemsMap[$itemID]['itemImage'])
                        ? 'data:image/jpeg;base64,' . base64_encode($itemsMap[$itemID]['itemImage'])
                        : 'path/to/default-image.jpg';
                }
            }
        }

        // Attach updated history to main data array
        $data[$i]['history_update'] = $history_orderID;
    }

    echo json_encode($data);

    // print_r($data[3]['history_update'][0]['previousOrder'][0]['itemID']);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

?>
