<?php
include ('../session.php');
include('../assets/connection/sqlconnection.php');

require "../vendor/autoload.php";  // Ensure Composer's autoload is included
use WebSocket\Client;

$date = date('Y-m-d H:i:s');

try {
    $client = new Client("ws://192.168.42.222:8081");
    $client->send(json_encode(["action" => "refreshSideBar"]));

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
