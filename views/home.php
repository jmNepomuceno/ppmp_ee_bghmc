<?php 
    include ('../session.php');
    include('../assets/connection/sqlconnection.php');

    $allowed_roles = ["admin" , "user"];

    if (!in_array($_SESSION["role"], $allowed_roles)) {
        header("Location: ../views/home.php"); // Redirect unauthorized users
        exit();
    }

    // Lightweight session caching — NO image
    // if (empty($_SESSION['fetch_inventory'])) {
        
    // }

    $sql = "SELECT itemID, itemName, itemPrice, itemSpecs, itemVisibility, itemImagePath, itemCategory FROM imiss_inventory";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $item_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // echo "<pre>"; print_r($item_data); echo "</pre>";
    // echo print_r($item_data);
    

    for ($i = 0; $i < count($item_data); $i++) {
        if (isset($item_data[$i]['itemName']) && strlen($item_data[$i]['itemName']) > 65) {
            // Store full name in a new key for tooltip
            $item_data[$i]['itemName_full'] = $item_data[$i]['itemName'];
            $item_data[$i]['itemName'] = substr($item_data[$i]['itemName'], 0, 65) . "...";
        } else {
            $item_data[$i]['itemName_full'] = $item_data[$i]['itemName'];
        }
    }

    $_SESSION['fetch_inventory'] = $item_data;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHOP-PPMPee</title>
    <link rel="stylesheet" href="../css/home.css">

    <?php require "../links/header_link.php" ?>

</head>
<body>
    
    <?php 
        $view = "inventory-list-sub-div";
        include("./sidebar.php");
        $items_per_page = 10; // Set how many items to show per page
        $total_items = count($item_data);
        $total_pages = ceil($total_items / $items_per_page); // Calculate total pages
    ?>

    <div class="right-container">
        <?php 
            $navbar_view = "INVENTORY LIST";
            include("./navbar.php");
        ?>

        <div id="sample-id">
            
        </div>

        <div class="function-bar">
            <i class="fa-solid fa-bars" id="burger-icon"></i>

            <div class="filter-div">
                <button class="filter-btn" data-category="All">All Items</button>
                <button class="filter-btn" data-category="Common">Common Items</button>
                <button class="filter-btn" data-category="Rare">Uncommon Items</button>
                <button class="filter-btn" data-category="IMISS">IMISS Items</button>
            </div>

            <div class="search-bar">
                <input type="text" id="search-input" autocomplete="off"/>
                <button id="search-btn">Search</button>
            </div>
            
            <div class="cart-div">
                <img id="item-img-animation" src="../source/inventory_image/item_1.jpg" alt="item-1-img">
                <span id="notif-value"></span>
                <i class="fa-solid fa-cart-shopping" id="cart-icon"></i>
            </div>
        </div>

        <div class="inventory-div">
            <?php for ($i = 0; $i < $total_items; $i++) { 
                $item = $item_data[$i];

                // Use the stored path from the database
                $imageSrc = $item['itemImagePath'];

                $dataSpecs = !empty($item['itemSpecs']) 
                    ? htmlspecialchars(json_encode(json_decode($item['itemSpecs'], true)), ENT_QUOTES)
                    : htmlspecialchars(json_encode([]), ENT_QUOTES);

                // echo "<pre>"; print_r($item['itemSpecs']); echo "</pre>";
                // echo "<pre>"; print_r($dataSpecs); echo "</pre>";
            ?>
                <!-- <div class="tiles-div item-tile" data-index="<?php echo $i; ?>" style="display: none;"> -->
                <div class="tiles-div item-tile" data-index="<?php echo $i; ?>" data-category="<?php echo $item['itemCategory']; ?>" style="display: none;">

                    <img class="item-img" src="../<?php echo $imageSrc; ?>" 
                        alt="item-<?php echo $item['itemID']; ?>-img">
                    <p class="item-description" title="<?php echo htmlspecialchars($item['itemName_full'], ENT_QUOTES); ?>">
                        <?php
                            $displayName = strlen($item['itemName']) > 65
                                ? substr($item['itemName'], 0, 65) . "..."
                                : $item['itemName'];
                            echo $displayName;
                        ?>
                        <span style="display:none" class="item-id"><?php echo $item['itemID']; ?></span>
                    </p>
                    <span 
                        class="item-specs view-specs-btn" 
                        data-item-name="<?php echo htmlspecialchars($item['itemName'], ENT_QUOTES); ?>"
                        data-item-price="<?php echo number_format($item['itemPrice'], 2); ?>"
                        data-item-image="../<?php echo $imageSrc; ?>"
                        data-item-specs="<?php echo $dataSpecs; ?>"';
                    >
                        View Specs
                    </span>

                    <span class="item-price"><?php echo "P " . number_format($item['itemPrice'], 2, '.', ','); ?></span>

                    <div class="function-div">
                        <div class="add-div">
                            <button class="minus-btn">-</button>
                            <input type="text" class="current-total-span" value="0">
                            <button class="add-btn">+</button>
                        </div>
                        <button class="add-to-cart-btn">Add to Cart</button>
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

    <div class="modal fade" id="modal-place-order" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered custom-modal-width modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modal-title-incoming" class="modal-title-incoming" id="exampleModalLabel">Your Cart</h5>
                </div>
                <div id="modal-body-incoming" class="modal-body-incoming ml-2">
                    <table id="cart-table" class="display">
                        <thead>
                            <tr >
                                <th>ITEM ID</th>
                                <th>IMAGE</th>
                                <th>PRODUCT</th>
                                <th>PRICE</th>
                                <th>QUANTITY</th>
                                <th>SUBTOTAL</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>

                        <tbody>
                            <!-- <tr >
                                <td><img src="../source/inventory_image/item_1.png" alt="item-1-img"></td>
                                <td>Brand New Desktop Computer</td>
                                <td>P 80,000.00</td>
                                <td>1</td>
                                <td>P 80,000.00</td>
                                <td><button id="remove-item-btn">Remove</button></td>
                            </tr> -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button id="close-modal-btn-incoming" type="button" type="button" data-bs-dismiss="modal">CLOSE</button>
                    <button id="placeorder-btn" type="button">PLACE ORDER</button>
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

    <div class="modal fade" id="specsModal" tabindex="-1" aria-labelledby="specsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="specsModalLabel">Item Specifications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="text-center mb-3">
                        <img id="specs-item-img" src="" alt="Item Image" style="max-height: 200px;">
                    </div>

                    <h5 id="specs-item-name" style="font-weight:600; font-size:1.5rem;"></h5>
                    <p><strong>Price:</strong> <span id="specs-item-price"></span></p>

                    <hr>
                    <div id="specs-list">
                        <!-- Dynamically filled -->
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
    <!-- <script src="../js-obf/home_traverse-obf.js?v=<?php echo time(); ?>"></script>
    <script src="../js-obf/home_function-obf.js?v=<?php echo time(); ?>"></script> -->
                
    <script src="../js/home_traverse.js?v=<?php echo time(); ?>"></script>
    <script src="../js/home_function.js?v=<?php echo time(); ?>"></script>
</body>
</html>
 