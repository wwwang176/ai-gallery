<?php
require_once(__DIR__.'/connect.php');


$sql = "SELECT `id`,`author`,`name`,`date` FROM `gallery` ORDER BY `date` ASC LIMIT 0,50";
$sth = $DBC->prepare($sql);
$sth->execute();
$gallerys = $sth->fetchAll(PDO::FETCH_ASSOC);


echo json_encode($gallerys);

