<?php
include ('../session.php');
include('../assets/connection/sqlconnection.php');

try {
    $sql = "SELECT cart FROM user_cart WHERE bioID=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION["user"]]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Decode cart JSON if available
    if ($data && !empty($data['cart'])) {
        $data['cart'] = json_decode($data['cart'], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $data['cart'] = []; // Fallback if JSON decoding fails
        }
    } else {
        $data = ["cart" => []]; // Ensure cart is an array
    }

    // Fetch item names and images
    foreach ($data['cart'] as &$cartItem) {
        $sql = "SELECT itemName, itemImage FROM imiss_inventory WHERE itemID=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cartItem['itemID']]);
        $itemData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($itemData) {
            $cartItem['itemName'] = $itemData['itemName'];

            // Convert image to base64 format
            if (!empty($itemData['itemImage'])) {
                $cartItem['itemImage'] = 'data:image/jpeg;base64,' . base64_encode($itemData['itemImage']);
            } else {
                $cartItem['itemImage'] = 'path/to/default-image.jpg'; // Fallback image
            }
        }
    }
    unset($cartItem); // Break reference to avoid unexpected issues

    // Send JSON response
    echo json_encode($data, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
