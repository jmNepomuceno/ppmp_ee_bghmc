<?php
    include('../session.php');
    include('../assets/connection/sqlconnection.php');

    // Get posted data
    $itemID = $_POST['item_id'] ?? '';
    $itemName = $_POST['item_name'] ?? '';
    $itemPrice = isset($_POST['item_price']) ? trim($_POST['item_price']) : '';
    $itemSpecs = $_POST['item_specs'] ?? '';

    // Check if item ID is provided
    if (empty($itemID)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing item ID']);
        exit;
    }

    // Handle optional image update
    $imageProvided = (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK);
    if ($imageProvided) {
        $itemImageContent = file_get_contents($_FILES['item_image']['tmp_name']);

        // Update including image
        $sql = "UPDATE imiss_inventory 
                SET itemName = ?, itemPrice = ?, itemSpecs = ?, itemImage = ?
                WHERE itemID = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$itemName, $itemPrice, $itemSpecs, $itemImageContent, $itemID]);
    } else {
        // Update without changing image
        $sql = "UPDATE imiss_inventory 
                SET itemName = ?, itemPrice = ?, itemSpecs = ?
                WHERE itemID = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$itemName, $itemPrice, $itemSpecs, $itemID]);
    }

    // ✅ Update session
    $fetchStmt = $pdo->query("SELECT itemID, itemName, itemPrice, itemSpecs, itemVisibility, itemImagePath FROM imiss_inventory");
    $item_data = $fetchStmt->fetchAll(PDO::FETCH_ASSOC);

    // Optional: match truncation logic for display
    for ($i = 0; $i < count($item_data); $i++) {
        if (isset($item_data[$i]['itemName']) && strlen($item_data[$i]['itemName']) > 75) {
            $item_data[$i]['itemName'] = substr($item_data[$i]['itemName'], 0, 75) . "...";
        }
    }

    $_SESSION['fetch_inventory'] = $item_data;

    echo json_encode([
        'status' => 'success',
        'message' => 'Item updated successfully',
        'updated_inventory' => $_SESSION['fetch_inventory']
    ]);
?>
