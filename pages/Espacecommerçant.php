<?php

    session_start();
    include 'connexionBD.php';
    $errorMsgProfil='';
    $successMsgProfil='';
    $successCmd='';
    $errorCmd='';
    $successMsg='';
    $errorMsg='';
    $errorAddFile="";
    $successAddFile="";
    $errorInfo="";
    $successInfo="";
    $action='';

    $formatter=new IntlDateFormatter(
                'fr_FR',
                IntlDateFormatter::LONG,
                IntlDateFormatter::NONE
    );
    function tempsEcoule($date){
        $timestamp=strtotime($date);
        $diff=time()-$timestamp;
        if($diff<60) return"il y a quelque secondes";
        if($diff<3600)return"il y a ".round($diff/60)." min";
        if($diff<86400)return"il y a ".round($diff/3600)." h";
        return"Le ".date('d/m', $timestamp);
    }


                                /* ===========================================================
                                =========================== MAJ PROFIL =======================
                                ============================================================== */
    
 
 
    $id = $_GET['id'] ?? $_SESSION['active_merchant_id'] ?? null;
    if(!$id && isset($_SESSION['id'])) {
        $stmt = $conn->prepare("SELECT id_commerçant FROM commerçant WHERE id = ?");
        $stmt->execute([$_SESSION['id']]);
        $res = $stmt->fetch();
        $id = $res['id_commerçant'] ?? null;
    }

    if(!$id){
        header('Location: connexion.php');
        exit();
    }
    $sql = "SELECT * FROM commerçant WHERE id_commerçant = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $com = $stmt->fetch(PDO::FETCH_ASSOC);

                        
    if(isset($_GET['id']) && !empty($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT * FROM commerçant WHERE id_commerçant = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $com = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$com) {
            $_SESSION['errorMsg'] = "Commerçant introuvable.";
            header('Location: dashboard.php');
            exit();
        }
    } 
    else {
        header('Location: dashboard.php');
        exit();
    }

    if(!isset($_SESSION['id'] )){
        header('Location: connexion.php');
        exit();
    }
    if($_SESSION['compte']==='client'){
        header('Location: inscription.php');
        exit();
    }
    
    $btqID=$_SESSION['id'];//ID en tant que utilisateur
    $infobtq=['nom_boutique'=>'', 'description_boutique'=>'', 'profil_boutique'=>'', 'nom_commerçant'=>''];
    $infoUsers=null; 

    try{
        $stmtshow=$conn->prepare("SELECT * FROM commerçant WHERE id=?");
        $stmtshow->execute([$btqID]);
        $rowBtq=$stmtshow->fetch(PDO::FETCH_ASSOC);

        if($rowBtq){
            $infobtq=$rowBtq;
        }
    }
    catch(PDOException $e){
        $_SESSION['errorMsgProfil']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i> Erreur: ' .htmlspecialchars($e->getMessage());
    } 

    try{   
        $stmtShowUsers=$conn->prepare("SELECT * FROM utilisateur WHERE id=?");
        $stmtShowUsers->execute([$btqID]);
        $rowUsers=$stmtShowUsers->fetch(PDO::FETCH_ASSOC);
        if($rowUsers){
            $infoUsers=$rowUsers;
        }   
    }
    catch(PDOException $e){
        $_SESSION['errorMsgProfil']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i> Erreur: ' .htmlspecialchars($e->getMessage());
    }

    $_SESSION['idBtq']=$infobtq['id_commerçant']??'';//Recuperation en session de l'id du commerçant ou id de la boutique
    $_SESSION['nomBtq']=$infobtq['nom_commerçant']??'';//Recuperation en session du nom du commerçant

    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action'])){
        $action=$_POST['action'];

        if($action==='MAJProfil'){
            $nomBoutique=htmlspecialchars(trim($_POST['boutique']?? ''));
            $description=htmlspecialchars(trim($_POST['description']?? ''));
            if(empty($nomBoutique) ||empty($description)){
                $_SESSION['errorMsgProfil']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Veuillez remplir tous les champs avant de sauvegarder!';
            }
            
            $profil = null;
            if (isset($_FILES["profil"]) && $_FILES["profil"]["error"] == 0 && !empty($_FILES["profil"]["name"])){
                $file_basename = pathinfo($_FILES["profil"]["name"], PATHINFO_FILENAME);//PATHINFO_FILENAME pour récupérer le nom de l'image
                $files_extension = strtolower(pathinfo($_FILES["profil"]["name"], PATHINFO_EXTENSION));//strlower pour recupérer l'extention et les infos de l'image
                $new_image_name = $file_basename . '_' . date("Ymdhis") . '.' . $files_extension;//Pour rennomer l'image avant de passer en bd
                if(move_uploaded_file($_FILES["profil"]["tmp_name"], '../ImagesBD/' . $new_image_name)){
                    $profil=$new_image_name;
                } 
                else{
                    $_SESSION['errorMsgProfil']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Erreur lors du téléchargement du logo!';  
                }
            }
            try{
                $stmt=$conn->prepare("UPDATE commerçant SET nom_boutique=?, description_boutique=?, profil_boutique=? WHERE id=?");
                $stmt->execute([$nomBoutique, $description, $profil, $btqID]);
                $_SESSION['successMsgProfil']='<i class="fa-solid fa-check-circle" style="color: green;"></i>Informations enregistrer avec success!';

                header("Location: Espacecommerçant.php?id=" . $id);
                exit();
            }
            catch(PDOException $e){
                $_SESSION['errorMsgProfil']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i> Erreur: ' .htmlspecialchars($e->getMessage());
            }        
        }     
    }
    $nomBtq=$infobtq['nom_boutique']?: "Ma boutique";
    $desc=$infobtq['description_boutique']?: "Votre description";
    $nomCom=$infobtq['nom_commerçant']?: "Nom du commerçant";
    $logo= !empty($infobtq['profil_boutique']) ? $infobtq['profil_boutique']??'':"../Image/astronaut.svg";
    $prenomCom=$infoUsers['prenom']?: " ";
    $comNom=$infoUsers['nom']?:"";
    $comEmail=$infoUsers['email']?:"";
    $comTel=$infoUsers['tel']?:"";
    $comPass=$infoUsers['pass']?:"";
    $comDate=$infoUsers['dateNaiss']?:"";


                        /* ===========================================================
                        ================== PUBLICATION ARTICLE =======================
                        ============================================================== */

    $userID = $_SESSION['id']??'';
    $IDcom = null;
    if(isset($_GET['id']) && !empty($_GET['id'])) {
        $IDcom=$_GET['id'];
    } 
    else {
        try{
            $stmtcom = $conn->prepare("SELECT id_commerçant FROM commerçant WHERE id=?");
            $stmtcom->execute([$userID]);
            $commmerçant = $stmtcom->fetch();

            if($commmerçant) {
                $IDcom = $commmerçant['id_commerçant'];
            } 
            else{
                header('Location: connexion.php');
                exit(); 
            }
        } 
        catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }
    }

    if (!$IDcom) {
        header('Location: connexion.php');
        exit();
    }

    if(isset($_GET['supprimer_article'])){
        $codeA=$_GET['supprimer_article'];
        try{
            $stmt=$conn->prepare("DELETE FROM article WHERE id_article=?");
            $stmt->execute([$codeA]);
            $_SESSION['successMsg']='<i class="fa-solid fa-check-circle" style="color: green;"></i>Article supprimé avec succèss!';
        }
        catch(PDOException $e){
            $_SESSION['errorMsg']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Erreuur lors de la suppression!';
            $e->getMessage();
        }
    }

    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action_article'])){
        $action=$_POST['action_article'];
        $idCatArt=$_POST['id_cat']??'';
        $nomArt=htmlspecialchars($_POST['nomArt'])??'';
        $categorie=$_POST['categorie']??'';
        $prixUArt=htmlspecialchars($_POST['prix'])??'';
        $quantiteArt=htmlspecialchars($_POST['quantite'])??'';
        $descriptionArt=htmlspecialchars($_POST['desc'])??'';
        $id_article_invisible=$_POST['id_article_invisible']??'';
        $statutArt="Epuisé";

        if (empty($nomArt) || empty($prixUArt) || empty($quantiteArt) || empty($descriptionArt) || empty($categorie)) {
            $_SESSION['errorMsg']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Tous les champs sont obligatoires!';
        }

        $photoArt = $_POST['ancienne_photo']??null;
        if (isset($_FILES["photoArt"]) && $_FILES["photoArt"]["error"] == 0 && !empty($_FILES["photoArt"]["name"])){
            $file_basename = pathinfo($_FILES["photoArt"]["name"], PATHINFO_FILENAME);//PATHINFO_FILENAME pour récupérer le nom de l'image
            $files_extension = strtolower(pathinfo($_FILES["photoArt"]["name"], PATHINFO_EXTENSION));//strlower pour recupérer l'extention et les infos de l'image
            $new_image_name = $file_basename . '_' . date("Ymdhis") . '.' . $files_extension;//Pour rennomer l'image avant de passer en bd
            if(move_uploaded_file($_FILES["photoArt"]["tmp_name"], '../ImagesBD/' . $new_image_name)){
                $photoArt=$new_image_name;
            } 
            else{
                $_SESSION['errorMsg']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Erreur lors du téléchargement du logo!';
            }
        }

        try{
            if($action==='publier'){
                $codeArt="ART-".date("Y")."-".random_int(1000, 9999);
                $stmtInsertArt=$conn->prepare("INSERT INTO article (id_article, nom_article, desc_article, prix_article, photo_article, Statut, quantite_stock, id_cat, id_commerçant) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmtInsertArt->execute([$codeArt, $nomArt, $descriptionArt, $prixUArt, $photoArt, $statutArt, $quantiteArt, $categorie, $IDcom]);
                $_SESSION['successMsg']='<i class="fa-solid fa-check-circle" style="color: green;"></i>Article ajouté avec succès!';
            }
            else{
                $stmtUpdateArt=$conn->prepare("UPDATE article SET nom_article=?, desc_article=?, prix_article=?, photo_article=?, quantite_stock=?, id_cat=? WHERE id_article=? AND id_commerçant=?");
                $stmtUpdateArt->execute([$nomArt, $descriptionArt, $prixUArt, $photoArt, $quantiteArt, $categorie, $id_article_invisible, $IDcom]);
                $_SESSION['successMsg']='<i class="fa-solid fa-check-circle" style="color: green;"></i>Article modifié avec succès!';

            }  
        }
        catch(PDOException $e){
            $_SESSION['errorMsg']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Tous les champs sont obligatoires!';
        }
        header("Location: Espacecommerçant.php?id=" . $id);
        exit();
    }

                /* ===========================================================
                   ================== AUGMENTER PHOTO ARTICLE ================
                   =========================================================== */

    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['id_art_photo'])){
        $id_article_photo=$_POST['id_art_photo'];
        if(isset($_FILES['details_photo'])&& !empty($_FILES['details_photo']['name'][0])){
            $files=$_FILES['details_photo'];
            $nombreFichier=count(array_filter($files['name']));//Compte le fichiers envoyer

            if($nombreFichier>2){
                $_SESSION['errorAddFile']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Vous ne pouvez pas evoyer plus de 2 photos!';
                header('Location: Espacecommerçant.php?id=' . $id . '&success=1#panel-articles');
                exit();
            }

            $photos=[null, null]; 
            for($i=0; $i<min(count($files['name']), 2); $i++){
                if($files['error'][$i]===0){
                    $nomDet="det_".time()."_".$i.".".pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                    if(move_uploaded_file($files['tmp_name'][$i], "../ImagesBD/".$nomDet)){
                        $photos[$i]=$nomDet;
                    }
                }
            }
            if($photos[0]!==null || $photos[1]!==null){
                $sqlPhotos="UPDATE article SET photo2=?, photo3=? WHERE id_article=?";
                $stmtPhotos=$conn->prepare($sqlPhotos);
                $stmtPhotos->execute([$photos[0], $photos[1], $id_article_photo]);
                $_SESSION['successAddFile']='<i class="fas fa-check-circle"></i>Photo(s) ajoutée(s) avec succèss!';
            }
            else{
                $_SESSION['errorAddFile']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Erreur lors de l\'ajout!';
            }
            header('Location: Espacecommerçant.php?id=' . $id . '&success=1#panel-articles');
            exit();
        }
    }

                                        /* ===========================================================
                                           ================== MAJ INFO USERS =========================
                                           =========================================================== */

    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action_Info'])){
        $action=$_POST['action_Info'];
        $nom=htmlspecialchars($_POST['nomCom'])??'';
        $prenom=htmlspecialchars($_POST['prenomCom'])??'';
        $mail=htmlspecialchars($_POST['mailCom'])??'';
        $tel=htmlspecialchars($_POST['telCom'])??'';
        $date=htmlspecialchars($_POST['dateCom'])??'';
        $cpas=$_POST['cPassCom']??'';
        $pass=$_POST['passCom']??'';

        try{
            if($action==='MAJInfo'){
                if(empty($nom)|| empty($prenom)|| empty($mail)|| empty($tel)|| empty($pass)|| empty($cpas)){
                    $_SESSION['errorInfo']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Veuillez remplir tous les champs avant de modifier!';
                    header("Location: Espacecommerçant.php?id=" . $id);
                    exit();
                }
                if($cpas!==$pass){
                    $_SESSION['errorInfo']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Les deux mot de passe doivent correspondre!';
                    header("Location: Espacecommerçant.php?id=" . $id);
                    exit();
                }
                else{
                    $stmtUpdateInfo=$conn->prepare("UPDATE utilisateur SET nom=?, prenom=?, email=?, pass=?, tel=?, dateNaiss=? WHERE id=?");
                    $stmtUpdateInfo->execute([$nom, $prenom, $mail, $pass, $tel, $date, $btqID]);
                    $_SESSION['successInfo']='<i class="fa-solid fa-check-circle" style="color: green;"></i>information modifié avec succès!';
                }
            }
        }
        catch(PDOException $e){
            $_SESSION['errorInfo']="Erreur:".$e->getMessage();
        }
        header("Location: Espacecommerçant.php?id=" . $id);
        exit();
    }

                                            /* ===========================================================
                                            ===================== GESTION COMMANDE =======================
                                            ============================================================== */

    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action_commande'])){
        $id_cmd=$_POST['id_commande'];
        $type_action=$_POST['action_commande'];

        if($type_action==='preparer'){
            $updatePreparer=$conn->prepare("UPDATE commande SET statut='En Préparation' WHERE id_commande=?");
            $updatePreparer->execute([$id_cmd]);
            $_SESSION['successCmd']='<i class="fa-solid fa-check-circle" style="color: green;"></i>L\'état a modifié avec succès!';
            header("Location: Espacecommerçant.php?tab=com&openModal=".$id_cmd."&status=success");
        }
        if($type_action==='livrer'){
            $code_retrait=htmlspecialchars($_POST['code_retrait'])??'';

            if(empty($code_retrait)){
                $_SESSION['errorCmd']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Veuillez rentrez le code de livraison/récupération!';
                header ("Location: Espacecommerçant.php?tab=com&openModal=".$id_cmd."&status=success");
                exit();
            }

            $checkCode=$conn->prepare("SELECT Code_retrait FROM commande WHERE id_commande=?");
            $checkCode->execute([$id_cmd]);
            $result=$checkCode->fetch();
            

            if($result && $result['Code_retrait']===$code_retrait){
                $conn->beginTransaction();
                try{
                    $updateLivrer=$conn->prepare("UPDATE commande SET statut='Livré' WHERE id_commande=?");
                    $updateLivrer->execute([$id_cmd]);

                    $stmtItems=$conn->prepare("SELECT id_article, quantite_cmd FROM details_commande WHERE id_commande=?");
                    $stmtItems->execute([$id_cmd]);
                    $items=$stmtItems->fetchAll();

                    foreach($items as $item){
                        $updateStock=$conn->prepare("UPDATE article SET quantite_stock=quantite_stock-? WHERE id_article=?");
                        $updateStock->execute([$item['quantite_cmd'], $item['id_article']]);
                    }
                    $conn->commit();
                    $_SESSION['successCmd']='<i class="fa-solid fa-check-circle" style="color: green;"></i>Livraison éffectuée avec success!';
                    header("Location: Espacecommerçant.php?tab=com&openModal=".$id_cmd."&status=success");
                    exit();
                }
                catch(PDOException $e){
                    die("ERREUR SQL: ".$e->getMessage());
                    $conn->rollBack();
                    header("Location: Espacecommerçant.php");
                    exit();
                }
            }
            else{
                $_SESSION['errorCmd']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Code éronné pour cette commande!';
                header("Location: Espacecommerçant.php?id=" . $id . "&tab=com&openModal=" . $id_cmd . "&status=success");
                exit();
            }
        }
        if($type_action==='supprimer'){
            $motif=htmlspecialchars(trim($_POST['order_delete']))??'';
 
            $updateSupprimer=$conn->prepare("UPDATE commande set statut='Annulé', motif=? WHERE id_commande=?");
            $updateSupprimer->execute([$motif, $id_cmd]);

            $_SESSION['successCmd']='<i class="fa-solid fa-check-circle" style="color: green;"></i>Suppression effectué avec succès!';
            header("Location: Espacecommerçant.php?id=" . $id . "&tab=com&openModal=" . $id_cmd . "&status=success");
            exit();
            
        }
    }

    $listeArticles=[];
    if(isset($IDcom)){
        $afficherArticles=$conn->prepare("SELECT a.*, c.nom_cat 
                                          FROM article a, catégorie c
                                          WHERE a.id_cat=c.id_cat AND a.id_commerçant=?"); 
        $afficherArticles->execute([$IDcom]);
        $listeArticles=$afficherArticles->fetchAll(PDO::FETCH_ASSOC);
    }
    $categorie=$conn->query("SELECT * FROM catégorie")->fetchAll(PDO:: FETCH_ASSOC);

                            /* Affichage des avis */
    $sqlCom="SELECT avs.*, u.nom, u.prenom, u.id
             FROM avis avs, utilisateur u, client clt
             WHERE clt.id_client=avs.id_client AND clt.id=u.id AND avs.id_commerçant=?
             ORDER BY avs.date_avis DESC";
    $stmtCom=$conn->prepare($sqlCom);
    $stmtCom->execute([$IDcom]);
    $commentaires=$stmtCom->fetchAll();

    $notifications=[];
    $sqlNotifCmd="SELECT u.prenom, u.nom, cmd.Date_commande
                  FROM commande cmd, utilisateur u, client clt
                  WHERE cmd.id_client=clt.id_client AND u.id=clt.id AND cmd.id_commerçant=? AND cmd.Date_commande>=NOW()-INTERVAL 1 DAY";
    $resNotifCmd=$conn->prepare($sqlNotifCmd);
    $resNotifCmd->execute([$IDcom]);
    foreach($resNotifCmd->fetchAll() as $row){
        $notifications[]=[
            'type'=>'Nouvelle reservation: ',
            'titre'=>$row['prenom'].' '.$row['nom'],
            'message'=>' a reservé(e) un article.',
            'date'=>$row['Date_commande'],
            'icon'=>'fa-shopping-cart text-success'
        ];
    }
    $sqlNotifCom="SELECT u.prenom, u.nom, a.date_avis
                  FROM avis a, utilisateur u, client clt
                  WHERE a.id_client=clt.id_client AND u.id=clt.id AND a.date_avis>=NOW()-INTERVAL 1 DAY AND a.id_commerçant=?";
    $resNotifCom=$conn->prepare($sqlNotifCom);
    $resNotifCom->execute([$IDcom]);
    foreach($resNotifCom->fetchAll() as $row){
        $notifications[]=[
            'type'=>'Nouvel avis: ',
            'titre'=>$row['prenom'].' '.$row['nom'],
            'message'=>' a laissé(e) un avis sur votre boutique.',
            'date'=>$row['date_avis'],
            'icon'=>'fa-comment-dots text-warning'
        ];
    }
    $sqlNotifQnt="SELECT nom_article FROM article WHERE id_commerçant=? AND quantite_stock<10";
    $resNotifQnt=$conn->prepare($sqlNotifQnt);
    $resNotifQnt->execute([$IDcom]);
    foreach($resNotifQnt->fetchAll() as $row){
        $notifications[]=[
            'type'=>'Alerte stock: ',
            'titre'=>'Stock faible pour'.$row['nom_article'],
            'message'=>'!',
            'date'=>date('Y-m-d H:i:s'),
            'icon'=>'fa-exclamation-triangle text-dark'
        ];
    }
    usort($notifications, function ($a, $b) {return strtotime($b['date'])-strtotime($a['date']);});

    $sqlFav="SELECT COUNT(f.id_article) as fav 
             FROM favoris f, article a
             WHERE f.id_article=a.id_article AND id_commerçant=?";
    $stmtFav=$conn->prepare($sqlFav);
    $stmtFav->execute([$IDcom]);
    $resultatFav=$stmtFav->fetch();
    $resFav=$resultatFav['fav'];

    $commandes=[];
    try{
        $stmtCmd=$conn->prepare("SELECT cmd.*, u.nom, u.prenom, u.id
                                 FROM  commande cmd, utilisateur u, client clt
                                 WHERE cmd.id_client=clt.id_client AND clt.id=u.id AND cmd.id_commerçant=?
                                 ORDER BY cmd.statut ASC, cmd.Date_commande DESC");
        $stmtCmd->execute([$IDcom]);
        $commandes=$stmtCmd->fetchAll(PDO::FETCH_ASSOC);
        $totalCmd=count($commandes);
    }
    catch(PDOException $e){
        $_SESSION['errorInfo']="Erreur:".$e->getMessage();
    }
    
?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Espace personnel - Leboutiquier</title>
        <link rel="stylesheet" href="../fontawesome/css/all.min.css">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../style/Espacecommerçant.css">
        <link rel="stylesheet" href="../style/Espacecommerçant2.css">
        <!-- PWA -->
        <link rel="manifest" href="../vendor/manifest.json">
        <meta name="theme-color" content="#EA580C">
        <link rel="icon" href="../Image/favoicon.ico" sizes="48x48">
        <link rel="apple-touch-icon" href="../Image/logo.png">
    </head>
    <body>
        <?php include 'splash.php'; ?>
        <!-- === NAVBAR DU HAUT (flèche retour, titre, cloche desktop) === -->
        <nav class="fixed-top-navbar">
            <!-- Flèche retour -->
            <button class="btn-back" onclick="window.location.href='accueil.php';" title="Accueil"><i class="fas fa-home"></i></button>
            <!-- Titre central (remplace "Notifications") -->
            <div class="navbar-main-title">Le boutiquier</div>
            <!-- Cloche notifications (seulement affichée sur desktop) -->
            <button class="notif-bell-top" id="notifBellTop" type="button" aria-expanded="false" aria-haspopup="true" style="margin-left:auto;">
                <i class="fas fa-bell"></i>
                <!-- COMMENTAIRE PHP : ici, afficher dynamiquement le nombre de notifications non lues -->
                <?php if(count($notifications)>0): ?>
                    <span class="notif-dot" id="notif-count-top"><?= count($notifications) ?></span>
                <?php endif; ?>
            </button>
            <!-- Dropdown des notifications (desktop) -->
            <ul class="dropdown-menu-notif-top" id="notifDropdownMenuTop">
                <li class="notif-title">Notifications</li>
                <!-- COMMENTAIRE PHP : Boucle foreach ici sur notifications pour afficher chaque notification desktop -->
                <?php foreach($notifications as $notif): ?>
                    <li class="notif-item important"<?= ($notif['type']=='Nouvelle reservation: ')?'bg-warning-light':'' ?> style="<?= ($notif['type']=='Nouvelle reservation: ')?'background-color: #fdf5e6;':''?>">
                        <i class="fas <?= $notif['icon'] ?> me-1"></i>
                        <span><?= $notif['type'] ?><strong><?= $notif['titre'] ?></strong><?= $notif['message'] ?></span> <br>
                        <small class="text-muted"><?= tempsEcoule($notif['date']) ?></small>
                    </li>
                <?php endforeach; ?>
                <?php if(empty($notifications)): ?>
                    <p style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Aucune notification pour l'instant!</p>
                <?php endif; ?>
                <!-- COMMENTAIRE PHP : Fin boucle notifications -->
            </ul>
        </nav>

        <!-- === BARRE DE NAVIGATION BASSE (mobile/tablette seulement) === -->
        <nav class="leb-bottom-navbar d-lg-none" id="mobileNavbar">
            <!-- Onglet profil -->
            <a class="nav-link active" id="mobile-profile-tab" href="#" data-tab="profile" aria-controls="mobile-profile-panel" aria-selected="true">
                <i class="fas fa-user"></i>
                <span class="nav-label">Profil</span>
            </a>
            <!-- Onglet articles -->
            <a class="nav-link" id="mobile-articles-tab" href="#" data-tab="articles" aria-controls="mobile-articles-panel" aria-selected="false">
                <i class="fas fa-box"></i>
                <span class="nav-label">Articles</span>
            </a>
            <!-- Onglet commentaires -->
            <a class="nav-link" id="mobile-comments-tab" href="#" data-tab="comments" aria-controls="mobile-comments-panel" aria-selected="false">
                <i class="fas fa-comment-alt"></i>
                <span class="nav-label">Commentaires</span>
            </a>
            
            <!-- Onglet commandes (Ajouté) -->
            <a class="nav-link" id="mobile-orders-tab" href="#" data-tab="orders" aria-controls="mobile-orders-panel" aria-selected="false">
                <i class="fas fa-shopping-bag"></i>
                <span class="nav-label">Commandes</span>
            </a>
            <!-- Onglet notifications (mobile, panel) -->
            <a class="nav-link" id="mobile-notif-tab" href="#" data-tab="notif" aria-controls="mobile-notif-panel" aria-selected="false">
                <i class="fas fa-bell"></i>
                <span class="nav-label">Cloche</span>
                <!-- COMMENTAIRE PHP : ici, afficher dynamiquement le nb notifications non lues mobile -->
                <?php if(count($notifications)>0): ?>
                    <span class="notif-dot-mobile" id="notif-count-mobile"><?= count($notifications) ?></span>
                <?php endif; ?>
            </a>
        </nav>


                                <!-- ============================================================
                                ======== DEBUT CONTENU MOBILE UNIQUEMENT =======================                                                      
                                ================================================================ -->                                
        <div id="mobile-content-panels">
            <!-- Panel profil (mobile/tablette) -->
            <div id="mobile-profile-panel" class="mobile-panel">
                <div class="card profile-card p-4 text-center position-relative">
                    <form action="Espacecommerçant.php?id=<?= $_GET['id'] ?>" method="post" enctype="multipart/form-data">
                        <div class="mb-2 position-relative d-inline-block">
                                <!-- COMMENTAIRE PHP : Affichage photo de profil de l'utilisateur -->
                            <img src="../ImagesBD/<?= htmlspecialchars($logo); ?>" alt="Profil" class="profile-avatar" id="profile-img-mobile">
                            <label for="profile-upload-mobile" class="edit-profile-pic" title="Changer la photo de profil"><i class="fas fa-camera"></i></label>
                            <input type="file" id="profile-upload-mobile" name="profil" class="d-none" accept="image/*" required>
                        </div>
                            <!-- COMMENTAIRE PHP : Affichage pseudo/nom commerçant -->
                        <h4 class="mb-1"><?= htmlspecialchars($comNom).' '.htmlspecialchars($prenomCom); ?></h4>
                        <div class="mb-3 text-muted"><input type="text" class="text-center" style="border: none; outline: none;" value="<?= htmlspecialchars($nomBtq); ?>" name="boutique"><i class="fa-pencil fas" style="cursor: pointer;"></i></div>
                            <!-- COMMENTAIRE PHP : Ici formulaire de description à sauvegarder -->
                        <div id="desc-form-mobile">
                                <!-- COMMENTAIRE PHP : Pré-remplir la description selon user -->
                            <textarea class="desc-textarea" id="desc-mobile" name="description" required><?= htmlspecialchars($desc); ?></textarea>
                            <button class="btn btn-orange btn-sm" type="submit" name="action" value="MAJProfil"><i class="fas fa-save"></i> Sauvegarder</button>
                        </div>
                        <!-- COMMENTAIRE PHP : Affichage du nombre de favoris (favoris mis par d'autres utilisateurs) -->
                        <div class="mt-2 fav-counter">
                            <i class="fas fa-heart accent-color"></i>
                            <span id="fav-count-mobile" class="fw-bold"><?= $resFav ?></span> mis en favoris
                        </div>
                        <?php if (!empty($_SESSION['errorMsg'])): ?>
                            <span id="JsErrorMsg" class="text-danger text-center text-dismissible" style="font-weight: bold;">
                        <?php 
                                echo $_SESSION['errorMsg'];
                        ?>
                            </span>
                        <?php endif;  ?><br>
                        <?php if (!empty($_SESSION['successMsg'])): ?>
                            <span id="JsSuccessMsg" class="text-success text-center text-dismissible" style="font-weight: bold;">
                        <?php 
                                echo $_SESSION['successMsg'];
                        ?>
                            </span>
                        <?php endif;  ?><br>
                    </form><br><br>
                    <form action="Espacecommerçant.php?id=<?= $_GET['id'] ?>" method="post" enctype="multipart/form-data">
                        <h4 class="mb-1" style="color: var(--leb-orange);">Modifier vos Informations</h4><br>
                        <?php if (!empty($_SESSION['errorInfo'])): ?>
                                <span id="JsErrorMsg" class="text-danger text-center text-dismissible fw-bold">
                        <?php 
                                    echo $_SESSION['errorInfo'];
                        ?>
                                </span>
                        <?php endif;  ?><br>
                        <?php if (!empty($_SESSION['successInfo'])): ?>
                                <span id="JsSuccessMsg" class="text-success text-center text-dismissible" style="font-weight: bold;">
                        <?php 
                                    echo $_SESSION['successInfo'];
                        ?>
                                </span>
                        <?php endif;  ?>
                        <div class="mb-2 row">
                            <label class="form-label text-start">Nom</label>
                            <input type="text" class="form-control" name="nomCom" id="nomCom" value="<?= htmlspecialchars($comNom)?>" required>
                        </div>
                        <div class="mb-2 row">
                            <label class="form-label text-start">Prénom</label>
                            <input type="text" class="form-control" name="prenomCom" id="prenomCom" value="<?= htmlspecialchars($prenomCom) ?>" required>
                        </div>
                        <div class="mb-2 row">
                            <label class="form-label text-start">E-mail</label>
                            <input type="text" class="form-control" name="mailCom" id="mailCom" value="<?= htmlspecialchars($comEmail) ?>" required>
                        </div>
                        <div class="mb-2 row">
                            <label class="form-label text-start">Numéro de téléphone</label>
                            <input type="number" class="form-control" name="telCom" id="telCom" value="<?= htmlspecialchars($comTel) ?>" required>
                        </div>
                        <div class="mb-2 row">
                            <label class="form-label text-start">Date de naissance</label>
                            <input type="date" class="form-control" name="dateCom" id="dateCom" value="<?= htmlspecialchars($comDate) ?>" required>
                        </div>
                        <div class="mb-2 row">
                            <label class="form-label text-start">Mot de passe</label>
                            <input type="text" class="form-control" name="passCom" id="passCom" value="<?= htmlspecialchars($comPass) ?>" required>
                        </div>
                        <div class="mb-2 row">
                            <label class="form-label text-start">Confirmer mot de Passe</label>
                            <input type="text" class="form-control" name="cPassCom" id="cPassCom" value="<?= htmlspecialchars($comPass) ?>" required>
                        </div><br>
                        <div id="desc-form">
                            <button class="btn btn-orange btn-sm" type="submit" name="action_Info" value="MAJInfo"><i class="fa-pencil fas"></i> Modifier</button>
                        </div>
                    </form>
                </div>
            </div> 
            <!-- Panel articles (mobile/tablette) -->
            <div id="mobile-articles-panel" class="mobile-panel" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-2" id="panel-articles">
                    <h5 class="mb-0">Vos articles</h5>
                    <button class="btn btn-orange btn-sm" data-bs-toggle="modal" data-bs-target="#modalAjoutArticle"><i class="fas fa-plus"></i> Publier un article</button>
                </div><br>
                <?php if(!empty($_SESSION['successAddFile'])): ?>
                            <p id="statutPhotos" class="text-center fw-bold text-success">
                <?php 
                                echo $_SESSION['successAddFile'];
                ?>
                            </p>
                <?php endif;?>
                <?php if(!empty($_SESSION['errorAddFile'])): ?>
                            <p id="statutPhotos" class="text-center fw-bold text-danger">
                <?php 
                                echo $_SESSION['errorAddFile'];
                ?>
                            </p>
                <?php endif;?>
                <div class="row g-3">
                    <!-- COMMENTAIRE PHP : Boucle foreach ici pour afficher tous les articles de l'utilisateur (mobile) -->
                    <?php  foreach ($listeArticles as $article): ?>
                        <div class="col-12">
                            <div class="card article-card p-2">
                                <div class="row align-items-center g-2">
                                    <div class="col-4">
                                        <img src="../ImagesBD/<?= htmlspecialchars($article['photo_article']); ?>" class="img-fluid rounded" alt="Produit">
                                    </div>
                                    <div class="col-8">
                                        <div class="fw-bold mb-1"><?= htmlspecialchars($article['nom_article']); ?></div>
                                        <div class="text-muted small mb-1"><?= htmlspecialchars($article['nom_cat']); ?></div>
                                        <div><strong><?= number_format($article['prix_article'], 0, '', ' ') ?> FCFA</strong></div>
                                        <div>
                                            <?php if($article['quantite_stock']<10): ?>
                                                <span class="badge bg-warning text-dark mt-1"><i class="fas fa-exclamation-triangle"></i> Stock bas</span>
                                            <?php else: ?>
                                                <span class="badge bg-success text-dark mt-1"><i class="fa-solid fa-check-circle"></i>Disponible</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="d-flex gap-1 mt-2">
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" name="action_article" data-bs-target="#modalAjoutArticle" id="modifier"  
                                                data-id="<?= htmlspecialchars($article['id_article']); ?>"
                                                data-nom="<?= htmlspecialchars($article['nom_article']); ?>"
                                                data-pu="<?= htmlspecialchars($article['prix_article']); ?>"
                                                data-qnt="<?= htmlspecialchars($article['quantite_stock']); ?>"
                                                data-desc="<?= htmlspecialchars($article['desc_article']); ?>"
                                                data-photo="'../ImagesBD/<?= htmlspecialchars($article['photo_article']); ?>'" onclick="modifierArt.call(this)"><i class="fas fa-pen"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Voulez-vous vraiment supprimer cet article?')"><a href="Espacecommerçant.php?supprimer_article=<?=$article['id_article']?>"><i class="fa-solid fa-trash text-danger" id="supp" style=":hover:color: white;"></i></a></button>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <form action="Espacecommerçant.php?id=<?= $_GET['id'] ?>" method="post" enctype="multipart/form-data">
                                                <input type="hidden" name="id_art_photo" value="<?= $article['id_article']; ?>">
                                                <input type="file" name="details_photo[]" id="file_<?= $article['id_article']; ?>" multiple accept="image/*" class="d-none" onchange="afficherApercu(this)">
                                                <button class="btn btn-sm btn-outline-primary fw-bold" type="button" onclick="document.getElementById('file_<?= $article['id_article']; ?>').click()"><i class="fas fa-plus"></i> Ajouter des photos</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach;  ?>
                    <?php if(empty($listeArticles)){ ?>
                            <p style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Aucun Publié pour l'instant</p>
                    <?php 
                        }
                    ?>
                    <!-- COMMENTAIRE PHP : Fin de la boucle des articles mobile -->
                </div>
            </div>
            <!-- Panel commentaires (mobile/tablette) -->
            <div id="mobile-comments-panel" class="mobile-panel" style="display: none;">
                <h5 class="mb-3">Commentaires sur votre boutique</h5>
                <!-- COMMENTAIRE PHP : Boucle foreach sur les commentaires reçus (mobile) -->
                <?php foreach($commentaires as $com):  
                    $idNum=abs(crc32($com['id']));
                    $photoIndex=($idNum%4)+1;
                    $photoPath="../UsersImages/User".$photoIndex.".png";

                    $nom=trim($com['nom']);
                    $initialeNom=mb_strtoupper(mb_substr($nom, 0, 1)).'.';
                    $prenomMaj=ucfirst(mb_strtolower(htmlspecialchars($com['prenom'])));

                    $dateObjet=new DateTime($com['date_avis']);
                    $dateFrer=$formatter->format($dateObjet);

                    $noteActive=$com['note'];
                ?>
                    <div class="card mb-3 border-0 shadow-sm p-3">
                        <div class="d-flex align-items-center mb-2">
                            <!-- COMMENTAIRE PHP : avatar auteur commentaire -->
                            <img src="<?= $photoPath ?>" style="width:38px;height:38px;border-radius:50%;" alt="avatar">
                            <div class="ms-2">
                                <!-- COMMENTAIRE PHP : nom/pseudo + date -->
                                <div class="fw-bold"><?=$prenomMaj.' '.$initialeNom?></div>
                                <div class="text-muted small">le <?= $dateFrer ?></div>
                            </div>
                        </div>
                        <!-- COMMENTAIRE PHP : texte du commentaire -->
                        <div class="px-2"><?= $com['commentaire'] ?></div><br>
                        <span class="star-rating" style="font-size:0.7em;color:#f7b930;">
                            <?php for($i=1; $i<=5; $i++):  
                                if($i<=$noteActive):
                            ?>
                                    <i class="fas fa-star"></i>
                                <?php else: ?>
                                    <i class="far fa-star"></i>
                                <?php endif;  ?>
                            <?php endfor;  ?>
                        </span>
                    </div>
                <?php endforeach;  ?>
                <?php if(empty($commentaires)): ?>
                    <p style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Vous n'avez aucun commentaire pour l'instant!</p>
                <?php endif; ?>
                <!-- COMMENTAIRE PHP : Fin boucle commentaires mobile -->
            </div>
            
            
            <!-- Panel commandes (mobile/tablette) - MODERNISÉ -->
            <div id="mobile-orders-panel" class="mobile-panel" style="display: none;">
                <div class="px-3 pt-3">
                    <h5 class="fw-bold mb-3">Mes Commandes</h5>
                    <?php foreach ($commandes as $cmd): 
                        $selectArticle=$conn->prepare("SELECT dtl.*, a.nom_article, a.photo_article, a.prix_article, a.id_article, cmd.frais_livraison
                                                        FROM details_commande dtl, article a, commande cmd
                                                        WHERE dtl.id_article=a.id_article AND cmd.id_commande=dtl.id_commande AND dtl.id_commande=?");
                        $selectArticle->execute([$cmd['id_commande']]);
                        $articlesCmd=$selectArticle->fetchAll(PDO::FETCH_ASSOC);
                        $articlesJSON=json_encode($articlesCmd);

                        if($cmd['frais_livraison']==0){
                            $total=$cmd['Montant_commande'];
                            $Livraison="Retrait en boutique";
                        }
                        else{
                            $total=$cmd['Montant_commande']-1500;
                            $Livraison=number_format($cmd['frais_livraison'], 0, '', ' ').' FCFA';
                        }
                    ?>
                    <div class="card order-card border-0 shadow-sm p-3 mb-3" data-id="<?= $cmd['id_commande'] ?>" onclick="openOrderDetails('<?= $cmd['id_commande'] ?>', '<?= $cmd['nom'].' ' ?> <?= $cmd['prenom'] ?>', '<?= date('d/m/Y', strtotime($cmd['Date_commande'])); ?>', '<?= number_format($total, 0, '', ' ') ?> FCFA', '<?= $cmd['statut'] ?>', '<?= number_format($cmd['Montant_commande'], 0, '', ' ') ?> FCFA', '<?= $cmd['adresse_livraison'] ?>', '<?= htmlspecialchars($Livraison) ?>', '<?= $cmd['numero_livraison'] ?>', <?= htmlspecialchars($articlesJSON, ENT_QUOTES, 'UTF-8') ?>)">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold">#<?= $cmd['id_commande'] ?></h6>
                                <small class="text-muted"><?= date('d/m/Y', strtotime($cmd['Date_commande'])); ?></small>
                            </div>
                            <?php if($cmd['statut']==='En attente'): ?>
                                <span class="status-badge status-pending"><?= $cmd['statut'] ?></span>
                            <?php endif; ?>
                            <?php if($cmd['statut']==='Annulé'): ?>
                                <span class="status-badge status-cancelled "><?= $cmd['statut'] ?></span>
                            <?php endif; ?>
                            <?php if($cmd['statut']==='En Préparation'): ?>
                                <span class="status-badge status-shipped"><?= $cmd['statut'] ?></span>
                            <?php endif; ?>
                            <?php if($cmd['statut']==='Livré' && $cmd['frais_livraison']!=0):?>
                                <span class="status-badge status-delivered"><?= $cmd['statut'] ?></span>
                            <?php endif; ?>
                            <?php if($cmd['statut']==='Livré' && $cmd['frais_livraison']==0): ?>
                                <span class="status-badge status-delivered">Retiré</span>
                            <?php endif; ?>
                        </div>
                        <hr class="my-2 opacity-25">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><?= $cmd['prenom']." ".$cmd['nom'] ?></small>
                            <div class="fw-bold text-orange"><?= number_format($cmd['Montant_commande'], 0, '', ' ') ?> FCFA</div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if(empty($commandes)): ?>
                        <p style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Vous n'avez aucune commandes pour l'instant!</p>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Panel notifications (mobile/tablette) -->
            <div id="mobile-notif-panel" class="mobile-panel" style="display: none;">
                <ul class="dropdown-menu dropdown-menu-notif show" style="position:static;float:none;min-width: 100%; box-shadow:none; border-radius:12px; border-width:0 0 1.5px 0;">
                    <li class="notif-title">Notifications</li>
                    <!-- COMMENTAIRE PHP : Boucle foreach ici sur notifications pour afficher chaque notification mobile -->
                    <?php foreach($notifications as $notif): ?>
                        <li class="notif-item important" <?= ($notif['type']=='Nouvelle reservation: ')?'bg-warning-light':'' ?> style="<?= ($notif['type']=='Nouvelle reservation: ')?'background-color: #fdf5e6;':''?>">
                            <i class="fas <?= $notif['icon'] ?> me-1"></i>
                            <span><?= $notif['type'] ?><strong><?= $notif['titre'] ?></strong><?= $notif['message'] ?></span> <br>
                            <small class="text-muted"><?= tempsEcoule($notif['date']) ?></small>
                        </li>
                    <?php endforeach; ?>
                    <?php if(empty($notifications)): ?>
                        <p style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Aucune notification pour l'instant!</p>
                    <?php endif; ?>
                    <!-- COMMENTAIRE PHP : Fin boucle notifications mobile -->
                </ul>
            </div>
        </div>
                                <!-- ============================================================
                                ======== FIN CONTENU MOBILE UNIQUEMENT ==========================                                                     
                                ================================================================ -->



                                <!-- ============================================================
                                ======== DEBUT CONTENU DESKTOP UNIQUEMENT =======================                                                      
                                ================================================================ -->
        <div class="container pt-2 pb-5 desktop-content" id="panel-articles">
            <div class="row">
                <!-- Bloc de profil (desktop) -->
                    <div class="col-lg-4 mb-4">
                        <div class="card profile-card p-4 text-center position-relative">
                            <form action="Espacecommerçant.php?id=<?= $_GET['id'] ?>" method="post" enctype="multipart/form-data">
                                    <div class="mb-2 position-relative d-inline-block">
                                        <!-- COMMENTAIRE PHP : Affichage photo de profil de l'utilisateur-->
                                        <img src="../ImagesBD/<?= htmlspecialchars($logo); ?>" alt="Profil" class="profile-avatar" id="profile-img">
                                        <label for="profile-upload" class="edit-profile-pic" title="Changer la photo de profil"><i class="fas fa-camera"></i></label>
                                        <input type="file" id="profile-upload" class="d-none" accept="image/*" name="profil">
                                    </div>
                                        <!-- COMMENTAIRE PHP : Affichage pseudo/nom commerçant -->
                                    <h4 class="mb-1" ><?= htmlspecialchars($comNom).' '.htmlspecialchars($prenomCom); ?></h4><i class="fa-solid fa-badge-check" style="color: #1DA1F2; cursor: pointer;"></i>
                                    <div class="mb-3 text-muted"><input type="text" class="text-center" style="border: none; outline: none;" value="<?= htmlspecialchars($nomBtq); ?>" name="boutique"><i class="fa-pencil fas" style="cursor: pointer;"></i></div>
                                        <!-- COMMENTAIRE PHP : Ici formulaire de description à sauvegarder -->
                                    <div id="desc-form">
                                        <textarea class="desc-textarea" id="desc"  name="description" required><?= htmlspecialchars($desc); ?></textarea>
                                        <button class="btn btn-orange btn-sm" type="submit" name="action" value="MAJProfil"><i class="fas fa-save"></i> Sauvegarder</button>
                                    </div>
                                <!-- COMMENTAIRE PHP : Affichage du nombre de favoris -->
                                <div class="mt-2 fav-counter">
                                    <i class="fas fa-heart accent-color"></i>
                                    <span id="fav-count" class="fw-bold"><?= $resFav ?></span> mis en favoris pour l'instant
                                </div>
                                <?php if (!empty($_SESSION['errorMsgProfil'])): ?>
                                    <span id="JsErrorMsg" class="text-danger text-center text-dismissible fw-bold">
                                        <?php 
                                            echo $_SESSION['errorMsgProfil'];
                                            unset($_SESSION['errorMsgProfil']);
                                        ?>
                                    </span>
                                <?php endif;  ?><br>
                                <?php if (!empty($_SESSION['successMsgProfil'])): ?>
                                    <span id="JsSuccessMsg" class="text-success text-center text-dismissible" style="font-weight: bold;">
                                        <?php 
                                            echo $_SESSION['successMsgProfil'];
                                            unset($_SESSION['successMsgProfil']);
                                        ?>
                                    </span>
                                <?php endif;  ?><br>
                            </form>
                        </div><br><br>
                        <!-- Bloc de profil (Vos informations) -->
                        <div class="card profile-card p-4 text-center position-relative">
                            <form action="Espacecommerçant.php?id=<?= $_GET['id'] ?>" method="post" enctype="multipart/form-data">
                                <h4 class="mb-1" style="color: var(--leb-orange);">Modifier vos Informations</h4><br>
                                <?php if (!empty($_SESSION['errorInfo'])): ?>
                                    <span id="JsErrorMsg" class="text-danger text-center text-dismissible fw-bold">
                                        <?php 
                                            echo $_SESSION['errorInfo'];
                                            unset($_SESSION['errorInfo']);
                                        ?>
                                    </span>
                                <?php endif;  ?><br>
                                <?php if (!empty($_SESSION['successInfo'])): ?>
                                    <span id="JsSuccessMsg" class="text-success text-center text-dismissible" style="font-weight: bold;">
                                        <?php 
                                            echo $_SESSION['successInfo'];
                                            unset($_SESSION['successInfo']);
                                        ?>
                                    </span>
                                <?php endif;  ?>
                                <div class="mb-2 row">
                                    <label class="form-label text-start">Nom</label>
                                    <input type="text" class="form-control" name="nomCom" id="nomCom" value="<?= htmlspecialchars($comNom) ?>" required>
                                </div>
                                <div class="mb-2 row">
                                    <label class="form-label text-start">Prénom</label>
                                    <input type="text" class="form-control" name="prenomCom" id="prenomCom" value="<?= htmlspecialchars($prenomCom) ?>" required>
                                </div>
                                <div class="mb-2 row">
                                    <label class="form-label text-start">E-mail</label>
                                    <input type="text" class="form-control" name="mailCom" id="mailCom" value="<?= htmlspecialchars($comEmail) ?>" required>
                                </div>
                                <div class="mb-2 row">
                                    <label class="form-label text-start">Numéro de téléphone</label>
                                    <input type="number" class="form-control" name="telCom" id="telCom" value="<?= htmlspecialchars($comTel) ?>" required>
                                </div>
                                <div class="mb-2 row">
                                    <label class="form-label text-start">Date de naissance</label>
                                    <input type="date" class="form-control" name="dateCom" id="dateCom" value="<?= htmlspecialchars($comDate) ?>" required>
                                </div>
                                <div class="mb-2 row">
                                    <label class="form-label text-start">Mot de passe</label>
                                    <input type="text" class="form-control" name="passCom" id="passCom" value="<?= htmlspecialchars($comPass) ?>" required>
                                </div>
                                <div class="mb-2 row">
                                    <label class="form-label text-start">Confirmer mot de Passe</label>
                                    <input type="text" class="form-control" name="cPassCom" id="cPassCom" value="<?= htmlspecialchars($comPass) ?>" required>
                                </div><br>
                                    <!-- COMMENTAIRE PHP : Ici formulaire de description à sauvegarder -->
                                <div id="desc-form">
                                    <button class="btn btn-orange btn-sm" type="submit" name="action_Info" value="MAJInfo"><i class="fa-pencil fas"></i> Modifier</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                <!-- Onglets à droite du profil (desktop) -->
                <div class="col-lg-8">
                    <ul class="nav nav-tabs mb-3" id="mainTabs" role="tablist">
                        <!-- Onglet articles -->
                        <li class="nav-item">
                            <button class="nav-link active" id="tab-articles" data-bs-toggle="tab" data-bs-target="#articles" type="button" role="tab" aria-controls="articles">Mes articles</button>
                        </li>
                        <!-- Onglet commentaires reçus -->
                        <li class="nav-item">
                            <button class="nav-link" id="tab-comments" data-bs-toggle="tab" data-bs-target="#comments" type="button" role="tab" aria-controls="comments">Commentaires reçus</button>
                        </li>
                        
                        <!-- Onglet commandes (Ajouté) -->
                        <li class="nav-item">
                            <button class="nav-link" id="tab-orders" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab" aria-controls="orders">Mes commandes</button>
                        </li>
                        <!-- Onglet notifications supprimé sur desktop -->
                    </ul>
                    <div class="tab-content" id="mainTabsContent">
                        <!-- Tab articles (desktop) -->
                        <div class="tab-pane fade show active" id="articles" role="tabpanel" aria-labelledby="tab-articles">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Vos articles</h5>
                                <button class="btn btn-orange btn-sm" data-bs-toggle="modal" data-bs-target="#modalAjoutArticle" id="publier"><i class="fas fa-plus"></i> Publier un article</button>
                            </div>
                            <?php if(!empty($_SESSION['successAddFile'])): ?>
                                    <div id="statutPhotos" class="alert alert-success alert-dismissible text-center">
                            <?php 
                                        echo $_SESSION['successAddFile'];
                                        unset($_SESSION['successAddFile']);
                            ?>
                                    </div>
                            <?php endif;?>
                            <?php if(!empty($_SESSION['errorAddFile'])): ?>
                                    <div id="statutPhotos" class="alert alert-danger alert-dismissible text-center">
                            <?php 
                                        echo $_SESSION['errorAddFile'];
                                        unset($_SESSION['errorAddFile']);
                            ?>
                                    </div>
                            <?php endif;?>
                            <?php if (!empty($_SESSION['successMsg'])): ?>
                                    <div id="JsErrorMsg" class="alert alert-success alert-dismissible text-center">
                            <?php 
                                        echo $_SESSION['successMsg'];
                                        unset($_SESSION['successMsg']);
                            ?>
                                    </div>
                            <?php endif;  ?>
                            <div class="row g-3">
                                <!-- COMMENTAIRE PHP : Boucle foreach ici pour afficher tous les articles desktop -->
                                <!-- Article exemple 1 -->
                                <?php  
                                        foreach ($listeArticles as $article): 
                                ?>
                                <div class="col-md-6">
                                    <div class="card article-card p-2">
                                        <div class="row align-items-center g-2">
                                            <div class="col-4">
                                                <img src="../ImagesBD/<?= htmlspecialchars($article['photo_article']); ?>" class="img-fluid rounded" alt="Produit">
                                            </div>
                                            <div class="col-8">
                                                <div class="fw-bold mb-1"><?= htmlspecialchars($article['nom_article']); ?></div>
                                                <div class="text-muted small mb-1"><?= htmlspecialchars($article['nom_cat']); ?></div>
                                                <div><strong><?= number_format($article['prix_article'], 0, '', ' ') ?> FCFA</strong></div>
                                                <div>
                                                    <?php if($article['quantite_stock']<10): ?>
                                                        <span class="badge bg-warning text-dark mt-1"><i class="fas fa-exclamation-triangle"></i> Stock bas</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success text-dark mt-1"><i class="fa-solid fa-check-circle"></i>Disponible</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="d-flex gap-1 mt-2">
                                                    <button class="btn btn-sm btn-outline-secondary" name="action_article" id="modifier"  onclick="modifierArt('<?= htmlspecialchars($article['id_article']); ?>', '<?= htmlspecialchars($article['nom_article']); ?>', '<?= htmlspecialchars($article['prix_article']); ?>', '<?= htmlspecialchars($article['quantite_stock']); ?>', '<?= htmlspecialchars($article['desc_article']); ?>', '<?= htmlspecialchars($article['nom_cat']); ?>', '../ImagesBD/<?= htmlspecialchars($article['photo_article']); ?>');"><i class="fas fa-pen"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Voulez-vous vraiment supprimer cet article?')"><a href="Espacecommerçant.php?supprimer_article=<?=$article['id_article']?>"><i class="fa-solid fa-trash text-danger" id="supp" style=":hover:color: white;"></i></a></button>
                                                </div><br>
                                                <div class="d-flex gap-1">
                                                    <form action="Espacecommerçant.php?id=<?= $_GET['id'] ?>" method="post" enctype="multipart/form-data">
                                                        <input type="hidden" name="id_art_photo" value="<?= $article['id_article']; ?>">
                                                        <input type="file" name="details_photo[]" id="file_<?= $article['id_article']; ?>" multiple accept="image/*" class="d-none" onchange="afficherApercu(this)">
                                                        <button class="btn btn-sm btn-outline-primary fw-bold" type="button" onclick="document.getElementById('file_<?= $article['id_article']; ?>').click()"><i class="fas fa-plus"></i> Ajouter des photos</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach;  ?>
                                <?php if(empty($listeArticles)){ ?>
                                        <p style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Aucun article Publié pour l'instant</p>
                                <?php 
                                    }
                                ?>
                                <!-- COMMENTAIRE PHP : Fin boucle des articles desktop --> 
                            </div>
                        </div>
                        <!-- Tab commentaires (desktop) -->
                        <div class="tab-pane fade" id="comments" role="tabpanel" aria-labelledby="tab-comments">
                            <h5 class="mb-3">Commentaires sur votre boutique</h5>
                            <!-- COMMENTAIRE PHP : Boucle foreach sur les commentaires reçus (desktop)-->
                            <?php foreach($commentaires as $com):  
                                $idNum=abs(crc32($com['id']));
                                $photoIndex=($idNum%4)+1;
                                $photoPath="../UsersImages/User".$photoIndex.".png";

                                $nom=trim($com['nom']);
                                $initialeNom=mb_strtoupper(mb_substr($nom, 0, 1)).'.';
                                $prenomMaj=ucfirst(mb_strtolower(htmlspecialchars($com['prenom'])));

                                $dateObjet=new DateTime($com['date_avis']);
                                $dateFrer=$formatter->format($dateObjet);

                                $noteActive=$com['note'];
                            ?>
                                <div class="card mb-3 border-0 shadow-sm p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="<?= $photoPath ?>" style="width:38px;height:38px;border-radius:50%;" alt="avatar">
                                        <div class="ms-2">
                                            <div class="fw-bold"><?=$prenomMaj.' '.$initialeNom?></div>
                                            <div class="text-muted small">le <?= $dateFrer ?></div>
                                        </div>
                                    </div>
                                    <div class="px-2"><?= $com['commentaire'] ?></div><br>
                                    <span class="star-rating" style="font-size:0.7em;color:#f7b930;">
                                        <?php for($i=1; $i<=5; $i++):  
                                            if($i<=$noteActive):
                                        ?>
                                                <i class="fas fa-star"></i>
                                            <?php else: ?>
                                                <i class="far fa-star"></i>
                                            <?php endif;  ?>
                                        <?php endfor;  ?>
                                    </span>
                                </div>
                            <?php endforeach;  ?>
                            <?php if(empty($commentaires)): ?>
                                <p style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Vous n'avez aucun commentaire pour l'instant!</p>
                            <?php endif; ?>
                            <!-- COMMENTAIRE PHP : Fin boucle commentaires desktop -->
                        </div>
                        
                        <!-- Tab commandes (desktop) - MODERNISÉ -->
                        <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="tab-orders">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold">Gestion des Commandes</h5>
                                <span class="badge bg-light text-dark border">Total: <?= $totalCmd ?> commande(s)</span>
                            </div>
                            
                            <div class="row g-3">
                                <?php foreach ($commandes as $cmd): 
                                    $selectArticle=$conn->prepare("SELECT dtl.*, a.nom_article, a.photo_article, a.prix_article, a.id_article, cmd.frais_livraison
                                                                   FROM details_commande dtl, article a, commande cmd
                                                                   WHERE dtl.id_article=a.id_article AND cmd.id_commande=dtl.id_commande AND dtl.id_commande=?
                                                                    ");
                                    $selectArticle->execute([$cmd['id_commande']]);
                                    $articlesCmd=$selectArticle->fetchAll(PDO::FETCH_ASSOC);
                                    $articlesJSON=json_encode($articlesCmd);

                                    if($cmd['frais_livraison']==0){
                                        $total=$cmd['Montant_commande'];
                                        $Livraison="Retrait en boutique";
                                    }
                                    else{
                                        $total=$cmd['Montant_commande']-$cmd['frais_livraison'];
                                        $Livraison=number_format($cmd['frais_livraison'], 0, '', ' ').' FCFA';
                                    }

                                ?>
                                    <div class="col-12">
                                        <div class="card order-card border-0 shadow-sm p-3 item-commande" data-id="<?= $cmd['id_commande'] ?>" onclick="openOrderDetails('<?= $cmd['id_commande'] ?>', '<?= $cmd['nom'].' ' ?> <?= $cmd['prenom'] ?>', '<?= date('d/m/Y', strtotime($cmd['Date_commande'])); ?>', '<?= number_format($total, 0, '', ' ') ?> FCFA', '<?= $cmd['statut'] ?>', '<?= number_format($cmd['Montant_commande'], 0, '', ' ') ?> FCFA', '<?= $cmd['adresse_livraison'] ?>', '<?= $Livraison ?>', '<?= $cmd['numero_livraison'] ?>', <?= htmlspecialchars($articlesJSON, ENT_QUOTES, 'UTF-8') ?>)">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="order-icon bg-light p-3 rounded-circle me-3">
                                                        <i class="fas fa-shopping-bag text-orange"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">#<?= $cmd['id_commande'] ?></h6>
                                                        <small class="text-muted">Par <?= $cmd['prenom']." ".$cmd['nom'] ?> • <?= date('d/m/Y', strtotime($cmd['Date_commande'])); ?></small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="fw-bold mb-1"><?= number_format($cmd['Montant_commande'], 0, '', ' ') ?> FCFA</div>
                                                    <?php if($cmd['statut']==='En attente'): ?>
                                                        <span class="status-badge status-pending"><?= $cmd['statut'] ?></span>
                                                    <?php endif; ?>
                                                    <?php if($cmd['statut']==='Annulé'): ?>
                                                        <span class="status-badge status-cancelled "><?= $cmd['statut'] ?></span>
                                                    <?php endif; ?>
                                                    <?php if($cmd['statut']==='En Préparation'): ?>
                                                        <span class="status-badge status-shipped"><?= $cmd['statut'] ?></span>
                                                    <?php endif; ?>
                                                    <?php if($cmd['statut']==='Livré' && $cmd['frais_livraison']!=0):?>
                                                        <span class="status-badge status-delivered"><?= $cmd['statut'] ?></span>
                                                    <?php endif; ?>
                                                    <?php if($cmd['statut']==='Livré' && $cmd['frais_livraison']==0): ?>
                                                        <span class="status-badge status-delivered">Retiré</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php if(empty($commandes)): ?>
                                    <p style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Vous n'avez aucune commandes pour l'instant!</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- Onglet notifications supprimé du desktop -->
                    </div>
                </div>
            </div>
        </div>
                                <!-- ============================================================
                                ======== FIN CONTENU DESKTOP UNIQUEMENT =========================                                                      
                                ================================================================ -->



                                <!-- ============================================================
                                ======== MODALS POUR AJOUT/EDITION ARTICLE ======================                                                       
                                ================================================================ -->
        <div class="modal fade" id="modalAjoutArticle" tabindex="-1" aria-labelledby="modalAjoutArticleLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" id="formArticle" method="post" enctype="multipart/form-data" action="Espacecommerçant.php?id=<?= $_GET['id'] ?>">
                    <input type="hidden" name="ancienne_photo" id="ancienne_photo">
                    <input type="hidden" name="id_article_invisible" id="id_article_invisible">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAjoutArticleLabel">Publier un article</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <?php if (!empty($_SESSION['errorMsg'])): ?>
                            <span id="JsErrorMsg" class="text-danger fw-bold text-center text-dismissible">
                    <?php 
                                echo $_SESSION['errorMsg'];
                                unset($_SESSION['errorMsg']);
                    ?>
                            </span>
                    <?php endif;  ?> 
                    <div class="modal-body">
                        <!-- COMMENTAIRE PHP : Formulaire pour ajouter un nouvel article (traitement en PHP en POST) -->
                            <div class="mb-2">
                                <label class="form-label">Titre</label>
                                <input type="text" class="form-control" name="nomArt" id="nomArt" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Catégorie</label>
                                <select class="form-select" name="categorie" id="cat"> 
                                    <option  selected disabled>Choisissez la catégorie</option>
                                    <?php 
                                        foreach($categorie as $cat):
                                    ?>
                                            <option value="<?= htmlspecialchars($cat['id_cat']) ?>"><?= htmlspecialchars($cat['nom_cat']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Prix (FCFA)</label>
                                <input type="number" class="form-control" name="prix" id="prixUArt" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Quantité en Stock</label>
                                <input type="number" class="form-control" name="quantite" id="quantite" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Photo</label>
                                <input type="file" class="form-control" name="photoArt" id="photo" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" rows="2" name="desc" id="description" required></textarea>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetForm()">Annuler</button>
                        <button type="submit" class="btn btn-orange" id="btnPublier"  name="action_article" value="publier">Publier</button>
                    </div>
                </form>
            </div>
        </div>

    
                                <!-- ============================================================
                                ======== MODALS POUR GESTION DES COMMANDES ======================                                                       
                                ================================================================ -->
        <div class="modal fade" id="modalOrderDetails" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="orderTitle">Détails Commande</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="window.location.href='Espacecommerçant.php';"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-between mb-4">
                            <div>
                                <small class="text-muted d-block">Client</small>
                                <span class="fw-bold" id="orderClient"></span>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Date</small>
                                <span class="fw-bold" id="orderDate"></span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <div>
                                <small class="text-muted d-block">Adresse</small>
                                <span class="fw-bold" id="Adresse">Rue de la Joie</span>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Numéro de téléphone</small>
                                <span class="fw-bold" id="Numero">692238528</span>
                            </div>
                        </div>

                        <hr class="my-4">
                        
                        <h6 class="fw-bold mb-3">Articles</h6>
                        <div id="orderItemsList">
                            
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="mb-0 fw-bold">Total</h6>
                            <h5 class="mb-0 fw-bold text-orange" id="orderTotal"></h5>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="mb-0 fw-bold">Frais de livraison</h6>
                            <h5 class="mb-0 fw-bold text-primary" id="orderFrais"></h5>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="mb-0 fw-bold">Grand Total</h6>
                            <h5 class="mb-0 fw-bold text-orange" id="grandTotal"></h5>
                        </div>

                        <hr class="my-4">
                        
                        <p style="color: gray; text-align:center;" id="section-message-livre"><i class="fa-solid fa-circle-exclamation"></i></p>
                        <div id="section-formulaire-commande">
                            <h6 class="fw-bold mb-3">Changer l'état</h6> 
                            <form action="Espacecommerçant.php?id=<?= $_GET['id'] ?>" method="post"> 
                                <input type="hidden" name="id_commande" id="id_commande">
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <button class="btn btn-outline-primary btn-sm btn-state" type="submit" name="action_commande" value="preparer" id="btn-preparer">En Préparation</button>
                                    <button class="btn btn-outline-success btn-sm btn-state" type="button" id="btn-other-action" onclick="showOtherInput()">Livré</button>
                                    <button class="btn btn-outline-danger btn-sm btn-state" type="button" name="" value="" id="btn-supprimer" title="supprimer la commande"><i class="fa-solid fa-trash"></i></button>
                                </div>

                                <div id="delete-orders" style="display: none !important;">
                                    <label class="small text-muted mb-1">Entrez le motif de suppréssion</label>
                                    <div class="input-group">
                                        <textarea  class="form-control form-control-sm" id="other-action-textarea" placeholder="motif..." name="order_delete"></textarea>
                                        <button class="btn btn-danger btn-sm" type="submit" name="action_commande" value="supprimer" id="btn_supprimer">Supprimer</button>
                                    </div>
                                </div>
                                
                                <div id="dynamic-input-container">
                                    <label class="small text-muted mb-1">Entrez le code de confirmation pour la livraison</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm" id="other-action-input" placeholder="CDR-2026-8245" name="code_retrait">
                                        <button class="btn btn-orange btn-sm" type="submit" name="action_commande" value="livrer">Valider</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <?php if (!empty($_SESSION['errorCmd'])): ?>
                                <span id="JsErrorMsg" class="text-danger fw-bold text-center text-dismissible">
                        <?php 
                                    echo $_SESSION['errorCmd'];
                                    unset($_SESSION['errorCmd']);
                        ?>
                                </span>
                        <?php endif;  ?> 
                        <?php if (!empty($_SESSION['successCmd'])): ?>
                                <span id="JsSuccessMsg" class="text-success text-center text-dismissible" style="font-weight: bold;">
                        <?php 
                                    echo $_SESSION['successCmd'];
                                    unset($_SESSION['successCmd']);
                        ?>
                                </span>
                        <?php endif;  ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['admin_mode']) && $_SESSION['admin_mode'] === true): ?>
            <div class="alert alert-warning border-0 rounded-0 shadow-sm d-flex justify-content-between align-items-center mb-0" style="background-color: #fff3cd; color: #856404; position: sticky; top: 0; z-index: 1050;">
                <div>
                    <i class="fas fa-user-shield me-2"></i>
                    <strong>Mode Administrateur :</strong> Vous visualisez actuellement le compte de 
                    <span class="badge bg-dark"><?= htmlspecialchars($nomBtq) ?> Boutique</span>
                </div>
                <a href="retourAdmin.php" class="btn btn-sm btn-outline-dark fw-bold">
                    <i class="fas fa-arrow-left me-1"></i> Quitter et revenir à l'Admin
                </a>
            </div>
        <?php endif; ?>
        <?php include '../vendor/chatAI/discuss.php'; ?>

        <!-- === Footer général site === -->
        <footer class="lb-footer-main mt-5 pt-4">
            <div class="container pb-2">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h5 class="footer-title mb-2">À propos</h5>
                        <ul class="list-unstyled">
                            <li><a href="LeBoutiquier_Description.php" class="text-decoration-none text-dark">Qui sommes-nous</a></li>
                            <li><a href="Notre_Mission.php" class="text-decoration-none text-dark">Notre mission</a></li>
                            <li><a href="Aide_&_FAQ.php" class="text-decoration-none text-dark">Aide & FAQ</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h5 class="footer-title mb-2">Nos services</h5>
                        <ul class="list-unstyled">
                            <li><a href="Vendre_un_Article.php" class="text-decoration-none text-dark">Vendre un article</a></li>
                            <li><a href="Trouver_une_Boutique.php" class="text-decoration-none text-dark">Trouver une boutique</a></li>
                            <li><a href="Conseil_de_Securite.php" class="text-decoration-none text-dark">Conseils de sécurité</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h5 class="footer-title mb-2">Mentions</h5>
                        <ul class="list-unstyled">
                            <li><a href="Conditions_Générales.php" class="text-decoration-none text-dark">Conditions générales</a></li>
                            <li><a href="Vie_Privée.php" class="text-decoration-none text-dark">Vie privée</a></li>
                            <li><a href="Accessibilite.php" class="text-decoration-none text-dark">Accessibilité</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h5 class="footer-title mb-2">Contact</h5>
                        <ul class="list-unstyled">
                            <li><a href="Contact.php" class="text-decoration-none text-dark"><i class="fas fa-envelope me-1 accent-color"></i> Nous contacter</a></li>
                            <li class="mt-2">
                                <span class="accent-color fw-bold">Suivez-nous :</span>
                                <span class="ms-2">
                                    <i class="fab fa-facebook-f accent-color"></i>
                                    <i class="fab fa-whatsapp accent-color"></i>
                                    <i class="fab fa-instagram accent-color"></i>
                                    <i class="fab fa-twitter accent-color"></i>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>

        <!-- === Scripts JQuery / Bootstrap + JS pour interactions === -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script>
            // Gestion des panels mobile (changement d'onglet)
            function setMobilePanel(tab) {
                let panels = ["profile", "articles", "comments", "orders", "notif"];
                panels.forEach(function(p){
                    document.getElementById('mobile-' + p + '-panel').style.display = (p === tab ? '' : 'none');
                });
                ['mobile-profile-tab','mobile-articles-tab','mobile-comments-tab','mobile-orders-tab','mobile-notif-tab'].forEach(function(id){
                    document.getElementById(id).classList.remove('active');
                });
                document.getElementById('mobile-'+tab+'-tab').classList.add('active');
                // Mise à jour du titre topbar (visible mobile)
                let title = "";
                switch(tab) {
                    case "profile": title = "Mon profil"; break;
                    case "articles": title = "Mes articles"; break;
                    case "comments": title = "Commentaires"; break;
                    case "orders": title = "Mes commandes"; break;
                    case "notif": title = "Notifications"; break;
                }
                var panelTitle = document.getElementById('mobile-panel-title');
                if (panelTitle) panelTitle.textContent = title;
            }
            // Actions navigation mobile
            document.getElementById('mobile-profile-tab').onclick = function(e){ e.preventDefault(); setMobilePanel('profile'); };
            document.getElementById('mobile-articles-tab').onclick = function(e){ e.preventDefault(); setMobilePanel('articles'); };
            document.getElementById('mobile-comments-tab').onclick = function(e){ e.preventDefault(); setMobilePanel('comments'); };
            document.getElementById('mobile-orders-tab').onclick = function(e){ e.preventDefault(); setMobilePanel('orders'); };
            document.getElementById('mobile-notif-tab').onclick = function(e){ e.preventDefault(); setMobilePanel('notif'); };

            // Prévisualisation image de profil desktop
            document.getElementById('profile-upload').addEventListener('change', function(e){
                if(e.target.files && e.target.files[0]){
                    let reader = new FileReader();
                    reader.onload = function(ev){
                        document.getElementById('profile-img').src = ev.target.result;
                    }
                    reader.readAsDataURL(e.target.files[0]);
                }
            });
            // Prévisualisation image de profil mobile
            document.getElementById('profile-upload-mobile').addEventListener('change', function(e){
                if(e.target.files && e.target.files[0]){
                    let reader = new FileReader();
                    reader.onload = function(ev){
                        document.getElementById('profile-img-mobile').src = ev.target.result;
                    }
                    reader.readAsDataURL(e.target.files[0]);
                }
            });
            // Sauvegarde de description (popup fake pour démo)
            document.getElementById('desc-form').addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Description enregistrée !');
            });
            document.getElementById('desc-form-mobile').addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Description enregistrée !');
            });

            // ===== Dropdown cloche notifications (DESKTOP) =====
            function closeNotifDropdownTop() {
                var dropdown = document.getElementById('notifDropdownMenuTop');
                var bell = document.getElementById('notifBellTop');
                var dot = document.getElementById('notif-count-top');
                if (dropdown) dropdown.style.display = 'none';
                if (bell) bell.setAttribute('aria-expanded', 'false');
                if (dot) dot.style.display = 'inline-block';
                document.removeEventListener('mousedown', onClickOutsideNotifTop);
                document.removeEventListener('keydown', onEscNotifTop);
            }
            function onClickOutsideNotifTop(e) {
                var bell = document.getElementById('notifBellTop');
                var dropdown = document.getElementById('notifDropdownMenuTop');
                if (dropdown && !dropdown.contains(e.target) && e.target !== bell) {
                    closeNotifDropdownTop();
                }
            }
            function onEscNotifTop(e) {
                if(e.key === "Escape") closeNotifDropdownTop();
            }
            function notifBellTopHandler(e) {
                e.stopPropagation();
                var dropdown = document.getElementById('notifDropdownMenuTop');
                var bell = document.getElementById('notifBellTop');
                var dot = document.getElementById('notif-count-top');
                if(dropdown.style.display === 'block'){
                    closeNotifDropdownTop();
                } 
                else {
                    dropdown.style.display = 'block';
                    bell.setAttribute('aria-expanded', 'true');
                    if (dot) dot.style.display = 'none';
                    setTimeout(function() {
                        document.addEventListener('mousedown', onClickOutsideNotifTop);
                        document.addEventListener('keydown', onEscNotifTop);
                    }, 10);
                }
            }
            // Affichage de la cloche top seulement desktop
            function handleNotifBellDisplay() {
                if(window.innerWidth < 992) {
                    // Mobile: masque cloche top
                    var bell = document.getElementById('notifBellTop');
                    var dropdown = document.getElementById('notifDropdownMenuTop');
                    if (bell) bell.style.display = 'none';
                    if (dropdown) dropdown.style.display = 'none';
                } 
                else {
                    var bell = document.getElementById('notifBellTop');
                    if (bell) bell.style.display = '';
                }
            }
            document.addEventListener('DOMContentLoaded', function(){
                 handleNotifBellDisplay();
                if(window.innerWidth >= 992) {
                    var bell = document.getElementById('notifBellTop');
                    if (bell) {
                        bell.addEventListener('click', notifBellTopHandler);
                    }
                }
            });
            // Réajuste la cloche selon la fenêtre
            window.addEventListener('resize', function() {
                handleNotifBellDisplay();
                closeNotifDropdownTop();
                if(window.innerWidth < 992) {
                    let activeTab = document.querySelector('.leb-bottom-navbar .nav-link.active');
                    setMobilePanel(
                        activeTab ? activeTab.getAttribute('data-tab') : 'profile'
                    );
                }
            });
            
            // --- Logique Gestion des Commandes (AJOUTÉ) ---
            function openOrderDetails(id, client, date, total, status, bigTotal, address, frais, number, articles) {
                document.getElementById('orderTitle').textContent = 'Commande #' + id;
                document.getElementById('orderClient').textContent = client;
                document.getElementById('orderDate').textContent = date;
                document.getElementById('orderTotal').textContent = total;
                document.getElementById('grandTotal').textContent = bigTotal;
                document.getElementById('Adresse').textContent = address;
                document.getElementById('orderFrais').textContent= frais;
                document.getElementById('Numero').textContent = number;
                document.getElementById('id_commande').value=id;

                if(frais==="Retrait en boutique"){
                    document.getElementById('orderFrais').style.color="#28a745";
                }

                const conteneur= document.getElementById('orderItemsList');
                conteneur.innerHTML="";

                articles.forEach(art=>{
                    const htmlArticles=`
                    <div class="d-flex align-items-center mb-3">
                        <img src="../ImagesBD/${art.photo_article}" class="order-item-img me-3" alt="article">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 small fw-bold">${art.nom_article}</h6>
                            <small class="text-muted">Qté: ${art.quantite_cmd} • ${parseInt(art.prix_article).toLocaleString()} FCFA</small>
                        </div>
                    </div>`;
                    conteneur.insertAdjacentHTML('beforeend', htmlArticles);
                });
                
                // Réinitialiser l'input dynamique
                document.getElementById('dynamic-input-container').style.display = 'none';
                document.getElementById('other-action-input').value = '';

                var inputId=document.getElementById('modal-id-commande');
                if(inputId){
                    inputId.value=id;
                    console.log("ID de la commande chargé dans le modal: "+id);
                }

                const sectionForm=document.getElementById('section-formulaire-commande');
                const sectionLivre=document.getElementById('section-message-livre');
                const btnPrep=document.getElementById('btn-preparer');

                if(status==='Livré'||status==='livré'){
                    sectionForm.style.display='none';
                    sectionLivre.style.display='block';
                    if(frais==="Retrait en boutique"){
                        sectionLivre.textContent='Cette commande a déja été retirée!';
                    } 
                    else{
                        sectionLivre.textContent='Cette commande a déja été livrée!';
                    }
                }
                else if(status==='Annulé'||status==='annulé'){
                    sectionForm.style.display='none';
                    sectionLivre.style.display='block';
                    sectionLivre.textContent='Vous avez supprimer cette commande!';
                }
                else{
                    sectionForm.style.display='block';
                    sectionLivre.style.display='none';

                    if(status==='En Préparation'||status==='En préparation'){
                        btnPrep.style.display='none';
                    }
                    else{
                        btnPrep.style.display='block';
                    }
                }
                
                var myModal = new bootstrap.Modal(document.getElementById('modalOrderDetails'));
                myModal.show();
            }
            
            var supprimer=document.getElementById("btn-supprimer").addEventListener('click', afficher);
            var container=document.getElementById("delete-orders");
            var btnLiv=document.getElementById('btn-other-action');
            var btnPrep=document.getElementById('btn-preparer');
            var input=document.getElementById('other-action-input');
            var textarea=document.getElementById('other-action-textarea');
            var i=0;
            function afficher(){
                i+=1;
                if(i%2!=0){
                    container.style.setProperty('display', 'block', 'important');
                    btnLiv.style.pointerEvents='none';
                    btnLiv.style.opacity="0.3";

                    btnPrep.style.pointerEvents='none';
                    btnPrep.style.opacity="0.3";

                    textarea.setAttribute('required', 'required');
                    input.removeAttribute('required');
                }
                else{
                    container.style.setProperty('display', 'none', 'important');
                    btnLiv.style.pointerEvents='auto';
                    btnLiv.style.opacity="1";

                    btnPrep.style.pointerEvents='auto';
                    btnPrep.style.opacity="1";

                    input.removeAttribute('required');
                    textarea.removeAttribute('required');
                }
            }

            function showOtherInput() {
                const container = document.getElementById('dynamic-input-container');
                const inputField=document.getElementById('other-action-input');
                const textarea=document.getElementById('other-action-textarea');

                const containerSupp=document.getElementById('delete-orders');
                const btnSupp=document.getElementById("btn-supprimer");
        
                if(container){
                    if((container.style.display === 'none' || container.style.display === '')) {
                        container.style.setProperty('display', 'block', 'important');

                        btnSupp.style.pointerEvents='none';
                        btnSupp.style.opacity="0.3";

                        inputField.setAttribute('required', 'required');
                        textarea.removeAttribute('required');

                        console.log("Affichage du conteneur");
                        if(inputField) inputField.focus();
                    }
                    else{
                        container.style.setProperty('display', 'none', 'important');

                        btnSupp.style.pointerEvents='auto';
                        btnSupp.style.opacity="1";

                        inputField.removeAttribute('required');
                        textarea.removeAttribute('required');

                        console.log("Masquage du conteneur");
                    }
                }
                else{
                    console.log("ERREUR: conteneur introuvé");
                }
            }

            // Panel par défaut mobile : profil si on arrive en mobile
            if(window.innerWidth < 992) {
                setMobilePanel('profile');
            }

            var modifier=document.getElementById("modifier").addEventListener("click", modifierArt);
            var pubCross=document.getElementById("publier").addEventListener("click", publierArt);
            var titre=document.getElementById("modalAjoutArticleLabel");
            var btnPublier=document.getElementById("btnPublier");

            function modifierArt(id, nom, prix, quantite, description, categorie, photo){
                titre.innerHTML="Modifier un Article";
                btnPublier.innerHTML="Modifier";
                btnPublier.value="modifier";
                document.getElementById('nomArt').value=nom;
                document.getElementById('prixUArt').value=prix;
                document.getElementById('quantite').value=quantite;
                document.getElementById('id_article_invisible').value=id;
                document.getElementById('description').value=description;
                document.getElementById('cat').value=categorie;
                document.getElementById('photo').src=`../ImagesBD/${photo}`;
                document.getElementById('ancienne_photo').value=photo;

                const modal = new bootstrap.Modal(document.getElementById('modalAjoutArticle'));
                modal.show();
            }
            function publierArt(){
                titre.innerHTML="Publier un Article";
                btnPublier.innerHTML="Publier";
                btnPublier.value="publier";
                document.getElementById('nomArt').value="";
                document.getElementById('prixUArt').value="";
                document.getElementById('quantite').value="";
                document.getElementById('ancienne_photo').value="";
                document.getElementById('id_article_invisible').value="";
                document.getElementById('description').value="";
                document.getElementById('formArticle').resetForm();
            }
            function afficherApercu(input){
              var nbr_photo=input.files.length;
              
                if(confirm("Voulez-vous vraiment ajouter ces "+nbr_photo+" photo(s)?")){
                    if(nbr_photo>2){
                        alert("Vous ne pouvez choisir que 2 images");
                    }
                    else{
                        input.form.submit();
                    }
                }
                else{
                    input.value="";
                }

            }
             
            /*function premplirModif(article){
                document.getElementById('nomArt').article.nomArt;
                document.getElementById('prixUArt').article.prixUArt;
                document.getElementById('quantite').article.qntArt;
                document.getElementById('ancienne_photo').article.photo;
                document.getElementById('description').article.desc;
            } 
            function modeAjout(){
                document.getElementById('formArticle').reset();
                document.getElementById('id_art_invisible').value=""
            }*/  
            
            window.addEventListener('load', function(){
                const urlParams=new URLSearchParams(window.location.search);
                const idToOpen=urlParams.get('openModal');
                const status=urlParams.get('status');

                if(idToOpen){
                    const triggerCard=document.querySelector(`.item-commande[data-id="${idToOpen}"]`);
                    if(triggerCard){
                        triggerCard.click();

                        setTimeout(()=>{
                            if(status==='success'){
                                document.getElementById('section-formulaire-commande').style.display='none';
                                document.getElementById('section-message-livre').style.display='block';
                            } 
                            else if(status==='error'){
                                showOtherInput();
                            }
                        }, 200);
                        
                    }
                }
            });

        </script>
    </body>
</html>
