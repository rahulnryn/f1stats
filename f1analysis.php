<?php
    error_reporting(0);
    
    


    function contains($haystack, $needle)
    {
        return strpos($haystack, $needle) !== false;
    }
    function calculate_mean($array){
        $mean = number_format((double)array_sum($array) / count($array), 3);
        return $mean;
    }
    
    function converToSeconds($timestr){
        $minutes = ((double)$timestr[0]) * 60;
        $str1 = $timestr[2] . $timestr[3];
        $seconds = (double)($str1);
        $str2 = $timestr[5] . $timestr[6] . $timestr[7];
        $milliseconds = (double)$str2/1000.0;
        return $minutes + $seconds + $milliseconds;
    }
    function converToSeconds2($timestr){
        $minutes = ((double)$timestr[1]) * 60;
        $str1 = $timestr[3] . $timestr[4];
        $seconds = (double)($str1);
        $str2 = $timestr[6] . $timestr[7] . $timestr[8];
        $milliseconds = (double)$str2/1000.0;
        return $minutes + $seconds + $milliseconds;
    }
    function convertToMinutes($timestr){
        $input = (double)($timestr*1000.0);

        $uSec = $input % 1000;
        $input = floor($input / 1000);

        $seconds = $input % 60;
        $input = floor($input / 60);

        $minutes = $input % 60;
        $input = floor($input / 60);

        $hour = $input;

        return sprintf('%2d:%02d.%03d', $minutes, $seconds, $uSec);
      
    }
    function computeDiff($time1, $time2){
        $x = 0;
        $y = -1;
        if($time1 > $time2){
            $y = 1;
            $x = (($time1 / $time2) * 100.0);
        }
        else{
            $y = -1;
            $x = (($time2 / $time1) * 100.0);
        }
        return $y * ($x - 100.0);
    }
    
    function calculate_median($arr) {
        sort($arr);
        if (($key = array_search(null, $array)) !== false) {
            array_splice($array, $key, 1);
        }
        $count = count($arr);  
        $middleval = floor(($count-1)/2); 
        if($count % 2 == 1) { 
            $median = $arr[$middleval];
        } else { 
            $low = $arr[$middleval];
            $high = $arr[$middleval+1];
            $median = (($low+$high)/2);
        }
        return $median;
    }
    if(!empty($_POST['years'])){
        $getYears = $_POST['years'];

    }
    else{
        $getYears = "2020";
    }
    if(!empty($_POST['teams'])){

            $getTeams = $_POST['teams'];
    }
    else{
        $getTeams = "mercedes";
    }
    if(!empty($_POST['racecheck'])){

        $check = $_POST['racecheck'];
    }
   $json = file_get_contents("https://ergast.com/api/f1/" . $getYears . "/constructors" . "/" . $getTeams . "/results.json?limit=100");
   $obj = json_decode($json);
   $drivername1 = ($obj->MRData->RaceTable->Races[0]->Results[0]->Driver->familyName);
   $drivername2 = ($obj->MRData->RaceTable->Races[0]->Results[1]->Driver->familyName);

   $countOcc=array();
   $rc = file_get_contents("https://ergast.com/api/f1/" . $getYears . "/races.json");
   $racecount = json_decode($rc);
   $countRaces = $racecount->MRData->total;
   $countOcc2 = array();
   for($x = 0; $x < $countRaces; $x++){
        array_push($countOcc, $obj->MRData->RaceTable->Races[$x]->Results[0]->Driver->familyName);
        array_push($countOcc, $obj->MRData->RaceTable->Races[$x]->Results[1]->Driver->familyName);
        array_push($countOcc2, $obj->MRData->RaceTable->Races[$x]->Results[0]->Driver->driverId);
        array_push($countOcc2, $obj->MRData->RaceTable->Races[$x]->Results[1]->Driver->driverId);
   }
   $values = array_count_values($countOcc);
   $values2 = array_count_values($countOcc2);
   arsort($values2);
   arsort($values);
   $driversComp2 = array_slice(array_keys($values2), 0, 2, true);

   $driversComp = array_slice(array_keys($values), 0, 2, true);
   $drivername1 = $driversComp[0];
   $drivername2 = $driversComp[1];
   $dId1 = $driversComp2[0];
   $dId2 = $driversComp2[1];
   $countWins=array($drivername1=>0,$drivername2=>0);
   $countPoints=array($drivername1=>0,$drivername2=>0);
   $countTotalPoints=array($drivername1=>0,$drivername2=>0);



    $rounds = array();
    for($i = 0; $i < ($countRaces); $i++){
        array_push($rounds, $i+1);
    }
    $getRacePositions1 = array();
    $getRacePositions2 = array();
    $racesFinished = Array($drivername1 => 0, $drivername2=>0);
    for($x = 0; $x < $countRaces; $x++){
  
        $driver1points = $obj->MRData->RaceTable->Races[$x]->Results[0]->points;
        $driver2points = $obj->MRData->RaceTable->Races[$x]->Results[1]->points;
        $driver1pos = ($obj->MRData->RaceTable->Races[$x]->Results[0]->positionText);
        $driver2pos = ($obj->MRData->RaceTable->Races[$x]->Results[1]->positionText);
        $cdn1 = ($obj->MRData->RaceTable->Races[$x]->Results[0]->Driver->familyName);
        $cdn2 = ($obj->MRData->RaceTable->Races[$x]->Results[1]->Driver->familyName);
        $driver1status = $obj->MRData->RaceTable->Races[$x]->Results[0]->status;
        $driver2status = $obj->MRData->RaceTable->Races[$x]->Results[1]->status;
        $racesFinished[$cdn2]++;
        $racesFinished[$cdn1]++;
        $countTotalPoints[$cdn1] += $driver1points;
        $countTotalPoints[$cdn2] += $driver2points;
        if(($driver1pos == "R" || $driver1pos == "W" || $driver1pos == "D") ){
            if(!contains($driver1status, "Collision") && !contains($driver1status, "Accident") && !contains($driver1status, "Disqualified")
            && !contains($driver1status, "Spun")){
                $racesFinished[$cdn1]--;
                $driver1pos = "RET (Non-driver/Mechanical)";
            }
            else{
                $driver1pos = "RET (Driver/Crash)";
            }
        }
        if(($driver2pos == "R" || $driver2pos == "W" || $driver2pos == "D")){
            if(!contains($driver2status, "Collision") && !contains($driver2status, "Accident") && !contains($driver2status, "Disqualified")
            && !contains($driver2status, "Spun")){
                $racesFinished[$cdn2]--;
                $driver2pos = "RET (Non-driver/mechanical)";
            }
            else{
                $driver2pos = "RET (Driver/Crash)";
            }
        }
        if($cdn1 == $drivername1){
            array_push($getRacePositions1, $driver1pos);
        }
        else if($cdn1 == $drivername2){
            array_push($getRacePositions2, $driver1pos);
        }
        $stat = "DNS";
        if($drivername1 != $cdn2 && $drivername1 != $cdn1){
          array_push($getRacePositions1, $stat);
  
        }
        if($cdn2 == $drivername1){
            array_push($getRacePositions1, $driver2pos);
        }
        else if($cdn2 == $drivername2){
            array_push($getRacePositions2, $driver2pos);
        }
        if($drivername2 != $cdn2 && $drivername2 != $cdn1){
  
          array_push($getRacePositions2, $stat);
  
        }
        if( ($cdn1 == $drivername1 || $cdn1 == $drivername2) && ($cdn2 == $drivername1 || $cdn2 == $drivername2)){
            $countPoints[$cdn1] += $driver1points;
            $countPoints[$cdn2] += $driver2points;
        }
        else{

            continue;
        }
      
      if(contains($driver1pos, "RET")){
        if((contains($driver1status, "Collision") || contains($driver1status, "Accident") || contains($driver1status, "Disqualified")
            || contains($driver1status, "Spun") )
            and ($driver2pos >= "1" && $driver2pos <= "25")){
                $countWins[$cdn2]++;
        }
        continue;
            
      }
        if(contains($driver2pos, "RET")){
            if((contains($driver2status, "Collision") || contains($driver2status, "Accident")
            || contains($driver2status, "Disqualified") || contains($driver2status, "Spun"))
            and ($driver1pos >= "1" && $driver1pos <= "25")){
                $countWins[$cdn1]++;
            }
            
            continue;
        }
        if( $driver1pos < $driver2pos)
            $countWins[$cdn1]++;
        else if($driver2pos < $driver1pos)
            $countWins[$cdn2]++;
     
            
   }
   $driverNames = array_keys($countWins);
   $driverWins = array_values($countWins);
   sleep(1);

    $jsonquali = file_get_contents("http://ergast.com/api/f1/" .$getYears . "/constructors" ."/" . $getTeams . "/qualifying.json?limit=100");
    $qualifying = json_decode($jsonquali);
    $countQualiWins=array($drivername1=>0,$drivername2=>0);

    $timeDelta = array();
    $timeDelta2 = array();
    $allQual1 = array();
    $allQual2 = array();
    if($getYears >= '2003'){
            for($x = 0; $x < $countRaces; $x++){
                    $q1pos =$qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[0]->position;
                    $q2pos =$qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[1]->position;
                    $cdq1 = $qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[0]->Driver->familyName;
                    $cdq2 = $qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[1]->Driver->familyName;
                    if( (converToSeconds($qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[0]->Q1) == NULL)
                        or (converToSeconds($qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[1]->Q1) == NULL)){
                        array_push($allQual1, "Not Available");
                        array_push($allQual2, "Not Available");
                        continue;
                    }
                    $q1d1 = converToSeconds($qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[0]->Q1);
                    $q2d1 = converToSeconds($qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[0]->Q2);
                    $q3d1 = converToSeconds($qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[0]->Q3);
                    $q1d2 = converToSeconds($qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[1]->Q1);
                    $q2d2 = converToSeconds($qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[1]->Q2);
                    $q3d2 = converToSeconds($qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[1]->Q3);
                    
                    if($q2d1 == NULL || $q2d2 == NULL){
                        $d1Further = $q1d1;
                        $d2Further = $q1d2;
                        $d1further1 =$q1d1;
                        $d2further1 =$q1d2;

                    }
                    else if($q3d2 == NULL || $q3d1 == NULL){
                        $d1Further = min($q2d1, $q1d1);
                        $d2Further = min($q2d2, $q1d2);
                        $d1further1 =$q2d1;
                        $d2further1 =$q2d2;

                    }
                    else if($q3d2 != NULL and $q3d1 != NULL){
                        $d1Further = min($q2d1, min($q1d1, $q3d1));
                        $d2Further = min($q2d2, min($q1d2, $q3d2));
                        $d1further1 =$q3d1;
                        $d2further1 =$q3d2;

                    }
                    
                    $driver1time = $d1Further1;
                    $driver2time = $d2Further1;
                
                    
                    if($q1pos < $q2pos  and ($cdq2 == $drivername2 || $cdq2 == $drivername1)){
                        $countQualiWins[$cdq1]++;
                    }
                    else if($q2pos < $q1pos  and ($cdq1 == $drivername2 || $cdq1 == $drivername1)){
                        $countQualiWins[$cdq2]++;
                    }   
                    if($cdq1 == $drivername1 and $cdq2 == $drivername2){
                        $diff = number_format((double)computeDiff($driver1time, $driver2time), 3);
                        if($diff > -2 && $diff < 2){
                            array_push($timeDelta, $diff);
                        }
                    }             
                    else if($cdq2 == $drivername1 and $cdq1 == $drivername2){
                        $diff = number_format((double)computeDiff($driver2time, $driver1time), 3);
                        if($diff > -2 && $diff < 2){
                            array_push($timeDelta, $diff);
                        }
                    }
                    $driver1time = $d1further1;
                    $driver2time = $d2further1;

                    if($cdq1 == $drivername1 and $cdq2 == $drivername2){

                        array_push($allQual1, $driver1time);
                        array_push($allQual2, $driver2time);
                        $diff = number_format((double)computeDiff($driver1time, $driver2time), 3);
                        //if($diff > -2 && $diff < 2){
                            array_push($timeDelta2, $diff);
                        //}
                    }             
                    else if($cdq2 == $drivername1 and $cdq1 == $drivername2){
                        array_push($allQual2, $driver1time);
                        array_push($allQual1, $driver2time);

                        $diff = number_format((double)computeDiff($driver2time, $driver1time), 3);
                    //if($diff > -2 && $diff < 2){
                            array_push($timeDelta2, $diff);
                        //}
                    }
                    else{
                        array_push($allQual1, "Not Available");
                        array_push($allQual2, "Not Available");
                    }

                
            }
    
            $qualinames = array_keys($countQualiWins);
            $qualiscores = array_values($countQualiWins);
        }
        else{
            
            
            for($x = 0; $x < $countRaces; $x++){
                $q1pos =$obj->MRData->RaceTable->Races[$x]->Results[0]->grid;
                $q2pos =$obj->MRData->RaceTable->Races[$x]->Results[1]->grid;
                $cdq1 = $obj->MRData->RaceTable->Races[$x]->Results[0]->Driver->familyName;
                $cdq2 = $obj->MRData->RaceTable->Races[$x]->Results[1]->Driver->familyName;
                if($q1pos < $q2pos  and ($cdq2 == $drivername2 || $cdq2 == $drivername1)){
                    $countQualiWins[$cdq1]++;
                }
                else if($q2pos < $q1pos  and ($cdq1 == $drivername2 || $cdq1 == $drivername1)){
                    $countQualiWins[$cdq2]++;
                }   
            }
            $qualinames = array_keys($countQualiWins);
            $qualiscores = array_values($countQualiWins);
        }
    
    
    $ppr1 = number_format((float)($countTotalPoints[$drivername1] / $racesFinished[$drivername1]), 2);
    $ppr2 = number_format((float)($countTotalPoints[$drivername2] / $racesFinished[$drivername2]), 2);
    $allRaces = array();
    $allRaces50 = array();
    $allRaces75 = array();
    $allRaces95 = array();
    $allRaces100 = array();


    $allRaces_ = array();
    $allRaces50_ = array();
    $allRaces75_ = array();
    $allRaces95_ = array();
    $allRaces100_ = array();


    $starter = 1;
    if($check == false || $getYears < '1996')
        $ender = 0;
    else{
        $ender = 15;
    }
    if( ($dId1 == 'sainz' || $dId2 == 'sainz') && $getYears == '2017' && $check){
        $ender = 15;
    }
    if( ($dId1 == 'trulli' || $dId2 == 'trulli') && $getYears == '2004' && $check){
        $ender = 15;
    }
    if( ($dId1 == 'kvyat' || $dId2 == 'kvyat') && $getYears == '2016' && $check){
        $starter = 5;
    }
    if( ($dId1 == 'max_verstappen' || $dId2 == 'max_verstappen') && $getYears == '2016' && $check){
        $starter = 5;
    }
    if( ($dId1 == 'gasly' || $dId2 == 'gasly') && $getYears == '2019' && $check){
        $ender = 12;
    }
    if( ($dId1 == 'albon' || $dId2 == 'albon') && $getYears == '2019' && $check){
        $ender = 12;
    }
    if( ($dId1 == 'fisichella' || $dId2 == 'fisichella') && $getYears == '2009' && $check){
        $ender = 12;
    }
    sleep(0.3);
    $allTimes = array();
    $allTimes2 = array();
    $firstVals = array();

    if(!file_exists($getYears . $getTeams . '.txt') and $check == true){
        for($t = 1; $t <= $ender; $t++){
            sleep(0.5);
            $rjson = file_get_contents('https://ergast.com/api/f1/' . $getYears . '/' . $t . '/' . 'drivers/' . $dId1 . '/laps.json?limit=100');
            $rjson2 = file_get_contents('https://ergast.com/api/f1/' . $getYears . '/' . $t . '/' . 'drivers/' . $dId2 . '/laps.json?limit=100');

            $obj = json_decode($rjson);
            $obj2 = json_decode($rjson2);
            $laps = $obj->MRData->total;
            $laps2 = $obj2->MRData->total;
            $laps--;
            $laps2--;
            if($laps <= 1 || $laps2 <= 1){
                $laps = 100;
            }
            $first = array();
            $second = array();
            $alap1 = array();
            $alap2 = array();

            $xAxis = array();


         
            for($i = 2; $i < min($laps, $laps2); $i++){
                if($obj->MRData->RaceTable->Races[0]->Laps[$i]->Timings[0]->time == 0){
                    break;
                }
                if($obj2->MRData->RaceTable->Races[0]->Laps[$i]->Timings[0]->time == 0){
                    break;
                }
                $dt1 = converToSeconds($obj->MRData->RaceTable->Races[0]->Laps[$i]->Timings[0]->time);
                $dt2 = converToSeconds($obj2->MRData->RaceTable->Races[0]->Laps[$i]->Timings[0]->time);
                array_push($first, $dt1);
                array_push($second, $dt2);

            }

            if( abs($laps-$laps2) <= 1.0 and $t >= $starter){
              
                

                sort($first);
                sort($second);
                
               


                $length251 = (int)(count($first) * 0.75);
                $length252 = (int)(count($second) * 0.75);
                $length501 = (int)(count($first) * 0.80);
                $length502 = (int)(count($second) * 0.80);


                $length751 = (int)(count($first) * 0.85);
                $length752 = (int)(count($second) * 0.85);

                $length951 = (int)(count($first) * 0.90);
                $length952 = (int)(count($second) * 0.90);

                $length1001 = (int)(count($first) * 0.95);
                $length1002 = (int)(count($first) * 0.95);

                $length1051 = (int)(count($first) * 1.00);
                $length1052 = (int)(count($first) * 1.00);


                $diff = array();
                $meantime251=0;
                $meantime501=0;
                $meantime751=0;
                $meantime951=0;

                $meantime252=0;
                $meantime502=0;
                $meantime752=0;
                $meantime952=0;


                $meantime1001=0;
                $meantime1002=0;

                $meantime1051=0;
                $meantime1052=0;

                $mmean = 0.0;
                $mmean2 = 0.0;
                $target = 0.80;
                for($i = 0; $i < $length1002; $i++){
                    $meantime1002 += $second[$i];
                }
                for($i = 0; $i < $length1052; $i++){
                    $meantime1052 += $second[$i];
                }
                for($i = 0; $i < $length252; $i++){
                    //echo("\n");
                    //echo($first[$i] . "\n");
                    //echo($second[$i] . "\n");
                    $meantime252 += $second[$i];
                    //echo($delta);
                }
                for($i = 0; $i < $length502; $i++){
                    //echo("\n");
                    //echo($first[$i] . "\n");
                    //echo($second[$i] . "\n");
                    $meantime502 += $second[$i];
                    //echo($delta);
                }
                for($i = 0; $i < $length752; $i++){
                    //echo("\n");
                    //echo($first[$i] . "\n");
                    //echo($second[$i] . "\n");
                    $meantime752 += $second[$i];
                    //echo($delta);
                }
                for($i = 0; $i < $length952; $i++){
                    //echo("\n");
                    //echo($first[$i] . "\n");
                    //echo($second[$i] . "\n");
                    $meantime952 += $second[$i];
                    //echo($delta);
                }
                for($i = 0; $i < $length251; $i++){
                    //echo("\n");
                    //echo($first[$i] . "\n");
                    //echo($second[$i] . "\n");
                    $meantime251 += $first[$i];
                    //echo($delta);
                }
                for($i = 0; $i < $length501; $i++){
                    //echo("\n");
                    //echo($first[$i] . "\n");
                    //echo($second[$i] . "\n");
                    $meantime501 += $first[$i];
                    //echo($delta);
                }
                for($i = 0; $i < $length751; $i++){
                    //echo("\n");
                    //echo($first[$i] . "\n");
                    //echo($second[$i] . "\n");
                    $meantime751 += $first[$i];
                    //echo($delta);
                }
                for($i = 0; $i < $length951; $i++){
                    //echo("\n");
                    //echo($first[$i] . "\n");
                    //echo($second[$i] . "\n");
                    $meantime951 += $first[$i];
                    //echo($delta);
                }
                for($i = 0; $i < $length1001; $i++){
                    $meantime1001 += $first[$i];
                }
                for($i = 0; $i < $length1051; $i++){
                    $meantime1051 += $first[$i];
                }
                
                array_push($allRaces, number_format(computeDiff($meantime251/$length251, $meantime252/$length252), 3));

                $meantime502 = (number_format($meantime502 / ($length502), 3));
                $meantime501 = (number_format($meantime501 / ($length501), 3));
                $meantime751 = (number_format($meantime751 / ($length751), 3));
                $meantime752 = (number_format($meantime752 / ($length752), 3));
                $meantime951 = (number_format($meantime951 / ($length951), 3));
                $meantime952 = (number_format($meantime952 / ($length952), 3));
                $meantime1001 = (number_format($meantime1001 / ($length1001), 3));
                $meantime1002 = (number_format($meantime1002 / ($length1002), 3));


                //echo(($meantime1) . "\n");
                //echo("\n");
                //echo(calculate_median($diff) . "\n");
                array_push($allTimes, $meantime951);
                array_push($allTimes2, $meantime952);

                array_push($allRaces50, number_format(computeDiff($meantime501, $meantime502), 3));
                array_push($allRaces75, number_format(computeDiff($meantime751, $meantime752), 3));
                array_push($allRaces95, number_format(computeDiff($meantime951, $meantime952), 3));
                array_push($allRaces100, number_format(computeDiff($meantime1001, $meantime1002), 3));



                array_push($allRaces_, number_format(computeDiff($meantime251/$length251, $meantime252/$length252), 3));
                array_push($allRaces50_, number_format(computeDiff($meantime501, $meantime502), 3));
                array_push($allRaces75_, number_format(computeDiff($meantime751, $meantime752), 3));
                array_push($allRaces95_, number_format(computeDiff($meantime951, $meantime952), 3));
                array_push($allRaces100_, number_format(computeDiff($meantime1001, $meantime1002), 3));
             

            }
            else{
               array_push($allRaces, "NAN");
               array_push($allTimes, "NAN");
               array_push($allTimes2, "NAN");

               array_push($allRaces50, "NAN");
               array_push($allRaces75, "NAN");
               array_push($allRaces95, "NAN");
               array_push($allRaces100, "NAN");


            }
        }
        array_push($firstVals, number_format(calculate_median($allRaces100_), 3));
        array_push($firstVals, number_format(calculate_median($allRaces95_), 3));
        array_push($firstVals, number_format(calculate_median($allRaces75_), 3));
        array_push($firstVals, number_format(calculate_median($allRaces50_), 3));
        array_push($firstVals, number_format(calculate_median($allRaces_), 3));

        $medGap = (number_format(calculate_median($allRaces_), 3));
        $medGap2 = (number_format(calculate_median($allRaces50_), 3));
        $medGap3 = (number_format(calculate_median($allRaces75_), 3));
        $medGap4 = (number_format(calculate_median($allRaces95_), 3));

        $minRange = $medGap4;
        $maxRange = $medGap4;
        $current = "";
        $current2 = "";
        $current3 = "";
        $current4 = "";
        $current5 = "";
        for($i = 0; $i < count($allRaces); $i++){
            $current .= ($allRaces[$i]);
            if($i != count($allRaces) - 1){
                $current .= ',';
            }
        }
        for($i = 0; $i < count($allRaces50); $i++){
            $current2 .= ($allRaces50[$i]);
            if($i != count($allRaces50) - 1){
                $current2 .= ',';
            }
        }
        for($i = 0; $i < count($allRaces75); $i++){
            $current3 .= ($allRaces75[$i]);
            if($i != count($allRaces75) - 1){
                $current3 .= ',';
            }
        }
        for($i = 0; $i < count($allRaces95); $i++){
            $current4 .= ($allRaces95[$i]);
            if($i != count($allRaces95) - 1){
                $current4 .= ',';
            }
        }
        for($i = 0; $i < count($allRaces_); $i++){
            $current5 .= ($allRaces_[$i]);
            if($i != count($allRaces_) - 1){
                $current5 .= ',';
            }
        }
        for($i = 0; $i < count($allRaces50_); $i++){
            $current6 .= ($allRaces50_[$i]);
            if($i != count($allRaces50_) - 1){
                $current6 .= ',';
            }
        }
        for($i = 0; $i < count($allRaces75_); $i++){
            $current7 .= ($allRaces75_[$i]);
            if($i != count($allRaces75_) - 1){
                $current7 .= ',';
            }
        }
        for($i = 0; $i < count($allRaces95_); $i++){
            $current8 .= ($allRaces95_[$i]);
            if($i != count($allRaces95_) - 1){
                $current8 .= ',';
            }
        }
        
        for($i = 0; $i < count($firstVals); $i++){
            $current9 .= ($firstVals[$i]);
            if($i != count($firstVals) - 1){
                $current9 .= ',';
            }
        }
        for($i = 0; $i < count($xAxis); $i++){
            $current10 .= ($xAxis[$i]);
            if($i != count($xAxis) - 1){
                $current10 .= ',';
            }
        }
        for($i = 0; $i < count($allTimes); $i++){
            $current11 .= ($allTimes[$i]);
            if($i != count($allTimes) - 1){
                $current11 .= ',';
            }
        }
        for($i = 0; $i < count($allTimes2); $i++){
            $current12 .= ($allTimes2[$i]);
            if($i != count($allTimes2) - 1){
                $current12 .= ',';
            }
        }
       for($i = 0; $i < count($allRaces100_); $i++){
            $current13 .= ($allRaces100_[$i]);
            if($i != count($allRaces100_) - 1){
                $current13 .= ',';
            }
        }
        for($i = 0; $i < count($allRaces100); $i++){
            $current14 .= ($allRaces100[$i]);
            if($i != count($allRaces100) - 1){
                $current14 .= ',';
            }
        }

            $filename = $getYears . $getTeams . '.txt'; 
            $fp = fopen($filename,"w");  
            fwrite($fp,$current); 
            fclose($fp);  
        
            $filename = $getYears . $getTeams . '1' . '.txt'; 
            $fp = fopen($filename,"w");  
            fwrite($fp,$current2); 
            fclose($fp);  
        
            $filename = $getYears . $getTeams . '2' . '.txt'; 
            $fp = fopen($filename,"w");  
            fwrite($fp,$current3); 
            fclose($fp);  
        
            $filename = $getYears . $getTeams . '3' . '.txt'; 
            $fp = fopen($filename,"w");  
            fwrite($fp,$current4); 
            fclose($fp);  
        
            $filename = $getYears . $getTeams . '4' . '.txt'; 
            $fp = fopen($filename,"w");  
            fwrite($fp,$current5); 
            fclose($fp);  
        
            $filename = $getYears . $getTeams . '5' . '.txt'; 
            $fp = fopen($filename,"w");  
            fwrite($fp,$current6); 
            fclose($fp);  
        
            $filename = $getYears . $getTeams . '6' . '.txt'; 
            $fp = fopen($filename,"w");  
            fwrite($fp,$current7); 
            fclose($fp);  
        
            $filename = $getYears . $getTeams . '7' . '.txt'; 
            $fp = fopen($filename,"w");  
            fwrite($fp,$current8); 
            fclose($fp);  
        
            $filename = $getYears . $getTeams . '8' . '.txt'; 
            $fp = fopen($filename,"w");  
            fwrite($fp,$current9); 
            fclose($fp);  
        
            $filename = $getYears . $getTeams . '9' . '.txt'; 
            $fp = fopen($filename,"w");  
            fwrite($fp,$current10); 
            fclose($fp);  
            $filename = $getYears . $getTeams . '10' . '.txt'; 
            $fp = fopen($filename,"w");  
            fwrite($fp,$current11); 
            fclose($fp); 
            $filename = $getYears . $getTeams . '11' . '.txt'; 
            $fp = fopen($filename,"w");  
            fwrite($fp,$current12); 
            fclose($fp); 
            $filename = $getYears . $getTeams . '12' . '.txt'; 
            $fp = fopen($filename,"w");  
            fwrite($fp,$current13); 
            fclose($fp); 

            $filename = $getYears . $getTeams . '13' . '.txt'; 
            $fp = fopen($filename,"w");  
            fwrite($fp,$current14); 
            fclose($fp); 

          
        
    }
    else{
        $r1 = file_get_contents($getYears . $getTeams . '.txt');
        $r2 = file_get_contents($getYears . $getTeams . '1' . '.txt');
        $r3 = file_get_contents($getYears . $getTeams . '2' . '.txt');
        $r4 = file_get_contents($getYears . $getTeams . '3' . '.txt');
        $r5 = file_get_contents($getYears . $getTeams . '4' . '.txt');
        $r6 = file_get_contents($getYears . $getTeams . '5' . '.txt');
        $r7 = file_get_contents( $getYears . $getTeams . '6' . '.txt');
        $r8 = file_get_contents( $getYears . $getTeams . '7' . '.txt');
        $r9 = file_get_contents( $getYears . $getTeams . '8' . '.txt');
        $r10 = file_get_contents( $getYears . $getTeams . '9' . '.txt');
        $r11 = file_get_contents( $getYears . $getTeams . '10' . '.txt');
        $r12 = file_get_contents( $getYears . $getTeams . '11' . '.txt');
        $r13 = file_get_contents( $getYears . $getTeams . '12' . '.txt');
        $r14 = file_get_contents( $getYears . $getTeams . '13' . '.txt');


        $allRaces = preg_split("/\,/", $r1);
        $allRaces50 = preg_split("/\,/", $r2);
        $allRaces75 = preg_split("/\,/", $r3);
        $allRaces95 = preg_split("/\,/", $r4);

        $allRaces_ = preg_split("/\,/", $r5); 
        $allRaces50_ = preg_split("/\,/", $r6); 
        $allRaces75_ = preg_split("/\,/", $r7); 
        $allRaces95_ = preg_split("/\,/", $r8);
        $firstVals = preg_split("/\,/", $r9);
        $xAxis = preg_split("/\,/", $r10);
        $allTimes = preg_split("/\,/", $r11);
        $allTimes2 = preg_split("/\,/", $r12);
        $allRaces100_ = preg_split("/\,/", $r13);
        $allRaces100 = preg_split("/\,/", $r14);



        $minRange = number_format( calculate_median($allRaces95_), 3);


        $maxRange = number_format( calculate_median($allRaces95_), 3);

    }
    $xAxis = array();
    array_push($xAxis, 5);
    array_push($xAxis, 10);
    array_push($xAxis, 15);
    array_push($xAxis, 20);
    array_push($xAxis, 25);
