<?php
include('./assets/connection/sqlconnection.php');

$sql = "SELECT itemID, itemImage FROM imiss_inventory";
$stmt = $pdo->query($sql);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($items as $item) {
    $itemID = $item['itemID'];

    if (!empty($item['itemImage'])) {
        $imagePath = "source/inventory_image/item_{$itemID}.jpg";
    } else {
        $imagePath = "source/inventory_image/default.jpg";
    }

    $update = $pdo->prepare("UPDATE imiss_inventory SET itemImagePath = ? WHERE itemID = ?");
    $update->execute([$imagePath, $itemID]);
}
echo "✅ Temp path set in itemImagePath column.";
?>
