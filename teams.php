<?php
    if(!empty($_POST["yearsID"])){
        $obj1 = file_get_contents("https://ergast.com/api/f1/" . $_POST['yearsID'] . "/constructors.json");
        $teams = json_decode($obj1);
        $allTeams = Array($teams->MRData->ConstructorTable->Constructors[0]->name=>$teams->MRData->ConstructorTable->Constructors[0]->constructorId);
        for($x = 0; $x < 23; $x++){
            if($teams->MRData->ConstructorTable->Constructors[$x]->name == NULL){
                continue;
            }
            $allTeams[$teams->MRData->ConstructorTable->Constructors[$x]->name] = $teams->MRData->ConstructorTable->Constructors[$x]->constructorId;
            if($_POST['yearsID'] == "2009" and $teams->MRData->ConstructorTable->Constructors[$x]->name == "Ferrari"){
                $allTeams["Ferrari (with Fisichella)"] = "ferrari2";
            }
            if($_POST['yearsID'] == "2004" and $teams->MRData->ConstructorTable->Constructors[$x]->name == "Williams"){
                $allTeams["Williams (with Pizzonia)"] = "williams2";
            }
            if($_POST['yearsID'] == "2004" and $teams->MRData->ConstructorTable->Constructors[$x]->name == "Jordan"){
                $allTeams["Jordan (with Glock)"] = "jordan2";
            }
            if($_POST['yearsID'] == "2005" and $teams->MRData->ConstructorTable->Constructors[$x]->name == "Red Bull"){
                $allTeams["Red Bull (with Liuzzi)"] = "red_bull2";
            }
            if($_POST['yearsID'] == "2006" and $teams->MRData->ConstructorTable->Constructors[$x]->name == "McLaren"){
                $allTeams["McLaren (with De La Rosa)"] = "mclaren2";
            }
            if($_POST['yearsID'] == "2006" and $teams->MRData->ConstructorTable->Constructors[$x]->name == "BMW Sauber"){
                $allTeams["BMW Sauber (with Kubica)"] = "bmw_sauber2";
            }
            if($_POST['yearsID'] == "2007" and $teams->MRData->ConstructorTable->Constructors[$x]->name == "Toro Rosso"){
                $allTeams["Toro Rosso (with Vettel)"] = "toro_rosso2";
            }
            if($_POST['yearsID'] == "2009" and $teams->MRData->ConstructorTable->Constructors[$x]->name == "Renault"){
                $allTeams["Renault (with Grosjean)"] = "renault2";
            }
            if($_POST['yearsID'] == "2016" and $teams->MRData->ConstructorTable->Constructors[$x]->name == "Manor Marussia"){
                $allTeams["Manor Marussia (with Ocon)"] = "manor2";
            }
            if($_POST['yearsID'] == "2016" and $teams->MRData->ConstructorTable->Constructors[$x]->name == "Red Bull"){
                $allTeams["Red Bull (with Kvyat)"] = "red_bull2";
            }
            if($_POST['yearsID'] == "2016" and $teams->MRData->ConstructorTable->Constructors[$x]->name == "Toro Rosso"){
                $allTeams["Toro Rosso (with Verstappen)"] = "toro_rosso2";
            }
            if($_POST['yearsID'] == "2017" and $teams->MRData->ConstructorTable->Constructors[$x]->name == "Renault"){
                $allTeams["Renault (with Sainz)"] = "renault2";
            }
            if($_POST['yearsID'] == "2019" and $teams->MRData->ConstructorTable->Constructors[$x]->name == "Toro Rosso"){
                $allTeams["Toro Rosso (with Gasly)"] = "toro_rosso2";
            }
            if($_POST['yearsID'] == "2019" and $teams->MRData->ConstructorTable->Constructors[$x]->name == "Red Bull"){
                $allTeams["Red Bull (with Albon)"] = "red_bull2";
            }
        
        }
        
    
        foreach($allTeams as $key => $value):
            echo '<option value="'.$value.'">'.$key.'</option>'; 
        endforeach;
    }
    else{
        echo '<option value="">Teams not available</option>'; 
    }

   
    
?>
