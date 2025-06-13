<?php 
    include ('../session.php');
    include('../assets/connection/sqlconnection.php');

    $item_data = $_SESSION['fetch_inventory'];
    // echo "<pre>"; print_r($item_data); echo "</pre>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHOP-PPMPee</title>
    <link rel="stylesheet" href="../css/imiss_inventory.css">

    <?php require "../links/header_link.php" ?>

</head>
<body>
    
    <?php 
        $view = "imiss-inventory-sub-div";
        include("./sidebar.php");
        $items_per_page = 10; // Set how many items to show per page
        $total_items = count($item_data);
        $total_pages = ceil($total_items / $items_per_page); // Calculate total pages
    ?>

    <div class="right-container">
        <?php 
            $navbar_view = "IMISS INVENTORY MANAGEMENT";
            include("./navbar.php");
        ?>

        <div id="sample-id">
            
        </div>

        <div class="function-bar">
            <i class="fa-solid fa-bars" id="burger-icon"></i>
            <div class="search-bar">
                <input type="text" id="search-input" autocomplete="off"/>
                <button id="search-btn">Search</button>
            </div>
            
            <!-- <div class="cart-div">
                <img id="item-img-animation" src="../source/inventory_image/item_1.png" alt="item-1-img">
                <span id="notif-value"></span>
                <i class="fa-solid fa-cart-shopping" id="cart-icon"></i>
            </div> -->

            <button type="button" class="btn btn-primary" id="add-new-item-btn" data-category="add-item" data-bs-toggle="modal" data-bs-target="#modal-add-item">Add New Item</button>  
        </div>

        <div class="inventory-div">
            <?php for ($i = 0; $i < $total_items; $i++) { 
                // Convert BLOB to base64
                $itemImageData = $item_data[$i]['itemImage']; // Get the BLOB data
                if (!empty($itemImageData)) {
                    $imageSrc = 'data:image/jpeg;base64,' . base64_encode($itemImageData);
                } else {
                    $imageSrc = '../source/inventory_image/item_1.png'; // Provide a default image path
                }
            ?>
                <div class="tiles-div item-tile" data-index="<?php echo $i; ?>" style="display: none;">
                    <img class="item-img" src="<?php echo $imageSrc; ?>" alt="item-1-img">
                    
                    <p class="item-description">
                        <?php echo $item_data[$i]['itemName']; ?> 
                        <span style="display:none" class="item-id"><?php echo $item_data[$i]['itemID']; ?></span>
                    </p>
                    <span class="item-price"><?php echo "P " . number_format($item_data[$i]['itemPrice'], 2, '.', ','); ?></span>
                    
                    <div class="function-div">
                        <!-- <div class="add-div">
                            <button class="minus-btn">-</button>
                            <input type="text" class="current-total-span" value="0">
                            <button class="add-btn">+</button>
                        </div>
                        <button class="add-to-cart-btn">Add to Cart</button> -->
                        <button class="edit-item-btn">Edit</button>
                        <button class="delete-item-btn">Delete</button>
                    </div>
                </div>
            <?php } ?>

            
        </div>


        <!-- Pagination Controls -->
        <div class="pagination-controls">
            <button id="prevPage" disabled>Previous</button>
            <span id="pagination-numbers"></span>
            <button id="nextPage">Next</button>
        </div>
    </div>

    <div class="modal fade" id="modal-add-item" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered custom-modal-width modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modal-title-incoming" class="modal-title-incoming" id="exampleModalLabel">New Item</h5>
                </div>
                <!-- Modal Body -->
                <div id="modal-body-incoming" class="modal-body-incoming ml-2">
                    <form id="add-item-form">
                        <!-- Row 1: Basic Info -->
                        <div class="form-group mb-3">
                            <label for="item-name">Item Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="item-name" name="item_name" placeholder="e.g., Laptop, Printer, Router" required>
                        </div>

                        <!-- Row 2: Image Upload -->
                        <div class="form-group mb-3">
                            <label for="item-image">Item Image</label>
                            <input type="file" class="form-control" id="item-image" name="item_image" accept="image/*" required>
                        </div>

                        <!-- Row 3: Price -->
                        <div class="form-group mb-3">
                            <label for="item-price">Estimated Price (₱) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="item-price" name="item_price" step="0.01" required>
                        </div>

                        <!-- Row 4: Specifications -->
                        <div class="form-group mb-3">
                            <label for="item-specs">Item Specifications <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="item-specs" name="item_specs" rows="5" placeholder="e.g., Processor: Intel i5, RAM: 8GB, Storage: 512GB SSD" required></textarea>
                        </div>

                        <!-- Row 6: Justification -->
                        <!-- Row 5: Optional Fields --> 
                        <!-- <div class="form-group mb-3">
                            <label for="item-unit">Unit</label>
                            <select class="form-control" id="item-unit" name="item_unit">
                                <option value="">Select Unit</option>
                                <option value="pc">pc</option>
                                <option value="set">set</option>
                                <option value="box">box</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="item-justification">Justification / Purpose</label>
                            <textarea class="form-control" id="item-justification" name="item_justification" rows="3" placeholder="Why is this item needed?"></textarea>
                        </div> -->
                    </form>
                </div>

                <div class="modal-footer">
                    <button id="close-modal-btn-incoming" type="button" type="button" data-bs-dismiss="modal">CLOSE</button>
                    <button id="add-item-btn" type="button">ADD ITEM</button>
                </div>
            </div>
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

        var itemsPerPage = <?php echo $items_per_page; ?>;
        var totalItems = <?php echo $total_items; ?>;
        var totalPages = <?php echo $total_pages; ?>;
    </script>

    <script src="../js/home_traverse.js?v=<?php echo time(); ?>"></script>
    <script src="../js/imiss_inventory.js?v=<?php echo time(); ?>"></script>
</body>
</html>
 