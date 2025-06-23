<?php
include('../session.php');
include('../assets/connection/sqlconnection.php');

$date = date('Y-m-d H:i:s');

// Generate itemReqID like REQ000001
function generateItemReqID($pdo) {
    $stmt = $pdo->query("SELECT itemReqID FROM item_order_request ORDER BY itemReqID DESC LIMIT 1");
    $latest = $stmt->fetchColumn();

    if ($latest) {
        $num = intval(substr($latest, 3)) + 1;
        return 'REQ' . str_pad($num, 6, '0', STR_PAD_LEFT);
    } else {
        return 'REQ000001';
    }
}

// Get form inputs
$product_name   = $_POST['product_name'] ?? '';
$product_link   = $_POST['product_link'] ?? '';
$product_reason = $_POST['request_reason'] ?? '';

$image_path = 'source/request_image/default.jpg'; // fallback image

$itemReqID = generateItemReqID($pdo);

// Save initial request (image_path will be updated if a file is uploaded)
$sql = "INSERT INTO item_order_request (
            itemReqID, request_itemName, request_itemLink, request_itemImage, request_itemReason, request_date, request_userName, request_userSection
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $itemReqID,
    $product_name,
    $product_link,
    $image_path,
    $product_reason,
    $date,
    $_SESSION['name'],
    $_SESSION['sectionName']
]);

// Upload image if provided
if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../source/request_image/';
    $relativePath = 'source/request_image/' . $itemReqID . '.jpg';
    $targetPath = $uploadDir . $itemReqID . '.jpg';

    // Make sure directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetPath)) {
        // Ensure proper permissions (Windows/IIS safe)
        shell_exec('icacls ' . escapeshellarg($targetPath) . ' /inheritance:e');

        // Update the record with new image path
        $pdo->prepare("UPDATE item_order_request SET request_itemImage = ? WHERE itemReqID = ?")
            ->execute([$relativePath, $itemReqID]);
    }
}

echo json_encode([
    'status' => 'success',
    'itemReqID' => $itemReqID
]);
?>
