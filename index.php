<?php 
    include ('./session.php');
    include('./assets/connection/sqlconnection.php');

    // $permissions = '{"inventory_lists": false, "order_management": false, "admin_function": false}';

    // $sql = "INSERT INTO permission (role, permission) VALUES ('admin' , ?)";
    // $stmt = $pdo->prepare($sql);
    // $stmt->execute([$permissions]);
    
    // echo '<pre>'; print_r($data); echo '</pre>';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHOP-PPMPee</title>
    <link rel="stylesheet" href="./index.css">

    <?php require "./links/header_link.php" ?>
</head>
<body>
    <div class="left-container">
        <img src="./source/landing_css/logo.PNG" alt="logo-img">
    </div>


    <div class="right-container">
        <span>USER LOGIN</span>

        <div class="credential-div" id="username-div">
            <div class="credential-icon-div">
                <i class="fa-solid fa-user"></i>
            </div>
            <input type="text" class="credential-inputs" id="username-txt" placeholder="Username" autocomplete="off">
        </div>

        <div class="credential-div" id="password-div">
            <div class="credential-icon-div">
                <i class="fa-solid fa-lock"></i>
            </div>
            <input type="password" class="credential-inputs" id="password-txt" placeholder="Password" autocomplete="off">
        </div>

        <h6>No account yet? <a id="sign-up-a" href="http://192.168.42.10:8085/">Sign Up</a></h6>

        <button id="login-btn">LOGIN</button>
    </div>

    <div class="modal fade" id="modal-notif" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-top" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modal-title-incoming" class="modal-title-incoming" id="exampleModalLabel">Successfully Updated</h5>
                </div>
                <div id="modal-body-incoming" class="modal-body-incoming ml-2">
                    
                </div>
                <div class="modal-footer">
                    <button id="close-modal-btn-incoming" type="button" type="button" data-bs-dismiss="modal">CLOSE</button>
                </div>
            </div>
        </div>
    </div>

    <?php require "./links/script_links.php" ?>
    <script type="text/javascript" src="./index.js"></script>
</body>
</html>
