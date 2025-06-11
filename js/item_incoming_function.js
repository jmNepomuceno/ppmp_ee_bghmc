let modal_notif = new bootstrap.Modal(document.getElementById('modal-notif'));

const dataTable = () =>{
    try {
        $.ajax({
            url: '../php/fetch_ppmp_distribution.php',
            method: "POST",
            data : {
                "todo" : "section"
            },
            dataType : "json",
            success: function(response) {

                try {
                    let dataSet = [];

                    for (let i = 0; i < response.length; i++) {
                        // Generate a list for each section and its total quantity
                        let sectionList = "<div class='end-users-div'>"; // Add padding for indentation
                        response[i].sections.forEach(section => {
                            if(section.section === "Integrated Hospital Operations and Management Program"){
                                section.section = "IMISS"
                            }
                            sectionList += `<div class='end-user-sub-div'> 
                                                <span>${section.section}</span> <span>(${section.total_quantity})</span>
                                            </div>`;
                        });
                        sectionList += "</div>";

                        dataSet.push([
                            `<span>${response[i].itemDescription}</span>`,
                            `<span>${response[i].totalQuantity}</span>`,
                        ]);
                    }


                    if ($.fn.DataTable.isDataTable('#cart-table')) {
                        $('#cart-table').DataTable().destroy();
                        $('#cart-table tbody').empty(); // Clear previous table body
                    }

                    $('#cart-table').DataTable({
                        data: dataSet,
                        columns: [
                            { title: "ITEM NAME", data: 0 },
                            { title: "QUANTITY", data: 1 },
                        ],
                        columnDefs: [
                            { targets: 0, createdCell: function(td) { $(td).addClass('item-ppmp-td'); } },
                            { targets: 1, createdCell: function(td) { $(td).addClass('item-ppmp-td'); } },
                        ],
                        "paging": false,
                        "info": false,
                        "ordering": false,
                        "stripeClasses": [],
                        "search": false,
                        autoWidth: false,
                    });

                } catch (innerError) {
                    console.error("Error processing response:", innerError);
                }

            },
            error: function(xhr, status, error) {
                console.error("AJAX request failed:", error);
            }
        });
    } catch (ajaxError) {
        console.error("Unexpected error occurred:", ajaxError);
    }
   
}

const exportExcel = () => {
    try {
        fetch("../ppmp_distribution_2.xlsx") // Ensure this file exists in your directory
        .then(response => {
            if (!response.ok) throw new Error("❌ Failed to fetch the Excel file");
            return response.arrayBuffer();
        })
        .then(data => {
            let workbook = XLSX.read(data, { type: "array" });
            let sheetName = workbook.SheetNames[0]; // Get first sheet
            let worksheet = workbook.Sheets[sheetName];

            let table = $('#cart-table').DataTable();
            if (!table.data().any()) {
                console.error("❌ No data found in DataTable!");
                return;
            }

            // Convert DataTable data into an array
            let dataSet = [];
            table.rows().every(function () {
                let rowData = [];
                $(this.node()).find("td").each(function () {
                    rowData.push($(this).text().trim());
                });
                dataSet.push(rowData);
            });

            // Append data to the worksheet
            XLSX.utils.sheet_add_json(worksheet, dataSet, {
                skipHeader: true, // Don't overwrite existing headers
                origin: "A3" // Start inserting at row 3
            });

            // Save and export the modified file
            XLSX.writeFile(workbook, "ppmp_distribution.xlsx");
        })
    } catch (error) {
        console.error("❌ Error generating Excel file:", error);
    }
};


$(document).ready(function(){
    dataTable()

    $(document).on("click", "#exportExcelBtn", exportExcel)

    $('#inventory-list-sub-div').click(function(){
        window.location.href = "../views/home.php";
    });

    $('#logout-btn').click(function(){
        modal_logout.show()
        

        $(document).off('click', '#yes-modal-btn-logout').on('click', '#yes-modal-btn-logout', function() {
            $.ajax({
                url: '../php/logout.php',
                method: "GET",
                
                success: function(response) {
                    window.location.href = response;
                }
            });
        })
    });

    // $(document).off('click', '#close-modal-btn-incoming').on('click', '#close-modal-btn-incoming', function() {        
    //     dataTable("Pending")
    // });
    $(document).off('click', '#burger-icon').on('click', '#burger-icon', function() {
        if($('#burger-icon').css('color') != 'rgb(255, 85, 33)'){
            $('body .left-container').css('display', 'none');
            $('#burger-icon').css('color', '#ff5521');
        }else{
            $('body .left-container').css('display', 'flex');
            $('#burger-icon').css('color', 'white');
        }
    });
})