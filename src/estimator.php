<?php
function covid19ImpactEstimator($data)
{
  $info = json_decode($data,true);
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
  $impact->severeCasesByRequestedTime = (15 / 100) * $impact->infectionsByRequestedTime;
  $severeImpact->severeCasesByRequestedTime = (15 / 100) * $severeImpact->infectionsByRequestedTime;

  $totalHospitalBeds = $info['totalHospitalBeds'];
  $availableBedSpace = (35 / 100) * $totalHospitalBeds;

  $impact->hospitalBedsByRequestedTime = $availableBedSpace - $impact->severeCasesByRequestedTime;
  $severeImpact->hospitalBedsByRequestedTime = $availableBedSpace - $severeImpact->severeCasesByRequestedTime;

  //Challenge 3
  $impact->casesForICUByRequestedTime = (5 / 100) * $impact->infectionsByRequestedTime;
  $severeImpact->casesForICUByRequestedTime = (5 / 100) * $severeImpact->infectionsByRequestedTime;
  
  $impact->casesForVentilatorsByRequestedTime = (2 / 100) * $impact->infectionsByRequestedTime;
  $severeImpact->casesForVentilatorsByRequestedTime = (2 / 100) * $severeImpact->infectionsByRequestedTime;
  
  $avgDailyIncomeInUSD = $info['region']['avgDailyIncomeInUSD'];
  $avgDailyIncomePopulation = $info['region']['avgDailyIncomePopulation'];

  $impact->dollarsInFlight = $impact->infectionsByRequestedTime * $avgDailyIncomePopulation * $avgDailyIncomeInUSD * $timeToElapse;
  $severeImpact->dollarsInFlight = $impact->infectionsByRequestedTime * $avgDailyIncomePopulation * $avgDailyIncomeInUSD * $timeToElapse;

  $response['data'] = $data;
	$response['impact'] = $impact;
	$response['severeImpact'] = $severeImpact;
  $json_response = json_encode($response);
  return $json_response;
}

function checkPeriodType($periodType,$timeToElapse,$impact,$severeImpact){
  if($periodType == 'days'){
    $impact->infectionsByRequestedTime = $impact->currentlyInfected * pow(2,($timeToElapse / 3));
    $severeImpact->infectionsByRequestedTime = $severeImpact->currentlyInfected * pow(2,($timeToElapse / 3));
  }

  if($periodType == 'weeks'){
    $timeToElapse = $timeToElapse * 7;
    $impact->infectionsByRequestedTime = $impact->currentlyInfected * pow(2,($timeToElapse / 3));
    $severeImpact->infectionsByRequestedTime = $severeImpact->currentlyInfected * pow(2,($timeToElapse / 3));
  }

  if($periodType == 'months'){
    $timeToElapse = $timeToElapse * 30;
    $impact->infectionsByRequestedTime = $impact->currentlyInfected * pow(2,($timeToElapse / 3));
    $severeImpact->infectionsByRequestedTime = $severeImpact->currentlyInfected * pow(2,($timeToElapse / 3));
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


