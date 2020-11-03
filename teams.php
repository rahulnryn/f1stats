<?php
    if(!empty($_POST["yearsID"])){
        $obj1 = file_get_contents("https://ergast.com/api/f1/" . $_POST['yearsID'] . "/constructors.json");
        $teams = json_decode($obj1);
        $allTeams = Array($teams->MRData->ConstructorTable->Constructors[0]->name=>$teams->MRData->ConstructorTable->Constructors[0]->constructorId);
        for($x = 0; $x < 13; $x++){
            if($teams->MRData->ConstructorTable->Constructors[$x]->name == NULL){
                continue;
            }
            $allTeams[$teams->MRData->ConstructorTable->Constructors[$x]->name] = $teams->MRData->ConstructorTable->Constructors[$x]->constructorId;
            
        }
    
        foreach($allTeams as $key => $value):
            echo '<option value="'.$value.'">'.$key.'</option>'; 
        endforeach;
    }
    else{
        echo '<option value="">Teams not available</option>'; 
    }

   
    
?>
