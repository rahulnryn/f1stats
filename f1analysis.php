<?php
    error_reporting(0);

    function contains($haystack, $needle)
    {
        return strpos($haystack, $needle) !== false;
    }
    function converToSeconds($timestr){
        $minutes = ((double)$timestr[0]) * 60;
        $str1 = $timestr[2] . $timestr[3];
        $seconds = (double)($str1);
        $str2 = $timestr[5] . $timestr[6] . $timestr[7];
        $milliseconds = (double)$str2/1000.0;
        return $minutes + $seconds + $milliseconds;
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
   $json = file_get_contents("https://ergast.com/api/f1/" . $getYears . "/constructors" . "/" . $getTeams . "/results.json?limit=100");
   $obj = json_decode($json);
   $drivername1 = ($obj->MRData->RaceTable->Races[0]->Results[0]->Driver->familyName);
   $drivername2 = ($obj->MRData->RaceTable->Races[0]->Results[1]->Driver->familyName);
   $countWins=array($drivername1=>0,$drivername2=>0);
   $countPoints=array($drivername1=>0,$drivername2=>0);
   $countTotalPoints=array($drivername1=>0,$drivername2=>0);
   $rc = file_get_contents("http://ergast.com/api/f1/" . $getYears . "/races.json");
   $racecount = json_decode($rc);
   $countRaces = $racecount->MRData->total;


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

   $jsonquali = file_get_contents("https://ergast.com/api/f1/" .$getYears . "/constructors" ."/" . $getTeams . "/qualifying.json?limit=100");
   $qualifying = json_decode($jsonquali);
   $countQualiWins=array($drivername1=>0,$drivername2=>0);

   $timeDelta = array();
  
    for($x = 0; $x < $countRaces; $x++){
            $q1pos =$qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[0]->position;
            $q2pos =$qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[1]->position;

            $cdq1 = $qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[0]->Driver->familyName;
            $cdq2 = $qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[1]->Driver->familyName;
            if( (converToSeconds($qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[0]->Q1) == NULL)
                or (converToSeconds($qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[1]->Q1) == NULL)){
                continue;
            }
            $q1d1 = converToSeconds($qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[0]->Q1);
            $q2d1 = converToSeconds($qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[0]->Q2);
            $q3d1 = converToSeconds($qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[0]->Q3);
            if($q2d1 == NULL){
                $q2d1 = 500;
            }
            if($q3d1 == NULL){
                $q3d1 = 500;
            }
            $q1d2 = converToSeconds($qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[1]->Q1);
            $q2d2 = converToSeconds($qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[1]->Q2);
            $q3d2 = converToSeconds($qualifying->MRData->RaceTable->Races[$x]->QualifyingResults[1]->Q3);

            if($q2d2 == NULL){
                $q2d2 = 500;
            }
            if($q3d2 == NULL){
                $q3d2 = 500;
            }
            $driver1time = min($q1d1, min($q2d1, $q3d1));
            $driver2time = min($q1d2, min($q2d2, $q3d2));
            if($driver1time == 500){
                continue;
            }
            if($driver2time == 500){
                continue;
            }
                
            if($q1pos < $q2pos  and ($cdq2 == $drivername2 || $cdq2 == $drivername1)){
                $countQualiWins[$cdq1]++;
            }
            else if($q2pos < $q1pos  and ($cdq1 == $drivername2 || $cdq1 == $drivername1)){
                $countQualiWins[$cdq2]++;
            }   
            if($cdq1 == $drivername1 and $cdq2 == $drivername2){
                array_push($timeDelta, number_format((double)computeDiff($driver1time, $driver2time), 3));
            }             
            else if($cdq2 == $drivername1 and $cdq1 == $drivername2){
                array_push($timeDelta, number_format((double)computeDiff($driver2time, $driver1time), 3));
            }

        
    }
    $qualinames = array_keys($countQualiWins);
    $qualiscores = array_values($countQualiWins);
    $ppr1 = number_format((float)($countTotalPoints[$drivername1] / $racesFinished[$drivername1]), 2);
    $ppr2 = number_format((float)($countTotalPoints[$drivername2] / $racesFinished[$drivername2]), 2);


?>

<!doctype html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>Line Chart Test</title>
  <style type = "text/css">
        #racedata{
            border-collapse: collapse;
            border: 3px solid black;
            border-spacing: 5rem;
            width:50%;
            height:5%;
            color: black;
            background-color: #D2691E;
     
        }
        #racedata th{
            border-left: 2px solid grey;
            border-bottom: 2px solid grey;
            font-family: "Gotham-Black" ;

        }
        #racedata td{
            border-left: 2px solid grey;
            border-bottom: 2px solid grey;
            font-size: 16px;
            font-family: "Gotham-Black" ;


        }
        body{
            background-color: #DCDCDC;
        }
        .xaxisfont{
            font-weight: underline;
            font-size: 15px;
            font-family: "Fira";
        }
        td.underl{
            background: #32CD32;

        }
        .qualifyingcomp{
            position: absolute;
            background-color: #EEEEEE;

            top: 155px;
            right: 200px;
            border-collapse: collapse;
            border: 3px solid black;
            border-spacing: 5rem;
            width:30%;
            height:5%;
            color: black;
        }
        .qualifyingcomp td{
            border-left: 2px solid grey;
            border-bottom: 2px solid grey;
            font-size:16px;

        }
        .qualifyingcomp th{
            border-left: 2px solid grey;
            border-bottom: 2px solid grey;
        }
        .racecomp{
            position: absolute;
            background-color: #EEEEEE;

            top: 550px;
            right: 200px;
            border-collapse: collapse;
            border: 3px solid black;
            border-spacing: 5rem;
            width:30%;
            height:5%;
            color: black;
        }
        .racecomp td{
            border-left: 2px solid grey;
            border-bottom: 2px solid grey;
            font-size:16px;

        }
        .racecomp th{
            border-left: 2px solid grey;
            border-bottom: 2px solid grey;
        }
        .xaxisfont{
            font-family: "Gotham-Black";
            font: red;
            text-decoration: underline
        }
       
  </style>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
  <script language="JavaScript"><!--
    var data = {
            labels: <?php echo json_encode($rounds); ?>,

            datasets: [
                {
                    label: "Median % GAP",
                    fillColor: "rgba(220,220,220,0.2)",
                    strokeColor: "black",
                    pointColor: "red",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: <?php  echo json_encode($timeDelta); ?>
                }
            ]
        
    };
    var options = {};
  
    window.onload = function(){
        window.lineChart = new Chart(document.getElementById("lineChart").getContext("2d")).Line(data, options);        
    }
  --></script>
