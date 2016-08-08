<?php

/**
 * Created by PhpStorm.
 * User: Stagiaire
 * Date: 01/08/2016
 * Time: 10:22
 */
class Auth
{
    public function __construct()
    {
        //init db object
        require_once 'model/db.class.php';
        $this->db = new db();
        $this->report = 'author';
        $this->error = array();
    }

    /*
     * Function srt_random
     *
     * Generate random token for identification
     *
     * @param length of your token
     * @return string
     */
    private function generateToken($length){
        $alphabet = "0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN";
        return substr(str_shuffle(str_repeat($alphabet, $length)), 0, $length);
    }

    private function cryptPassword($uncrypted) {
        $crypted = password_hash($uncrypted, PASSWORD_BCRYPT);

    }

    private function verifUserArray($array) {
        if (strlen($array['first']) <= 45) {
            $array['first'] = htmlentities($array['first']);
        } else {return false;}
        if (strlen($array['last']) <= 45) {
            $array['last'] = htmlentities($array['last']);
        } else {return false;}
        if (strlen($array['pseudo']) <= 45) {
            $array['pseudo'] = htmlentities($array['pseudo']);
        } else {return false;}
        if (filter_var($array['mail'], FILTER_VALIDATE_EMAIL)) {
            $array['mail'] = htmlentities($array['mail']);
        } else {return false;}
        if (isset($array['password']) && strlen($array['password']) >= 8 && strlen($array['password']) <= 50) {
            $array['password'] = $this->cryptPassword($data['password']);
        } else {return false;}
    }

    public function newUser($array) {
        $data = $this->verifUserArray($array);
        if ($array == false) {
            return false;
        }
        else {
            // generate token
            $token = generateToken(60);
            $req = $this->db->selectSQL(
                "INSERT INTO author SET first = :first, last = :last, pseudo = :pseudo, mail = :mail, password = :password, date = :date, ipadress = :ipadress, useragent = :useragent, registered = :registered, token = :token",
                array(
                    "first" => $data['first'],
                    "last" => $data['last'],
                    "pseudo" => $data['pseudo'],
                    "mail" => $data['mail'],
                    "password" => $data['password'],
                    "date" => $data['date'],
                    "ipadress" => $data['ipadress'],
                    "useragent" => $data['useragent'],
                    "registered" => $data['registered'],
                    "token" => $data['token']
                ));
            // create mail confirmation
            $user_id = $this->db->pdo->lastInsertId();
            // On envoit l'email de confirmation
            mail($_POST['email'], 'Confirmation de votre compte', "Afin de valider votre compte merci de cliquer sur ce lien\n\nhttp:/leog.student.codeur.online/confirm.php?id=$user_id&token=$token");
            // On redirige l'utilisateur vers la page de login avec un message flash
            $_SESSION['flash']['success'] = 'Un email de confirmation vous a été envoyé pour valider votre compte';
            header('Location: login.php');

            exit();

        }
    }
}