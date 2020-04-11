<?php

$start = microtime(true);
include_once 'src/estimator.php';
//Require composer autoloader
require __DIR__ .'/vendor/autoload.php';

// Create Router instance
$router = new \Bramus\Router\Router();
$router->post('/api/v1/on-covid-19', function() {
  $json = file_get_contents('php://input');
  $json_response = json_encode(covid19ImpactEstimator(json_decode($json)));
  echo $json_response;
});

$router->get('/api/v1/on-covid-19/logs', function() {
  
    $content = file_get_contents("src/logs.txt");
    echo $content;
});

$router->post('/api/v1/on-covid-19/{returnType}', function($returnType) {
    $json = file_get_contents('php://input');
    if($returnType == 'xml'){
        $xml = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
        array_to_xml(covid19ImpactEstimator(json_decode($json)), $xml);
        echo $xml->asXML();
    }else if($returnType == 'json'){
        $json_response = json_encode(covid19ImpactEstimator(json_decode($json)));
        echo $json_response;
    }else{
        header("HTTP/1.0 404 Not Found");
        die();
    }
});



$router->run();

$myFile = "src/logs.txt";

    $fh = fopen($myFile, 'a') or die("can't open file");
    fwrite($fh, $_SERVER['REQUEST_TIME']. "\t\t". $_SERVER['REQUEST_URI'] . "\t\t done in " .(microtime(true) - $start) . " seconds");
    fwrite($fh, "\r\n");
    fclose($fh);


