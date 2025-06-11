<?php
include ('../session.php');
include('../assets/connection/sqlconnection.php');

require "../vendor/autoload.php";  // Ensure Composer's autoload is included
use WebSocket\Client;

$date = date('Y-m-d H:i:s');

try {
    $sql = "SELECT cart FROM user_cart WHERE bioID=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION["user"]]);
    $fetch_cart = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($fetch_cart && !empty($fetch_cart['cart'])) {
        $decodedCart = json_decode($fetch_cart['cart'], true);

        // Ensure valid JSON decoding
        if (json_last_error() === JSON_ERROR_NONE) {
            $fetch_cart['cart'] = $decodedCart;
        } else {
            $fetch_cart['cart'] = []; // Fallback to an empty array if decoding fails
        }
    } else {
        $fetch_cart = ["cart" => []]; // Handle case where no data is found
    }

    $orderID = "";
    $sql = "SELECT orderID FROM ppmp_request ORDER BY orderID DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $last_id = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($last_id && isset($last_id['orderID'])) {
        // Extract numeric part (assuming format ORDER00002)
        $num = (int) substr($last_id['orderID'], 5); // Get '00002' and convert to int (2)
        $new_num = str_pad($num + 1, 5, "0", STR_PAD_LEFT); // Increment and pad to 5 digits
        $orderID = "ORDER" . $new_num; // Concatenate with 'ORDER'
    } else {
        $orderID = "ORDER00001"; // Default if no record exists
    }

    $sql = "INSERT INTO ppmp_request (orderID, order_date, order_item, order_by, order_by_section, order_status) VALUES (?,?,?,?,?,'Pending')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $orderID,
        $date,
        json_encode($fetch_cart['cart']),
        $_SESSION["user"],      
        $_SESSION["section"],      
    ]);

    $notifMessage = "Incoming request from {$_SESSION["sectionName"]}";
    $sql = "INSERT INTO ppmp_notification (orderID, notifStatus, notifMessage, notifReceiver, isRead, created_at) VALUES (?,?,?,?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $orderID,
        "incoming_request",
        $notifMessage,
        ($_SESSION["role"] === "user") ? 'admin' : 'user', // Check if the user is an admin
        0,
        $date
    ]);


    // $sql = "DELETE cart FROM user_cart WHERE bioID=?";
    // $stmt = $pdo->prepare($sql);
    // $stmt->execute([$_SESSION["user"]]);
    $sql = "UPDATE user_cart SET cart=NULL WHERE bioID=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION["user"]]);

    
    $client = new Client("ws://192.168.42.222:8081");
    $client->send(json_encode(["action" => "refreshIncomingOrder"]));
    $client->send(json_encode(["action" => "refreshSideBar"]));
    $client->send(json_encode(["action" => "refreshNavbar"]));

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
