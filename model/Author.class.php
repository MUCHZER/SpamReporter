<?php
 /**
 *
 */
class Author
{

  function __construct()
  {
    require_once 'model/db.class.php';
    $this->db = new db();
    $this->author = 'author';
    $this->error = array();
  }

  /**
   * Function createNewAuthor
   * @param  [type] $exec [description]
   * @return [type]       [description]
   */
  public function createNewAuthor($exec)
  {
    if ($this->authorExist($exec)) {
      return $this->error;
    } else {
      $sql = "INSERT INTO ".$this->author." VALUES (:first, :last, :pseudo, :mail, :password, NOW(), :ipadress, :useragent) ";
      $result = $this->db->selectSQL($sql, $exec);
    }
  }

  /**
   * Function authorExist
   * @param  [type] $sql [description]
   * @return [type]      [description]
   */
  private function authorExist($array)
  {
    $number = array("author" => $array['author']);
    $sql = "SELECT * FROM ".$this->report." WHERE author = :author";
    $result = $this->db->selectSQL($sql, $number);
    print_r($result);
    if ($result == array()) {
      $this->error = array("authorExist" => false);
      return false;
    } else {
      $this->error = array("authorExist" => true);
      return true;
    }
  }

}
 ?>
