<?php

include_once('model/DataReport.class.php');
$dr = new DataReport();
$data = $dr->execute($method, $arg);

echo $data;

 ?>
