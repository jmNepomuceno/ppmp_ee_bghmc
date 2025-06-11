<?php 
    include ('../session.php');
    include('../assets/connection/sqlconnection.php');


    $sql = "SELECT * FROM imiss_inventory";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $item_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    for($i = 0; $i < count($item_data); $i++) {
        $itemImageData = $item_data[$i]['itemImage']; // Get the BLOB data
        if (!empty($itemImageData)) {
            $item_data[$i]['itemImage'] = 'data:image/jpeg;base64,' . base64_encode($itemImageData);
        } else {
            $item_data[$i]['itemImage'] = 'path/to/default-image.jpg'; // Provide a default image path
        }

        if (isset($item_data[$i]['itemName']) && strlen($item_data[$i]['itemName']) > 75) {
            $item_data[$i]['itemName'] = substr($item_data[$i]['itemName'], 0, 75) . "...";
        }
    }

    echo json_encode($item_data);
?>