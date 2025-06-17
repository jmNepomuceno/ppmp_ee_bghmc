<?php
    include('../session.php');
    include('../assets/connection/sqlconnection.php');

    if (isset($_POST['item_id'])) {
        $itemID = $_POST['item_id'];
        $stmt = $pdo->prepare("SELECT itemName, itemPrice, itemSpecs, itemImage FROM imiss_inventory WHERE itemID = ?");
        $stmt->execute([$itemID]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            if (!empty($item['itemImage'])) {
                $item['itemImage'] = 'data:image/jpeg;base64,' . base64_encode($item['itemImage']);
            } else {
                $item['itemImage'] = null;
            }

            echo json_encode([
                'success' => true,
                'data' => $item
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Item not found']);
        }
    }
    
?>