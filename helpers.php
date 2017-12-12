<?php function test_input($data)
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
}