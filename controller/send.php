<?php

    include_once 'controller/Controller.php';
    include_once 'model/Auth.class.php';

    $auth = new Auth();


switch ($route) {
    case 'report' :
        $auth->checkSessionToken($_COOKIE['token']);
        $array['country'] = $_REQUEST['country'];
        $array['number'] = $_REQUEST['number'];
        $array['type'] = $_REQUEST['type'];
        $array['resume'] = $_REQUEST['resume'];
        $array['author_id'] = $auth->user['id'];
        $curl = curl_init("https://AC658c8a5e871283dde3bd686dab7f2ad3:e62b29cdcbbe445c95fa8c7d8ee4d20f@lookups.twilio.com/v1/PhoneNumbers/" . '+' . $_REQUEST['country'] . $_REQUEST['number'] . "?Type=carrier&Type=caller-name");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $array['json'] = curl_exec($curl);
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
        header( 'Location: ../subscribe/?error='.$error );
        break;
}







?>
