<?php
    include('../session.php');
    include('../assets/connection/sqlconnection.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $itemID = $_POST['itemID'] ?? null;

        if ($itemID) {
            // Delete image file
            $imagePath = "../source/inventory_image/item_" . $itemID . ".jpg";
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Delete item from DB
            $stmt = $pdo->prepare("DELETE FROM imiss_inventory WHERE itemID = ?");
            $stmt->execute([$itemID]);

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Missing itemID']);
        }
    }
?>