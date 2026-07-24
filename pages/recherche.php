<?php 

    session_start();
    include 'connexionBD.php';
    ob_clean();
    header('Content-Type: application/json');

    if(isset($_GET['query'])){
        $query = isset( $_GET['query'])? $_GET['query'] . '%':'';
        $type =  isset($_GET['type'])?$_GET['type']:'article';
        try{
            if(!empty($_GET['query'])){
                $searchItem="%".$_GET['query']."%";
                if ($type === 'categorie') {
                    $sql = "SELECT id_cat as id_categorie, nom_cat as nom 
                            FROM catégorie 
                            WHERE nom_cat 
                            LIKE ? 
                            LIMIT 5";
                } 
                else {
                    $sql = "SELECT a.id_article, a.nom_article as nom, co.id_commerçant
                    FROM article a, commerçant co
                    WHERE co.id_commerçant=a.id_commerçant AND a.nom_article
                    LIKE ? 
                    LIMIT 5";
                }
                $stmt = $conn->prepare($sql);
                $stmt->execute([$searchItem]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            }
            echo json_encode($results);
            exit();
        }
        catch(PDOException $e){
            die('ERREUR SQL'.$e->getMessage());
        }
    }

?>