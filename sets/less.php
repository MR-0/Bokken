<?php

require dirname(__DIR__, 1).'/vendors/LessInc/lessc.inc.php';

$lessc = new lessc;
$lessc->setFormatter("compressed");

return function ($source, $output) use ($lessc) {
  try {
    $lessc->checkedCompile($source, $output);
    return true;
  }
  catch (Exception $e) {
    error_log('Error compiling LESS: '.$e->getMessage());
    return false;
  }
};