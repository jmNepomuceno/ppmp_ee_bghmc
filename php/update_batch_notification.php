<?php
    include ('../session.php');
    include('../assets/connection/sqlconnection.php');

    require "../vendor/autoload.php";  // Ensure Composer's autoload is included
    use WebSocket\Client;

    $date = date('Y-m-d H:i:s');
    $type = $_POST['type'];

    switch ($type) {
        case "Pending":
            $type = "incoming_request";
          break;
        case "Cancelled":
            $type = "updated";
          break;
    }

    try {
        $sql = "UPDATE ppmp_notification SET isRead=1 WHERE isRead=0 AND notifReceiver='admin' AND notifStatus=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$type]);

    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
?>
