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
$servername = "localhost";
$username = "root";
$password = "Skule123";
$dbname = "TODO";

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

    $label = $description = $category = $active = "";
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

}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
function createModel($name, $user, $pass, $db, $model_name, $model)
{
    $sql = "CREATE TABLE ${model_name} (" . implode(", ", $model) . ")";

    $conn = new mysqli($name, $user, $pass, $db);
    if ($conn->query($sql) === true) {
        echo "Table was made.";
    } else {
        echo "An error occured: " . $conn->error;
    }
}
function createDB($name, $user, $pass, $db)
{
    $conn = new mysqli($name, $user, $pass);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "CREATE DATABASE IF NOT EXISTS " . $db;
    if ($conn->query($sql) === true) {
        echo "Database ready.";
    } else {
        echo "An error occured: " . $conn->error;
    }
}?>
    <div>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
        Label:<br/>
        <input type="text" name="label" value=""><br/>
        Description:<br/>
        <textarea name="description" rows="5" columns="50"></textarea><br/>
        <div class=container>
        Category:<br>
        <select name="category">
            <option value="TODO">TODO</option>
            <option value="Other">Other</option>
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
$sql = "SELECT * FROM todos";
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
        echo "<td><a href=close.php?id=" . $row[id] . ">CLOSE</a></td>";
        echo "<td><a href=edit.php?id=" . $row[id] . ">EDIT</a></td>";
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
