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
        $this->settings = [];
        $this->settings['basepath'] = '/spamreportv2/';
        $this->search = '';

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

        //export to twig
        $pagedata['formdata'] = $_REQUEST;
        $pagedata['userdata'] = $this->auth->user;
        $pagedata['settings'] = $this->settings;
        $pagedata['user'] = $this->auth->user;
        $pagedata['reportfromsearch'] = $_REQUEST['fromsearch'];

        switch ($method) {
            case '404' :
                $pagedata['results'] = '';
                break;
            case 'search' :
                $data = $this->searchReport($arg['term']);
                $pagedata['search'] = $arg['term'];
                $pagedata['results'] = $data;
                if (empty($data)) {
                    header('Location:'. $this->settings['basepath'] . 'report/?fromsearch=' . $pagedata['search']);
                }
                break;
            case 'report' :
                $data = $this->getReportById($arg['term']);
                $pagedata['results'] = $data[0];
                $pagedata['com'] = $this->getComList($arg['term']);
                $pagedata['json'] = json_decode($data[0]['json']);
                break;
            case 'commentpost' :
                $this->auth->checkSessionToken($_COOKIE['token']);
                $data = [$pagedata['formdata'], $this->auth->user];
                $this->addComment($data);
                //$id = $this->db->bdd->lastInsertId();
                header('Location: ' . $this->settings['basepath'] . 'report/' . $data[0]['report_id'] . '/fiche.html');
                break;
            case 'reportlist' :
                $data = $this->getSpamAwards();
                $pagedata['results'] = $data;
                break;
            case 'annuaire' :
                $data = $this->getReportList('DESC', '45541848741');
                $pagedata['results'] = $data;
                break;
            case 'formulaire' :
                $pagedata['results'] = '';
                break;
            case 'vote' :
                $data = $this->getVote($pagedata['formdata']);
                $pagedata['results'] = $data;
                break;
            case 'subscribe' :
                $data = $this->auth->verifUser($pagedata['formdata']);
                if ($data) {
                    $data = $this->auth->newUser($pagedata['formdata']);
                    $pagedata['results'] = $data;
                }else{

                }
                break;
            case 'logout' :
                $this->auth->disconnect();
                $pagedata['results'] = '';
                header ('location: '. $this->settings['basepath'] );
                break;case 'post' :
                $this->auth->checkSessionToken($_COOKIE['token']);

$check = $this->checkNumValid(xxxxxxxxxxxx)
if ($check) {
                $array = $this->post($pagedata['formdata']);
                $this->addReport($array);
                $id = $this->db->bdd->lastInsertId();
                header('Location: ../report/'.$id."/fiche.html" );
}else{}
                break;
            case 'login' :
                $login = $this->login($pagedata['formdata']);
                if ($this->auth->user == false) {

                } else {
                  $this->auth->createCookie($login);
                    header('Location: ' . $this->settings['basepath'] . '');
                }
                break;
        }
        switch ($arg['format']) {
            case 'json' :
                $data = json_encode($pagedata['results']);
                break;
            case 'html' :
                $data = $this->parseTwig($pagedata, $arg['view']);
                break;
            default :
                //$data = $pagedata;
                $data = $this->parseTwig($pagedata, $arg['view']);
                break;
        }
        return $data;
    }
    public function checkNumValid($num)
    {
        // Twilio API connection
        $curl = curl_init("https://AC658c8a5e871283dde3bd686dab7f2ad3:e62b29cdcbbe445c95fa8c7d8ee4d20f@lookups.twilio.com/v1/PhoneNumbers/" . $num . "?Type=carrier&Type=caller-name");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $check = json_decode( curl_exec($curl), 1 );

        if ($check['status']== '404') {
            return false;
        }else{return true;}

    }

    public function getSpamAwards() {

        $sql = "SELECT *, sum(vote.vote) AS nb, report.id AS prim_key, report.date AS datespam
                FROM report
                INNER JOIN vote
                ON vote.report_id = report.id
                GROUP BY report.id
                ORDER BY nb ASC
                LIMIT 8";
        $result = $this->db->selectSQL($sql);
        return $result;
    }

    /**
     * @param $formdata
     * @return bool|string
     */
    public function login($formdata)
    {
        if (isset($formdata['pseudo']) && isset($formdata['password']))
        {
            $check = $this->auth->checkLogin($formdata);
        }

        if (@$check) {
            $token = $this->auth->createSessionToken();
            return $token;
        } else {
            return false;
        }
    }

    /**
     * @param $data
     * @return array
     */
    public function post($data) {
        $array['country'] = $data['country'];
        $array['number'] = htmlentities($data['number']);
        $array['type'] = $data['type'];
        $array['resume'] = htmlentities($data['resume']);

        // Check if user is logged in, else create a temp user
        $user = $this->auth->user['id'];
        ($user) ? $array['author_id'] = $user : $array['author_id'] = $this->auth->newUser($data['pseudo'], false);

        // Twilio API connection
        $curl = curl_init("https://AC658c8a5e871283dde3bd686dab7f2ad3:e62b29cdcbbe445c95fa8c7d8ee4d20f@lookups.twilio.com/v1/PhoneNumbers/" . $data['number'] . "?Type=carrier&Type=caller-name");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $array['json'] = curl_exec($curl);

        return $array;
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
                ON report.author_id = author.id  
                WHERE report.id = :id";
        $exec = array(':id' => $id);
        $result = $this->db->selectSQL($sql, $exec);

        //$result['count'] =
        $result[0]['negative'] = $this->countSpam($id, '-1');
        $result[0]['positive'] = $this->countSpam($id, '1');

        return $result;
    }

    public function countSpam($id, $rate) {
        //$sql = "SELECT nb.spam, vote.* FROM vote, (select COUNT(*) AS spam FROM vote WHERE vote = '-1') nb WHERE vote = '-1' AND report_id = :id";
        $sql = "SELECT COUNT(*) AS count FROM vote WHERE vote = :rate AND report_id = :id";
        $exec = array(':id' => $id, ':rate' => $rate);
        $result = $this->db->selectSQL($sql, $exec);

        return $result[0]['count'];
    }

    /**
     * @return string
     */
    public function getVote($arg)
    {
        $author_id = $this->auth->user['id'];
        $report_id = $arg['report_id'];

        switch ($arg['vote']) {
            case '-1' :
                $result = $this->rateSpam($author_id, $report_id);
                break;
            case '1' :
                $result = $this->rateNoSpam($author_id, $report_id);
                break;
        }
        return $result;
    }

    private function checkRate($author_id, $report_id) //check si l'auteur a déjà voté
    {
        $sql = "SELECT * FROM " . $this->vote . " WHERE author_id = :author AND report_id = :report";
        $exec = array('author' => $author_id, 'report' => $report_id);
        $result = $this->db->selectSQL($sql, $exec);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    public function removeRate($author_id, $report_id)
    {
        $sql = "DELETE FROM " . $this->vote . " WHERE author_id = :author_id AND report_id = :report_id";
            $exec = array(
                'author_id' => $author_id,
                'report_id' => $report_id
                );
        $result = $this->db->selectSQL($sql, $exec);
        return $result;
    }

    public function rateSpam($author_id, $report_id) //vote négatif (rouge)
    {
        $check = $this->checkRate($author_id, $report_id);

        if (empty($check)) {
            $sql = "INSERT INTO " . $this->vote . " (`report_id`, `author_id`, `vote`, `date`) VALUES (:report_id, :author_id, :vote, NOW())";
            $exec = array(
                'report_id' => $report_id,
                'author_id' => $author_id,
                'vote' => '-1'
            );
            $result = $this->db->selectSQL($sql, $exec);
            print_r($result);
            echo "vote pris en compte";
        } elseif ($check[0]['vote'] == '-1') {
            $this->removeRate($author_id, $report_id);
            echo "vote supprimé";
        }else{
            $sql = "UPDATE " . $this->vote . " SET vote = '-1' WHERE author_id = :author_id AND report_id = :report_id";
            $exec = array(
                'author_id' => $author_id,
                'report_id' => $report_id
                );
            $result = $this->db->selectSQL($sql, $exec);
            echo "vote mis à jour";
        }
    }


    public function rateNoSpam($author_id, $report_id) //vote positif (vert)
    {
        $check = $this->checkRate($author_id, $report_id);
        print_r($check);

        if (empty($check)) {
            $sql = "INSERT INTO " . $this->vote . " (`report_id`, `author_id`, `vote`, `date`) VALUES (:report_id, :author_id, :vote, NOW())";
            $exec = array(
                'report_id' => $report_id,
                'author_id' => $author_id,
                'vote' => '1'
            );
            $result = $this->db->selectSQL($sql, $exec);
            echo "vote pris en compte";
        } elseif ($check[0]['vote'] == '1') {
            $this->removeRate($author_id, $report_id);
            echo "vote supprimé";
        }else {
            $sql = "UPDATE ".$this->vote." SET vote = '1' WHERE author_id = :author_id AND report_id = :report_id";
            $exec = array(
                'author_id' => $author_id,
                'report_id' => $report_id
                );
            $result = $this->db->selectSQL($sql, $exec);
            echo "vote mis à jour";
        }
    }

    public function parseTwig($data, $view)
    {
        $loader = new Twig_Loader_Filesystem('view/');
        $twig = new Twig_Environment($loader, array(
            'cache' => false,
            'debug' => true
        ));
        $twig->addExtension(new Twig_Extension_Debug());
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
            $this->error = true;
            $this->errorMsg[] = 'Doublon !!!';
        } else {
            $sql = "INSERT INTO " . $this->report . "(`country`, `number`, `type`, `date`, `resume`, `author_id`, `json`) VALUES (:country, :number, :type, NOW(), :resume, :author_id, :json);";
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
    public function addComment($array)
    {
        $sql = "INSERT INTO " . $this->comment . "(`comment`, `author_id`, `date`, `report_id`) VALUES (:comment, :author_id, NOW(), :report_id);";
        $exec = array(
            'comment' => $array[0]['comment'],
            'author_id' => $array[1]['id'],
            'report_id' => $array[0]['report_id']
            );
        $result = $this->db->selectSQL($sql, $exec);

        return $result;
    }

    public function getComList($id, $orderby = "DESC", $limit = '500')
    {
        $sql = "SELECT *, comment.id AS comId, comment.date AS datecom
                FROM comment
                INNER JOIN author
                ON comment.author_id = author.id 
                INNER JOIN report
                ON comment.report_id = report.id
                WHERE report.id = $id
                ORDER BY comment.date " . $orderby .  " LIMIT " . $limit . ";";
        $result = $this->db->selectSQL($sql);
        return $result;
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
    public function getReportList($orderby = "DESC", $limit = '8')
    {
        $sql = "SELECT *, report.id AS prim_key, report.date AS reportdate
                FROM report
                INNER JOIN author
                ON report.author_id = author.id
                ORDER BY report.date " . $orderby .  " LIMIT " . $limit . ";";
        $result = $this->db->selectSQL($sql);
        return $result;
    }

    public function checkAuth($arg, $cookie)
    {
        $res = '';
        if (isset($arg['pseudo']) && isset($arg['password']))
        {
            $res = $this->login($arg);
        }
        elseif (isset($cookie['token']))
        {
            $res = $this->auth->checkSessionToken($cookie['token']);
        }
        return $res;
    }

}

?>
