<?php
require_once('lib/autoload.php');

use \Response\GeneratorResponse;
use \UploadHandler\UploadHandler;
use \Latex\Render;

function sendResponse(GeneratorResponse $resp) {
	echo $resp->get();
	exit();
}

$response = new GeneratorResponse();
try {
	if(!isset($_FILES['source'])) {
		$response->setMessage('No file uploaded!');
		sendResponse($response);
	}

	$uploadHandler = new UploadHandler($_FILES['source']);

	$fileDir = $uploadHandler->unpack();

	if(isset($_GET['entryFile'])) {
		$entryFile = str_replace('..', '', $_GET['entryFile']);
	} elseif(isset($_POST['entryFile'])) {
		$entryFile = str_replace('..', '', $_POST['entryFile']);
	} else {
		$entryFile = 'main.tex';
	}

	$latex = new Render($fileDir, $entryFile);

	$response->setFile($latex->getPDF());
	$response->setLog($latex->getLog());
	$response->setSuccess(true);
	sendResponse($response);
} catch(\Latex\LbuildException $e) {
	$response->setLog($e->getLog());
	$response->setMessage($e->getMessage());
	sendResponse($response);
} catch(\Exception $e) {
	$response->setMessage($e->getMessage());
	sendResponse($response);
}