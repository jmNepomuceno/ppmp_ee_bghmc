<?php
    include ('../session.php');
    include('../assets/connection/sqlconnection.php');

    $orderID = $_POST['orderID'];
    $itemID = (int)$_POST['itemID']; 
    $itemQuantity = $_POST['itemQuantity']; 

    $action = "update";
    $date = date('Y-m-d H:i:s');

    try {
        // PPMP DRAFT EDIT
        $sql = "SELECT itemPrice FROM imiss_inventory WHERE itemID=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$itemID]);
        $itemPrice_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $itemPrice_data = (int)$itemPrice_data['itemPrice'] * (int)$itemQuantity;

        $sql = "UPDATE imiss_ppmp_finaldraft SET itemTotalQuantity=?, itemEstimBudget=? WHERE orderID=? AND itemID=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $itemQuantity, 
            $itemPrice_data,
            $orderID,
            $itemID
        ]);

        // PPMP REQUEST
        $sql = "SELECT order_item FROM ppmp_request WHERE orderID=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderID]);
        $current_cart = $stmt->fetch(PDO::FETCH_ASSOC);
        
        
        if ($current_cart && !empty($current_cart['order_item'])) {
            $decodedCart = json_decode($current_cart['order_item'], true);

            // Ensure valid JSON decoding
            if (json_last_error() === JSON_ERROR_NONE) {
                $current_cart['order_item'] = $decodedCart;
            } else {
                $current_cart['order_item'] = []; // Fallback to an empty array if decoding fails
            }
        } else {
            $current_cart = ["order_item" => []]; // Handle case where no data is found
        }

        $orig = $current_cart;

        if($action == 'update'){
            for($i = 0; $i < count($current_cart['order_item']); $i++) {
                if($current_cart['order_item'][$i]['itemID'] == $itemID){
                    $current_cart['order_item'][$i]['itemQuantity'] = $itemQuantity;
                }
            }
        }else{
            // Use array_filter to remove the item with the specified itemID
            $current_cart['order_item'] = array_filter($current_cart['order_item'], function($item) use ($itemID) {
                return $item['itemID'] != $itemID; // Keep items where itemID is not equal to the one to be deleted
            });

            // Reindex the array to avoid gaps in the keys
            $current_cart['order_item'] = array_values($current_cart['order_item']);
        }


        $sql = "UPDATE ppmp_request SET order_item=? WHERE orderID=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            json_encode($current_cart['order_item']), // Store as JSON
            $orderID,
        ]);

        // request history update
        $historyID = "";
        $sql = "SELECT historyID FROM request_history ORDER BY historyID DESC LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $last_id = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($last_id && isset($last_id['historyID'])) {
            // Extract numeric part (assuming format ORDER00002)
            $num = (int) substr($last_id['historyID'], 5); // Get '00002' and convert to int (2)
            $new_num = str_pad($num + 1, 5, "0", STR_PAD_LEFT); // Increment and pad to 5 digits
            $historyID = "HSTRY" . $new_num; // Concatenate with 'ORDER'
        } else {
            $historyID = "HSTRY00001"; // Default if no record exists
        }

        // $edit_by = ($_SESSION['role'] == 'admin') ? 'admin' : ;

        $sql = "INSERT INTO request_history (historyID, orderID, previousOrder, updatedOrder, dateEdited, edit_by) 
        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $historyID,
            $orderID,
            json_encode($orig['order_item']),
            json_encode($current_cart['order_item']),
            $date,
            $_SESSION['user']
        ]);

        // echo $itemQuantity;
        // print_r($_POST);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }

?>
