<?php
require_once(__DIR__.'/connect.php');

$noun = trim($_POST['noun']);

//沒給名詞
if(empty($noun)){

    http_response_code(403);
    echo json_encode([
        'message' => 'noun empty',
    ]);
    exit;
}

$sql = "INSERT INTO `queue` SET 
    `noun` = :noun,
    `date` = :date
";
$sth = $DBC->prepare($sql);
$sth->execute(array(
    'noun' => $noun,
    'date' => date('Y-m-d H:i:s'),
));

echo json_encode([
    'message' => 'ok'
]);


