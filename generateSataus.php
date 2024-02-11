<?php
require_once(__DIR__.'/connect.php');

$id = intval($_POST['id']);

$sql = "SELECT * FROM `queue` WHERE `id`=:id LIMIT 0,1";
$sth = $DBC->prepare($sql);
$sth->execute(array(
    'id' => $id
));
$gallery = $sth->fetch(PDO::FETCH_ASSOC);

if(empty($gallery)){
    http_response_code(404);
    exit;
}

echo json_encode([
    'status' => intval($gallery['status']),
]);