</head>
<body>
  <h1> <?php echo $drivername1 . " vs " . $drivername2 . " (" . $getYears . ")"  ?> </h1>
  <div class="box">
   
    <canvas id="lineChart" height="300" width="700"></canvas> 

  </div>

  <div class ="box2">
      <?php
          echo '<p class ="xaxisfont"> Y-Axis: Median % Gap of fastest qualifying lap to Teammate (' . $drivername1 . " to " . $drivername2  . ")</p> ";
      ?>
      <p class ="xaxisfont"> X-Axis: Session Number (ONLY representative sessions are included.) </p>
  </div>
  <div class ="tabledata">
    <table id="racedata">
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
  </div>
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
                <td> Median Qualifying % Difference (fastest lap out of Q1-Q3 is taken) </td>
                <?php if(number_format(calculate_median($timeDelta), 3) > 0){
                        echo '<td>' . number_format(calculate_median($timeDelta), 3) . "%" . "</td>";
                        echo '<td class="underl"> ' . -1 * number_format(calculate_median($timeDelta), 3) . "%". "</td>";
                    }
                    else{
                        echo '<td class="underl"> ' . number_format(calculate_median($timeDelta), 3) . "%". "</td>";
                        echo '<td>' . -1 * number_format(calculate_median($timeDelta), 3) . "%" . "</td>";

                    }
                ?>

            </tr>
  </table>
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
  </table>
  <h2> <a href = "index.php"> Try Another Comparison </a> </h2> 
</body>
</html>