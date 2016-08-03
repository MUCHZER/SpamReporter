<?php

class DataReport
{

  /**
   * DataReport constructor.
   */
  function __construct() {
	$this->basedir = dirname(__FILE__).'/';
	//init db object
	require_once 'model/db.class.php';
	$this->db = new db();
	$this->report = 'report';
	$this->error = array();
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
                $data = $this->searchList($arg['term']);
                $pagedata['search'] = $arg['term'];
                $pagedata['results'] = $data;
                break;
            case 'report' :
                $data = $this->getReportById($arg['term']);
                $pagedata['results'] = $data;
                break;
        }
        switch ($arg['format']) {
            case 'json' :
                return json_encode($pagedata);
                break;
            case 'html' :
                return $this->parseTwig($pagedata, $arg['view']);
                break;
        }
    }


    public function parseTwig($data, $view) {
        $loader = new Twig_Loader_Filesystem('view/');
        $twig = new Twig_Environment($loader, array(
            'cache' => false
        ));
        return $twig->render($view.'.twig', $data);
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
	  $sql = "INSERT INTO ".$this->report."(`country`, `number`, `type`, `date`, `resume`, `author_id`) VALUES (:country, :number, :type, NOW(), :resume, :author_id);";
	  $result = $this->db->selectSQL($sql, $exec);
	}

	return true;
  }

  /**
   * Function reportExist
   * @param  [type] $sql [description]
   * @return [type]      [description]
   */
  private function reportExist($array)
  {
	$number = array("number" => $array['number']);
	$sql = "SELECT * FROM ".$this->report." WHERE number = :number";
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
	# code...
  }

  /**
  * Function editComment($comment)
  *
  * Edit a comment
  *
  * @param (array) "report_id" => $report_id (int),
  *		 "comment" => $comment (char),
  *		 "id" => $id (int)
  *		 "modified" => time() (timestamp)
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
  *
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
  public function getReportById($id) {
	  $sql = "SELECT * FROM ".$this->report." WHERE id = :id ";
	  $exec = array(':id' => $id);
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
  public function getReportList() {
	$sql = "SELECT * FROM ".$this->report."";
	$result = $this->db->selectSQL($sql);
	return $result;
  }

  public function searchList($term) {
      $sql = "SELECT * FROM ".$this->report." WHERE number LIKE :term";
      $exec = array('term' =>  $term."%");
      $result = $this->db->selectSQL($sql, $exec);
      return $result;
    }



}

 ?>
