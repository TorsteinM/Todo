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
        <link href="/css/style.css" rel="stylesheet">
        <!--Import Google Icon Font-->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <!--Import materialize.css-->
        <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>

        <!--Let browser know website is optimized for mobile-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
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
    $id = test_input($_POST["id"]);
    $label = test_input($_POST["label"]);
    $description = test_input($_POST["description"]);
    $category = test_input($_POST["category"]);
    $active = 1;
    $closing = test_input($_POST["closing"]) || "NULL";
    
    if ($_POST['submit']=='EDIT') {
        $sql = "UPDATE todos SET label='$label', description='$description', category = '$category', active = '$active', closing = '$closing' WHERE id=$id";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        if ($conn->query($sql) === true) {
            header('location: ' . htmlspecialchars($_SERVER["PHP_SELF"]));
            exit();
        } else {
            echo "Error: $sql <br/>$conn->error.";
        }  

    } elseif ($_POST['submit']=='REMOVE') {
        $sql = "DELETE FROM todos WHERE id=$id";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        if ($conn->query($sql) === true) {
            header('location: ' . htmlspecialchars($_SERVER["PHP_SELF"]));
            exit();
        } else {
            echo "Error: $sql <br/>$conn->error.";
        }
    } else {
        $sql = "INSERT INTO todos (label,description,category,active,closing) VALUES ('$label','$description','$category','$active','$closing')";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        if ($conn->query($sql) === true) {
            header('location: ' . htmlspecialchars($_SERVER["PHP_SELF"]));
            exit();
        } else {
            echo "Error: $sql <br/>$conn->error.";
        }
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

            //then lest set up the form for easy closing comment.
            $label_html = 'Label:<br/>
            <input type="text" name="label" value="'.$label.'"><br/>';
            $id_html = 'ID:<br/>
            <input type="text" name="id" value="'.$id.'" readonly><br/>';;
            $description_html = 'Description:<br/>
            <textarea name="description" rows="5" columns="50">'.$description.'</textarea><br/>';
            $renew_html = "";
            if($active){
                $active_html = 'Renew:<br>
                <input type="radio" name="renew" value="renew" checked>Renew
                <input type="radio" name="renew" value="closed">Closed<br/>'; 
            } else {
                $active_html = 'Renew:<br>
                <input type="radio" name="renew" value="renew">Renew
                <input type="radio" name="renew" value="closed" checked>Closed<br/>';
            }
            $category_html = "";
            $closing_html = 'Closing Comment<br/>
            <textarea name="closing" rows="5" columns="50"></textarea><br/>';
            $submit_html = '<input type="submit" value="EDIT" name="submit">
            <input type="submit" value="REMOVE" name="submit">';   

        } else {
            echo "Not a valid item ID. Edit mode failed.";
        }
        
    } elseif ($_GET['mode']== 'close'){
        $item_id = test_input($_GET['id']);

        if($item_id){
            $sql = "SELECT * FROM todos WHERE id='$item_id'";

            $conn = new mysqli($servername, $username, $password,$dbname);
            if($conn->connect_error){
                die("Connection failed: " . $conn->connect_error);
            }

            // get item from database
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
            
            //then lest set up the form for easy closing comment.
            $label_html = 'Label:<br/>
            <input type="text" name="label" value="'.$label.'"><br/>';
            $id_html = 'ID:<br/>
            <input type="text" name="id" value="'.$id.'" readonly><br/>';
            $description_html = 'Description:<br/>
            <textarea name="description" rows="5" columns="50" readonly>'.$description.'</textarea><br/>';
            $active_html = "";
            /* $renew_html = 'Renew:<br>
            <input type="radio" name="renew" value="renew" checked>Renew
            <input type="radio" name="renew" value="closed">Closed<br/>'; */
            $category_html = "";
            $closing_html = 'Closing Comment<br/>
            <textarea name="closing" rows="5" columns="50"></textarea><br/>';
            $submit_html = '<input type="submit" value="CLOSE" name="submit">';


        } else {
            echo "Not a valid item ID. Close mode failed.";
        }

    } else {
        // Default: lets just show an empty form for registring a new TODO
        $label_html = 'Label:<br/>
        <input type="text" name="label" value="'.$label.'"><br/>';
        $id_html = "";
        $description_html = 'Description:<br/>
        <textarea name="description" rows="5" columns="50">'.$description.'</textarea><br/>';
        $active_html = "";
        /* $renew_html = 'Renew:<br>
        <input type="radio" name="renew" value="renew" checked>Renew
        <input type="radio" name="renew" value="closed">Closed<br/>'; */
        $category_html = 'Category:<br>
        <select name="category">
          <option value="TODO">TODO</option>
          <option value="Other">Other</option>
        </select><br>';
        $closing_html = "";
/*         $closing_html = 'Closing Comment<br/>
        <textarea name="closing" rows="5" columns="50"></textarea><br/>'; */
        $submit_html = '<input type="submit" value="ADD" name="submit">';

    }


}?>
    <div>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
    <?php
    echo $id_html;
    echo $label_html;
    echo $category_html;
    echo $description_html;
    echo $active_html;
    echo $closing_html;
    echo $submit_html;

    ?>
    </form>
    </div>
    <?php
// make a list of the query_set gathered
$sql = "SELECT * FROM todos WHERE active='1'";
$conn = new mysqli($servername, $username, $password, $dbname);
$query_set = $conn->query($sql);
if ($query_set->num_rows > 0) {
    echo "<h4>Active TODOs</h4>";
    echo "<table>
                <th>LABEL</th>
                <th>DESCRIPTION</th>
                <th>CLOSE</th>
                <th>EDIT</th>";
    while ($row = $query_set->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row[label] . "</td>";
        if (strlen($row[description]) > 25) {
            echo "<td>" . substr($row[description], 0, 22) . "...</td>";
        } else {
            echo "<td>" . $row[description] . "</td>";
        }
        echo "<td><a href=".htmlspecialchars($_SERVER["PHP_SELF"])."?id=" . $row[id] . "&mode=close>CLOSE</a></td>";
        echo "<td><a href=".htmlspecialchars($_SERVER["PHP_SELF"])."?id=" . $row[id] . "&mode=edit>EDIT</a></td>";
        echo "</tr>";

    }
    echo "</table>";
} else {
    echo "<h2> No active todo items.</h2>";
}
$conn->close();
?>
      <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
      <script type="text/javascript" src="js/materialize.min.js"></script>
    </body>


</html>
