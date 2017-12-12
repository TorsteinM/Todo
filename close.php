<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>Examples</title>
<meta name='viewport' content='width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no'/>
<meta name="description" content="">
<meta name="keywords" content="">
<link href="static/style.css" rel="stylesheet">
</head>
<div>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
        Label:<br/>
        <input type="text" name="label" value=""><br/>
        Description:<br/>
        <textarea name="description" rows="5" columns="50" readonly></textarea><br/>
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
</html>
