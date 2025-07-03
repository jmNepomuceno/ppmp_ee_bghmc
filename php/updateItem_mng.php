<?php
    include('../session.php');
    include('../assets/connection/sqlconnection.php');

    // Get posted data
    $itemID = $_POST['item_id'] ?? '';
    $itemName = $_POST['item_name'] ?? '';
    $itemPrice = isset($_POST['item_price']) ? trim($_POST['item_price']) : '';
    $itemSpecs = $_POST['item_specs'] ?? '';
    $itemUnit = $_POST['item_unit'] ?? '';

    // Check if item ID is provided
    if (empty($itemID)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing item ID']);
        exit;
    }

    // No need to validate JSON anymore since specs is now plain text

    $imageProvided = (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK);
    if ($imageProvided) {
        $itemImageContent = file_get_contents($_FILES['item_image']['tmp_name']);

        // Update including image
        $sql = "UPDATE imiss_inventory 
                SET itemName = ?, itemPrice = ?, itemSpecs = ?, itemImage = ?, itemUnit = ?
                WHERE itemID = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$itemName, $itemPrice, $itemSpecs, $itemImageContent, $itemUnit, $itemID]);
    } else {
        // Update without changing image
        $sql = "UPDATE imiss_inventory 
                SET itemName = ?, itemPrice = ?, itemSpecs = ?, itemUnit = ?
                WHERE itemID = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$itemName, $itemPrice, $itemSpecs, $itemUnit, $itemID]);
    }

    // ✅ Update session
    $fetchStmt = $pdo->query("SELECT itemID, itemName, itemPrice, itemSpecs, itemVisibility, itemImagePath FROM imiss_inventory");
    $item_data = $fetchStmt->fetchAll(PDO::FETCH_ASSOC);

    // Truncate long item names
    foreach ($item_data as &$item) {
        if (isset($item['itemName']) && strlen($item['itemName']) > 75) {
            $item['itemName'] = substr($item['itemName'], 0, 75) . "...";
        }
    }

    $_SESSION['fetch_inventory'] = $item_data;

    echo json_encode([
        'status' => 'success',
        'message' => 'Item updated successfully',
        'updated_inventory' => $_SESSION['fetch_inventory']
    ]);
?>
