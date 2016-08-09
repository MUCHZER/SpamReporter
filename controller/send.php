<?php

    include_once 'Controller.php';



switch ($route) {
    case 'report' :
        $array['country'] = $_REQUEST['country'];
        $array['number'] = $_REQUEST['number'];
        $array['type'] = $_REQUEST['type'];
        $array['resume'] = $_REQUEST['resume'];
        $array['author_id'] = $_REQUEST['author'];
        $dr->addReport($array);
        $id = $dr->db->bdd->lastInsertId();
        header('Location: ../report/'.$id."/fiche.html" );
        break;
    case 'author' :
    
        break;
}







?>
