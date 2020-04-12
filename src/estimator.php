<?php
function covid19ImpactEstimator($data)
{
  $info = $data;
  $impactStructure = array('currentlyInfected' => 0,'infectionsByRequestedTime' => 0,'severeCasesByRequestedTime' => 0,'hospitalBedsByRequestedTime' => 0,'casesForICUByRequestedTime' => 0,'casesForVentilatorsByRequestedTime' => 0,'dollarsInFlight' => 0);
  $severeImpactStructure = array('currentlyInfected' => 0,'infectionsByRequestedTime' => 0,'severeCasesByRequestedTime' => 0,'hospitalBedsByRequestedTime' => 0,'casesForICUByRequestedTime' => 0,'casesForVentilatorsByRequestedTime' => 0,'dollarsInFlight' => 0);
  
  //Challenge 1
  $impact = (object)$impactStructure;
  $severeImpact = (object)$severeImpactStructure;
  $impact->currentlyInfected = $info['reportedCases'] * 10;
  $severeImpact->currentlyInfected = $info['reportedCases'] * 50;
  
  $timeToElapse = $info['timeToElapse'];
  $periodType = $info['periodType'];
  checkPeriodType($periodType,$timeToElapse,$impact,$severeImpact);

  //Challenge 2
  $impact->severeCasesByRequestedTime = floor((15 / 100) * $impact->infectionsByRequestedTime);
  $severeImpact->severeCasesByRequestedTime = floor((15 / 100) * $severeImpact->infectionsByRequestedTime);

  $totalHospitalBeds = $info['totalHospitalBeds'];
  $availableBedSpace = floor((35 / 100) *  $totalHospitalBeds);

  $impacthospitalBedsByRequestedTime = (int) ($availableBedSpace - $impact->severeCasesByRequestedTime);
  $severeImpacthospitalBedsByRequestedTime = (int) ($availableBedSpace - $severeImpact->severeCasesByRequestedTime);

  if($impacthospitalBedsByRequestedTime < 0 && $severeImpacthospitalBedsByRequestedTime < 0){
    $impact->hospitalBedsByRequestedTime = floor($availableBedSpace - $impact->severeCasesByRequestedTime + 1);
    $severeImpact->hospitalBedsByRequestedTime = floor($availableBedSpace - $severeImpact->severeCasesByRequestedTime + 1);
  }else{
    $impact->hospitalBedsByRequestedTime = floor($availableBedSpace - $impact->severeCasesByRequestedTime);
    $severeImpact->hospitalBedsByRequestedTime = floor($availableBedSpace - $severeImpact->severeCasesByRequestedTime);
  }
  
  //Challenge 3
  $impact->casesForICUByRequestedTime = (int) ((5 / 100) * $impact->infectionsByRequestedTime);
  $severeImpact->casesForICUByRequestedTime = (int) ((5 / 100) * $severeImpact->infectionsByRequestedTime);
  
  
  $impact->casesForVentilatorsByRequestedTime = floor((2 / 100) * $impact->infectionsByRequestedTime);
  $severeImpact->casesForVentilatorsByRequestedTime = floor((2 / 100) * $severeImpact->infectionsByRequestedTime);
  
  $avgDailyIncomeInUSD = $info['region']['avgDailyIncomeInUSD'];
  $avgDailyIncomePopulation = $info['region']['avgDailyIncomePopulation'];
  
  if($periodType == 'weeks'){
    $timeToElapse = $timeToElapse * 7;
  }

  if($periodType == 'months'){
    $timeToElapse = $timeToElapse * 30;
  }
  $impact->dollarsInFlight = floor(($impact->infectionsByRequestedTime * $avgDailyIncomePopulation * $avgDailyIncomeInUSD) / $timeToElapse);
  $severeImpact->dollarsInFlight = floor(($severeImpact->infectionsByRequestedTime * $avgDailyIncomePopulation * $avgDailyIncomeInUSD ) / $timeToElapse);

  $response['data'] = $data;
	$response['impact'] = cvf_convert_object_to_array($impact);
	$response['severeImpact'] = cvf_convert_object_to_array($severeImpact);
  //$json_response = json_encode($response);
  return $response;
}

function checkPeriodType($periodType,$timeToElapse,$impact,$severeImpact){
  if($periodType == 'days'){
    $impact->infectionsByRequestedTime = floor($impact->currentlyInfected * pow(2,floor($timeToElapse / 3)));
    $severeImpact->infectionsByRequestedTime = floor($severeImpact->currentlyInfected * pow(2,floor($timeToElapse / 3)));
  }

  if($periodType == 'weeks'){
    $timeToElapse = $timeToElapse * 7;
    $impact->infectionsByRequestedTime = floor($impact->currentlyInfected * pow(2,floor($timeToElapse / 3)));
    $severeImpact->infectionsByRequestedTime = floor($severeImpact->currentlyInfected * pow(2,floor($timeToElapse / 3)));
  }

  if($periodType == 'months'){
    $timeToElapse = $timeToElapse * 30;
    $impact->infectionsByRequestedTime = floor($impact->currentlyInfected * pow(2,floor($timeToElapse / 3)));
    $severeImpact->infectionsByRequestedTime = floor($severeImpact->currentlyInfected * pow(2,floor($timeToElapse / 3)));
  }

}

function array_to_xml( $data, &$xml_data ) {
  foreach( $data as $key => $value ) {
      if( is_array($value) ) {
          if( is_numeric($key) ){
              $key = 'item'.$key; //dealing with <0/>..<n/> issues
          }
          $subnode = $xml_data->addChild($key);
          array_to_xml($value, $subnode);
      } else {
          $xml_data->addChild("$key",htmlspecialchars("$value"));
      }
   }
}

function cvf_convert_object_to_array($data) {

  if (is_object($data)) {
      $data = get_object_vars($data);
  }

  if (is_array($data)) {
      return array_map(__FUNCTION__, $data);
  }
  else {
      return $data;
  }
}

function writeToLog(){
        list($usec, $sec) = explode(" ", microtime());
        $fh = fopen(myFile, 'a') or die("can't open file");
        fwrite($fh, $_SERVER['REQUEST_METHOD']. "\t\t". $_SERVER['REQUEST_URI'] . "\t\t" . http_response_code() ."\t\t".floor((((float)$usec + (float)$sec) - LUMEN_START) * 1000) . "ms");
        fwrite($fh, "\n");
        fclose($fh);
}