?>


    


<!doctype html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>Teammate Comparison</title>
  <style type = "text/css">
        .box{
            width: 40%;
            height: 40%;
        }
    
        .tabledata{
        }
        .Footer1{
            position: absolute;
            font-size: 100%;
            bottom: -170%;
            left: 0%;

        }
        .Footer2{
            position: absolute;
            font-size: 65%;
            text-decoration: underline;
            bottom: -140%;
            left: 1%;

        }
        h3{
            color: #4682b4
        }
        .graph1 p{
            top: 100%;
            font-size: 50%;
        }
        .box123{
            position: absolute;
            font-size: 40%;
            width: 38%;
            
            top: 95%;
            left: 56%;
        }
        .qualdata2{
            position: absolute;
            border-collapse: collapse;
            border: 0.2rem solid black;
            border-spacing: 5rem;
            width: 40%;
            height: 34%;
            top: 166%;
            right: 1%;
            font-size: 100%;
            color: black;
            background-color:"#ff6361";
     
        }
        .qualdata2 th{
            font-size: 100%;
            border-left: 0.1rem solid grey;
            border-bottom: 0.1rem solid grey;
            font-family: "Georgia" ;

        }
        .qualdata2 td{

            font-size: 100%;

            border-left:  0.1rem solid grey;
            border-bottom: 0.1rem solid grey;
            font-family: "Georgia" ;


        }
        .racedata2{
            position: absolute;
            border-collapse: collapse;
            border: 0.2rem solid black;
            border-spacing: 5rem;
            width: 40%;
            height: 34%;
            top: 166%;
            left: 1%;
            font-size: 100%;
            color: black;
            background-color:"#ff6361";
     
        }
        .racedata2 p{
            font-size: 20%;
            font-family: "Georgia" ;
        }
        .racedata2 th{
            font-size: 100%;
            border-left: 0.1rem solid grey;
            border-bottom: 0.1rem solid grey;
            font-family: "Georgia" ;

        }
        .racedata2 td{

            font-size: 80%;

            border-left:  0.1rem solid grey;
            border-bottom: 0.1rem solid grey;
            font-family: "Georgia" ;


        }
        .racedata{
            position: absolute;
            border-collapse: collapse;
            border: 0.2rem solid black;
            border-spacing: 5rem;
            width: 50%;
            height: 80%;
            top: 70%;
            right: 50%;
            font-size: 100%;
            color: black;
            background-color: #fed8b1;
     
        }
        .racedata th{
            font-size: 100%;
            border-left: 0.1rem solid grey;
            border-bottom: 0.1rem solid grey;
            font-family: "Georgia" ;

        }
        .racedata td{

            font-size: 100%;

            border-left:  0.1rem solid grey;
            border-bottom: 0.1rem solid grey;
            font-family: "Georgia" ;


        }
        body{
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: #DCDCDC;
        }
        .xaxisfont{
            text-decoration: underline;
            font-size: 100%;
            font-family: "Georgia";
        }
        .xaxisfont2{

            font-size: 200%;
            font-family: "Georgia";
        }
        td.underl{
            background: #32CD32;

        }
        .qualbox{
            position: absolute;
            width: 40%;
            height: 5%;
            font-size: 100%;
            right: 5%;
            top: 10%;
        }
        .racebox{
        
            position: absolute;
            width: 40%;
            height: 5%;
            font-size: 100%;
            top: 70%;
            right: 5%;
        }
       
        .qualifyingcomp{
            border: 0.14rem solid black;

            position: absolute;
            background-color: #EEEEEE;

            border-collapse: collapse;
            border-color: black;
            border-spacing: 5rem;
            color: black;
        }
        .qualifyingcomp td{
            border-left: 0.1rem solid grey;
            border-bottom: 0.1rem solid grey;
            font-size:100%;

        }
        .qualifyingcomp th{
            font-size: 100%;
            border-left: 0.1rem solid grey;
            border-bottom: 0.1rem solid grey;
        }
        .racecomp{

            position: absolute;
            background-color: #EEEEEE;
            width: 113%;
            height: 15%;
            border-collapse: collapse;
            border: 0.14rem solid black;
            border-spacing: 5rem;
            color: black;
        }
        .racecomp td{
            font-size:100%;

            border-left: 0.1rem solid grey;
            border-bottom: 0.1rem solid grey;

        }
        .racecomp th{
            font-size:100%;

            border-left: 0.1rem solid grey;
            border-bottom: 0.1rem solid grey;
        }
        
        .tt{
           top: 20%;
        }
       
  </style>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
  <script language="JavaScript"><!--
    var data1 = {
      labels: <?php echo json_encode($rounds); ?>,

            datasets: [
                {
                    label: "Median % GAP in Qualifying",
                    strokeColor: "blue",
                    fillColor: "rgba(220,220,220,1)",
                    pointColor: "black",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "white",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: <?php  echo json_encode($timeDelta2); ?>
                }
            ]
  
    };
    var options = {
        responsive: true,
        width: "150%",
        height: "100%"
    };
   

    var data2 = {
            labels: <?php echo json_encode($xAxis); ?>,

            datasets: [
                {
                    label: "Median % GAP in Races",
                    strokeColor: "blue",
                    fillColor: "rgba(220,220,220,1)",
                    pointColor: "black",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "white",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: <?php  echo json_encode($firstVals); ?>
                }
            ]
        
    };
  
    window.onload = function(){
        window.lineChart = new Chart(document.getElementById("lineChart").getContext("2d")).Line(data1, options);        
        window.lineChart = new Chart(document.getElementById("lineChart2").getContext("2d")).Line(data2, options);        

    }
  --></script>
