<?php
    include('../session.php');
    include('../assets/connection/sqlconnection.php');

    if (isset($_POST['item_id'])) {
        $itemID = $_POST['item_id'];
        $stmt = $pdo->prepare("SELECT itemName, itemPrice, itemSpecs, itemImagePath, itemUnit FROM imiss_inventory WHERE itemID = ?");
        $stmt->execute([$itemID]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            echo json_encode([
                'success' => true,
                'data' => $item
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Item not found']);
        }
    }

?>