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
        $this->author = 'author';
        $this->error = array();
        $this->err = 0;
        $this->errMsg = array();
        $this->user = false;
        $this->secretKey = "qs;dk!5f1:lm5j;56s*dpf9mpvl";
    }

    /*
     * Function srt_random
     *
     * Generate random token for identification
     *
     * @param length of your token
     * @return string
     */
    private function generateToken($length)
    {
        $alphabet = "0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN";
        return substr(str_shuffle(str_repeat($alphabet, $length)), 0, $length);
    }

    private function cryptPassword($uncrypted)
    {
        $crypted = password_hash($uncrypted, PASSWORD_BCRYPT);
        return $crypted;
    }

    /* Function verifUser
     *
     *
     */
    public function verifUser($array)
    {
        $this->validFname($array['first']);
        $this->validLname($array['last']);
        $this->validPseudo($array['pseudo']);
        $this->validMail($array['mail']);
        $this->validPassword($array['password']);

        if ($this->$err) {
            return false;
        } else {
            return true;
        }
    }

    /* Function verifUser
     *
     *
     */
    public function validFname($fName)
    {
        if (isset($fName)) {
            $err = 1;
            $this->errMsg[] = "Champ du Prénom vide";
        }
        if (strlen($fName) >= 45) {
            $err = 1;
            $this->errMsg[] = "Nom trop long";
        }
        if ($err) {
            $this->err = 1;
            return false;
        } else {
            return true;
        }
    }

    public function validLname($lName)
    {
        if (isset($lName)) {
            $err = 1;
            $this->errMsg[] = "Champ du Nom vide";
        }
        if (strlen($lName) >= 45) {
            $err = 1;
            $this->errMsg[] = "Nom trop long";
        }
        if ($err) {
            $this->err = 1;
            return false;
        } else {
            return true;
        }
    }

    public function validPseudo($pseudo)
    {
        if (isset($pseudo)) {
            $err = 1;
            $this->errMsg[] = "Champ du Pseudo vide";
        }
        if (strlen($pseudo) >= 25) {
            $err = 1;
            $this->errMsg[] = "Pseudo trop long";
        }
        $arg['pseudo'] = $pseudo;
        $check = $this->db->selectSQL("SELECT * FROM " . $this->author . " WHERE pseudo = :pseudo", $arg);
        if (!empty($check)) {
            $err = 1;
            $this->errMsg[] = "Ce pseudo est déjà utilisé";
        }
        if ($err) {
            $this->err = 1;
            return false;
        } else {
            return true;
        }
    }

    public function validMail($mail)
    {
        if (isset($mail)) {
            $err = 1;
            $this->errMsg[] = "Champ du Mail vide";
        }
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $err = 1;
            $this->errMsg[] = "Email incorrect";
        }
        if ($err) {
            $this->err = 1;
            return false;
        } else {
            return true;
        }
    }

    public function validPassword($password)
    {
        if (isset($password)) {
            $err = 1;
            $this->errMsg[] = "Champ du Nom vide";
        }
        if (isset($password) && strlen($password) >= 8 && strlen($password) <= 50) {
            $password = $this->cryptPassword($password);
        } else {
            $err = 1;
            $this->errMsg[] = "Email incorrect";
        }
        if ($err) {
            $this->err = 1;
            return false;
        } else {
            return true;
        }
    }

    public function checkLogin($array)
    {
        $sql = "SELECT * FROM " . $this->author . " WHERE pseudo = :pseudo AND password = :password";
        $exec = array(
            'pseudo' => $array['pseudo'],
            'password' => $this->cryptPassword($array['password'])
        );
        $result = $this->db->selectSQL($sql, $exec);
        if (isset($result[0]['id'])) {
            $this->user = $result[0];
            return true;
        } else {
            return false;
        }
    }

    public function createSessionToken()
    {
        if ($this->user) {
            $token = md5($this->user['id'] . $this->secretKey . $_SERVER['HTTP_USER_AGENT']);
            $token .= "|" . $this->user['id'];
            $this->token = $token;
            return $token;
        } else {
            return false;
        }
    }

    public function checkSessionToken($token)
    {
        $check = explode('|', $token);
        $checkToken = md5($check[1] . $this->secretKey . $_SERVER['HTTP_USER_AGENT']);
        $checkId = $check[1];
        if ($checkToken == $check[0]) {
            $this->token = $token;
            $this->user = $this->getUserById($checkId);
            return true;
        } else {
            return false;
        }
    }

    /* Function getUserById
     *
     */
    private function getUserById($id) {
        $user = $this->db->selectSQL('SELECT * FROM author WHERE id = ' . $id);
        return $user[0];
    }

    public function newUser($array)
    {
        $data = $this->verifUserArray($array);
        if ($array == false) {
            return false;
        } else {
            // generate token
            $token = generateToken(60);
            $req = $this->db->selectSQL(
                "INSERT INTO author SET first = :first, last = :last, pseudo = :pseudo, mail = :mail, password = :password, date =  NOW(), ipadress = :ipadress, useragent = :useragent, registered = :registered, token = :token",
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
            $_SESSION['flash']['success'] = 'Un mail de confirmation vous a été envoyé pour valider votre compte';
            header('Location: login.php');
        }
    }


    /**
     * @param string $token
     *
     */
    public function createCookie($token)
    {
        setcookie("token", $token, time()+10000, "/");
        return true;
    }

    public function deleteCookie()
    {
        setcookie("token", '', time()+10000, "/");
        return true;
    }
}
}
