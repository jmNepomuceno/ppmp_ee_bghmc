<?php
    include ('../session.php');
    include('../assets/connection/sqlconnection.php');

    try {
        $sql = "SELECT cart FROM user_cart WHERE bioID=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION["user"]]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data && !empty($data['cart'])) {
            $decodedCart = json_decode($data['cart'], true);

            // Ensure valid JSON decoding
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['cart'] = $decodedCart;
            } else {
                $data['cart'] = []; // Fallback to an empty array if decoding fails
            }
        } else {
            $data = ["cart" => []]; // Handle case where no data is found
        }

        // Send JSON response
        echo json_encode($data, JSON_PRETTY_PRINT);

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
?>
