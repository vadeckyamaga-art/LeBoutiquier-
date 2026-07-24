<?php 

    session_start();
    include 'connexionBD.php';
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    file_put_contents('debug_log.txt', print_r($_POST, true));
    header('Content-Type: application/json');
    ob_clean();

    try{
        $sqlBoutiques="SELECT *
                       FROM commerçant
                       WHERE Quartier_boutique IS NOT NULL AND Quartier_boutique!=''";
        $stmtBoutiques=$conn->prepare($sqlBoutiques);
        $stmtBoutiques->execute();
        $resultats=$stmtBoutiques->fetchAll();
        $boutiqueFinales=[];

        if(count($resultats)===0){
            echo json_encode(["debug_error"=>"La table commerçant est vide"]);
        }

        foreach($resultats as $row){
            $btqCoords=json_decode(stripslashes(trim($row['Quartier_boutique'], '"')), true);

            if($btqCoords && isset($btqCoords['lat'], $btqCoords['long'])){
                $nomBtq=!empty($row['nom_boutique'])?$row['nom_boutique']: "Boutique sans nom";
                $boutiqueFinales[]=[
                    "id"=>$row['id_commerçant'],
                    "nom"=>$nomBtq,
                    "lat"=>(float)$btqCoords['lat'],
                    "lng"=>(float)$btqCoords['long'],
                    "photo"=>$row['profil_boutique']
                ];
                //var_dump($boutiqueFinales);
            }
        }
         
        echo json_encode($boutiqueFinales);
        exit();
    }
    catch(PDOException $e){
        echo json_encode(['ERREUR SQL: '.$e->getMessage()]);
        exit();
    }

?>