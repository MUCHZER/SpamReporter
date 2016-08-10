<?php

    include_once 'controller/Controller.php';
    include_once 'model/Auth.class.php';

    $auth = new Auth();


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
    case 'sub' :
        $array['first'] = $_REQUEST['first'];
        $array['last'] = $_REQUEST['last'];
        $array['pseudo'] = $_REQUEST['pseudo'];
        $array['mail'] = $_REQUEST['mail'];
        $array['password'] = $_REQUEST['password'];
        $array['ipadress'] = $_SERVER['REMOTE_ADDR'];
        $array['useragent'] = $_SERVER['HTTP_USER_AGENT'];
        $auth->newUser($array);

        break;
}







?>
