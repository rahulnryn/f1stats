<?php
    error_reporting(0);
    
    

    function calc_trend($X, $Y){
        
        $logX = array_map('log', $X);

        $n = count($X);
        $square = create_function('$x', 'return pow($x,2);');
        $x_squared = array_sum(array_map($square, $logX));
        $xy = array_sum(array_map(create_function('$x,$y', 'return $x*$y;'), $logX, $Y));

        $bFit = ($n * $xy - array_sum($Y) * array_sum($logX)) /
                ($n * $x_squared - pow(array_sum($logX), 2));

        $aFit = (array_sum($Y) - $bFit * array_sum($logX)) / $n;
        $Yfit = array();
        foreach($X as $x) {
            $Yfit[] = number_format($aFit + $bFit * log($x),3);
        }
        return $Yfit;
    }
    function Stand_Deviation($arr) 
    { 
        $num_of_elements = count($arr); 
          
        $variance = 0.0; 
          
                // calculating mean using array_sum() method 
        $average = array_sum($arr)/$num_of_elements; 
          
        foreach($arr as $i) 
        { 
            // sum of squares of differences between  
                        // all numbers and means. 
            $variance += pow(($i - $average), 2); 
        } 
          
        return (float)sqrt($variance/$num_of_elements); 
    } 

    function contains($haystack, $needle)
    {
        return strpos($haystack, $needle) !== false;
    }
    function calculate_mean($array){
        $mean = number_format((double)array_sum($array) / count($array), 3);
        return $mean;
    }
    function remove_outliers($dataset, $magnitude = 1) {

        $count = count($dataset);
        $mean = array_sum($dataset) / $count; // Calculate the mean
        $deviation = sqrt(array_sum(array_map("sd_square", $dataset, array_fill(0, $count, $mean))) / $count) * $magnitude; // Calculate standard deviation and times by magnitude
      
        return array_filter($dataset, function($x) use ($mean, $deviation) { return ($x <= $mean + $deviation && $x >= $mean - $deviation); }); // Return filtered array of values that lie within $mean +- $deviation.
      }

      function remove_outliers2($dataset, $magnitude = 1) {

        $count = count($dataset);
        $mean = array_sum($dataset) / $count; // Calculate the mean
        $deviation = sqrt(array_sum(array_map("sd_square", $dataset, array_fill(0, $count, $mean))) / $count) * $magnitude; // Calculate standard deviation and times by magnitude
      
        return array_filter($dataset, function($x) use ($mean, $deviation) { return ($x <= $mean + $deviation); }); // Return filtered array of values that lie within $mean +- $deviation.
      }
      
      function sd_square($x, $mean) {
        return pow($x - $mean, 2);
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
                $timeDelta2 = remove_outliers($timeDelta2, 3);

            
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
    $starter = 1;
    if($check == false || $getYears < '1996')
        $ender = 0;
    else{
        $ender = $countRaces;
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
  
    if(!file_exists($getYears . $getTeams . '.txt')){
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

            for($i = 1; $i < min($laps, $laps2); $i++){
                if($obj->MRData->RaceTable->Races[0]->Laps[$i]->Timings[0]->time == 0){
                    break;
                }
                if($obj2->MRData->RaceTable->Races[0]->Laps[$i]->Timings[0]->time == 0){
                    break;
                }
                $dt1 = converToSeconds($obj->MRData->RaceTable->Races[0]->Laps[$i]->Timings[0]->time);
                $dt2 = converToSeconds($obj2->MRData->RaceTable->Races[0]->Laps[$i]->Timings[0]->time);
                array_push($alap1, $dt1);
                array_push($alap2, $dt2);

            }
            $avg1 = calculate_median($alap1);
            $avg2 = calculate_median($alap2);

            for($i = 2; $i < min($laps, $laps2); $i++){
                if($obj->MRData->RaceTable->Races[0]->Laps[$i]->Timings[0]->time == 0){
                    break;
                }
                if($obj2->MRData->RaceTable->Races[0]->Laps[$i]->Timings[0]->time == 0){
                    break;
                }
                $dt1 = converToSeconds($obj->MRData->RaceTable->Races[0]->Laps[$i]->Timings[0]->time);
                $dt2 = converToSeconds($obj2->MRData->RaceTable->Races[0]->Laps[$i]->Timings[0]->time);
                $cm1 = calculate_mean($first);
                $cm2 = calculate_mean($second);
                if($i == 2){
                    array_push($first, $avg1);
                    array_push($second, $avg2);
                }
                $dev1 = Stand_Deviation($first);
                $dev2 = Stand_Deviation($second);
                
                if($dt1 <= ($cm1 + 3 * $dev1)){
                   
                    array_push($first, $dt1);
                }

                if($dt2 <= ($cm2 + 3 * $dev2)){
                    
                    array_push($second, $dt2);
                }

            }

            if(abs($laps-$laps2) <= 1 and $t >= $starter){
                unset($first[0]);
                unset($second[0]);
                

                sort($first);
                sort($second);
              

                $length1 = (int)(count($first));
                $length2 = (int)(count($second));

                $diff = array();
                $meantime1=0;
                $meantime2=0;
                echo($length . "\n");
                for($i = 0; $i < $length1; $i++){
                    //echo("\n");
                    //echo($first[$i] . "\n");
                    //echo($second[$i] . "\n");
                    $meantime1 += $first[$i];
                    //echo($delta);
                }
                for($i = 0; $i < $length2; $i++){
                    //echo("\n");
                    //echo($first[$i] . "\n");
                    //echo($second[$i] . "\n");
                    $meantime2 += $second[$i];
                    //echo($delta);
                }
                
                array_push($allRaces, number_format(computeDiff($meantime1/$length1, $meantime2/$length2), 3));

                $meantime1 = convertToMinutes(number_format($meantime1 / $length1, 3));
                $meantime2 = convertToMinutes(number_format($meantime2 / $length2, 3));
                //echo(($meantime1) . "\n");
                //echo("\n");
                //echo(calculate_median($diff) . "\n");
                array_push($allTimes, $meantime1);
                array_push($allTimes2, $meantime2);

            }
            else{
                array_push($allTimes, "Not Available");
                array_push($allTimes2, "Not Available");

            }
        }
        $allRaces = remove_outliers($allRaces, 4);

        $medGap = (number_format(calculate_median($allRaces), 3));
        $current = "";
        $current2 = "";
        $current3 = "";

        for($i = 0; $i < count($allRaces); $i++){
            $current .= ($allRaces[$i]);
            if($i != count($allRaces) - 1){
                $current .= ',';
            }
        }
        for($i = 0; $i < count($allTimes); $i++){
            $current2 .= ($allTimes[$i]);
            if($i != count($allTimes) - 1){
                $current2 .= ',';
            }
        }
        for($i = 0; $i < count($allTimes2); $i++){
            $current3 .= ($allTimes2[$i]);
            if($i != count($allTimes2) - 1){
                $current3 .= ',';
            }
        }
        if(!file_exists($getYears . $getTeams . '.txt') && $getYears >= "1996" && $check ){
            $filename = $getYears . $getTeams . '.txt'; 
            $fp = fopen($filename,"w");  
            fwrite($fp,$current); 
            fclose($fp);  
        }
        if(!file_exists($getYears . $getTeams . '1' . '.txt') && $getYears >= "1996" && $check){
            $filename = $getYears . $getTeams . '1' . '.txt'; 
            $fp = fopen($filename,"w");  
            fwrite($fp,$current2); 
            fclose($fp);  
        }
        if(!file_exists($getYears . $getTeams . '2' . '.txt') && $getYears >= "1996" && $check){
            $filename = $getYears . $getTeams . '2' . '.txt'; 
            $fp = fopen($filename,"w");  
            fwrite($fp,$current3); 
            fclose($fp);  
        }
    }
    else{
        $allRaces = array();
        $racegapdata = fopen( $getYears . $getTeams . '.txt','r');
        $racegapdata2 = fopen( $getYears . $getTeams . '1' . '.txt','r');
        $racegapdata3 = fopen( $getYears . $getTeams . '2' . '.txt','r');

        while ($line = fgets($racegapdata)) {
            $allRaces = preg_split ("/\,/", $line);  
        }
        while ($line = fgets($racegapdata2)) {
            $allTimes = preg_split ("/\,/", $line);  
        }
        while ($line = fgets($racegapdata3)) {
            $allTimes2 = preg_split ("/\,/", $line);  
        }
        $medGap = (number_format(calculate_median($allRaces), 3));
    }
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
            width: 40%;
            
            top: 105%;
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

            font-size: 100%;

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
            width: 100%;
            height: 10%;
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
            labels: <?php echo json_encode($rounds); ?>,

            datasets: [
                {
                    label: "Median % GAP in Races",
                    strokeColor: "blue",
                    fillColor: "rgba(220,220,220,1)",
                    pointColor: "black",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "white",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: <?php  echo json_encode($allRaces); ?>
                }
            ]
        
    };
  
    window.onload = function(){
        window.lineChart = new Chart(document.getElementById("raceChart").getContext("2d")).Line(data2, options);   
        window.lineChart = new Chart(document.getElementById("lineChart").getContext("2d")).Line(data1, options);        
     
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
        <p class ="xaxisfont"> X-Axis: Session Number (ONLY representative sessions are included.) </p>
    </div>

    <div class ="box123">

        <canvas id="raceChart" style="height: 18rem" ></canvas> 

        <?php

            echo '<p class ="xaxisfont"> Y-Axis: AVG Race Laptime Gap: Start/finish laptimes and laptimes set 3 SDs slower than the moving average are not includes, base time is the median lap. (' . $drivername1 . " to " . $drivername2  . ")</p> ";

        ?>
        <p class ="xaxisfont"> X-Axis: Session Number (ONLY races both drivers finished are included.) </p>

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
                    <td> AVG Race Lap Time % Gap (over the season) </td>
                    
                    <?php 
                      

                        if($medGap > 0){
                            echo '<td>' . $medGap . "%" . "</td>";
                            echo '<td class="underl"> ' . -1 * $medGap . "%". "</td>";
                        }
                        else if($medGap < 0){
                            echo '<td class = "underl">' . $medGap . "%" . "</td>";
                            echo '<td>' . -1 * $medGap . "%". "</td>";
                        }
                        if($medGap == 0){
                            echo '<td>' . $medGap . "</td>";
                            echo '<td> ' . $medGap . "</td>";
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

                <th> Race Number (AVG Laptime) </th>

                <th> <?php echo $drivername1 ?> </th>
                <th> <?php echo $drivername2 ?> </th>
                <th> <?php echo "% Diff" ?> </th>
        
                </tr>
            
                <?php 
                    $i=0;
                    while($i < $countRaces){
                        echo "<tr>";
                        echo "<td>" . (int)($i+1) . "</td>";
                            if($allTimes[$i] != "Not Available" && $allTimes[$i] != null){
                                if($allTimes[$i] <= $allTimes2[$i]){
                                    echo '<td class="underl">' . $allTimes[$i] . "</td>";
                                    echo '<td>' . $allTimes2[$i] . "</td>";
                                    echo '<td>' . number_format(computeDiff(converToSeconds2($allTimes[$i]), converToSeconds2($allTimes2[$i])), 3)  . '</td>';
                                }
                                else{
                                    echo '<td>' . $allTimes[$i] . "</td>";
                                    echo '<td class="underl">' . $allTimes2[$i] . "</td>";
                                    echo '<td>' . number_format(computeDiff(converToSeconds2($allTimes[$i]), converToSeconds2($allTimes2[$i])), 3)  . '</td>';

                                }
                            }
                            else{
                                echo '<td>' .  $allTimes[$i] . "</td>";
                                echo '<td>' .  $allTimes[$i] . "</td>";

                            }
                        echo "</tr>";
                        $i++;
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
