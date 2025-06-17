<?php
    include ('../session.php');
    include('../assets/connection/sqlconnection.php');

    // Set visibility to true
    $itemVisibility = 'true';

    // Get form inputs
    $itemName = $_POST['item_name'] ?? '';
    $itemPrice = isset($_POST['item_price']) ? trim($_POST['item_price']) : '';
    $itemSpecs = $_POST['item_specs'] ?? '';

    // Default image content
    $itemImageContent = null;

    // Read file as binary blob
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
        $itemImageContent = file_get_contents($_FILES['item_image']['tmp_name']);
    }

    // Prepare SQL
    $sql = "INSERT INTO imiss_inventory (itemName, itemPrice, itemSpecs, itemVisibility, itemImage) VALUES (?,?,?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $itemName,
        $itemPrice,
        $itemSpecs,
        $itemVisibility,
        $itemImageContent
    ]);

    // ✅ Update session
    $fetchStmt = $pdo->query("SELECT itemID, itemName, itemPrice, itemSpecs, itemVisibility FROM imiss_inventory WHERE itemVisibility = 'true'");
    $item_data = $fetchStmt->fetchAll(PDO::FETCH_ASSOC);

    // Optional: match truncation logic for display
    for ($i = 0; $i < count($item_data); $i++) {
        if (isset($item_data[$i]['itemName']) && strlen($item_data[$i]['itemName']) > 75) {
            $item_data[$i]['itemName'] = substr($item_data[$i]['itemName'], 0, 75) . "...";
        }
    }

    $_SESSION['fetch_inventory'] = $item_data;

    echo json_encode(['status' => 'success', 'message' => 'Item added with image blob']);

?>
