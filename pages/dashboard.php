<?php

    session_start();
    include 'connexionBD.php';
    $errorMsg="";
    $successMsg="";
    $successCmd="";
    $errorCmd="";
    $errorLvr="";
    $successLvr="";

    if (!isset($_SESSION['id']) || $_SESSION['compte']!=='admin') {
        header('Location: connexion.php');
        exit();
    }
    $adminID = $_SESSION['id'];

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
                                            ===================== GESTION COMMERCANTS =====================
                                            ============================================================== */
    $openModal = false;
    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['create_user'])) {
        $openModal = true;
        try {
            $ID = "USER-".date('Y')."-".random_int(1000, 9999);
            $nom = htmlspecialchars(trim($_POST['nom']))??'';
            $prenom = htmlspecialchars(trim($_POST['prenom']))??'';
            $email = htmlspecialchars(trim($_POST['email']))??'';
            $date = htmlspecialchars(trim($_POST['date']))??'';
            $compte = "boutiquier";
            $numero = htmlspecialchars(trim($_POST['tel']));
            $nomBtq=htmlspecialchars(trim($_POST['nomBtq']))??'';
            $desc=htmlspecialchars(trim($_POST['desc']))??'';
            $pass = $_POST['pass']??'';
            $cpass = $_POST['cpass']??'';
            $localisation = $_POST['localisation'] ?? '';

            if($pass!==$cpass){
                $_SESSION['errorMsg'] = 'Les deux mots de passe doivent correspondre!';
            }
            else{
                $profil = null;
                if (isset($_FILES["profil"]) && $_FILES["profil"]["error"] == 0 && !empty($_FILES["profil"]["name"])){
                    $file_basename = pathinfo($_FILES["profil"]["name"], PATHINFO_FILENAME);//PATHINFO_FILENAME pour récupérer le nom de l'image
                    $files_extension = strtolower(pathinfo($_FILES["profil"]["name"], PATHINFO_EXTENSION));//strlower pour recupérer l'extention et les infos de l'image
                    $new_image_name = $file_basename . '_' . date("Ymdhis") . '.' . $files_extension;//Pour rennomer l'image avant de passer en bd
                    if(move_uploaded_file($_FILES["profil"]["tmp_name"], '../ImagesBD/' . $new_image_name)){
                        $profil=$new_image_name;
                    } 
                    else{
                        $_SESSION['errorMsg']='Erreur lors du téléchargement de la photo de profil!';  
                    }
                }

                $conn->beginTransaction();

                $sqlUser = "INSERT INTO utilisateur (id, nom, prenom, email, pass, tel, compte, localisation, dateNaiss) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmtUser = $conn->prepare($sqlUser);
                $stmtUser->execute([$ID, $nom, $prenom, $email, $pass, $numero, $compte, $localisation, $date]);

                if ($compte==='boutiquier') {
                    $idBtq="BTQ-".date("Y")."-".random_int(1000, 9999);
                    $statut="certifie";
                    $sqlComm = "INSERT INTO commerçant (id_commerçant, nom_commerçant, nom_boutique, description_boutique, Quartier_boutique, statut, profil_boutique, id, ajoute_par) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmtComm = $conn->prepare($sqlComm);
                    $stmtComm->execute([$idBtq, $nom, $nomBtq, $desc, $localisation, $statut, $profil, $ID, $adminID]);

                } 
                $conn->commit();
                $_SESSION['successMsg']='Le compte a été créer avec succès!';
            }
            header('Location: dashboard');
            exit();
        } 
        catch(PDOException $e) {
            $conn->rollBack();
            $_SESSION['errorMsg'] = 'Erreur lors de la création: '. $e->getMessage();
            header('Location: dashboard.php');
            exit();
        }
    }


                                            /* ===========================================================
                                            ===================== GESTION LIVREUR ========================
                                            ============================================================== */
    //1. CREER LIVREURS
    if($_SERVER['REQUEST_METHOD'] ==='POST' && isset($_POST['create_deliverer'])){
        try {
            $ID = "USER-".date('Y')."-".random_int(1000, 9999);
            $nom = htmlspecialchars(trim($_POST['nomL']))??'';
            $prenom = htmlspecialchars(trim($_POST['prenomL']))??'';
            $email = htmlspecialchars(trim($_POST['emailL']))??'';
            $date = htmlspecialchars(trim($_POST['dateL']))??'';
            $compte = "livreur";
            $numero = htmlspecialchars(trim($_POST['telL']));
            $statut=htmlspecialchars(trim($_POST['statutL']))??'';
            $pass = $_POST['passL']??'';
            $cpass = $_POST['cpassL']??'';

            if($pass!==$cpass){
                $_SESSION['errorLvr'] = 'Les deux mots de passe doivent correspondre!';
            }
            else{
                $profil = null;
                if (isset($_FILES["profilL"]) && $_FILES["profilL"]["error"] == 0 && !empty($_FILES["profilL"]["name"])){
                    $file_basename = pathinfo($_FILES["profilL"]["name"], PATHINFO_FILENAME);//PATHINFO_FILENAME pour récupérer le nom de l'image
                    $files_extension = strtolower(pathinfo($_FILES["profilL"]["name"], PATHINFO_EXTENSION));//strlower pour recupérer l'extention et les infos de l'image
                    $new_image_name = $file_basename . '_' . date("Ymdhis") . '.' . $files_extension;//Pour rennomer l'image avant de passer en bd
                    if(move_uploaded_file($_FILES["profilL"]["tmp_name"], '../ImagesBD/' . $new_image_name)){
                        $profil=$new_image_name;
                    } 
                    else{
                        $_SESSION['errorLvr']='Erreur lors du téléchargement de la photo de profil!';  
                    }
                }

                $conn->beginTransaction();

                $sqlUser = "INSERT INTO utilisateur (id, nom, prenom, email, pass, tel, compte, dateNaiss) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmtUser = $conn->prepare($sqlUser);
                $stmtUser->execute([$ID, $nom, $prenom, $email, $pass, $numero, $compte, $date]);

                if ($compte==='livreur') {
                    $idLiv="LVR-".date("Y")."-".random_int(1000, 9999);
                    $sqlLiv = "INSERT INTO livreur (id_livreur, statut, profil, id) VALUES (?, ?, ?, ?)";
                    $stmtLiv = $conn->prepare($sqlLiv);
                    $stmtLiv->execute([$idLiv, $statut, $profil, $ID]);
                } 
                $conn->commit();
                $_SESSION['successLvr']='Le compte a été créer avec succès!';
            }
            header('Location: dashboard');
            exit();
        } 
        catch(PDOException $e) {
            $conn->rollBack();
            $_SESSION['errorMsg'] = 'Erreur lors de la création: '. $e->getMessage();
            header('Location: dashboard.php');
            exit();
        }
    }
    //1. MODIFIER LIVREURS
    if($_SERVER['REQUEST_METHOD'] ==='POST' && isset($_POST['update_deliverer'])){
        $id_liv=$_POST['id_livreur'];

        $sql=$conn->prepare("SELECT u.* 
                             FROM utilisateur u, livreur lvr
                             WHERE u.id=lvr.id AND lvr.id_livreur=?");
        $sql->execute([$id_liv]);
        $rowliv=$sql->fetch(PDO::FETCH_ASSOC);
        $IDLiv=$rowliv['id'];
        
        try {
            $nom = htmlspecialchars(trim($_POST['nomM']))??'';
            $prenom = htmlspecialchars(trim($_POST['prenomM']))??'';
            $email = htmlspecialchars(trim($_POST['emailM']))??'';
            $numero = htmlspecialchars(trim($_POST['telM']));
            $statut=htmlspecialchars(trim($_POST['statutM']))??'';
            $pass = $_POST['passM']??'';
            $cpass = $_POST['cpassM']??'';

            if($pass!==$cpass){
                $_SESSION['errorLvr'] = 'Les deux mots de passe doivent correspondre!';
            }
            else{
                $conn->beginTransaction();

                $sqlUser = "UPDATE utilisateur 
                            SET nom=?, prenom=?, email=?, pass=?, tel=?
                            WHERE id=?";
                $stmtUser = $conn->prepare($sqlUser);
                $stmtUser->execute([$nom, $prenom, $email, $pass, $numero, $IDLiv]);

                if($rowliv['compte']==='livreur'){
                    $sqlLiv=$conn->prepare("UPDATE livreur 
                                            SET statut=?
                                            WHERE id_livreur=?");
                    $sqlLiv->execute([$statut, $id_liv]);
                } 
                $conn->commit();
                $_SESSION['successLvr']='Le livreur a été modifier avec succès!';
            }
            header('Location: dashboard');
            exit();
        } 
        catch(PDOException $e) {
            $conn->rollBack();
            $_SESSION['errorLvr'] = 'Erreur lors de la création: '. $e->getMessage();
            header('Location: dashboard.php');
            exit();
        }
    }

    //2. SUPPRIMER LIVREUR
    if(isset($_GET['supprimer'])){
        $id_liv=$_GET['supprimer'];

        $sql=$conn->prepare("SELECT u.* 
                             FROM utilisateur u, livreur lvr
                             WHERE u.id=lvr.id AND lvr.id_livreur=?");
        $sql->execute([$id_liv]);
        $rowliv=$sql->fetch(PDO::FETCH_ASSOC);
        $IDLiv=$rowliv['id']??'';

        try{
            $conn->beginTransaction();

            $stmt=$conn->prepare("DELETE FROM livreur WHERE id_livreur=?");
            $stmt->execute([$id_liv]);

            $sqlLiv=$conn->prepare("DELETE FROM utilisateur WHERE id=?");
            $sqlLiv->execute([$IDLiv]);
  
            $conn->commit();
        }
        catch(PDOException $e){
            $conn->rollBack();
            die("ERREUR: ".$e->getMessage());
        }
    }


                                            /* ===========================================================
                                            ===================== GESTION COMMANDE =======================
                                            ============================================================== */

    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action_commande'])){   

        $id_cmd=$_POST['id_commande'];
        $type_action=$_POST['action_commande'];

        try{
            if($type_action==='supprimer'){
                $motif=htmlspecialchars(trim($_POST['order_delete']))??'';
    
                $updateSupprimer=$conn->prepare("UPDATE commande set statut='Annulé', motif=? WHERE id_commande=?");
                $updateSupprimer->execute([$motif, $id_cmd]);

                $_SESSION['successCmd']='<i class="fa-solid fa-check-circle" style="color: green;"></i>Suppression effectué avec succès!';
                header("Location: dashboard.php");
                exit();
            }
        }
        catch(PDOException $e) {
            die("ERREUR: ".$e->getMessage());
            header('Location: dashboard.php');
            exit();
        }
    }
                                            /* ===========================================================
                                            ==================== Affichage notifications =================
                                            ============================================================== */
    /*$notifications=[];
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
    $sqlNotifQnt="SELECT nom_article FROM article WHERE id_commerçant=? AND quantite_stock<5";
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
    usort($notifications, function ($a, $b) {return strtotime($b['date'])-strtotime($a['date']);});*/

    //1. AFFICHAGE DU NOMBRE DE COMMERCANTS
    $sqlCountComptes="SELECT COUNT(*) as total FROM commerçant WHERE ajoute_par IS NOT NULL";
    $stmtCountComptes=$conn->prepare($sqlCountComptes);
    $stmtCountComptes->execute();
    $totalcomptes = $stmtCountComptes->fetch(PDO::FETCH_ASSOC)['total']??0;

    // 2. Récupérer la liste de tous ces comptes pour les afficher
    $sqlCommerçant="SELECT co.*, u.tel, u.email, u.prenom, u.nom,
                           (SELECT COUNT(*) FROM article WHERE id_commerçant = co.id_commerçant) as nb_articles,
                           (SELECT COUNT(*) FROM avis WHERE id_commerçant = co.id_commerçant) as nb_avis,
                           (SELECT COUNT(*) FROM commande WHERE id_commerçant = co.id_commerçant) as nb_commandes
                    FROM commerçant co, utilisateur u
                    WHERE u.id=co.id AND co.ajoute_par IS NOT NULL
                    ORDER BY co.nom_boutique ASC";
    $stmtCommerçant=$conn->prepare($sqlCommerçant);
    $stmtCommerçant->execute();
    $commerçants=$stmtCommerçant->fetchAll(PDO::FETCH_ASSOC);

    // 3. Récupérer la liste de toutes les commandes pour affichage
    $commandes=[];
    try{
        $stmtCmd=$conn->prepare("SELECT cmd.*, u.nom, u.prenom, u.id, a.quantite_stock, co.nom_boutique
                                 FROM  commande cmd, utilisateur u, client clt, details_commande dtl, article a, commerçant co
                                 WHERE cmd.id_client=clt.id_client 
                                 AND clt.id=u.id 
                                 AND cmd.id_commande=dtl.id_commande 
                                 AND dtl.id_article=a.id_article 
                                 AND co.id_commerçant=cmd.id_commerçant
                                 AND co.ajoute_par IS NOT NULL
                                 ORDER BY cmd.statut ASC, cmd.Date_commande ASC");
        $stmtCmd->execute();
        $commandes=$stmtCmd->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e){
        die("Erreur:".$e->getMessage());
    }

    //4. AFFICHAGE DU NOMBRE DE COMMANDES
    $sqlCountCommandes="SELECT COUNT(*) as totalCmd 
                        FROM commande cmd, commerçant co
                        WHERE cmd.id_commerçant=co.id_commerçant AND cmd.statut='En attente' AND co.ajoute_par IS NOT NULL";
    $stmtCountCommandes=$conn->prepare($sqlCountCommandes);
    $stmtCountCommandes->execute();
    $totalcommandes = $stmtCountCommandes->fetch(PDO::FETCH_ASSOC)['totalCmd']??0;

    //5. AFFICHAGE DU NOMBRE DE LIVREURS ACTIFS
    $sqlCountLivreurs="SELECT COUNT(*) as totalLiv 
                        FROM livreur lvr
                        WHERE statut='Actif'";
    $stmtCountLivreurs=$conn->prepare($sqlCountLivreurs);
    $stmtCountLivreurs->execute();
    $totallivreurs = $stmtCountLivreurs->fetch(PDO::FETCH_ASSOC)['totalLiv']??0;
 
    //6. AFFICHAGE DES LIVREURS
    $sqlShowLivreurs="SELECT u.*, lvr.id_livreur, lvr.statut, lvr.profil 
                      FROM livreur lvr, utilisateur u
                      WHERE u.id=lvr.id";
    $stmtShowLivreurs=$conn->prepare($sqlShowLivreurs);
    $stmtShowLivreurs->execute();
    $Livreurs = $stmtShowLivreurs->fetchAll(PDO::FETCH_ASSOC);

    //7. AFFICHAGE DES AVIS COMMERCANT

    //8. STATISTIQUE ET GRAPHES
    $categories=[];
    $nbCommandes=[];
    $roles=[];
    $nbUsers=[];
    try{
        // --- DIAGRAMME EN BANDES : Top des catégories les plus commandées ---
        // Cette requête lie les catégories aux articles, puis aux items commandés
        $sqlCat="SELECT c.nom_cat, COUNT(dtl.id_article) as total_commandes
                 FROM catégorie c, details_commande dtl, article a
                 WHERE c.id_cat = a.id_cat AND a.id_article = dtl.id_article
                 GROUP BY c.id_cat, c.nom_cat
                 ORDER BY total_commandes DESC
                 LIMIT 5"; // On limite au Top 5 pour que ce soit lisible
        
        $reqCat=$conn->query($sqlCat);
        while($row=$reqCat->fetch(PDO::FETCH_ASSOC)) {
            $categories[]=$row['nom_cat'];
            $nbCommandes[]=(int)$row['total_commandes'];
        }

        // --- DIAGRAMME CIRCULAIRE : Répartition des utilisateurs par rôle ---
        $sqlUsers="SELECT compte, COUNT(*) as nb 
                   FROM utilisateur 
                   GROUP BY compte";
        $reqUsers=$conn->query($sqlUsers);
        
        while($row=$reqUsers->fetch(PDO::FETCH_ASSOC)){
            // On peut formater le nom du rôle pour l'affichage (ex: admin -> Administrateur)
            $roles[]=ucfirst($row['compte']); 
            $nbUsers[]=(int)$row['nb'];
        }
    } 
    catch(PDOException $e){
        die("ERREUR: ".$e->getMessage());
    }
     
?>



<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard Admin - Gestion des Comptes Commerçants</title>
        <link rel="stylesheet" href="../fontawesome/css/all.min.css">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../style/dashboard.css">
        <!-- PWA -->
        <link rel="manifest" href="../vendor/manifest.json">
        <meta name="theme-color" content="#EA580C">
        <link rel="icon" href="../Image/favoicon.ico" sizes="48x48">
        <link rel="apple-touch-icon" href="../Image/logo.png">
        <script src="../js/chart.js"></script>
    </head>
    <body>
        <?php include 'splash.php'; ?>
        <!-- === MODAL AJOUTER LIVREUR === -->
        <div class="modal fade" id="addDelivererModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="" method="post" enctype="multipart/form-data"> 
                        <div class="modal-header">
                            <h5 class="modal-title">Ajouter un Nouveau Livreur</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <?php if(isset($_SESSION['errorLvr'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
                                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $_SESSION['errorLvr']; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            <?php if(isset($_SESSION['successLvr'])): ?>
                                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
                                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $_SESSION['successLvr'];?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nom</label>
                                        <input type="text" name="nomL" class="form-control" id="delivererName" placeholder="Jean Dupont" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Prenom</label>
                                        <input type="text" name="prenomL" class="form-control" id="delivererLastName" placeholder="Jean Dupont" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="emailL" class="form-control" id="delivererEmail" placeholder="jean@example.com" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Telephone</label>
                                        <input type="tel" class="form-control" name="telL" id="delivererPhone" placeholder="+33 6 12 34 56 78" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date de naissance</label>
                                        <input type="date" class="form-control" name="dateLL" id="delivererDate" required>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Statut</label>
                                        <select class="form-select" name="statutL" id="delivererStatus" required>
                                            <option value="">Selectionner un statut</option>
                                            <option value="Actif">Actif</option>
                                            <option value="Inactif">Inactif</option>
                                            <option value="En congé">En congé</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Profil</label>
                                        <input type="file" class="form-control" name="profilL" id="delivererProfile" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mot de passe</label>
                                        <input type="password" name="passL" class="form-control" id="delivererPassword" placeholder="*******" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Confirmer le mot de passe</label>
                                        <input type="password" name="cpassL"  class="form-control" id="delivererPasswordConfirm" placeholder="*******" required>
                                    </div>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-orange" name="create_deliverer" onclick="">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- === MODAL MODIFIER LIVREUR === -->
        <div class="modal fade" id="editDelivererModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="editDelivererForm" method="post">
                        <input type="hidden" name="id_livreur" id="editDelivererId">
                        <div class="modal-header">
                            <h5 class="modal-title">Modifier le Livreur</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="window.location.reload();"></button>
                        </div>
                        <div class="modal-body">
                            <?php if(isset($_SESSION['errorLvr'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
                                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $_SESSION['errorLvr']; unset($_SESSION['errorLvr']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            <?php if(isset($_SESSION['successLvr'])): ?>
                                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
                                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $_SESSION['successLvr']; unset($_SESSION['successLvr']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nom</label>
                                    <input type="text" name="nomM" class="form-control" id="editDelivererName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Prenom</label>
                                    <input type="text" name="prenomM" class="form-control" id="editDelivererLastName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="emailM" class="form-control" id="editDelivererEmail" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telephone</label>
                                    <input type="tel" name="telM" class="form-control" id="editDelivererPhone" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Statut</label>
                                    <select class="form-select" name="statutM" id="editDelivererStatus" required>
                                        <option value="Actif">Actif</option>
                                        <option value="Inactif">Inactif</option>
                                        <option value="En congé">En congé</option>
                                    </select>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Adresse</label>
                                    <input type="text" class="form-control" id="editDelivererAddress" disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mot de passe</label>
                                    <input type="tel" name="passM" class="form-control" id="editDelivererPassword" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Confirmer le mot de passe</label>
                                    <input type="tel" name="cpassM" class="form-control" id="editDelivererConfirmPassword" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" name="update_deliverer" class="btn btn-orange">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- === MODAL DETAILS LIVREUR === -->
        <div class="modal fade" id="delivererDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="delivererDetailsTitle">Details du Livreur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Informations Personnelles -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Informations Personnelles</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Nom complet</small>
                                    <div class="fw-bold" id="detailsName">Jean Dupont</div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Email</small>
                                    <div class="fw-bold" id="detailsEmail">jean@example.com</div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <small class="text-muted">Telephone</small>
                                    <div class="fw-bold" id="detailsPhone">+33 6 12 34 56 78</div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <small class="text-muted">Adresse</small>
                                    <div class="fw-bold" id="detailsAddress">123 Avenue des Livreurs, Paris</div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <small class="text-muted">Statut</small>
                                    <div class="fw-bold"><span class="badge badge-success" id="detailsStatus">Actif</span></div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Statistiques -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Statistiques</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="card p-3 text-center">
                                        <div class="h4 text-orange fw-bold" id="statsDeliveries">45</div>
                                        <small class="text-muted">Livraisons effectuees</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card p-3 text-center">
                                        <div class="h4 text-orange fw-bold" id="statsPending">6</div>
                                        <small class="text-muted">En attente</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card p-3 text-center">
                                        <div class="h4 text-orange fw-bold" id="statsSuccessRate">98%</div>
                                        <small class="text-muted">Taux de reussite</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card p-3 text-center">
                                        <div class="h4 text-orange fw-bold" id="statsRevenue">2.45M</div>
                                        <small class="text-muted">Revenus (FCFA)</small>
                                    </div>
                                </div>
                            </div>
                        </div><hr>

                        <!-- Evaluation -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Evaluation</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Note moyenne</small>
                                    <div class="fw-bold">
                                        <span class="h4 text-warning" id="detailsRating">4.8</span>
                                        <span class="text-muted">/5.0</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Nombre d'avis</small>
                                    <div class="fw-bold">
                                        <span class="h4 text-orange" id="detailsReviewCount">24</span>
                                        <span class="text-muted">avis</span>
                                    </div>
                                </div>
                            </div>
                        </div><hr>

                        <!-- Avis Recents -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Avis Recents</h6>
                            <div id="recentReviewsContainer">
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="review-author">Client 1</div>
                                        <div class="review-date">15/02/2026</div>
                                    </div>
                                    <div class="review-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <span class="small text-muted ms-1">5.0</span>
                                    </div>
                                    <div class="review-text">Excellent livreur ! Tres professionnel et ponctuel.</div>
                                </div>
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="review-author">Client 2</div>
                                        <div class="review-date">14/02/2026</div>
                                    </div>
                                    <div class="review-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                        <span class="small text-muted ms-1">4.5</span>
                                    </div>
                                    <div class="review-text">Tres bon service, livraison rapide.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- === NAVBAR FIXE EN HAUT === -->
        <nav class="fixed-top-navbar">
            <button class="btn-back" onclick="window.location.href='deconnexion.php'" title="Deconnexion">
                <i class="fa-solid fa-right-from-bracket"></i>
            </button>
            <div class="navbar-main-title" id="navbarTitle">Leboutiquier Dashboard</div>
            <button class="notif-bell-top" id="notifBellTop" type="button" aria-expanded="false">
                <i class="fas fa-bell"></i>
                <span class="notif-dot" id="notif-count-top">2</span>
            </button>
            <ul class="dropdown-menu-notif-top" id="notifDropdownMenuTop">
                <li class="notif-title">Notifications</li>
                <li class="notif-item unread" onclick="openOrderNotification()">
                    <i class="fas fa-shopping-cart text-success me-1"></i>
                    <span><strong>Nouvelle commande</strong> de la boutique "Boutique Dupont"</span>
                    <small class="text-muted">il y a 5 min</small>
                </li>
                <li class="notif-item unread" onclick="openOrderNotification()">
                    <i class="fas fa-shopping-cart text-success me-1"></i>
                    <span><strong>Nouvelle commande</strong> de la boutique "Boutique Martin"</span>
                    <small class="text-muted">il y a 15 min</small>
                </li>
            </ul>
        </nav>

        <!-- === BARRE DE NAVIGATION BASSE (MOBILE) === -->
        <nav class="leb-bottom-navbar d-lg-none" id="mobileNavbar">
            <a class="nav-link active" href="#" data-tab="dashboard">
                <i class="fas fa-chart-line"></i>
                <span class="nav-label">Dashboard</span>
            </a>
            <a class="nav-link" href="#" data-tab="accounts">
                <i class="fas fa-store"></i>
                <span class="nav-label">Comptes</span>
            </a>
            <a class="nav-link" href="#" data-tab="orders">
                <i class="fas fa-shopping-bag"></i>
                <span class="nav-label">Commandes</span>
            </a>
            </li>
            <a class="nav-link" onclick="window.location.href='accueil.php';">
                <i class="fas fa-home"></i>
                <span class="nav-label">Accueil</span>
            </a>
        </nav>

        <!-- === CONTENU MOBILE === -->
        <div id="mobile-content-panels" class="main-content d-lg-none">
            <!-- Panel Dashboard (Mobile) -->
            <div id="mobile-dashboard-panel" class="mobile-panel active">
                <div class="section-title">
                    <i class="fas fa-chart-line"></i>
                    Aperçu du Système
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="card p-3 text-center">
                            <div class="h4 text-orange fw-bold"><?= $totalcomptes ?></div>
                            <small class="text-muted">Comptes gérés</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card p-3 text-center">
                            <div class="h4 text-orange fw-bold"><?= $totalcommandes ?></div>
                            <small class="text-muted">Commandes en attente</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card p-3 text-center">
                            <div class="h4 text-orange fw-bold"><?= $totallivreurs ?></div>
                            <small class="text-muted">Livreurs actifs</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card p-3 text-center">
                            <div class="h4 text-orange fw-bold">2</div>
                            <small class="text-muted">Alertes stock</small>
                        </div>
                    </div>
                </div>

                <div class="section-title">
                    <i class="fas fa-bell"></i>
                    Notifications Récentes
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <div class="card p-3 border-left-orange" onclick="openOrderNotification('CMD-001', 'Boutique Dupont', 'Jean Dupont', '15/02/2026', '45 000 FCFA', 'En attente', '45 000 FCFA', '123 Rue de Paris', 'Livraison à domicile', 'LIV-001')">
                            <div class="d-flex gap-2">
                                <i class="fas fa-shopping-cart text-success"></i>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">Nouvelle commande</h6>
                                    <small class="text-muted">De la boutique "Boutique Dupont"</small>
                                    <div class="small text-muted mt-2">il y a 5 min</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Comptes (Mobile) -->
            <div id="mobile-accounts-panel" class="mobile-panel">
                <div class="section-title">
                    <i class="fas fa-store"></i>
                    Gestion des Comptes
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded shadow-sm">
                    <div>
                        <p class="text-muted small mb-0">Créez des comptes pour les commerçants</p>
                    </div>
                    <button class="btn rounded-pill border-0 text-light px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalNewAccount" style="background-color: #ffc107;">
                        <i class="fas fa-user-plus me-2"></i> Créer un compte
                    </button>
                </div>
                <div class="row g-3">
                    <?php foreach($commerçants as $com): 
                        $initialePrenom=mb_strtoupper(mb_substr($com['prenom'], 0, 1));
                        $initialeNom=mb_strtoupper(mb_substr($com['nom'], 0, 1));
                        $initiales=$initialeNom.$initialePrenom;
                    ?>
                        <div class="col-12">
                            <div class="card merchant-card p-3" onclick="openMerchantAccount('<?= $com['id_commerçant'] ?>', '<?= $com['nom_boutique'] ?>', '<?= $com['nom'] ?>', '<?= $com['email'] ?>', '<?= $com['tel'] ?>', '<?= $com['nb_commandes'] ?>', '<?= $com['nb_articles'] ?>', '<?= $com['nb_avis'] ?>')">
                                <div class="d-flex align-items-center">
                                    <div class="merchant-avatar"><?= $initiales ?></div>
                                    <div class="merchant-info ms-3">
                                        <div class="merchant-name">Boutique <?= $com['nom_boutique'] ?></div>
                                        <div class="merchant-shop">Gérant: <?= $com['nom'].' '.$com['prenom'] ?></div>
                                        <div class="merchant-stats">
                                            <div class="stat-item">
                                                <div class="stat-number"><?= $com['nb_commandes'] ?></div>
                                                <div class="stat-label">Commandes</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-number"><?= $com['nb_articles'] ?></div>
                                                <div class="stat-label">Articles</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-number"><?= $com['nb_avis'] ?></div>
                                                <div class="stat-label">Avis</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if(empty($com)): ?>
                        <p style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Aucune compte commerçant pour l'instant</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Panel Commandes (Mobile) -->
             
            <div id="mobile-orders-panel" class="mobile-panel">
                <div class="section-title">
                    <i class="fas fa-shopping-bag"></i>
                    Gestion des Commandes
                </div>
                <div class="row g-3">
                    <?php foreach ($commandes as $cmd): 
                        $selectArticle=$conn->prepare("SELECT dtl.quantite_cmd, a.nom_article, a.photo_article, a.prix_article, a.quantite_stock, a.id_article, cmd.frais_livraison, cmd.Montant_commande, cmd.statut
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
                        <div class="col-12">
                            <div class="card order-card p-3" onclick="openOrderNotification('<?= $cmd['id_commande'] ?>', 'Boutique <?= $cmd['nom_boutique'] ?>', '<?= $cmd['nom'].' '.$cmd['prenom'] ?>', '<?= date('d/m/Y', strtotime($cmd['Date_commande'])); ?>', '<?= number_format($cmd['Montant_commande'], 0, '', ' ') ?> FCFA', '<?= $cmd['statut'] ?>', '<?= number_format($total, 0, '', ' ') ?> FCFA', '<?= $cmd['adresse_livraison'] ?>', '<?= htmlspecialchars($Livraison) ?>', 'LIV-001', <?= htmlspecialchars($articlesJSON, ENT_QUOTES, 'UTF-8') ?>)">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="order-icon bg-light p-3 rounded-circle me-3">
                                            <i class="fas fa-shopping-bag text-orange"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">#<?= $cmd['id_commande'] ?>'</h6>
                                            <small class="text-muted">Boutique <?= $cmd['nom_boutique'] ?> • <?= date('d/m/Y', strtotime($cmd['Date_commande'])); ?></small>
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
                        <p style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Aucune commande pour l'instant</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Panel Notifications (Mobile) -->
            <div id="mobile-notif-panel" class="mobile-panel">
                <div class="section-title">
                    <i class="fas fa-bell"></i>
                    Notifications
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <div class="card p-3" onclick="openOrderNotification('CMD-001', 'Boutique Dupont', 'Jean Dupont', '15/02/2026', '45 000 FCFA', 'En attente', '45 000 FCFA', '123 Rue de Paris', 'Livraison à domicile', 'LIV-001')">
                            <div class="d-flex gap-2">
                                <i class="fas fa-shopping-cart text-success"></i>
                                <div>
                                    <h6 class="mb-1 fw-bold">Nouvelle commande</h6>
                                    <small class="text-muted">De la boutique "Boutique Dupont"</small>
                                    <div class="small text-muted mt-2">il y a 5 min</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card p-3">
                            <div class="d-flex gap-2">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                <div>
                                    <h6 class="mb-1 fw-bold">Stock faible</h6>
                                    <small class="text-muted">L'article "Produit B" est en rupture de stock</small>
                                    <div class="small text-muted mt-2">il y a 1 h</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- === CONTENU DESKTOP === -->
        <div class="desktop-content">
            <div class="main-content">
                <!-- Onglets Desktop -->
                <ul class="nav nav-tabs" role="tablist" id="desktopTabs">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-dashboard" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab">
                            <i class="fas fa-chart-line me-2"></i>Dashboard
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-accounts" data-bs-toggle="tab" data-bs-target="#accounts" type="button" role="tab">
                            <i class="fas fa-store me-2"></i>Gestion des Comptes
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-orders" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">
                            <i class="fas fa-shopping-bag me-2"></i>Commandes
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-deliverers" data-bs-toggle="tab" data-bs-target="#deliverers" type="button" role="tab">
                            <i class="fas fa-truck me-2"></i>Gestion des Livreurs
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-4">
                    <!-- Tab Dashboard -->
                    <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <div class="card p-4 text-center">
                                    <div class="h3 text-orange fw-bold"><?= $totalcomptes ?></div>
                                    <small class="text-muted">Comptes gérés</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card p-4 text-center">
                                    <div class="h3 text-orange fw-bold"><?= $totalcommandes ?></div>
                                    <small class="text-muted">Commandes en attente</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card p-4 text-center">
                                    <div class="h3 text-orange fw-bold"><?= $totallivreurs ?></div>
                                    <small class="text-muted">Livreurs actifs</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card p-4 text-center">
                                    <div class="h3 text-orange fw-bold">2</div>
                                    <small class="text-muted">Alertes stock</small>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-lg-8">
                                <div class="card p-4">
                                    <h5 class="fw-bold mb-3">Commandes enregistrées</h5>
                                    <div class="row g-3">
                                        <?php foreach ($commandes as $cmd): 
                                            $selectArticle=$conn->prepare("SELECT dtl.quantite_cmd, a.nom_article, a.photo_article, a.prix_article, a.quantite_stock, a.id_article, cmd.frais_livraison, cmd.Montant_commande, cmd.statut
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
                                            <div class="col-12">
                                                <div class="card order-card p-3" onclick="openOrderNotification('<?= $cmd['id_commande'] ?>', 'Boutique <?= $cmd['nom_boutique'] ?>', '<?= $cmd['nom'].' '.$cmd['prenom'] ?>', '<?= date('d/m/Y', strtotime($cmd['Date_commande'])); ?>', '<?= number_format($total, 0, '', ' ') ?> FCFA', '<?= $cmd['statut'] ?>', 'FCFA<?= number_format($cmd['Montant_commande'], 0, '', ' ') ?> FCFA', '<?= $cmd['adresse_livraison'] ?>', '<?= htmlspecialchars($Livraison) ?>', 'LIV-001', <?= htmlspecialchars($articlesJSON, ENT_QUOTES, 'UTF-8') ?>)">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center">
                                                            <div class="order-icon bg-light p-3 rounded-circle me-3">
                                                                <i class="fas fa-shopping-bag text-orange"></i>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0 fw-bold">#<?= $cmd['id_commande'] ?></h6>
                                                                <small class="text-muted">Boutique <?= $cmd['nom_boutique'] ?> • <?= date('d/m/Y', strtotime($cmd['Date_commande'])); ?></small>
                                                            </div>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="fw-bold mb-1"><?= number_format($cmd['Montant_commande'], 0, '', ' ') ?></div>
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
                                            <p style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Aucune commande pour l'instant</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="card p-4">
                                    <h5 class="fw-bold mb-3">Comptes gérés</h5>
                                    <div class="row g-3">
                                        <?php foreach($commerçants as $com): 
                                            $initialePrenom=mb_strtoupper(mb_substr($com['prenom'], 0, 1));
                                            $initialeNom=mb_strtoupper(mb_substr($com['nom'], 0, 1));
                                            $initiales=$initialeNom.$initialePrenom;
                                        ?>
                                            <div class="col-12">
                                                <div class="d-flex align-items-center p-2 border rounded">
                                                    <div class="merchant-avatar" style="width: 40px; height: 40px; font-size: 0.9rem;"><img src="../ImagesBD/<?= $com['profil_boutique'] ?> " alt="profil boutique" class="merchant-avatar" style="width: 40px; height: 40px;"></div>
                                                    <div class="ms-2 flex-grow-1">
                                                        <div class="fw-bold small">Boutique <?= $com['nom_boutique'] ?></div>
                                                        <small class="text-muted"><?= $com['nom'].' '.$com['prenom'] ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                        <?php if(empty($com)): ?>
                                            <p style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Aucun compte gérer pour l'instant</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>


                            <div class="col-lg-8">
                                <div class="card p-4 h-100 shadow-sm">
                                    <h5 class="fw-bold mb-4">Statistiques</h5>
                                    
                                    <div class="row align-items-center">
                                        <div class="col-md-7 border-end">
                                            <p class="text-muted small text-center">Articles les plus commandés par catégorie</p>
                                            <canvas id="barChart" style="max-height: 250px;"></canvas>
                                        </div>
                                        
                                        <div class="col-md-5">
                                            <p class="text-muted small text-center">Répartition des utilisateurs</p>
                                            <canvas id="pieChart" style="max-height: 250px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Tab Gestion des Comptes -->
                    <div class="tab-pane fade" id="accounts" role="tabpanel">
                        <div class="card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold">Gestion des Comptes Commerçants</h5>
                                <span class="badge badge-info">Total:  <?= $totalcomptes ?></span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded shadow-sm">
                                <div>
                                    <h5 class="fw-bold mb-0 text-dark">Gestion des commerçants</h5>
                                    <p class="text-muted small mb-0">Créez des comptes pour les commerçants</p>
                                </div>
                                <button class="btn rounded-pill border-0 text-light px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalNewAccount" style="background-color: #ffc107;">
                                    <i class="fas fa-user-plus me-2"></i> Créer un compte
                                </button>
                            </div>

                            <div class="row g-3">
                                <?php foreach($commerçants as $com): 
                                    $initialePrenom=mb_strtoupper(mb_substr($com['prenom'], 0, 1));
                                    $initialeNom=mb_strtoupper(mb_substr($com['nom'], 0, 1));
                                    $initiales=$initialeNom.$initialePrenom;
                                ?>
                                    <div class="col-12">
                                        <div class="card merchant-card p-3" onclick="openMerchantAccount('<?= $com['id_commerçant'] ?>', '<?= $com['nom_boutique'] ?>', '<?= $com['nom'] ?>', '<?= $com['email'] ?>', '<?= $com['tel'] ?>', '<?= $com['nb_commandes'] ?>', '<?= $com['nb_articles'] ?>', '<?= $com['nb_avis'] ?>')">
                                            <div class="d-flex align-items-center">
                                                <div class="merchant-avatar"><?= $initiales?></div>
                                                <div class="merchant-info ms-3">
                                                    <div class="merchant-name">Boutique <?= $com['nom_boutique'] ?></div>
                                                    <div class="merchant-shop">Gérant: <?= $com['nom'].' '.$com['prenom'] ?></div>
                                                    <div class="merchant-stats">
                                                        <div class="stat-item">
                                                            <div class="stat-number"><?= $com['nb_commandes'] ?></div>
                                                            <div class="stat-label">Commandes</div>
                                                        </div>
                                                        <div class="stat-item">
                                                            <div class="stat-number"><?= $com['nb_articles'] ?></div>
                                                            <div class="stat-label">Articles</div>
                                                        </div>
                                                        <div class="stat-item">
                                                            <div class="stat-number"><?= $com['nb_avis'] ?></div>
                                                            <div class="stat-label">Avis</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php if(empty($commandes)): ?>
                                    <p style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Aucun compte pour l'instant</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Commandes -->
                    <div class="tab-pane fade" id="orders" role="tabpanel">
                        <div class="card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold">Gestion des Commandes</h5>
                                <span class="badge badge-info">Total: <?= $totalcommandes ?> commande(s)</span>
                            </div>

                            <div class="row g-3">
                                <?php foreach ($commandes as $cmd): 
                                    $selectArticle=$conn->prepare("SELECT dtl.quantite_cmd, a.nom_article, a.photo_article, a.prix_article, a.quantite_stock, a.id_article, cmd.frais_livraison, cmd.Montant_commande, cmd.statut
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
                                    <div class="col-12">
                                        <div class="card order-card p-3"  onclick="openOrderNotification('<?= $cmd['id_commande'] ?>', 'Boutique <?= $cmd['nom_boutique'] ?>', '<?= $cmd['nom'].' '.$cmd['prenom'] ?>', '<?= date('d/m/Y', strtotime($cmd['Date_commande'])); ?>', '<?= number_format($total, 0, '', ' ') ?> FCFA', '<?= $cmd['statut'] ?>', 'FCFA<?= number_format($cmd['Montant_commande'], 0, '', ' ') ?> FCFA', '<?= $cmd['adresse_livraison'] ?>', '<?= htmlspecialchars($Livraison) ?>', 'LIV-001', <?= htmlspecialchars($articlesJSON, ENT_QUOTES, 'UTF-8') ?>)">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="order-icon bg-light p-3 rounded-circle me-3">
                                                        <i class="fas fa-shopping-bag text-orange"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">#<?= $cmd['id_commande'] ?></h6>
                                                        <small class="text-muted">Boutique <?= $cmd['nom_boutique'] ?> • <?= date('d/m/Y', strtotime($cmd['Date_commande'])); ?></small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="fw-bold mb-1"><?= number_format($total, 0, '', ' ') ?> FCFA</div>
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
                                    <p style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Aucune commande pour l'instant</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Gestion des Livreurs -->
                    <div class="tab-pane fade" id="deliverers" role="tabpanel">
                        <div class="card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold">Gestion des Livreurs</h5>
                                <button type="button" class="btn btn-orange btn-sm" onclick="openAddDelivererModal()"><i class="fas fa-plus me-2"></i>Ajouter un Livreur</button>
                            </div>

                            <!-- Barre de recherche -->
                            <div class="mb-4">
                                <input type="text" class="form-control" id="searchDeliverer" placeholder="Rechercher un livreur..." onkeyup="filterDeliverers()">
                            </div>

                            <!-- Liste des Livreurs -->
                            <div class="row g-3" id="deliverersList">
                                <!-- Livreur 1 -->
                                <?php foreach($Livreurs as $lvr): ?>
                                    <div class="col-12">
                                        <div class="card deliverer-card p-3" onclick="openDelivererDetails('<?= $lvr['id_livreur'] ?>', '<?= $lvr['nom'].' '.$lvr['prenom'] ?>', '<?= $lvr['email'] ?>', '<?= $lvr['tel'] ?>', 'Douala-Cameroun', '<?= $lvr['statut'] ?>', 45, 6, 98, 2450000, 4.8, 24)">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="deliverer-avatar" style="overflow: hidden;"><img src="../ImagesBD/<?= $lvr['profil'] ?>" style="object-fit: cover; height: 100%; width: 100%;" alt="photo_livreur"></div>
                                                    <div class="ms-3">
                                                        <h6 class="mb-0 fw-bold"><?= $lvr['nom'].' '.$lvr['prenom'] ?></h6>
                                                        <small class="text-muted"><?= $lvr['email'] ?> • <?= $lvr['tel'] ?></small>
                                                        <div class="mt-2">
                                                            <span class="badge badge-success"><?= $lvr['statut'] ?></span>
                                                            <span class="badge badge-info">4.8/5.0</span>
                                                            <span class="badge badge-warning">24 avis</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="fw-bold mb-2">45 livraisons</div>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="event.stopPropagation(); openEditDelivererModal('<?= $lvr['id_livreur'] ?>', '<?= $lvr['nom'] ?>', '<?= $lvr['prenom'] ?>', '<?= $lvr['email'] ?>', '<?= $lvr['tel'] ?>', 'Douala-Cameroun', '<?= $lvr['statut'] ?>', '<?= $lvr['pass'] ?>')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation(); deleteDeliverer('<?= $lvr['id_livreur'] ?>', '<?= $lvr['nom'].' '.$lvr['prenom'] ?>')">
                                                        <a href="dashboard.php?supprimer=<?=$lvr['id_livreur']?>"><i class="fas fa-trash text-danger"></i></a>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php if(empty($commandes)): ?>
                                    <p style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Aucun livreur pour l'instant</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- === MODAL DÉTAILS COMMANDE === -->
        <div class="modal fade" id="orderDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="orderTitle"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="location.reload();"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Informations Client -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Informations Client</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Boutique</small>
                                    <div class="fw-bold" id="orderShop"></div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Client</small>
                                    <div class="fw-bold" id="orderClient"></div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <small class="text-muted">Date de commande</small>
                                    <div class="fw-bold" id="orderDate"></div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <small class="text-muted">Adresse de livraison</small>
                                    <div class="fw-bold" id="Adresse"></div>
                                </div>
                            </div>
                        </div><hr>

                        <!-- Articles de la Commande -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Articles commandés</h6>
                            <div class="articles-list" id="orderItemsList">
                        
                            </div>
                        </div><hr>

                        <!-- Vérification du Stock -->
                        <div id="section-stock-check" class="mb-4">
                            <h6 class="fw-bold mb-3">Vérification du Stock</h6>
                            <div class="stock-checker">
                                <div class="stock-item" id="stockItem">
                                     
                                </div>
                            </div>
                        </div><hr>

                        <!-- Résumé Financier -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Résumé Financier</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Sous-total</span>
                                <span class="fw-bold" id="orderTotal"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Frais de livraison</span>
                                <span class="fw-bold" id="orderFrais"></span>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-2">
                                <span class="fw-bold">Total</span>
                                <span class="fw-bold text-orange h5" id="grandTotal"></span>
                            </div>
                        </div><hr>

                        <p style="color: gray; text-align:center; display: none;" id="section-message-livre"><i class="fa-solid fa-circle-exclamation"></i></p>

                        <form action="" method="post" id="ordersNotes">
                            <input type="hidden" name="id_commande" id="id_commande">
                            <div id="section-actions" class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-danger flex-grow-1" onclick="rejectOrders()" id="rejectOrder">Refuser</button>
                                <button type="button" class="btn btn-orange flex-grow-1" onclick="acceptOrders()"  id="acceptOrder"><span id="btnText">Envoyer aux livreurs</span></button>
                            </div><div id="section-success-message"></div>

                            <div id="section-error-message" style="display: none;">
                                <div id="delete-orders" class="w-100">
                                    <label class="small text-muted mb-1">Entrez le motif de suppréssion</label>
                                    <div class="input-group">
                                        <textarea  class="form-control form-control-sm" id="other-action-textarea" placeholder="motif..." name="order_delete" required></textarea>
                                        <button class="btn btn-danger btn-sm" type="submit" name="action_commande" value="supprimer" id="btn_supprimer"><span id="btnTextSupp">Supprimer</span></button>
                                    </div>
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

        <!-- === MODAL COMPTE COMMERÇANT === -->
        <div class="modal fade" id="merchantAccountModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="merchantTitle">Gestion du Compte</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Informations Commerçant -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Informations du Commerçant</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Nom de la Boutique</small>
                                    <div class="fw-bold" id="merchantShopName"></div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Gérant</small>
                                    <div class="fw-bold" id="merchantManagerName"></div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <small class="text-muted">Email</small>
                                    <div class="fw-bold" id="merchantEmail"></div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <small class="text-muted">Téléphone</small>
                                    <div class="fw-bold" id="merchantPhone"></div>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <!-- Statistiques -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Statistiques</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="card p-3 text-center">
                                        <div class="h4 text-orange fw-bold" id="merchantOrdersCount"></div>
                                        <small class="text-muted">Commandes</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card p-3 text-center">
                                        <div class="h4 text-orange fw-bold" id="merchantArticlesCount"></div>
                                        <small class="text-muted">Articles</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card p-3 text-center">
                                        <div class="h4 text-orange fw-bold" id="merchantReviewsCount">8</div>
                                        <small class="text-muted">Avis</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <!-- Avis et commantaires -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Avis et notes</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="card p-3 text-center">
                                        <div class="h4 text-orange fw-bold" id="merchantOrdersCount"></div>
                                        <small class="text-muted">Commandes</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card p-3 text-center">
                                        <div class="h4 text-orange fw-bold" id="merchantArticlesCount"></div>
                                        <small class="text-muted">Articles</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card p-3 text-center">
                                        <div class="h4 text-orange fw-bold" id="merchantReviewsCount">8</div>
                                        <small class="text-muted">Avis</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <!-- Actions -->
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary flex-grow-1" data-bs-dismiss="modal">Fermer</button>
                            <button type="button" class="btn btn-orange flex-grow-1" id="btn-login-confirm">Acceder au compte</button>
                        </div>

                        <!-- Message de Connexion -->
                        <div id="section-login-message" class="alert alert-info mt-3" style="display: none;">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Redirection en cours...</strong> Vous allez être connecté au compte du commerçant.
                        </div>
                    </div>
                </div>
            </div>
        </div>

                                <!-- ============================================================
                                ======== MODAL AJOUT DES COMMERCANTS ==========================                                                     
                                ================================================================ -->
        <div class="modal fade" id="modalNewAccount" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header text-white p-4" style="border-radius: 20px 20px 0 0; background-color: #ffc107;">
                        <h5 class="modal-title fw-bold text-light"><i class="fas fa-id-badge me-2"></i> Nouvel Utilisateur</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <form action="" method="post" enctype="multipart/form-data" id="formNewAccount">
                        <input type="hidden" name="localisation" id="localisation">
                        <div class="modal-body p-4">
                            <div class="modal-body p-4">
                            <?php if(isset($_SESSION['errorMsg'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
                                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $_SESSION['errorMsg']; unset($_SESSION['errorMsg']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            <?php if(isset($_SESSION['successMsg'])): ?>
                                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
                                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $_SESSION['successMsg']; unset($_SESSION['successMsg']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nom</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i class="fas fa-user text-muted"></i></span>
                                    <input type="text" name="nom" class="form-control border-0 bg-light shadow-none" placeholder="Ex: Jean Luc" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">prénom</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i class="fas fa-user text-muted"></i></span>
                                    <input type="text" name="prenom" class="form-control border-0 bg-light shadow-none" placeholder="Ex: Jean Luc" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">E-mail</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i class="fas fa-user text-muted"></i></span>
                                    <input type="mail" name="email" class="form-control border-0 bg-light shadow-none" placeholder="Ex: Jean@Luc.com" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Date de naissance</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i class="fas fa-user text-muted"></i></span>
                                    <input type="date" name="date" class="form-control border-0 bg-light shadow-none" placeholder="Ex:  JJ/MM/AAAA" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Numéro WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i class="fab fa-whatsapp text-muted"></i></span>
                                    <input type="tel" name="tel" class="form-control border-0 bg-light shadow-none" placeholder="6XXXXXXXX" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nom de la boutique</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i class="fas fa-user text-muted"></i></span>
                                    <input type="text" name="nomBtq" class="form-control border-0 bg-light shadow-none" placeholder="Ex: Jean Luc" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Desciption de la boutique</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i class="fas fa-user text-muted"></i></span>
                                    <input type="text" name="desc" class="form-control border-0 bg-light shadow-none" placeholder="Ex: Jean Luc" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Profil de la boutique</label>
                                <div class="input-group">
                                    <input type="file" name="profil" class="form-control border-0 bg-light shadow-none" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Mot de passe</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i class="fas fa-user text-muted"></i></span>
                                    <input type="password" name="pass" class="form-control border-0 bg-light shadow-none" placeholder="********" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Confirmer mot de passe</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i class="fas fa-lock text-muted"></i></span>
                                    <input type="password" name="cpass" id="passInput" class="form-control border-0 bg-light shadow-none" placeholder="********" required>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" name="create_user" class="btn btn-primary rounded-pill px-4 shadow">
                                Créer le compte <i class="fas fa-paper-plane ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php include '../vendor/chatAI/discuss.php'; ?>

        <script src="../js/bootstrap.bundle.min.js"></script>
        <script>
            // === GESTION DES ONGLETS MOBILES ===
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const tab = link.dataset.tab;
                    
                    // Masquer tous les panneaux
                    document.querySelectorAll('.mobile-panel').forEach(panel => {
                        panel.classList.remove('active');
                    });
                    
                    // Afficher le panneau sélectionné
                    const panel = document.getElementById(`mobile-${tab}-panel`);
                    if (panel) {
                        panel.classList.add('active');
                    }
                    
                    // Mettre à jour les onglets actifs
                    document.querySelectorAll('.nav-link').forEach(l => {
                        l.classList.remove('active');
                    });
                    link.classList.add('active');
                });
            });

            // === GESTION DES NOTIFICATIONS ===
            const notifBell = document.getElementById('notifBellTop');
            const notifDropdown = document.getElementById('notifDropdownMenuTop');

            notifBell.addEventListener('click', () => {
                notifDropdown.classList.toggle('show');
            });

            document.addEventListener('click', (e) => {
                if (!notifBell.contains(e.target) && !notifDropdown.contains(e.target)) {
                    notifDropdown.classList.remove('show');
                }
            });

            // === NAVIGATION ===
            function goBack() {
                window.history.back();
            }

            // === MODALES COMMANDES ===
            function openOrderNotification(id, shop, client, date, total, status, bigTotal, address, frais, number, articles){
                document.getElementById('orderTitle').textContent = 'Commande #' + id;
                document.getElementById('orderShop').textContent = shop;
                document.getElementById('orderClient').textContent = client;
                document.getElementById('orderDate').textContent = date;
                document.getElementById('orderTotal').textContent = total;
                document.getElementById('grandTotal').textContent = bigTotal;
                document.getElementById('Adresse').textContent = address;
                document.getElementById('orderFrais').textContent = frais;
                document.getElementById('id_commande').value=id;

                const conteneur= document.getElementById('orderItemsList');
                conteneur.innerHTML="";
                articles.forEach(art=>{
                    const htmlArticles=`<div class="article-item">
                                            <div class="article-img">
                                                <img src="../ImagesBD/${art.photo_article}" alt="photo_article" style="height: 60px; width: 60px;">
                                            </div>
                                            <div class="article-details">
                                                <div class="article-name">${art.nom_article}</div>
                                                <div class="article-meta">Qté: ${art.quantite_cmd} • ${parseInt(art.prix_article).toLocaleString()} FCFA</div>
                                            </div>
                                            <div class="article-price">${parseInt(art.Montant_commande).toLocaleString()} FCFA</div>
                                        </div>`;
                    conteneur.insertAdjacentHTML('beforeend', htmlArticles);
                });

                const stock=document.getElementById('stockItem');
                stock.innerHTML="";
                articles.forEach(item=>{
                    const htmlStock=`<div>
                                        <div class="stock-name">${item.nom_article}</div>
                                        <small class="text-muted">Demandé: ${item.quantite_cmd} unité (s)</small>
                                    </div>
                                    <div class="stock-quantity">
                                        <span class="fw-bold">${item.quantite_stock} disponible (s)</span>
                                        <span class="stock-badge ${item.quantite_stock >= item.quantite_cmd ? 'available' : 'out'}">
                                            ${item.quantite_stock >= item.quantite_cmd ? 'OK' : 'Insuffisant'}
                                        </span>
                                    </div>`;
                    stock.insertAdjacentHTML('beforeend', htmlStock);
                    
                    const btn_accept=document.getElementById("acceptOrder");
                    const btn_reject=document.getElementById("rejectOrder");
                    const sectionForm=document.getElementById('ordersNotes');
                    const sectionLivre=document.getElementById('section-message-livre');

                    if(item.quantite_cmd>item.quantite_stock){
                        btn_accept.style.pointerEvents="none";
                        btn_accept.style.opacity="0.3";
                        btn_accept.style.pointerEvents="none";
                    }
                    
                    if(item.statut==='Livré'||item.statut==='livré'){
                        sectionForm.style.display="none";
                        sectionLivre.style.display='block';
                        if(item.frais==="Retrait en boutique"){
                            sectionLivre.textContent='Cette commande a déja été retirée!';
                        } 
                        else{
                            sectionLivre.textContent='Cette commande a déja été livrée!';
                        }
                    }
                    else if(item.statut==='Annulé'||item.statut==='annulé'){
                        sectionForm.style.display='none';
                        sectionLivre.style.display='block';
                        sectionLivre.textContent='Vous avez supprimer cette commande!';
                    }
                });
                document.getElementById('section-error-message').style.display = 'none';
                const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
                modal.show();
            }

            function rejectOrders() {
                if(confirm('Êtes-vous sûr de vouloir refuser cette commande ?')){
                    const sectionError = document.getElementById("section-error-message");
                    const form = document.getElementById('ordersNotes');
                    const btn = document.getElementById('btn_supprimer');
                    const btnText = document.getElementById('btnTextSupp');
                    const textarea = document.getElementById('other-action-textarea');

                    sectionError.style.display = "flex";
     
                    btn.onclick = function(e) {
                        e.preventDefault(); 

                        if (!form.checkValidity()) {
                            form.reportValidity();
                            return; 
                        }

                        btnText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Suppression en cours...';
                        btn.style.pointerEvents = "none"; 

                        setTimeout(() => {
                            btn.classList.remove('btn-danger');
                            btn.classList.add('btn-success');
                            btnText.innerHTML = '<i class="fas fa-check me-2"></i> Supprimé !';
                            btn.style.opacity = "0.3";
                            form.submit(); 
                        }, 2000);
                    };
                }
            }

            function acceptOrders(){
                document.getElementById('acceptOrder').addEventListener('click', function() {
                    const btn = this;
                    const btnText = document.getElementById('btnText');
                    btn.disabled = true;
                    btnText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Envoi en cours...';
                    setTimeout(() => {
                        btn.classList.remove('btn-orange');
                        btn.classList.add('btn-success');
                        btnText.innerHTML = '<i class="fas fa-check me-2"></i> Envoyé !';
                        
                       /*setTimeout(() => {
                            // bootstrap.Modal.getInstance(document.getElementById('orderDetailsModal')).hide();
                        }, 1500);*/
                        
                    }, 2000); 
                });
            }

            // === MODALES COMPTE COMMERÇANT ===
            function openMerchantAccount(id, shop, manager, email, phone, orders, articles, reviews) {
                document.getElementById('merchantTitle').textContent = 'Gestion du Compte - ' + shop;
                document.getElementById('merchantShopName').textContent = shop;
                document.getElementById('merchantManagerName').textContent = manager;
                document.getElementById('merchantEmail').textContent = email;
                document.getElementById('merchantPhone').textContent = phone;
                document.getElementById('merchantOrdersCount').textContent = orders;
                document.getElementById('merchantArticlesCount').textContent = articles;
                document.getElementById('merchantReviewsCount').textContent = reviews;

                document.getElementById('section-login-message').style.display = 'none';

                const loginBtn = document.getElementById('btn-login-confirm');
                loginBtn.onclick = function() {
                    loginToMerchantAccount(id);  
                };

                const modal = new bootstrap.Modal(document.getElementById('merchantAccountModal'));
                modal.show();
            }

            function loginToMerchantAccount(merchantId){
                document.getElementById('section-login-message').style.display = 'block';
                setTimeout(() => {
                    window.location.href = 'changerCompte.php?id=' + merchantId;
                    alert('Vous êtes maintenant connecté au compte du commerçant. Vous pouvez effectuer toutes les tâches de gestion.');
                    bootstrap.Modal.getInstance(document.getElementById('merchantAccountModal')).hide();
                }, 2000);
            }

            // === GESTION DES LIVREURS ===
            function openAddDelivererModal(){
                const modal = new bootstrap.Modal(document.getElementById('addDelivererModal'));
                modal.show();
            }

            function openEditDelivererModal(id, name, lastName, email, phone, address, status, pass) {
                document.getElementById('editDelivererId').value = id;
                document.getElementById('editDelivererName').value = name;
                document.getElementById('editDelivererLastName').value=lastName;
                document.getElementById('editDelivererEmail').value = email;
                document.getElementById('editDelivererPhone').value = phone;
                document.getElementById('editDelivererAddress').value = address;
                document.getElementById('editDelivererStatus').value = status;
                document.getElementById('editDelivererPassword').value = pass;
                document.getElementById('editDelivererConfirmPassword').value = pass;

                const modal = new bootstrap.Modal(document.getElementById('editDelivererModal'));
                modal.show();
            }

            function deleteDeliverer(id, name) {
                if (confirm('Etes-vous sur de vouloir supprimer le livreur "' + name + '" ?')) {
                    alert('Livreur "' + name + '" supprime avec succes !');
                    location.reload();
                }
            }

            function openDelivererDetails(id, name, email, phone, address, status, deliveries, pending, successRate, revenue, rating, reviewCount) {
                document.getElementById('delivererDetailsTitle').textContent = 'Details - ' + name;
                document.getElementById('detailsName').textContent = name;
                document.getElementById('detailsEmail').textContent = email;
                document.getElementById('detailsPhone').textContent = phone;
                document.getElementById('detailsAddress').textContent = address;
                document.getElementById('detailsStatus').textContent = status;
                document.getElementById('detailsStatus').className = status === 'Actif' ? 'badge badge-success' : 'badge badge-secondary';
                
                document.getElementById('statsDeliveries').textContent = deliveries;
                document.getElementById('statsPending').textContent = pending;
                document.getElementById('statsSuccessRate').textContent = successRate + '%';
                document.getElementById('statsRevenue').textContent = (revenue / 1000000).toFixed(2) + 'M';
                
                document.getElementById('detailsRating').textContent = rating;
                document.getElementById('detailsReviewCount').textContent = reviewCount;

                const modal = new bootstrap.Modal(document.getElementById('delivererDetailsModal'));
                modal.show();
            }

            function filterDeliverers(){
                const searchValue=document.getElementById('searchDeliverer').value.toLowerCase();
                const deliverersList=document.getElementById('deliverersList');
                const deliverers=deliverersList.getElementsByClassName('col-12');

                for(let i=0; i<deliverers.length; i++){
                    const card=deliverers[i];
                    const text=card.textContent.toLowerCase();
                    
                    if(text.includes(searchValue)){
                        card.style.display= '';
                    } 
                    else{
                        card.style.display='none';
                    }
                }
            }

            // === INITIALISATION ===
            document.addEventListener('DOMContentLoaded', () => {
                if(window.innerWidth < 992){
                    document.getElementById('mobile-dashboard-panel').classList.add('active');
                }
            });

            // === RESPONSIVE ===
            window.addEventListener('resize', () => {
                if(window.innerWidth < 992){
                    document.querySelector('.desktop-content').style.display = 'none';
                } 
                else{
                    document.querySelector('.desktop-content').style.display = 'block';
                }
            });

            //Réinitialiser le formulaire à la fermeture du modal ajout des comptes
            document.getElementById('modalNewAccount').addEventListener('hidden.bs.modal', function () {
                document.getElementById('formNewAccount').reset();
            });

            // Récupération de la géolocalisation
            window.onload=function(){
                if("geolocation" in navigator){
                    navigator.geolocation.getCurrentPosition(function(position){
                        const geoData={
                            lat:position.coords.latitude,
                            long:position.coords.longitude,
                            timestamp: new Date().toISOString()
                        };
                        document.getElementById('localisation').value=JSON.stringify(geoData);
                        console.log("position prete: "+document.getElementById('localisation').value);
                    }, function(error){
                        alert('Pour plus de performance lors de l\'utilisation de l\'application, veuillez acceptez la géolocalisation!');
                        console.warn('Erreur ou refus de géolocalisation: ', error.message);
                    });
                }
            };

            window.onload=function(){
                <?php if (isset($_SESSION['successMsg']) || isset($_SESSION['errorMsg'])): ?>
                    var myModalEl = document.getElementById('modalNewAccount');
                    if (myModalEl) {
                        var modalInstance = new bootstrap.Modal(myModalEl);
                        modalInstance.show();
                    } else {
                        console.error("Le modal avec l'ID 'modalNewAccount' est introuvable.");
                    }
                <?php endif; ?>
            };

            //GRAPHES ET STATISQUES
            // Configuration globale pour Chart.js
            Chart.defaults.font.family = 'Arial, sans-serif';
            Chart.defaults.color = '#666';

            // 1. Diagramme en Bandes (Bar Chart)
            const ctxBar = document.getElementById('barChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($categories); ?>,
                    datasets: [{
                        label: 'Commandes',
                        data: <?php echo json_encode($nbCommandes); ?>,
                        backgroundColor: '#ff8c00',  
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Permet de respecter la max-height
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { display: false } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 2. Diagramme Circulaire (Pie Chart)
            const ctxPie = document.getElementById('pieChart').getContext('2d');
            new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode($roles); ?>,
                    datasets: [{
                        data: <?php echo json_encode($nbUsers); ?>,
                        backgroundColor: ['#F59E0B', '#4E79A7', '#bdc3c7', '#76B7B2'],
                        hoverOffset: 10
                    }]
                },options:{
                    responsive:true,
                    maintainAspectRatio:false,
                    plugins:{
                        legend:{position:'bottom',labels:{boxWidth: 12}}
                    }
                }
            });
 
        </script>
        <div class="container"><button onclick="window.Location.href='accueil.php';">Accueil</button></div>
    </body>
</html>