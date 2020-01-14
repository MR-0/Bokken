<?php

class Assets {

  private static $compilers = array();
  private static $types = array(
    'css'  => 'style',
    'less' => 'style',
    'sass' => 'style',
    'js'   => 'script'
  );

	private static function compile ($ext, $type, $source, $output) {
    $compiler = isset(self::$compilers[$ext]) ? self::$compilers[$ext] : null;
    $compiler = $compiler && is_callable($compiler) ? $compiler($source, $output) : null;

    return (boolean) $compiler;
  }
  
  public static function add ($source, $out = null) {
    $arr    = explode('.', $source);
    $ext    = array_pop($arr);
    $type   = isset(self::$types[$ext]) ? self::$types[$ext] : 'other';
    $types  = $type.'s';
    
    $outext = array(
      'style'  => 'css',
      'script' => 'js'
    );
    
    $out = $out ? $out : implode('.', $arr).'.'.$outext[$type];
    
    $source = BOKKEN_PATH.'/'.$source;
    $output = BOKKEN_PATH.'/'.'public/'.$out;

    $compile = self::compile($ext, $type, $source, $output);

		return array( $types => array(
      'file' => $out,
      'time' => filemtime($output)
    ) );
	}

	public static function set ($type, $path) {
    $compiler = file_exists($path) ? include $path : null;
    
    if ($compiler) self::$compilers[$type] = $compiler;

		return null;
	}

}