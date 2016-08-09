<?php

include_once('model/DataReport.class.php');
$dr = new DataReport();

//Check login et vérif cookie, conditionne la suite


$data = $dr->execute($method, $arg);

print_r($data);

 ?>
