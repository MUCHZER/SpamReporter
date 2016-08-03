<?php

    include_once 'Controller.php';
    $array['country'] = $_REQUEST['country'];
    $array['number']  = $_REQUEST['number'];
    $array['type']  = $_REQUEST['type'];
    $array['resume']  = $_REQUEST['resume'];
    $array['author_id']  = $_REQUEST['author_id'];
    $dr->addReport($array);


?>
