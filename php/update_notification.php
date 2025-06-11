<?php
include ('../session.php');
include('../assets/connection/sqlconnection.php');

require "../vendor/autoload.php";  // Ensure Composer's autoload is included
use WebSocket\Client;

$date = date('Y-m-d H:i:s');

try {
    $notifID = $_POST['notifID'];
    $sql = "UPDATE ppmp_notification SET isRead=1 WHERE notifID=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$notifID]);


} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
