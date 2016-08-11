<?php

class DataReport
{
    /**
     * DataReport constructor.
     */
    function __construct()
    {
        $this->basedir = dirname(__FILE__) . '/';
        //init db object
        require_once 'model/db.class.php';
        $this->db = new db();
        $this->vote = 'vote';
        $this->report = 'report';
        $this->comment = 'comment';
        $this->error = array();
        $this->settings['basepath'] = '/spamreportv2/';

        //init auth object
        require_once 'model/Auth.class.php';
        $this->auth = new Auth();


    }

    /**
     * @param $method
     * @param $arg
     * @return string
     */
    public function execute($method, $arg)
    {
        switch ($method) {
            case 'search' :
                $data = $this->searchReport($arg['term']);
                $pagedata['search'] = $arg['term'];
                $pagedata['results'] = $data;
                break;
            case 'report' :
                $data = $this->getReportById($arg['term']);
                $pagedata['results'] = $data[0];
                //Gérer l'id non défini
                break;
            case 'reportlist' :
                $data = $this->getReportList();
                $pagedata['results'] = $data;
                break;
            case 'formulaire' :
                $pagedata['results'] = '';
                break;
            case 'vote' :
                $data = $this->getVote($arg['vote']);
                $pagedata['results'] = $data;
                break;
            case 'subscribe' :

                break;
            case 'login' :
                $login = $this->login($formdata);
                if (!$login) {
                    $pagedata['error'] = true;
                } else {
                    $this->auth->createCookie($token);
                }
                break;
        }

        //export to twig
        $pagedata['formdata'] = $_REQUEST;
        $pagedata['userdata'] = $this->auth->user;
        $pagedata['settings'] = $this->settings;

        switch ($arg['format']) {
            case 'json' :
                $data = json_encode($pagedata);
                break;
            case 'html' :
                $data = $this->parseTwig($pagedata, $arg['view']);
                break;
            default :
                $data = $pagedata;
        }
        return $data;
    }

    public function login($formdata)
    {
        $check = $this->auth->checkLogin($formdata);

        if ($check) {
            $token = $this->auth->createSessionToken();
            return $token;
        } else {
            return false;
        }
    }

    public function logout()
    {
        $this->auth->deleteCookie();
    }


    public function searchReport($term)
    {
        $sql = "SELECT * FROM " . $this->report . " WHERE number LIKE :term";
        $exec = array('term' => $term . "%");
        $result = $this->db->selectSQL($sql, $exec);
        return $result;
    }

    /**
     * Function getReportById
     *
     * Get a reported phone number by its id
     *
     * @param (int)
     * @return (array)
     */
    public function getReportById($id)
    {
        $sql = "SELECT *, report.id AS prim_key, report.date AS datespam
                FROM report
                INNER JOIN author
                ON report.author_id = author.id WHERE report.id = :id;";
        $exec = array(':id' => $id);
        $result = $this->db->selectSQL($sql, $exec);
        return $result;
    }

    /**
     * @return string
     */
    public function getVote($arg)
    {
        $author_id = $arg['author_id'];
        $report_id = $arg['report_id'];

        switch ($arg['vote']) {
            case '-1' :
                $result = $this->rateSpam($author_id, $report_id);
                return $result;
                break;
            case '0' :
                $result = $this->rateNeutralSpam($author_id, $report_id);
                return $result;
                break;
            case '1' :
                $result = $this->rateNoSpam($author_id, $report_id);
                return $result;
                break;
        }
    }

