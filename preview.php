<?php
require_once(__DIR__.'/connect.php');

$id = intval($_GET['id']);

$sql = "SELECT * FROM `gallery` WHERE `id`=:id LIMIT 0,1";
$sth = $DBC->prepare($sql);
$sth->execute(array(
    'id' => $id
));
$gallery = $sth->fetch(PDO::FETCH_ASSOC);

if(empty($gallery)){
    http_response_code(404);
    exit;
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/reset.css">
    <style>
        body{
            overflow: hidden;
        }
    </style>
</head>
<body>
    <canvas id="canvas" width="300" height="300"></canvas>
</body>
<script>
    <?php echo $gallery['code'];?>
</script>
</html>