<?php
    include ('../session.php');
    include('../assets/connection/sqlconnection.php');

if (isset($_POST['data'])) {
    $cart = json_decode($_POST['data'], true); // Convert JSON string to PHP array
    
    if ($cart === null) {
        die("Error decoding JSON");
    }


    try {
        $sql = "SELECT bioID, cart FROM user_cart WHERE bioID=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION["user"]]);
        $duplicate = $stmt->fetch(PDO::FETCH_ASSOC);

        // print_r($cart);

        if($duplicate){
            // check if itemID already exists in cart
            $duplicate_itemID = [];
            $duplicate_value = [];

            if(isset($duplicate['cart'])){
                $duplicate_value = json_decode($duplicate['cart'], true);
                for($i = 0; $i < count($cart); $i++){
                    if(in_array($cart[$i]['itemID'], array_column($duplicate_value, 'itemID'))){
                         array_push($duplicate_itemID, $cart[$i]['itemID']);
                    }
                 }
            }

            if(count($duplicate_itemID) == 0){
                $duplicate_value = array_merge($duplicate_value, $cart);
            }else{
                for($i = 0; $i < count($duplicate_itemID); $i++){
                    $duplicate_index = array_search($duplicate_itemID[$i], array_column($duplicate_value, 'itemID'));
                    $duplicate_value[$duplicate_index]['itemQuantity'] = (int)$duplicate_value[$duplicate_index]['itemQuantity'] + (int)$cart[$i]['itemQuantity'];
                }
            }

            $sql = "UPDATE user_cart SET cart=? WHERE bioID=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                json_encode($duplicate_value), // Store as JSON
                $_SESSION["user"],
            ]);

            // echo json_encode($cart);
            // print_r($duplicate_itemID);
            // print_r($duplicate_value);
            // print_r($cart);

            // echo "duplicate";
        }else{
            $sql = "INSERT INTO user_cart (bioID, fullName, divisionID, divisionName, cart) VALUES (?,?,?,?,?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $_SESSION["user"],
                $_SESSION["name"],
                $_SESSION["section"],
                $_SESSION["sectionName"],
                json_encode($cart) // Store as JSON               
            ]);
            // echo "inserted";
        }

        // Decode `cart` field so it's an array instead of a string
        $sql = "SELECT cart FROM user_cart WHERE bioID=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION["user"]]);
        $output = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($output && !empty($output['cart'])) {
            $decodedCart = json_decode($output['cart'], true);

            // Ensure valid JSON decoding
            if (json_last_error() === JSON_ERROR_NONE) {
                $output['cart'] = $decodedCart;
            } else {
                $output['cart'] = []; // Fallback to an empty array if decoding fails
            }
        } else {
            $output = ["cart" => []]; // Handle case where no data is found
        }

        // Send JSON response
        echo json_encode($output, JSON_PRETTY_PRINT);

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    echo "No data received";
}
?>
