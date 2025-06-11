<?php
include ('../session.php');
include('../assets/connection/sqlconnection.php');


try {
    // Fetch all necessary data with JOINs to avoid multiple queries
    $sql = "
        SELECT 
            f.*, 
            pr.order_by_section, 
            s.sectionName
        FROM imiss_ppmp_finaldraft f
        LEFT JOIN ppmp_request pr ON f.orderID = pr.orderID
        LEFT JOIN pgssection s ON pr.order_by_section = s.sectionID
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $filteredData = [];
    $rawData = [];
    
    foreach ($data as $row) {
        $itemID = $row['itemID'];
        $rawData[] = $row; // Store raw data

        if (!isset($filteredData[$itemID])) {
            // Initialize the item if it does not exist in filteredData
            $filteredData[$itemID] = $row;
        } else {
            // Sum the total quantity and estimated budget
            $filteredData[$itemID]['itemTotalQuantity'] += $row['itemTotalQuantity'];
            $filteredData[$itemID]['itemEstimBudget'] += $row['itemEstimBudget'];
        }
    }

    // Convert associative array to indexed array
    $finalData = array_values($filteredData);

    // Extract section names
    $sectionData = array_column($rawData, 'sectionName');

    $response = [
        "filteredData" => $finalData,
        "rawData" => $rawData,
        "section" => $sectionData
    ];

    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}


// e/i ka wo/u lo/u ngo/u
// i ka wa la nga
?>
