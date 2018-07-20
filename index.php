<?php
$servername = "localhost";
$username = "root";
$password = "root@321";
$dbname = "label";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_POST["sizeName"])){
    saveConfig($_POST["id"]);
}
//echo 'hi'.$sizeName;

function saveConfig($a){
    global $_POST;
    global $conn;

    $id = (isset($_POST["id"]) ? $_POST["id"] : '');
    $sizeName = (isset($_POST["sizeName"]) ? $_POST["sizeName"] : '');
    $paperWidth = (isset($_POST["paperWidth"]) ? $_POST["paperWidth"] : 0);
    $paperHeight = (isset($_POST["paperHeight"]) ? $_POST["paperHeight"] : 0);
    $blockWidth = (isset($_POST["blockWidth"]) ? $_POST["blockWidth"] : 0);
    $blockHeight = (isset($_POST["blockHeight"]) ? $_POST["blockHeight"] : 0);
    $totalX = (isset($_POST["totalX"]) ? $_POST["totalX"] : 0);
    $totalY = (isset($_POST["totalY"]) ? $_POST["totalY"] : 0);
    $displayType = (isset($_POST["displayType"]) ? $_POST["displayType"] : 0);
    $fontSize = (isset($_POST["fontSize"]) ? $_POST["fontSize"] : 0);
    $fontSizeQR = (isset($_POST["fontSizeQR"]) ? $_POST["fontSizeQR"] : 0);
    $active = (isset($_POST["active"]) ? $_POST["active"] : 0);
    
    if (!$a) {
        if ($active == 1){
            $sql = "UPDATE papersize SET active='0'";
            $result = $conn->query($sql);
        }
        $stmt = $conn->prepare("INSERT INTO papersize (sizeName, paperWidth, paperHeight, blockWidth, blockHeight, totalX, totalY, displayType, fontSize, fontSizeQR, active) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssssssss", $sizeName, $paperWidth, $paperHeight, $blockWidth, $blockHeight, $totalX, $totalY, $displayType, $fontSize, $fontSizeQR, $active);
        $stmt->execute();
    } else {
        if ($active == 1){
            $sql = "UPDATE papersize SET active='0'";
            $result = $conn->query($sql);
        }
        $stmt = $conn->prepare("UPDATE papersize SET sizeName = ?, paperWidth = ?, paperHeight = ?, blockWidth = ?, blockHeight = ?, totalX = ?, totalY = ?, displayType = ?, fontSize = ?, fontSizeQR = ?, active = ? WHERE id = ?");
        $stmt->bind_param("ssssssssssss", $sizeName, $paperWidth, $paperHeight, $blockWidth, $blockHeight, $totalX, $totalY, $displayType, $fontSize, $fontSizeQR, $active, $a);    
        $stmt->execute();
    }
    header("Location: ".$_SERVER['PHP_SELF']);
}

$sql = "SELECT * FROM papersize order by id asc";
$result = $conn->query($sql);
$resultIndex = $result->num_rows;


