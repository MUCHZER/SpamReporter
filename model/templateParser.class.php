<?php
/**
 *
 */
class templateParser
{

  /**
   * templateParser constructor.
   */
  function __construct() {

  }

  function parseData($data, $tpl) {
    if (file_exists($tpl)) {
      $tpl = file_get_contents($tpl);
    }
    $result = '';
    for ($i=0; $i < sizeof($data) ; $i++) {
      $result .= $this->parseToTemplate($tpl, $data[$i]);
    }
    return $result;
  }

  function parseToTemplate($tpl, $data) {
    foreach ($data as $key => $value) {
      $tpl = str_replace('{{'.$key.'}}', $value, $tpl);
    }
    return $tpl;
  }


}






 ?>
