<?php

use C3VOC\StreamingWebsite\Lib\Router;
use C3VOC\StreamingWebsite\Lib\NotFoundException;

use C3VOC\StreamingWebsite\Command;
use C3VOC\StreamingWebsite\View;

use C3VOC\StreamingWebsite;

require_once('bootstrap.php');

ob_start();
if(isset($argv) && isset($argv[1]))
{
	$cmd = null;
	switch($argv[1])
	{
		case 'download':
			$cmd = new Command\Download;
	}

	if(is_null($cmd))
	{
		fprintf(STDERR, "Unknown Command: %s", $argv[1]);
		exit(1);
	}
	else {
		exit( $cmd->run($argv) );
	}
}


try {
	if(isset($_GET['htaccess']))
	{
		$route = @$_GET['route'];
	}
	elseif(isset($_SERVER["REQUEST_URI"]))
	{
		$route = ltrim(@$_SERVER["REQUEST_URI"], '/');

		// serve static
		if($route != '' && file_exists($_SERVER["DOCUMENT_ROOT"].'/'.$route))
		{
			return false;
		}

	}
	else $route = '';


	try {
		$router = new Router($route);
		$view = $router->createView();
		$view->output();
	}
	catch(NotFoundException $e) {
		$view = new View\NotFoundView($router, $e);
		$view->output();
	}
	catch(Exception $e) {
		$view = new View\ErrorView($router, $e);
		$view->output();
	}

}
catch(Exception $e)
{
	ob_clean();
	header('Content-Type: text/plain');
	print_r($e);
}
