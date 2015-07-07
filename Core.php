<?php

ini_set('display_errors', 'on');
error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8');

class Core {
	public static $autoloadPaths;
	public static $basePath;
	public static $baseURL;

	public static function init() {
		Core::initPaths();
	}

	private static function initPaths() {
		static $SUBDIRS_TO_SCAN = 10;

		$dir = '.';
		$path = '/Core.php';
		for ($i = 0; $i < $SUBDIRS_TO_SCAN + 1; $i++) {
			if ($i == $SUBDIRS_TO_SCAN)
				die('');

			if (file_exists($dir.$path)) {
				Core::$basePath = $dir;
				require_once(Core::$basePath.'/Functions.php');

				$configJson = json_decode(file_get_contents(Core::$basePath.'/config.json'), true);
				Core::$baseURL = $configJson['baseURL'];

				if ($configJson['phperror']) {
					require_once(Core::$basePath.'/php_error.php');
	        \php_error\reportErrors();
				}

				Core::$autoloadPaths = array(Core::$basePath, Core::$basePath.'/model');
				spl_autoload_register(function ($class) {
					$class = str_replace('\\', '/', $class);
					foreach (Core::$autoloadPaths as $autoloadPath) {
						$path = $autoloadPath.'/'.$class.'.php';
						if (file_exists($path)) {
							require_once($path);
							if (method_exists($class, '__init'))
								call_user_func(array($class, '__init'));
							return;
						}
					}
				});

				break;
			} else {
				$dir .= '/..';
			}
		}
	}
}

Core::init();

?>
