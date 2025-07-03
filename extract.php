<?php
include('./assets/connection/sqlconnection.php');

    // $sql = "SELECT itemName FROM imiss_inventory";
    // $stmt = $pdo->query($sql);
    // $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // echo "<pre>"; print_r($items); echo "</pre>";

    $sql = "UPDATE imiss_inventory
    SET itemCategory = 'IT EQUIPMENTS AND SUPPLIES'
    WHERE itemCategory NOT IN ('INK & TONER', 'SUBSCRIPTION');";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();   

?>
