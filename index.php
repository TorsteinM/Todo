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
        
        <!--Import Google Icon Font-->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <!--Import materialize.css-->
        <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>

        <!--Let browser know website is optimized for mobile-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <link href="/css/style.css" rel="stylesheet">
    </head>
    <body>
<?php
require_once 'helpers.php';

$servername = "localhost";
$username = "root";
$password = "Skule123";
$dbname = "TODO";

$id = $label = $description = $category = $active = $closing = "";

$DB_EXIST = $TABLE_EXIST = TRUE;

//Create the DATABASE:
if(!$DB_EXIST) createDB($servername, $username, $password, $dbname);

if(!$TABLE_EXIST){
    // CREATE TABLE from createModel imported from helpers.php
    $model = array(
        "id INT(6) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "label VARCHAR(30) NOT NULL",
        "description TEXTFIELD(800)",
        "category VARCHAR(30)",
        "active BOOL DEFAULT TRUE",
        "closing VARCHAR(800)",
    );
    $model_name = "todos";
    createModel($servername, $username, $password, $dbname, $model_name, $model);
}
           


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $labelErr = "";
    $id = test_input($_POST["id"]);
    $label = test_input($_POST["label"]);
    $description = test_input($_POST["description"]);
    $category = test_input($_POST["category"]);
    $active = 1;
    $closing = test_input($_POST["closing"]);
    
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
    } elseif($_POST['submit']=='CLOSE') {
        $id = test_input($_POST['id']);
        $closing = test_input($_POST['closing']);
        if($id){
            $sql = "UPDATE todos SET closing='$closing', active='0' WHERE id=$id";
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
            echo "Did not resolve ID.";
        }
    } elseif($_POST['submit']=='ADD') {
        $closing = "";
        $active = 1;
        $sql = "INSERT INTO todos (label,description,category,active,closing) VALUES ('$label','$description','$category','$active','')";
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
        echo "Did not find valid post request.<br/>".var_dump($_POST);
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
            $title = '
            <h5 class="center">Edit Existing</h5>
            ';
            $id_html =
            '
            <div class="input-field col s6 ">
              <input name="id" id="id" type="text" class="validate" value="'.$id.'" readonly>
              <label for="id">ID</label>
            </div>
            ';
            if($active){
                $active_html = 
                '
                <div class="input-field col s6">
                    <input type="checkbox" class="filled-in" id="active" name="active" checked="checked" />
                    <label for="active">Active</span>
                  </label>
                </div>
                '; 

            } else {
                $active_html = 
                '
                <div class="input-field col s6">
                    <input type="checkbox" id="active" class="filled-in" name="active" />
                    <label for="active">Active</span>
                  </label>
                </div>
                ';
            }

            $label_html =
            '
            <div class="input-field col s6">
              <input name="label" id="label" type="text" class="validate" value="'.$label.'">
              <label for="label">Label</label>
            </div>
            ';
            $category_html = 
            '
            <div class="input-field col s6">
              <select name="category">
                <option value="TODO" selected>TODO</option>
                <option value="Other">Other</option>
              </select>
              <label for="category">Category</label>
            </div>
            ';
            $description_html =
            '
            <div class="input-field col s12">
              <textarea name="description" id="description" class="materialize-textarea">'.$description.'</textarea>
              <label for="description">Description</label>
            </div>
            ';
            $closing_html = 
            '
            <div class="input-field col s12">
              <textarea name="closing" id="closing" class="materialize-textarea">'.$closing.'</textarea>
              <label for="closing">Closing Comment</label>
            </div>
            ';
            $submit_html = 
            '
            <div class="input-field col s12 center">
              <button class="btn orange" type="submit" value="EDIT" name="submit">EDIT</button>
              <button class="btn red" type="submit" value="REMOVE" name="submit">REMOVE</button>
              <a href="/" class="btn">RETURN</a>
            </div>
            ';
              

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
            
            //then lets set up the form for easy closing comment.
            $title = '<h5 class="center">Close entry: '.$label.'</h5>';
            $id_html =
            '
              <input name="id" type="text" value='.$id.' hidden>

            ';
            $label_html =
            '';
            $category_html = 
            '';
            $active_html = 
            '';
            $description_html =
            '
            <div class="input-field col s12">
              <textarea name="description" id="description" class="materialize-textarea" readonly>'.$description.'</textarea>
              <label for="description">Description</label>
            </div>
            ';
            $submit_html =
            '
            <div class="input-field col s12 center">
              <button class="btn" type="submit" value="CLOSE" name="submit">CLOSE TODO</button>
              <a href="/" class="btn">RETURN</a>
            </div>
            ';
            $closing_html = 
            '
            <div class="input-field col s12">
              <textarea name="closing" id="closing" class="materialize-textarea">'.$closing.'</textarea>
              <label for="closing">Closing Comment</label>
            </div>
            ';


        } else {
            echo "Not a valid item ID. Close mode failed.";
        }

    } else {
        // Default: Empty form for registring a new TODO
        $title = '<h5 class="center">Add New</h5>';
        $id_html = '';
        $label_html =
        '
        <div class="input-field col s6">
          <input name="label" id="label" type="text" class="validate">
          <label for="label">Label</label>
        </div>
        ';
        $category_html = 
        '
        <div class="input-field col s6">
          <select name="category" id="category">
            <option value="TODO" selected>TODO</option>
            <option value="Other">Other</option>
          </select>
          <label for="category">Category</label>
        </div>
        ';
        $description_html =
        '
        <div class="input-field col s12">
          <textarea name="description" id="description" class="materialize-textarea">'.$description.'</textarea>
          <label for="description">Description</label>
        </div>
        ';
        $submit_html =
        '
        <div class="input-field col s12 center">
          <button class="btn" type="submit" value="ADD" name="submit">ADD TODO</button>
        </div>
        ';

    }

    //This is the end of the REQUEST handling. Below is some spaghetti for making the markup of the page.
    //The page is divided into two sections. The CSS is framework that handles it is materialize.
}?>
    <a href="/" class="center"><h1>A TODO LIST</h1></a>
    <div class="container row">
      
      <div id="table_frame" class="container col s8">
    <?php
    // make a list of the query_set gathered
    $sql = "SELECT * FROM todos WHERE active='1'";
    $conn = new mysqli($servername, $username, $password, $dbname);
    $query_set = $conn->query($sql);
    if ($query_set->num_rows > 0) {
        echo 
        '<h5 class="center">Active</h5>';
        
        
        while ($row = $query_set->fetch_assoc()) {
            echo 
            '
    <div class="row card-panel teal lighten-2">
        <div class="container col s10">';
            echo 
            '
              <div class="row">
                <div class="col s8"><strong>Label:</strong> ' . $row[label] . '</div>
                <div class="col s4 right"><strong>Category:</strong> ' . $row[category] . '</div>
              </div>';
            echo             
            '
            <div class="row">
              <div class="col s12">' . $row[description] . '</div>
            </div>';
            echo 
            '
        </div>
        <div class="container col s2">';
            echo 
            '
            <div class="row s2">
              <a class="btn center" href='.htmlspecialchars($_SERVER["PHP_SELF"]).'?id=' . $row[id] . '&mode=close>CLOSE</a>
            </div>';
            echo 
            '
            <div class="row s2">
              <a class="btn center" href='.htmlspecialchars($_SERVER["PHP_SELF"]).'?id=' . $row[id] . '&mode=edit>EDIT</a>
            </div>
        </div>
    </div>
';
        
        }
        
    } else {
        echo "<h2> No active todo items.</h2>";
    }
    $conn->close();
    ?> 
        </div>
        <div id="io_frame" class="container col s4">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
    <?php
    echo '
        <div class="row">' . $title . '
        </div>';
    echo '
        <div class="row">' . $label_html . $category_html . '
        </div>';
    echo '
        <div class="row">' . $id_html . $active_html . '
        </div>';
    echo '
        <div class="row">' . $description_html . '
        </div>';
    echo '
        <div class="row">' . $closing_html . '
        </div>';
    echo '
        <div class="row">' . $submit_html . '
        </div>';
    ?>
        </form>
      </div>
      </div>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="js/materialize.min.js"></script>
    <script>
    $(document).ready(function() {
        $('select').material_select();
    });
    </script>
</body>

</html>
