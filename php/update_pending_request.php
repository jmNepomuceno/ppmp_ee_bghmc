<?php
    include ('../session.php');
    include('../assets/connection/sqlconnection.php');

    require "../vendor/autoload.php";  // Ensure Composer's autoload is included
    use WebSocket\Client;

    $orderID = $_POST['orderID'];
    $itemID = (int)$_POST['itemID']; 
    $itemQuantity = $_POST['itemQuantity']; 
    $action = $_POST['action'];
    $date = date('Y-m-d H:i:s');
    $current_date = date('Y-m-d H:i:s');
    $todo = "";
    try {
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


        // print_r($current_cart);
        if(count($current_cart['order_item']) == 0){
            // $sql = "DELETE FROM ppmp_request WHERE orderID=?";
            // $stmt = $pdo->prepare($sql);
            // $stmt->execute([$orderID]);
            
            $todo = ($_POST['from'] == 'admin') ? 'Rejected' : 'Cancelled';
            $sql = "UPDATE ppmp_request SET order_status=? , order_item=? WHERE orderID=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$todo,  json_encode($current_cart['order_item']), $orderID]);
        }else{
            $sql = "UPDATE ppmp_request SET order_item=? WHERE orderID=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                json_encode($current_cart['order_item']), // Store as JSON
                $orderID,
            ]);
        }

        // insert request history
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

        $sql = "SELECT order_item FROM ppmp_request WHERE orderID=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderID]);
        $updated_cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($updated_cart && !empty($updated_cart['order_item'])) {
            $decodedCart = json_decode($updated_cart['order_item'], true);
    
            // Ensure valid JSON decoding
            if (json_last_error() === JSON_ERROR_NONE) {
                $updated_cart['order_item'] = $decodedCart;
            } else {
                $updated_cart['order_item'] = []; // Fallback to an empty array if decoding fails
            }
        } else {
            $updated_cart = ["order_item" => []]; // Handle case where no data is found
        }

        echo json_encode($updated_cart, JSON_PRETTY_PRINT);

        // notification query
        $sql = "SELECT order_date, order_by_section FROM ppmp_request WHERE orderID=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderID]);
        $notif_order_date = $stmt->fetch(PDO::FETCH_ASSOC);

        $notifMessage = "";
        $notifStatus = "";
        $notifReceiver = "";
        $date = $notif_order_date['order_date'];
        $dateTime = new DateTime($date);
        $notifDate = $dateTime->format("F j, Y"); // Format the date as "April 4, 2025"

        if ($action == 'update') {
            $notifStatus = "updated";
            $notifMessage = ($_POST['from'] == 'admin')
                ? "Admin updates your request on {$notifDate}"
                : "{$_SESSION['sectionName']} updated their request on {$notifDate}";
        } else {
            if($current_cart['order_item'] == null){
                if ($todo == 'Rejected') {
                    $notifStatus = "rejected";
                    $notifMessage = ($_POST['from'] == 'admin')
                        ? "Admin rejects your request on {$notifDate}"
                        : "{$_SESSION['sectionName']} Rejected their request on {$notifDate}";
                } else {
                    $notifStatus = "cancelled";
                    $notifMessage = ($_POST['from'] == 'admin')
                        ? "Admin cancels your request on {$notifDate}"
                        : "{$_SESSION['sectionName']} Cancelled their request on {$notifDate}";
                }    
            }else{
                $notifStatus = "updated";
                $notifMessage = ($_POST['from'] == 'admin')
                    ? "Admin updates your request on {$notifDate}"
                    : "{$_SESSION['sectionName']} updated their request on {$notifDate}";
            }

           
        }
        
        if($_POST['from'] == 'admin'){
            $notifReceiver = $notif_order_date['order_by_section'];
        }
        else{
            $notifReceiver = 'admin';
        }
        $sql = "INSERT INTO ppmp_notification (orderID, notifStatus, notifMessage, notifReceiver, isRead, created_at) VALUES (?,?,?,?,?,?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $orderID,
            $notifStatus,
            $notifMessage,
            $notifReceiver,
            0,
            $current_date
        ]);


        $client = new Client("ws://192.168.42.222:8081");
        $client->send(json_encode(["action" => "refreshSideBar"]));    
        $client->send(json_encode(["action" => "refreshIncomingOrder"]));    
        $client->send(json_encode(["action" => "refreshNavbar"]));    

    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }

?>