    private function checkRate($author_id) //check si l'auteur a déjà voté
    {
        $sql = "SELECT * FROM " . $this->vote . " WHERE author_id = :author";
        $exec = array('author' => $author_id);
        $result = $this->db->selectSQL($sql, $exec);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function removeRate($author_id)
    {
        $sql = "DELETE FROM " . $this->vote . " WHERE author_id = :author";
        $exec = array('author' => $author_id);
        $result = $this->db->selectSQL($sql, $exec);
    }

    public function rateSpam($author_id, $report_id) //vote négatif (rouge)
    {
        $check = $this->checkRate($author_id);

        if (!$check) {
            $sql = "INSERT INTO " . $this->vote . " (`report_id`, `author_id`, `vote`, `date`) VALUES (:report_id, :author_id, :vote, NOW())";
            $exec = array(
                'report_id' => $report_id,
                'author_id' => $author_id,
                'vote' => '-1'
            );
            $result = $this->db->selectSQL($sql, $exec);
            echo "vote pris en compte";
        } else {
            $sql = "UPDATE vote SET vote = '-1' WHERE author_id = :author_id";
            $exec = array('author_id' => $author_id);
            $result = $this->db->selectSQL($sql, $exec);
            echo "vote mis à jour";
        }
    }

    public function rateNeutralSpam($author_id, $report_id) //vote neutre (orange)
    {
        $check = $this->checkRate($author_id);
        if (!$check) {
            $sql = "INSERT INTO " . $this->vote . " (`report_id`, `author_id`, `vote`, `date`) VALUES (:report_id, :author_id, :vote, NOW())";
            $exec = array(
                'report_id' => $report_id,
                'author_id' => $author_id,
                'vote' => '0'
            );
            $result = $this->db->selectSQL($sql, $exec);
            echo "vote pris en compte";
        } else {
            $sql = "UPDATE vote SET vote = '0' WHERE author_id = :author_id";
            $exec = array('author_id' => $author_id);
            $result = $this->db->selectSQL($sql, $exec);
            echo "vote mis à jour";
        }
    }

    public function rateNoSpam($author_id, $report_id) //vote positif (vert)
    {
        $check = $this->checkRate($author_id);
        if (!$check) {
            $sql = "INSERT INTO " . $this->vote . " (`report_id`, `author_id`, `vote`, `date`) VALUES (:report_id, :author_id, :vote, NOW())";
            $exec = array(
                'report_id' => $report_id,
                'author_id' => $author_id,
                'vote' => '1'
            );
            $result = $this->db->selectSQL($sql, $exec);
            echo "vote pris en compte";
        } else {
            $sql = "UPDATE vote SET vote = '1' WHERE author_id = :author_id";
            $exec = array('author_id' => $author_id);
            $result = $this->db->selectSQL($sql, $exec);
            echo "vote mis à jour";
        }
    }

    public function parseTwig($data, $view)
    {
        $loader = new Twig_Loader_Filesystem('view/');
        $twig = new Twig_Environment($loader, array(
            'cache' => false
        ));
        return $twig->render($view . '.twig', $data);
    }

    /**
     * Function addReport($report)
     *
     * Add new phone number in report DB
     *
     * @param (array) $report = from form
     * @return (bool) true if it works
     */
    public function addReport($exec)
    {
        if ($this->reportExist($exec)) {
            echo '<br> Error Doublon';
        } else {
            $sql = "INSERT INTO " . $this->report . "(`country`, `number`, `type`, `date`, `resume`, `author_id`) VALUES (:country, :number, :type, NOW(), :resume, :author_id);";
            $result = $this->db->selectSQL($sql, $exec);
        }
        return $result;
    }

    /**
     * Function reportExist
     * @param  [type] $sql [description]
     * @return [type]      [description]
     */
    private function reportExist($array)
    {
        $number = array("number" => $array['number']);
        $sql = "SELECT * FROM " . $this->report . " WHERE number = :number";
        $result = $this->db->selectSQL($sql, $number);
        if ($result == array()) {
            $this->error = array("doublon" => false);
            return false;
        } else {
            $this->error = array("doublon" => true);
            return true;
        }
    }

    /**
     * Function addComment($comment)
     *
     * Add new comment on a report
     *
     * @param (array)
     * @return (bool) true if it works
     */
    public function addComment()
    {

    }

    /**
     * Function editComment($comment)
     *
     * Edit a comment
     *
     * @param (array) "report_id" => $report_id (int),
     *         "comment" => $comment (char),
     *         "id" => $id (int)
     *         "modified" => time() (timestamp)
     * @return (bool) if no error = true
     */
    public function editComment()
    {
        # code...
    }

    /**
     * Function deleteComment($id)
     *
     *
     */
    public function deleteComment($id)
    {
        # code...
    }

    /**
     * Function getReportById
     *
     * Get a reported phone number by its id
     *
     * @param (int)
     * @return (array)
     */
    public function getReportList()
    {
        $sql = "SELECT *, report.id AS prim_key, report.date AS reportdate
                FROM report
                INNER JOIN author
                ON report.author_id = author.id;
                ";
        $result = $this->db->selectSQL($sql);
        return $result;
    }

    public function checkAuth($arg, $cookie)
    {
        if (isset($arg['pseudo']) && isset($arg['password'])) {
            $this->login($arg);
        } elseif (isset($cookie['token'])) {
            $this->auth->checkSessionToken($cookie['token']);
        }
    }

}

?>
