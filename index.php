<?php
define('LUMEN_START', microtime(true));
define('myFile', "src/logs.txt");
include_once 'src/estimator.php';

//Require composer autoloader
require __DIR__ .'/vendor/autoload.php';

// Create Router instance
$router = new \Bramus\Router\Router();
$router->post('/api/v1/on-covid-19', function() {
  $json = file_get_contents('php://input');
  $json_response = json_encode(covid19ImpactEstimator(json_decode($json,true)));
  writeToLog();
  echo $json_response;
});

$router->get('/api/v1/on-covid-19/logs', function() {
    header('Content-Type: text/plain');
    writeToLog();
    $content = file_get_contents("src/logs.txt");
    echo strval($content);
});

$router->post('/api/v1/on-covid-19/{returnType}', function($returnType) {
    $json = file_get_contents('php://input');
    if($returnType == 'xml'){
        $xml = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
        array_to_xml(covid19ImpactEstimator(json_decode($json,true)), $xml);
        $fh = fopen(myFile, 'a') or die("can't open file");
        header('Content-Type: application/xml');
        writeToLog();
        echo $xml->asXML();
    }else if($returnType == 'json'){
        header('Content-Type: application/json');
        $json_response = json_encode(covid19ImpactEstimator(json_decode($json,true)));
        writeToLog();
        echo $json_response;
    }else{
        header("HTTP/1.0 404 Not Found");
        writeToLog();
        die();
    }
});

$router->run();