</head>
<body>
    <div class="box">
    <h2> <?php echo $drivername1 . " vs " . $drivername2 . " (" . $getYears . ")"  ?> </h2>

        <canvas id="lineChart" style="height: 18rem" ></canvas> 
        <?php
            echo '<p class ="xaxisfont"> Y-Axis: Median % Gap of fastest lap in final session both drivers competed (' . $drivername1 . " to " . $drivername2  . ")</p> ";
        ?>
        <p class ="xaxisfont"> X-Axis: Session Number - All sessions where both drivers set a lap are included, no data is excluded. </p>
    </div>

    <div class ="box123">

        
        <p class ="xaxisfont2"> <i> NOTE: The method that is used to compute average race lap difference over the season is to compute the average race lap times from the best 90% laps for each race. A median % difference is then computed from the races where both drivers finished, the final result displayed is this value. Below is a simple graph which shows the time differences based on the % of slowest laps excluded to check consistency of the results. </i> </p>
        <canvas id="lineChart2" style="height: 18rem" ></canvas> 
        <p class ="xaxisfont2"> <u> <i> X-Axis: % of slowest laps excluded </i> </u> </p>
        <p class ="xaxisfont2"> <u> <i> Y-Axis: AVG Race Pace Difference </i> </u> </p>

    </div>

   
        <table class="racedata">
            <tr>
            <th> Race Number </th>

            <th> <?php echo $drivername1 ?> </th>
            <th> <?php echo $drivername2 ?> </th>

            </tr>
            <?php 
                $i=1;
                while($i <= $countRaces){
                    echo "<tr>";
                    echo "<td>" . $i . "</td>";

                    if($getRacePositions1[$i-1] < $getRacePositions2[$i-1] && ($getRacePositions1[$i-1] >= "1" and $getRacePositions1[$i-1] <="25")
                    && $getRacePositions2[$i-1] != "RET (Non-driver/mechanical)" && $getRacePositions2[$i-1] != "DNS"){
                        echo '<td class="underl">' . $getRacePositions1[$i-1] . "</td>";
                    }
                    else{
                        echo '<td>' . $getRacePositions1[$i-1] . "</td>";
                    }


                    if($getRacePositions2[$i-1] < $getRacePositions1[$i-1] and ($getRacePositions2[$i-1] >= "1" and $getRacePositions2[$i-1] <="25")
                    and $getRacePositions1[$i-1] != "RET (Non-driver/mechanical)" and $getRacePositions1[$i-1] != "DNS"){
                        echo '<td class="underl">' . $getRacePositions2[$i-1] . "</td>";
                    }
                    else{
                        echo "<td>" . $getRacePositions2[$i-1] . "</td>";
                    }

                    echo "</tr>";
                    $i++;
                }
            ?>
        </table>
    <div class = "qualbox">
    <table class = "qualifyingcomp">
                <tr> 
                    <th> Criteria </th>
                    <th> <?php  echo $drivername1; ?> </th>
                    <th> <?php  echo $drivername2; ?></th>
                </tr>
                <tr>
                    <td> Head to Head Qualifying </td>
                    <?php if($qualiscores[1] > $qualiscores[0]){
                            echo '<td>' . $qualiscores[0] . "</td>";
                            echo '<td class="underl">' . $qualiscores[1] . "</td>";
                        }
                        else if($qualiscores[0] > $qualiscores[1]){
                            echo '<td class="underl"> ' . $qualiscores[0] . "</td>";
                            echo '<td> ' . $qualiscores[1] . "</td>";
                        }
                        else{
                            echo '<td> ' . $qualiscores[0] . "</td>";
                            echo '<td>' . $qualiscores[1] . "</td>";
                        }

                    ?>
                </tr>
                <tr>
                    <td> Median Qualifying % gap (fastest lap in final session) </td>
                    <?php 
                        if(number_format(calculate_median($timeDelta2), 3) > 0){
                            echo '<td>' . number_format(calculate_median($timeDelta2), 3) . "%" . "</td>";
                            echo '<td class="underl"> ' . -1 * number_format(calculate_median($timeDelta2), 3) . "%". "</td>";
                        }
                        else{
                            echo '<td class="underl"> ' . number_format(calculate_median($timeDelta2), 3) . "%". "</td>";
                            echo '<td>' . -1 * number_format(calculate_median($timeDelta2), 3) . "%" . "</td>";

                        }
                    ?>

                </tr>
    </table>
    </div>
    <div class = "racebox">
    <table class = "racecomp">
            <tr> 
                <th> Criteria </th>
                <th> <?php  echo $drivername1; ?> </th>
                <th> <?php  echo $drivername2; ?></th>
            </tr>
            <tr>
                <td> Points (in races both drivers competed) </td>
                <?php 
                    if($countPoints[$driverNames[0]] > $countPoints[$driverNames[1]]){
                        echo '<td class="underl">' . $countPoints[$driverNames[0]] . "</td>";
                        echo '<td>' . $countPoints[$driverNames[1]] . "</td>";

                    }
                    else if($countPoints[$driverNames[0]] < $countPoints[$driverNames[1]]){
                        echo '<td>' . $countPoints[$driverNames[0]] . "</td>";
                        echo '<td class="underl">' . $countPoints[$driverNames[1]] . "</td>";
                    }
                    else{
                        echo '<td>' . $countPoints[$driverNames[0]] . "</td>";
                        echo '<td>' . $countPoints[$driverNames[1]] . "</td>";
                    }
                ?> 
            </tr>
            <tr>
                    <td> Head to Head Races </td>
                    <?php 
                    if($driverWins[0] > $driverWins[1]){
                        echo '<td class="underl">' . $driverWins[0] . "</td>";
                        echo '<td>' . $driverWins[1] . "</td>";

                    }
                    else if($driverWins[0] < $driverWins[1]){
                        echo '<td>' . $driverWins[0] . "</td>";
                        echo '<td class="underl">' . $driverWins[1] . "</td>";
                    }
                    else{
                        echo '<td>' . $driverWins[0] . "</td>";
                        echo '<td>' . $driverWins[1] . "</td>";
                    }
                ?> 
            </tr>
            <tr>
                    <td> Points Per Race (excluding Non-Driver DNFs/DNS) </td>
                    <?php 
                    if($ppr1 > $ppr2){
                        echo '<td class="underl">' . $ppr1 . "</td>";
                        echo '<td>' . $ppr2 . "</td>";

                    }
                    else if($ppr1 < $ppr2){
                        echo '<td>' . $ppr1 . "</td>";
                        echo '<td class="underl">' . $ppr2 . "</td>";
                    }
                    else{
                        echo '<td>' . $ppr1 . "</td>";
                        echo '<td>' . $ppr2 . "</td>";
                    }
                ?> 
            </tr>
            <tr>
                    <td> AVG Race Lap Time % Gap </td>
                    
                    <?php 
                      
                        
                            
                            if($minRange < 0){
                                if($maxRange < 0){
                                    echo '<td class = "underl">' . $maxRange ."%" . "</td>";
                                    echo '<td class>' .  -1 * $maxRange . "%"  . "</td>";
                                }
                                else{
                                    echo '<td class = "smalla">' . $maxRange . "%" . "</td>";
                                    echo '<td class = "smalla">' .  -1 * $maxRange . "%" . "</td>";
                                }
                            }
                            else{
                                if($maxRange > 0){
                                    echo '<td class>' . $minRange . "%" . "</td>";
                                    echo '<td class = "underl">' .  -1 * $minRange . "%" . "</td>";
                                }
                                else{
                                    echo '<td class>' . $minRange . "%" . "</td>";
                                    echo '<td class>' .  -1 * $minRange . "%" . "</td>";
                                }
                            }
                     
                    ?>

            </tr>
    </table>
    
    </div>
    <div class = "Footer1">
        
        <h3> <a href = "index.php"> <h3> Try Another Comparison </h3> </a> </h3> 
    </div>
 
    <div class = "graph1">
   
        <table class = "racedata2">
        

            <tr>

                <th> Race Number (AVG Laptime from best 90% laps) </th>

                <th> <?php echo $drivername1 ?> </th>
                <th> <?php echo $drivername2 ?> </th>
        
                </tr>
            
                <?php 
                    $i=0;
                    $medCalcB = array();
                    $medCalcW = array();
                        $ti = 0;
                    while($i < $countRaces){

                        echo "<tr>";
                        echo "<td>" . (int)($i+1) . "</td>";
                        $sr1 = $allRaces95[$i];
                        $sr2 = $allRaces95[$i];
                            if(1){
                                if($sr1 < 0 && $sr2 < 0){
                                    echo '<td class="underl">' . convertToMinutes($allTimes[$ti]) . "</td>";
                                    echo '<td >' . convertToMinutes($allTimes2[$ti]) . "</td>";
                                }
                                else if($sr1 > 0 && $sr2 > 0){
                                    echo '<td class>' . convertToMinutes($allTimes[$ti])  . "</td>";
                                    echo '<td class="underl">' . convertToMinutes($allTimes2[$ti])  . "</td>";

                                }
                                else{
                                    echo '<td class>' . convertToMinutes($allTimes[$ti])  . "</td>";
                                    echo '<td class>' . convertToMinutes($allTimes2[$ti])  . "</td>";
                                }
                                $ti++;
                            }
                            $i++;

                        array_push($medCalcB, $sr1);
                        array_push($medCalcW, $sr2);

                    }
                ?>

        </table>
       

    </div>
    <div class = "def">


        <table class = "qualdata2">
        
            <tr>
                
                <th> Qualifying Number </th>

                <th> <?php echo $drivername1 ?> </th>
                <th> <?php echo $drivername2 ?> </th>
                <th> <?php echo "% Diff" ?> </th>

                </tr>
            
                <?php 
                    $i=0;
                    while($i < $countRaces){
                        echo "<tr>";
                        echo "<td>" . (int)($i+1) . "</td>";
                            if($allQual1[$i] != "Not Available" && $allQual2[$i] != null){
                                if($allQual1[$i] <= $allQual2[$i]){
                                    echo '<td class="underl">' . convertToMinutes($allQual1[$i]) . "</td>";
                                    echo '<td>' . convertToMinutes($allQual2[$i]) . "</td>";
                                    echo '<td>' . number_format(computeDiff($allQual1[$i], $allQual2[$i]), 3) . '</td>';

                                }
                                else{
                                    echo '<td>' . convertToMinutes($allQual1[$i]) . "</td>";
                                    echo '<td class="underl">' . convertToMinutes($allQual2[$i]) . "</td>";
                                    echo '<td>' . number_format(computeDiff($allQual1[$i], $allQual2[$i]), 3)  . '</td>';
                                }
                            }
                            else{
                                echo '<td>' .  ($allQual1[$i]) . "</td>";
                                echo '<td>' .  ($allQual2[$i]) .  "</td>";

                            }
                        echo "</tr>";
                        $i++;
                    }
                ?>
                
        </table>
    </div>
</body>
</html>
