<?php 
    session_start();
    include('./assets/connection/sqlconnection.php');
    date_default_timezone_set('Asia/Manila');

    // $sql = "SELECT * FROM request_history";
    // $stmt = $pdo->prepare($sql);
    // $stmt->execute();
    // $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // echo '<pre>'; print_r($data); echo '</pre>';

    // $sql = "DELETE FROM ppmp_request WHERE orderID='ORDER00016'";
    // $stmt = $pdo->prepare($sql);
    // $stmt->execute();

    // $sql = "DELETE FROM request_history WHERE orderID='ORDER00016'";
    // $stmt = $pdo->prepare($sql);
    // $stmt->execute();

    $filter = 'Approved';

    if($filter == 'All'){
        $sql = "SELECT * FROM ppmp_request";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }else{
        $sql = "SELECT * FROM ppmp_request WHERE order_status=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$filter]);
    }
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    for($i = 0; $i < count($data); $i++){
        if ($data[$i]['order_item'] && !empty($data[$i]['order_item'])) {
            $decodedCart = json_decode($data[$i]['order_item'], true);

            // Ensure valid JSON decoding
            if (json_last_error() === JSON_ERROR_NONE) {
                $data[$i]['order_item'] = $decodedCart;
            } else {
                $data[$i]['order_item'] = []; // Fallback to an empty array if decoding fails
            }
        } else {
            $data[$i]['order_item'] = ["order_item" => []]; // Handle case where no data is found
        }

        $sql = "SELECT fullName FROM user_cart WHERE bioID=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([(int)$data[$i]['order_by']]);
        $data_name = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql = "SELECT sectionName FROM pgsSection WHERE sectionID=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([(int)$data[$i]['order_by_section']]);
        $data_section = $stmt->fetch(PDO::FETCH_ASSOC);

        // $data[$i]['order_by_name'] = $data_name['fullName'];
        // $data[$i]['order_by_sectionName'] = $data_section['sectionName'];
        echo '<pre>'; print_r($data[$i]['order_by']); echo '</pre>';
        echo '<pre>'; print_r($data_name); echo '</pre>';

    }

    // echo '<pre>'; print_r($data); echo '</pre>';

?>