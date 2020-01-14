<?php

require dirname(__DIR__, 1).'/vendors/Mustache/Autoloader.php';

Mustache_Autoloader::register();

class Flush_Mustache_FilesystemLoader extends Mustache_Loader_FilesystemLoader {
	protected function loadFile($name) {
		$fileName = $this->getFileName($name);
		if ($this->shouldCheckPath() && !file_exists($fileName)) {
			throw new Mustache_Exception_UnknownTemplateException($name);
		}
		
		ob_start();
		include $fileName;
		return ob_get_clean();
	}
}

$partials = BOKKEN_PATH.'/views/partials';
$partials = file_exists($partials) ? new Flush_Mustache_FilesystemLoader($partials, array( 'extension' => '.php' )) : null;
$options = array(
  'cache'           => BOKKEN_PATH.'/views/cache',
  'escape'          => function($value) { return $value; },
  'partials_loader' => $partials
);
$mustache = new Mustache_Engine($options);

return function ($template, $data) use ($mustache) {
  return $mustache->render($template, $data);
};