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
    <link rel="stylesheet" href="../css/imiss_ppmp.css">
    <?php require "../links/header_link.php" ?>
</head>
<body>
    <?php 
        $view = "imiss-ppmp-sub-div";
        include("./sidebar.php")
    ?>


<i class="fa-solid fa-bars" id="burger-icon"></i>

    <div class="right-container">
        <?php 
            $navbar_view = "IMISS PPMP DRAFT";
            include("./navbar.php");
        ?>
        <h1>IMISS PPMP Draft</h1>
        <div class="table-div">
            <table id="cart-table" class="display">
                <thead>
                    <tr>
                        <th rowspan="2">Item No.</th>
                        <th rowspan="2">Item Description / General Specification</th>
                        <th rowspan="2">Total Quantity</th>
                        <th rowspan="2">Unit</th>
                        <th rowspan="2">Unit Price</th>
                        <th rowspan="2">Estimated Budget</th>
                        <th rowspan="2">Mode of Procurement</th>
                        <th colspan="17">Schedule / Milestone of Activities</th>
                    </tr>
                    <tr>
                        <th>Q1</th>
                        <th>Jan</th>
                        <th>Feb</th>
                        <th>Mar</th>
                        <th>Q2</th>
                        <th>Apr</th>
                        <th>May</th>
                        <th>June</th>
                        <th>Q3</th>
                        <th>July</th>
                        <th>Aug</th>
                        <th>Sept</th>
                        <th>Q4</th>
                        <th>Oct</th>
                        <th>Nov</th>
                        <th>Dec</th>
                    </tr>
                </thead>

                <tbody>
                   
                </tbody>
            </table>
        </div>
        <div class="function-btns-div">
            <button id="exportExcelBtn" class="btn btn-success">Export to Excel</button>
            <button id="deleteAll" class="btn btn-danger">Remove All</button>
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
    <script src="../js-obf/imiss_ppmp-obf.js?v=<?php echo time(); ?>"></script> -->

    <script src="../js/home_traverse.js?v=<?php echo time(); ?>"></script>
    <script src="../js/imiss_ppmp.js?v=<?php echo time(); ?>"></script>
</body>
</html>