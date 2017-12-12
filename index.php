<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>TODO</title>
        <link href="/static/style.css" rel="stylesheet">
    </head>
    <body>
        <h1>A TODO LIST</h1>
<?php
require_once 'helpers.php';

$servername = "localhost";
$username = "root";
$password = "Skule123";
$dbname = "TODO";

$id = $label = $description = $category = $active = $closing = "";

//Create the DATABASE:
//createDB($servername, $username, $password, $dbname);

//Create the TABLE:
//            $model = array(
//                "id INT(6) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
//                "label VARCHAR(30) NOT NULL",
//                "description TEXTFIELD(800)",
//                "category VARCHAR(30)",
//                "active BOOL DEFAULT TRUE",
//                "closing VARCHAR(800)",
//            );
//            $model_name = "todos";
//            createModel($servername, $username, $password, $dbname, $model_name, $model);
//

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $labelErr = "";

    $label = test_input($_POST["label"]);
    $description = test_input($_POST["description"]);
    $category = test_input($_POST["category"]);

    if (test_input($_POST["renew"]) === "renew") {
        $active = "1";
    } else {
        $active = "0";
    }

    $closing = test_input($_POST["closing"]) || "NULL";

    $sql = "INSERT INTO todos (label,description,category,active,closing) VALUES ('$label','$description','$category','$active','$closing')";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    if ($conn->query($sql) === true) {
        $_SESSION['last_id'] = $conn->insert_id;
        header('location ' . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    } else {
        echo "Error: $sql <br/>$conn->error.";
    }

} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    //lets handle some get requests. Mode can be either edit, to update or delete. 
    //Lets say no mode implies Read only. Mode implies edit or delete, which requires id.
    $read_only = $id = $mode = "";
    if($_GET['mode'] && !$_GET['id']){
        echo "Bad request. Need id of entry.";
    } elseif($_GET['mode'] == 'edit'){
        $item_id = test_input($_GET['id']);
        
        if($item_id){
            $sql = "SELECT * FROM todos WHERE id='$item_id'";
            
                    $conn = new mysqli($servername, $username, $password,$dbname);
                    if($conn->connect_error){
                        die("Connection failed: " . $conn->connect_error);
                    }
            
                    echo "sql: <br/>".$sql;
                    
                    $query_set = $conn->query($sql);
                    if($query_set->num_rows > 0){
                        $row = $query_set->fetch_assoc();
                        $id = $row['id'];
                        $label = $row['label'];
                        $description = $row['description'];
                        $category = $row['category'];
                        $active = $row['active'];
                        $closing = $row['closing'];
                    } else {
                        echo "Query appears to return empty result.";
                    }
                    

                    $conn->close();

        } else {
            echo "Not a valid item ID.";
        }
        
    }

}?>
    <div>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
        <?php if($id) {
            echo 'ID:<br/>
                <input type="text" name="ID" value="'.$id.'" readonly><br/>';
        }?>
        Label:<br/>
        <input type="text" name="label" value="<?php echo $label?>"><br/>
        Description:<br/>
        <textarea name="description" rows="5" columns="50"><?php echo $description?></textarea><br/>
        <div class=container>
        Category:<br>
        <select name="category">
            <option value="TODO" <?php if($category == "TODO") echo selected?>>TODO</option>
            <option value="Other" <?php if($category == "Other") echo selected?>>Other</option>
        </select><br>
        Renew:<br>
        <input type="radio" name="renew" value="renew" checked>Renew
        <input type="radio" name="renew" value="closed">Closed<br/>
        </div>
        Closing Comment<br/>
        <textarea name="closing" rows="5" columns="50"></textarea><br/>
        <input type="submit" value="Submit" name="submit">
    </form>
    </div>
    <?php
// make a list of the query_set gathered
$sql = "SELECT * FROM todos WHERE active='1'";
$conn = new mysqli($servername, $username, $password, $dbname);
$query_set = $conn->query($sql);
if ($query_set->num_rows > 0) {
    echo "<table>
                <th>ID</th>
                <th>LABEL</th>
                <th>DESCRIPTION</th>
                <th>ACTIVE</th>
                <th>CLOSE</th>
                <th>EDIT</th>";
    while ($row = $query_set->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row[id] . "</td>";
        echo "<td>" . $row[label] . "</td>";
        if (strlen($row[description]) > 16) {
            echo "<td>" . substr($row[description], 0, 13) . "...</td>";
        } else {
            echo "<td>" . $row[description] . "</td>";
        }

        if ($row[active]) {
            echo "<td>TRUE</td>";
        } else {
            echo "<td>FALSE</td>";
        }
        echo "<td><a href=".htmlspecialchars($_SERVER["PHP_SELF"])."?id=" . $row[id] . "&mode=close>CLOSE</a></td>";
        echo "<td><a href=".htmlspecialchars($_SERVER["PHP_SELF"])."?id=" . $row[id] . "&mode=edit>EDIT</a></td>";
        echo "</tr>";

    }
    echo "</table>";
} else {
    echo "<h4> No active todo items.</h4>";
}
$conn->close();
?>
    </body>


</html>
