<?php
$myFile = "src/logs.txt";

function microtime_float()
{
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
}

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
  $fh = fopen(myFile, 'a') or die("can't open file");
  fwrite($fh, $_SERVER['REQUEST_METHOD']. "\t\t". $_SERVER['REQUEST_URI'] . "\t\t" . http_response_code() ."\t\t 0".floor((microtime_float() - LUMEN_START) * 1000) . "ms");
  fwrite($fh, "\n");
  fclose($fh);
  echo $json_response;
});

$router->get('/api/v1/on-covid-19/logs', function() {
    header('Content-Type: text/plain');
    $fh = fopen(myFile, 'a') or die("can't open file");
    fwrite($fh, $_SERVER['REQUEST_METHOD']. "\t\t". $_SERVER['REQUEST_URI'] . "\t\t" . http_response_code() ."\t\t 0".floor((microtime_float() - LUMEN_START) * 1000) . "ms");
    fwrite($fh, "\n");
    fclose($fh);
    $content = file_get_contents("src/logs.txt");
    echo strval($content);
});

$router->post('/api/v1/on-covid-19/logs', function() {
    header('Content-Type: text/plain');
    $fh = fopen(myFile, 'a') or die("can't open file");
    fwrite($fh, $_SERVER['REQUEST_METHOD']. "\t\t". $_SERVER['REQUEST_URI'] . "\t\t" . http_response_code() ."\t\t 0".floor((microtime_float() - LUMEN_START) * 1000) . "ms");
    fwrite($fh, "\n");
    fclose($fh);
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
        fwrite($fh, $_SERVER['REQUEST_METHOD']. "\t\t". $_SERVER['REQUEST_URI'] . "\t\t" . http_response_code() ."\t\t 0".floor((microtime_float() - LUMEN_START) * 1000) . "ms");
        fwrite($fh, "\n");
        fclose($fh);
        echo $xml->asXML();
    }else if($returnType == 'json'){
        header('Content-Type: application/json');
        $json_response = json_encode(covid19ImpactEstimator(json_decode($json,true)));
        $fh = fopen(myFile, 'a') or die("can't open file");
        fwrite($fh, $_SERVER['REQUEST_METHOD']. "\t\t". $_SERVER['REQUEST_URI'] . "\t\t" . http_response_code() ."\t\t 0".floor((microtime_float() - LUMEN_START) * 1000) . "ms");
        fwrite($fh, "\n");
        fclose($fh);
        echo $json_response;
    }else{
        header("HTTP/1.0 404 Not Found");
        die();
    }
});



$router->run();
    //$time = $time_end - LUMEN_START;
  


