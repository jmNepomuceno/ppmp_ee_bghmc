<?php
    include ('../session.php');
    include('../assets/connection/sqlconnection.php');

    // // Set visibility to true
    // $itemVisibility = 'true';

    // // Get form inputs
    // $itemName = $_POST['item_name'] ?? '';
    // $itemPrice = isset($_POST['item_price']) ? trim($_POST['item_price']) : '';
    // $itemSpecs = $_POST['item_specs'] ?? '';

    // // Default image content
    // $itemImageContent = null;

    // // Read file as binary blob
    // if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
    //     $itemImageContent = file_get_contents($_FILES['item_image']['tmp_name']);
    // }

    // // Prepare SQL
    // $sql = "INSERT INTO imiss_inventory (itemName, itemPrice, itemSpecs, itemVisibility, itemImage) VALUES (?,?,?,?,?)";
    // $stmt = $pdo->prepare($sql);
    // $stmt->execute([
    //     $itemName,
    //     $itemPrice,
    //     $itemSpecs,
    //     $itemVisibility,
    //     $itemImageContent
    // ]);

    echo json_encode(['status' => 'success', 'message' => 'Item added with image blob']);

?>
