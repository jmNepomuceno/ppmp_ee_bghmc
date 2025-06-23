<?php 
    include ('../session.php');
    include('../assets/connection/sqlconnection.php');
    
    $allowed_roles = ["admin"];
    if (!in_array($_SESSION["role"], $allowed_roles)) {
        if (!in_array($_SESSION["role"], $allowed_roles)) {
            die("<h2>Access Denied</h2><p>You do not have permission to access this page.</p>");
        }
        header("Location: ../views/home.php");

    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="../css/item_request.css">
    <?php require "../links/header_link.php" ?>
</head>
<body>
    <?php 
        $view = "item-request-sub-div";
        include("./sidebar.php")
    ?>

    <i class="fa-solid fa-bars" id="burger-icon"></i>

    <div class="right-container">
        <?php 
            $navbar_view = "ITEM ORDER REQUEST";
            include("./navbar.php");
        ?>
        <div class="item-order-form-container">
            <h2>Item Order Request</h2>
            <form id="item-order-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="product-name">Product Name <span>*</span></label>
                    <input type="text" id="product-name" name="product_name" required>
                </div>

                <div class="form-group">
                    <label for="product-link">Website Link (Reference / Market Study)</label>
                    <input type="url" id="product-link" name="product_link" placeholder="https://example.com/item">
                </div>

                <div class="form-group">
                    <label for="product-image">Item Image</label>
                    <input type="file" id="product-image" name="product_image" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="reason">Reason for Request <span>*</span></label>
                    <textarea id="reason" name="request_reason" rows="4" required></textarea>
                </div>

                <button type="submit" class="submit-btn" id="submit-request-btn">
                    <i class="fa-solid fa-paper-plane"></i> Submit Request
                </button>
            </form>
        </div>

        <div class="user-request-history">
            <h2>My Item Requests</h2>
            <div id="user-request-container"></div>
        </div>


        
    </div>

    <div class="modal fade" id="modal-notif" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-top" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modal-title-incoming" class="modal-title-incoming" id="exampleModalLabel">Your Cart</h5>
                </div>
                <div id="modal-body-incoming" class="modal-body-incoming ml-2">
                    
                </div>
                <div class="modal-footer">
                    <button id="close-modal-btn-incoming" type="button" type="button" data-bs-dismiss="modal">CLOSE</button>
                </div>
            </div>
        </div>
    </div>

    <?php require "../links/script_links.php" ?>
    <script> 
        var section = "<?php echo $section ?>";
    </script>
    <!-- <script src="../js-obf/home_traverse-obf.js?v=<?php echo time(); ?>"></script>
    <script src="../js-obf/item_distribution_function-obf.js?v=<?php echo time(); ?>"></script> -->

    <script src="../js/home_traverse.js?v=<?php echo time(); ?>"></script>
    <script src="../js/item_request_function.js?v=<?php echo time(); ?>"></script>
</body>
</html>