if ($result->num_rows > 0) {
    $i = 0; //setting loop index
    $table = '';
    $toJson = array();
    while ($item = $result->fetch_assoc()) {

        //defaults
        $active = ($item['active'] == 1) ? '<i class="fas fa-check"></i>' : '';
        $toJson[] = $item;
        $table .= '
                <tr>
                    <th scope="row" class="text-center">'.$active.'</th>
                    <td>'.$item['sizeName'].'</td>
                    <td>'.$item['paperWidth'].'</td>
                    <td>'.$item['paperHeight'].'</td>
                    <td>'.$item['blockWidth'].'</td>
                    <td>'.$item['blockHeight'].'</td>
                    <td>'.$item['totalX'].'</td>
                    <td>'.$item['totalY'].'</td>
                    <td>'.$item['displayType'].'</td>
                    <td>'.$item['fontSize'].'</td>
                    <td>'.$item['fontSizeQR'].'</td>
                    <td><a href="javascript:editPaperSize('.$item['id'].')" class="btn btn-primary">Edit</a></td>
                </tr>
            ';
    }

}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>orangebike</title>

    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="#">Print Label</a>
    </nav>
    <div class="container spacer-20">
        <form action="print.php" method="post" target="_blank">
            <fieldset>
                <Legend>Options</Legend>
                <!-- <div class="form-check col-md-3">
                    <label class="form-check-label">
                        <input class="form-check-input" name="showMerchant" type="checkbox" value="1" checked="checked">Show Merchant Name
                    </label>
                </div> -->
                <!-- <div class="form-check col-md-3">
                    <label class="form-check-label">
                        <input class="form-check-input" name="showSKU" type="checkbox" value="1" checked="checked">Show SKU
                    </label>
                </div> -->
                <div class="form-check col-md-3">
                    <label class="form-check-label">
                        <input class="form-check-input" name="showAmount" type="checkbox" value="1" checked="checked">Show Amount
                    </label>
                </div>
                <div class="form-check col-md-3">
                    <label class="form-check-label">
                            <input class="form-check-input" name="showMobile" type="checkbox" value="1" checked="checked">Show Mobile
                        </label>
                </div>
                <div class="form-check col-md-3">
                    <label class="form-check-label">
                        <input class="form-check-input" name="showAddress" type="checkbox" value="1" checked="checked">Show Address
                    </label>
                </div>

                <div class="spacer-20"></div>
                
                <Legend>Paper Size Configuration</Legend>
                <div class="spacer-5">
                    <a href="javascript:newPaperSize()" class="btn btn-primary">Add New</a>
                </div>
                <table class="spacer-5 table table-hover">
                    <thead>
                        <tr>
                        <th scope="col">Selected</th>
                        <th scope="col">Name</th>
                        <th scope="col">Paper Width</th>
                        <th scope="col">Paper Height</th>
                        <th scope="col">Block Width</th>
                        <th scope="col">Block Height</th>
                        <th scope="col">Total X</th>
                        <th scope="col">Total Y</th>
                        <th scope="col">Display Type</th>
                        <th scope="col">Font Size</th>
                        <th scope="col">Font Size QR</th>
                        <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $table ?>
                    </tbody>
                </table>
                
                <div class="form-group spacer-10">
                    <button class="btn btn-primary" type="submit" value="Submit">Test Print</button>
                </div>

            </fieldset>
        </form>
    </div>

    <div id="PaperSizeModal" style="display:none">
        <form action="index.php" method="post" target="_self">
            <fieldset>
                <Legend>Add New Configuration</Legend>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="sizeName">Name</label>
                            <input type="text" required class="form-control" id="sizeName" name="sizeName" aria-describedby="sizeName" placeholder="Enter Size Name">
                            <small id="sizeName" class="form-text text-muted">Any name to describe this configuration</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                    <div class="form-group">
                            <label for="paperWidth">Paper Width</label>
                            <input type="text" class="form-control" id="paperWidth" name="paperWidth" aria-describedby="paperWidth" placeholder="Enter Paper Width">
                        </div>
                        <div class="form-group">
                            <label for="blockWidth">Block Width</label>
                            <input type="text" class="form-control" id="blockWidth" name="blockWidth" aria-describedby="blockWidth" placeholder="Enter Block Width">
                        </div>
                        <div class="form-group">
                            <label for="totalX">Total X</label>
                            <input type="text" class="form-control" id="totalX" name="totalX" aria-describedby="totalX" placeholder="Enter Total X">
                            <small id="totalX" class="form-text text-muted">Number of total blocks per row</small>
                        </div>
                        <div class="form-group">
                            <label for="fontSize">Font Size</label>
                            <input type="text" class="form-control" id="fontSize" name="fontSize" aria-describedby="fontSize" disabled placeholder="Enter Font Size">
                        </div>
                        <div class="form-group">
                            <label for="displayType">Display Type</label>
                            <input type="text" class="form-control" id="displayType" name="displayType" aria-describedby="displayType" disabled placeholder="Enter Display Type">
                        </div>
                    </div>
                    <div class="col-md-6">

                        <div class="form-group">
                            <label for="paperHeight">Paper Height</label>
                            <input type="text" class="form-control" id="paperHeight" name="paperHeight" aria-describedby="paperHeight" placeholder="Enter Paper Height">
                        </div>
                        <div class="form-group">
                            <label for="blockHeight">Block Height</label>
                            <input type="text" class="form-control" id="blockHeight" name="blockHeight" aria-describedby="blockHeight" placeholder="Enter Block Height">
                        </div>
                        <div class="form-group">
                            <label for="totalY">Total Y</label>
                            <input type="text" class="form-control" id="totalY" name="totalY" aria-describedby="totalY" placeholder="Enter Total Y">
                            <small id="totalY" class="form-text text-muted">Number of total rows</small>
                        </div>
                        <div class="form-group">
                            <label for="fontSizeQR">Font Size QR</label>
                            <input type="text" class="form-control" id="fontSizeQR" name="fontSizeQR" aria-describedby="fontSizeQR" disabled placeholder="Enter Font Size">
                        </div>
                        <div class="form-group">
                            <label for="fontSizeQR">Active</label>
                            <select class="form-control " name="active" id="active">
                                <option value="0">inactive</option>
                                <option value="1">active</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" id="configId" name="id" value=""></input>
                        <div class="form-group spacer-10 text-center">
                            <button class="btn btn-primary" type="submit" value="Submit">Save</button>
                        </div>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
        
    <script>
        var newPaperSize = function(a){
                $('#PaperSizeModal').dialog({
                        autoOpen: true,
                        modal: true,
                        width: "auto",
                        height: "auto"
                    });
        };
        var editPaperSize = function(id){
            this.paperDB = <?php echo json_encode($toJson) ?>;
            this.paperDB.map(function(a){
                if (a.id == id) {
                    $('#configId').val(id);
                    //sizeName, blockWidth, blockHeight, totalX, totalY, displayType, fontSize, fontSizeQR
                    $('#sizeName').val(a.sizeName);
                    $('#paperWidth').val(a.paperWidth);
                    $('#paperHeight').val(a.paperHeight);
                    $('#blockWidth').val(a.blockWidth);
                    $('#blockHeight').val(a.blockHeight);
                    $('#totalX').val(a.totalX);
                    $('#totalY').val(a.totalY);
                    $('#displayType').val(a.displayType);
                    $('#fontSize').val(a.fontSize);
                    $('#fontSizeQR').val(a.fontSizeQR);
                    $('#active option[value="'+a.active+'"]').attr("selected", "selected");
                }
            });
            $('#PaperSizeModal').dialog({
                        autoOpen: true,
                        modal: true,
                        width: "auto",
                        height: "auto"
                    });
        }
    </script>
</body>

</html>