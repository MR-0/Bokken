<?php

/**
 * Bokken - Minimal PHP MVC - V1.0.0
 *
 * @see       https://github.com/MR-0/Bokken/ Bokken
 *
 * @author    Mario Rojas (MR0) <contact@mr0.cl>
 * @copyright 2019 Mario Rojas
 * @license   https://raw.githubusercontent.com/MR-0/Bokken/master/LICENSE MIT License
 */


namespace Bokken;

class Bokken {
  public static function start ($path) {

    /* DEFINE */
    define('BOKKEN_PATH', $path);

    
    /* TIME ZONE */
    date_default_timezone_set('America/Santiago');


    /* ERRORS LOG */
    ini_set("log_errors", 1);
    ini_set("error_log", BOKKEN_PATH."/errors.log");


    /* REQUIRES */
    require __DIR__.'/assets/sanitize.php';
    require __DIR__.'/assets/functions.php';
    require __DIR__.'/assets/database.php';
    require __DIR__.'/assets/path.php';
    require __DIR__.'/assets/assets.php';
    require __DIR__.'/assets/render.php';
    require __DIR__.'/assets/request.php';
    require __DIR__.'/assets/router.php';
    require __DIR__.'/assets/presets.php';

  }
}