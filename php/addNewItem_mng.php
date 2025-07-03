<?php
include('../session.php');
include('../assets/connection/sqlconnection.php');

// Set visibility to true
$itemVisibility = 'true';

// Get form inputs
$itemName = $_POST['item_name'] ?? '';
$itemPrice = isset($_POST['item_price']) ? trim($_POST['item_price']) : '';
$itemSpecs = $_POST['item_specs'] ?? '';

// Default image path
$imagePath = 'source/inventory_image/default.jpg';

// Insert item first (with default image path)
$sql = "INSERT INTO imiss_inventory (itemName, itemPrice, itemSpecs, itemVisibility, itemImagePath, itemUnit)
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    $itemName,
    $itemPrice,
    $itemSpecs,
    $itemVisibility,
    $imagePath,
    $_POST['item_unit'] ?? 'pc' // Default to 'pcs' if not provided
]);

// Get the last inserted itemID
$lastItemID = $pdo->lastInsertId();

// If image is uploaded, store it and update path
if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../source/inventory_image/';
    $relativePath = "source/inventory_image/item_" . $lastItemID . ".jpg";
    $targetPath = $uploadDir . "item_" . $lastItemID . ".jpg";

    // Ensure folder exists and is writable
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Move uploaded file
    if (move_uploaded_file($_FILES['item_image']['tmp_name'], $targetPath)) {
        // Apply inherited permissions for IIS
        shell_exec('icacls ' . escapeshellarg($targetPath) . ' /inheritance:e');

        // Update image path in DB
        $updateStmt = $pdo->prepare("UPDATE imiss_inventory SET itemImagePath = ? WHERE itemID = ?");
        $updateStmt->execute([$relativePath, $lastItemID]);
    }
}

// Refresh session cache
$fetchStmt = $pdo->query("SELECT itemID, itemName, itemPrice, itemSpecs, itemVisibility, itemImagePath FROM imiss_inventory");
$item_data = $fetchStmt->fetchAll(PDO::FETCH_ASSOC);

// Truncate long item names
foreach ($item_data as &$item) {
    if (isset($item['itemName']) && strlen($item['itemName']) > 75) {
        $item['itemName'] = substr($item['itemName'], 0, 75) . "...";
    }
}

$_SESSION['fetch_inventory'] = $item_data;

// Respond to client
echo json_encode([
    'status' => 'success',
    'message' => 'Item added successfully',
    'updated_inventory' => $_SESSION['fetch_inventory']
]);
?>
