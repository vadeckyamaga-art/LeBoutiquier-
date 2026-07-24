<?php
    session_start();
    include 'connexionBD.php';
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    file_put_contents('debug_log.txt', print_r($_POST, true));
    $successAvis='';
    $errorAvis='';
    $errorMsg='';
    $successMsg='';
    $action='';
    $usersID=$_SESSION['id'];

    if(!isset($_SESSION['id'] )){
        header('Location: connexion.php');
        exit();
    }
    if(!$_SESSION['compte']==='client'){
        header('Location: inscription.php');
        exit();
    }

    $queryclient=$conn->prepare("SELECT * FROM client WHERE id=?");
    $queryclient->execute([$usersID]);
    $clientRow=$queryclient->fetch();
    if($clientRow){
        $clientID=$clientRow['id_client'];
    }
    else{
        header('Location: connexion.php');
        exit();
    }

    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action_Info'])){
        $action=$_POST['action_Info'];
        $nom=htmlspecialchars($_POST['nom'])??'';
        $prenom=htmlspecialchars($_POST['prenom'])??'';
        $mail=htmlspecialchars($_POST['email'])??'';
        $tel=htmlspecialchars($_POST['tel'])??'';
        $cpass=$_POST['cpass']??'';
        $pass=$_POST['pass']??'';
        $usersID=$_SESSION['id'];

        try{
            if($action==='MAJInfo'){
                if(empty($nom)|| empty($prenom)|| empty($mail)|| empty($tel)|| empty($pass)|| empty($cpass)){
                    $_SESSION['errorAvis']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Veuillez remplir tous les champs avant de modifier!';
                    
                }
                if($cpass!==$pass){
                    $_SESSION['errorAvis']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Les deux mot de passe doivent correspondre!';
                     
                }
                else{
                    $stmtUpdateInfo=$conn->prepare("UPDATE utilisateur SET nom=?, prenom=?, email=?, pass=?, tel=? WHERE id=?");
                    $stmtUpdateInfo->execute([$nom, $prenom, $mail, $pass, $tel, $usersID]);
                    $_SESSION['successAvis']='<i class="fa-solid fa-check-circle" style="color: green;"></i>information modifié avec succès!';
                }
            }
        }
        catch(PDOException $e){
            die("Erreur: ".$e->getMessage());
        }
        header('Location: espaceclient.php');
        exit();
    }

    $query=$conn->prepare("SELECT * FROM utilisateur WHERE id=?");
    $query->execute([$usersID]);
    $user=$query->fetch();
    $tel_actuel=$user?$user['tel']:'';
    $clientNom=$user['nom'];
    $clientPrenom=$user['prenom'];
    $clientTel=$user['tel'];
    $clientemail=$user['email'];
    $clientPass=$user['pass'];

    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['envoyer_avis'])){
        if(isset($_POST['envoyer_avis'])){
            $note=(int)$_POST['note_valeur']??'';
            $commentaire=htmlspecialchars($_POST['commentaire'])??'';
            $id_commerçant=$_POST['id_commerçant_avis']??'';
            $id_avis="AVS-".date("Y")."-".random_int(1000, 9999);

            try{
                if($note>0){
                    $sqlInsert=$conn->prepare("INSERT INTO avis (id_avis, note, commentaire, id_commerçant, id_client) VALUES (?, ?, ?, ?, ?)");
                    $sqlInsert->execute([$id_avis, $note, $commentaire, $id_commerçant, $clientID]);
                    echo"Merçi pour votre avis!";
                    exit;
                }
                if(empty($commentaire)||$note<0 || empty($note)){
                    echo"note ou commentaire manquant!";
                    exit;
                }
            }
            catch(PDOException $e){
                die($e->getMessage());
                //exit();
            }
        }
    }
    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action_favori_article'])){
        $id_art=$_POST['id_article'];

        try{
            $check=$conn->prepare("SELECT 1 FROM favoris WHERE id_client=? AND id_article=?");
            $check->execute([$clientID, $id_art]);

            if($check->fetch()){
                $stmtCheck=$conn->prepare("DELETE FROM favoris WHERE id_client=? AND id_article=?");
                $stmtCheck->execute([$clientID, $id_art]);
                echo"removed";
            }
            else{
                $id_fav="FAV-".date("Y")."-".random_int(1000, 9999);
                $stmtFav=$conn->prepare("INSERT INTO favoris (id_favoris, id_article, id_client) VALUES (?, ?, ?)");
                $stmtFav->execute([$id_fav, $id_art, $clientID]);
                echo"added";
            }
        }
        catch(PDOException $e){
            die($e->getMessage());
        }
        exit();
    }

    if(isset($_POST['ajouter_panier'])){
        $idArt=$_POST['id_article'];

        $checkPanier=$conn->prepare("SELECT id_panier FROM panier WHERE id_article=? AND id_client=?");
        $checkPanier->execute([$idArt, $clientID]);

        if($checkPanier->rowCount()>0){
            $updatePanier=$conn->prepare("UPDATE panier SET Quantite=Quantite+1 WHERE id_article=? AND id_client=?");
            $updatePanier->execute([$idArt, $clientID]);
            echo"update";
        }
        else{
            $id_panier="PAN-".date("Y")."-".random_int(1000, 9999);
            $insertPanier=$conn->prepare("INSERT INTO panier (id_panier, Quantite, id_article, id_client) VALUES (?, 1, ?, ?)");
            $insertPanier->execute([$id_panier, $idArt, $clientID]);
            echo"insert";
        }
        exit;
    }
     
    if(isset($_POST['update_qty'])){
        $id_article=$_POST['id_article'];
        $new_qty= (int)$_POST['new_qty'];

        if($new_qty>0){
            $updatePAN=$conn->prepare("UPDATE panier SET Quantite=? WHERE id_article=? AND id_client=?");
            if($updatePAN->execute([$new_qty, $id_article, $clientID])){
                echo"success";
                exit();
            }
            else{
                echo"error_update";
                exit();
            }
        }
        exit();
    }

    if(isset($_POST['supprimer_article'])){
        $id_article=$_POST['id_article'];

        $deletePAN=$conn->prepare("DELETE FROM panier WHERE id_article=? AND id_client=?");
        if($deletePAN->execute([$id_article, $clientID])){
            echo"successDelete";
        }
        else{
            echo"error_delete";
        }
        exit();
    }

    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action_Avis'])){
        $id_avis="AVS-".date("Y")."-".random_int(1000, 9999);
        $id_boutique=$_POST['id_commerçant'];
        $noteBtq=$_POST['note_boutique']??'';
        $comBoutique=htmlspecialchars($_POST['commentaire']??'');

        if(empty($id_boutique)|| empty($comBoutique)||empty($noteBtq)){
            $_SESSION['errorMsg']="Note ou commentaire manquant";
        }
        else{
            try{
                $stmAvis=$conn->prepare("INSERT INTO avis (id_avis, note, commentaire, id_commerçant, id_client) VALUES (?, ?, ?, ?, ?)");
                $stmAvis->execute([$id_avis, $noteBtq, $comBoutique, $id_boutique, $clientID]);
                $_SESSION['successMsg']="Meçi pour votre avis!";
            }
            catch(PDOException $e){
                $_SESSION['errorMsg']="Erreur SQL:".$e->getMessage();
            }
        }
        header("Location: espaceclient.php?tab=avis#avis");
        exit();
    }


    $mesFavoris=[];
    $sqlShowFav="SELECT a.*, c.nom_cat, co.nom_boutique, co.profil_boutique, co.Quartier_boutique, co.id_commerçant
                 FROM article a, catégorie c, commerçant co, favoris f
                 WHERE c.id_cat=a.id_cat AND a.id_commerçant=co.id_commerçant AND f.id_article=a.id_article AND f.id_client=?
                 ORDER BY date_ajout DESC";
    $stmtShowFav=$conn->prepare($sqlShowFav);
    $stmtShowFav->execute([$clientID]);
    $mesFavoris=$stmtShowFav->fetchAll(PDO::FETCH_ASSOC);

    $afficherArticles=[];
    $sqlPrincipal=("SELECT a.*, c.nom_cat, co.nom_boutique, co.profil_boutique, co.Quartier_boutique, co.id_commerçant,
                                          (SELECT COUNT(*) 
                                          FROM favoris f
                                          WHERE f.id_article=a.id_article AND id_client=?) as is_fav
                                          FROM article a, catégorie c, commerçant co
                                          WHERE a.id_cat=c.id_cat AND a.id_commerçant= co.id_commerçant
                                          ORDER BY a.prix_article ASC"); 
    $stmtprincipal=$conn->prepare($sqlPrincipal);
    $stmtprincipal->execute([$clientID]);
    $afficherArticles=$stmtprincipal->fetchAll(PDO::FETCH_ASSOC);

    $sqlTroisArticles="SELECT nom_article, photo_article, prix_article, id_article
                       FROM article 
                       WHERE id_commerçant=? AND id_article!=?
                       LIMIT 3";
    $stmtTroisArticles=$conn->prepare($sqlTroisArticles);

    $articlesPanier=[];
    $sqlPanier="SELECT p.*, a.nom_article, a.prix_article, a.photo_article, a.id_article, co.Quartier_boutique, co.id_commerçant, co.nom_boutique, co.profil_boutique
                FROM panier p, article a, commerçant co
                WHERE p.id_article=a.id_article AND a.id_commerçant=co.id_commerçant AND p.id_client=?
                ORDER BY co.nom_boutique ASC";
    $stmtPanier=$conn->prepare($sqlPanier);
    $stmtPanier->execute([$clientID]);
    $articlesPanier=$stmtPanier->fetchAll(PDO::FETCH_ASSOC);

    $reqEnCours=$conn->prepare("SELECT cmd.*, dtl.*, COUNT(dtl.id_article) as nbr_articles
                                FROM commande cmd, details_commande dtl
                                WHERE cmd.id_commande=dtl.id_commande AND cmd.id_client=? AND cmd.statut !='Livré'
                                GROUP BY cmd.id_commande 
                                ORDER BY cmd.statut ASC, cmd.Date_commande DESC");
    $reqEnCours->execute([$clientID]);
    $commandeEnCours=$reqEnCours->fetchAll();

    $reqLivrees=$conn->prepare("SELECT cmd.*, dtl.*, COUNT(dtl.id_article) as nbr_articles
                                FROM commande cmd, details_commande dtl
                                WHERE cmd.id_commande=dtl.id_commande AND cmd.id_client=? AND cmd.statut ='Livré'
                                GROUP BY cmd.id_commande 
                                ORDER BY cmd.Date_commande");
    $reqLivrees->execute([$clientID]);
    $commandeLivrees=$reqLivrees->fetchAll();

?>



<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Espace Client - Leboutiquier</title>
        <link rel="stylesheet" href="../fontawesome/css/all.min.css">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../style/espaceclient.css">
        <link rel="stylesheet" href="../vendor/dist/leaflet.css" />
        <!--<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>-->
        <!-- PWA -->
        <link rel="manifest" href="../vendor/manifest.json">
        <meta name="theme-color" content="#EA580C">
        <link rel="icon" href="../Image/favoicon.ico" sizes="48x48">
        <link rel="apple-touch-icon" href="../Image/logo.png">
    </head>
    <body>
        <?php include 'splash.php'; ?>
        <!-- ==========================
            BARRE DE TITRE PRINCIPALE (logo & retour) + icône profil partout
        ========================== -->
        <div class="header-bar mb-3">
            <a onclick="window.location.href='accueil.php';" class="return-arrow" title="Accueil"><i class="fas fa-home"></i></a>
            <div class="header-bar-title">Leboutiquier</div>
            <button class="profile-icon-btn" id="openProfileModal" title="Mon profil" type="button" data-bs-toggle="modal" data-bs-target="#modal-profil">
                <i class="fas fa-user-circle"></i>
            </button>
        </div>

        <?php if (!empty($_SESSION['errorAvis'])): ?>
            <div id="JsErrorMsg" class="alert alert-danger alert-dismissible text-center fw-bold mb-3 w-75 align-center mx-auto">
        <?php 
                echo $_SESSION['errorAvis'];
                unset($_SESSION['errorAvis']);
        ?>
            </div>
        <?php endif;  ?><br>
        <?php if (!empty($_SESSION['successAvis'])): ?>
            <div id="JsSuccessMsg" class="alert alert-success alert-dismissible text-center fw-bold mb-3 w-75 align-center mx-auto">
        <?php 
                echo $_SESSION['successAvis'];
                unset($_SESSION['successAvis']);
        ?>
            </div>
        <?php endif;  ?>

        <div class="container my-4">
            <!-- =================================
                PARTIE: Carte principale "autour de moi"
            =================================== -->
            <div class="lb-section mb-4">
                <div class="lb-title mb-2"><i class="fas fa-map-marker-alt accent-color me-2"></i> Boutiques autour de moi</div>
                <div id="main-map">
                    <!-- Simulation : message en attente de branchement backend -->
                    <div class="text-muted text-center" style="padding: 3rem 0;">Chargement de la localisation...</div>
                </div>
            </div>
            <!-- =================================
                Onglets navigation (desktop)
            =================================== -->
            <ul class="nav nav-tabs mb-4 tabs-custom" id="clientTabs">
                <li class="nav-item"><a class="nav-link lb-nav-link active" data-bs-toggle="tab" href="#favoris" id="tab-favoris"><i class="fas fa-heart me-1 accent-color"></i> Favoris</a></li>
                <li class="nav-item"><a class="nav-link lb-nav-link" data-bs-toggle="tab" href="#panier" id="tab-panier"><i class="fas fa-shopping-cart me-1 accent-color"></i> Mon Panier</a></li>
                <li class="nav-item"><a class="nav-link lb-nav-link" data-bs-toggle="tab" href="#commande" id="tab-commande"><i class="fas fa-clipboard-list me-1 accent-color"></i> Ma commande</a></li>
                <li class="nav-item"><a class="nav-link lb-nav-link" data-bs-toggle="tab" href="#articles" id="tab-articles"><i class="fas fa-tags me-1 accent-color"></i> Articles des vendeurs</a></li>
                <li class="nav-item"><a class="nav-link lb-nav-link" data-bs-toggle="tab" href="#avis" id="tab-avis"><i class="fas fa-star me-1 accent-color"></i> Noter une boutique</a></li>
            </ul>
            <!-- =================================
                Navigation Onglets Mobile (en bas, version boutons)
            =================================== -->
            <nav class="bottom-nav d-flex d-sm-none" id="bottomTabNav" style="display:flex; position:fixed;">
                <button class="bottom-nav-link active" type="button" data-bs-toggle="tab" data-bs-target="#favoris" aria-controls="favoris" aria-selected="true" tabindex="0" id="mobile-favoris">
                    <i class="fas fa-heart"></i><span style="font-size:0.89em;">Favoris</span>
                </button>
                <button class="bottom-nav-link position-relative" type="button" data-bs-toggle="tab" data-bs-target="#panier" aria-controls="panier" aria-selected="false" tabindex="0" id="mobile-panier">
                    <i class="fas fa-shopping-cart"></i><span class="cart-badge" id="cart-badge">1</span>
                    <span style="font-size:0.89em;">Panier</span>
                </button>
                <button class="bottom-nav-link" type="button" data-bs-toggle="tab" data-bs-target="#commande" aria-controls="commande" aria-selected="false" tabindex="0" id="mobile-commande">
                    <i class="fas fa-clipboard-list"></i><span style="font-size:0.89em;">Commande</span>
                </button>
                <button class="bottom-nav-link" type="button" data-bs-toggle="tab" data-bs-target="#articles" aria-controls="articles" aria-selected="false" tabindex="0" id="mobile-articles">
                    <i class="fas fa-tags"></i><span style="font-size:0.89em;">Articles</span>
                </button>
                <button class="bottom-nav-link" type="button" data-bs-toggle="tab" data-bs-target="#avis" aria-controls="avis" aria-selected="false" tabindex="0" id="mobile-avis">
                    <i class="fas fa-star"></i><span style="font-size:0.89em;">Note</span>
                </button>
            </nav>

            <div class="tab-content">
                                <!-- ============================================================
                                ===================== ONGLET FAVORIS =============================                                                      
                                ================================================================ -->
                <div class="tab-pane fade show active" id="favoris">
                    <div class="lb-section">
                        <div class="lb-title"><i class="fas fa-heart accent-color me-1"></i> Mes articles favoris</div>
                        <div id="fav-list" class="row">
                            <?php if(empty($mesFavoris)): ?>
                                <p style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Vous n'avez pas encore d'articles favoris!</p>
                            <?php else: ?>
                                <?php foreach($mesFavoris as $fav): 
                                
                                    $coords=json_decode($fav['Quartier_boutique']??'', true);
                                    $lat=$coords['lat']??0;
                                    $lng=$coords['long']??0;

                                    $id_vendeurFav=$fav['id_commerçant'];
                                    $id_Article_Fav=$fav['id_article'];
                                    $stmtTroisArticles->execute([$id_vendeurFav, $id_Article_Fav]);
                                    $resultTroisFav=$stmtTroisArticles->fetchAll(PDO::FETCH_ASSOC);

                                    $jsonArticlesFav=[];
                                    foreach($resultTroisFav as $itemFav){
                                        $articlesModalFav[]=[
                                            "img"=>"../ImagesBD/".$itemFav['photo_article'],
                                            "id"=>$itemFav['id_article'],
                                            "titre"=>$itemFav['nom_article'],
                                            "prix"=>number_format($itemFav['prix_article'], 0, '.', ' ')." FCFA"
                                        ];
                                    }
                                    $articlesStringFav=json_encode($articlesModalFav);

                                ?>
                                    <div class="col-md-6 col-lg-4 favori-item">
                                        <div class="fav-item-card p-3">
                                            <div class="d-flex align-items-center">
                                                <img src="../ImagesBD/<?= htmlspecialchars($fav['photo_article']) ?>" alt="Article" class="rounded" style="width:65px;height:65px;object-fit: cover;">
                                                <div class="ms-3 flex-grow-1">
                                                    <div class="fw-bold"><?= htmlspecialchars($fav['nom_article']); ?></div>
                                                    <div class="small text-muted"><?= htmlspecialchars($fav['nom_cat']).' - '.htmlspecialchars($fav['prix_article']); ?> FCFA</div>
                                                    <div class="small">
                                                        Vendu par : <a href="#" class="shop-link open-shop-link" data-bs-toggle="modal" data-bs-target="#modal-boutique"
                                                            data-boutique='{
                                                                    "id_commerçant":"<?= htmlspecialchars($fav['id_commerçant']) ?>",
                                                                    "nom":"<?= htmlspecialchars($fav['nom_boutique']) ?>",
                                                                    "photo":"../ImagesBD/<?= htmlspecialchars($fav['profil_boutique']) ?>",
                                                                    "lat":<?= $lat ?>,
                                                                    "lng":<?= $lng ?>,
                                                                    "articles":<?= htmlspecialchars($articlesStringFav, ENT_QUOTES, "UTF-8") ?>
                                                            }'><?= htmlspecialchars($fav['nom_boutique']) ?></a>
                                                    </div>
                                                </div>
                                                <button class="btn btn-outline-danger btn-sm ms-2 remove-fav btn-favori-article" title="retirer des favoris" data-id-article="<?php echo $fav['id_article']; ?>"><i class="fas fa-heart-broken"></i></button>
                                            </div>
                                            <div class="mt-2 d-flex justify-content-end">
                                                <button class="btn btn-outline-secondary btn-sm btn-add-to-cart" data-id-article="<?= $fav['id_article'] ?>">Ajouter au panier</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <!-- Autres favoris fictifs ici si besoin -->
                        </div>
                        <div class="text-muted mt-3" id="empty-fav" style="display:none;">
                            <i class="far fa-heart"></i> Vous n'avez aucun article en favori.
                        </div>
                    </div>
                </div>
                                            <!-- ============================================================
                                            ========================= ONGLET PANIER =========================                                                      
                                            ================================================================ -->
                <div class="tab-pane fade" id="panier">
                    <div class="lb-section">
                        <div class="lb-title"><i class="fas fa-shopping-cart accent-color me-1"></i> Mon Panier</div>
                        <!-- ==================== BLOC-SHOPPING-CART GROUPÉ PAR BOUTIQUE ==================== -->
                        <div id="cart-list" class="row">
                            <!-- ======= Début Bloc Boutique ======= -->
                            <?php
                                
                                $currentBoutique="";
                                if(!empty($articlesPanier)):
                                    foreach($articlesPanier as $index=>$item):

                                        $coords=json_decode($item['Quartier_boutique']??'', true);
                                        $lat=$coords['lat']??0;
                                        $lng=$coords['long']??0;

                                        $id_vendeurPan=$item['id_commerçant'];
                                        $id_ArticlePan=$item['id_article'];
                                        $stmtTroisArticles->execute([$id_vendeurPan, $id_ArticlePan]);
                                        $resultTroisPan=$stmtTroisArticles->fetchAll(PDO::FETCH_ASSOC);

                                        $jsonArticlesPan=[];
                                        foreach($resultTroisPan as $itemPan){
                                            $articlesModalPan[]=[
                                                "img"=>"../ImagesBD/".$itemPan['photo_article'],
                                                "id"=>$itemPan['id_article'],
                                                "titre"=>$itemPan['nom_article'],
                                                "prix"=>number_format($itemPan['prix_article'], 0, '.', ' ')." FCFA"
                                            ];
                                        }
                                        $articlesStringPan=json_encode($articlesModalPan);
                                        
                                        if($currentBoutique!=$item['id_commerçant']):
                                            if($currentBoutique!=""):
                            ?>
                                                </div> <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mt-3">
                                                    <span class="fw-bold boutique-total" style="font-size: 1.03em;">Total de la boutique: <span class="boutique-total-price"></span></span>
                                                    <button class="btn lb-btn-orange mt-2 mt-md-0 ms-md-auto" data-bs-toggle="modal" data-bs-target="#paiementModal" data-boutique-id="<?= $prev_boutique_id ?>">Valider votre panier</button>
                                                </div>
                                                </div>
                                            <?php endif; 
                                            $currentBoutique=$item['id_commerçant'];
                                            $prev_boutique_id=$item['id_commerçant'];    
                                            ?>
                                        <div class="col-12 mb-4 boutique-cart-bloc">
                                            <!-- Checkbox de sélection boutique -->
                                            <div class="d-flex align-items-center mb-2">
                                                <input type="checkbox" class="form-check-input me-2 select-boutique" id="boutique-1" data-boutique-id="<?= $item['id_commerçant'] ?>">
                                                <img src="../ImagesBD/<?= $item['profil_boutique'] ?>" alt="Boutique" style="width:36px;height:36px;object-fit:cover;border-radius:50%;border:1.5px solid #FFD28F;">
                                                <span class="fw-bold ms-2" style="font-size:1.05em;">Boutique <?= $item['nom_boutique'] ?></span>
                                            </div>
                                            <!-- Fin Checkbox -->
                                            <div class="cart-items-boutique">
                                        <?php endif; ?>
                                                <!-- ======= Début Article du panier ======= -->
                                                <div class="cart-item-card p-3 mb-2 d-flex align-items-center flex-wrap flex-md-nowrap bg-white rounded shadow-sm border boutique-article" data-id-article="<?= $item['id_article'] ?>" data-prix="<?= $item['prix_article'] ?>">
                                                    <!-- Checkbox de sélection article -->
                                                    <input type="checkbox" class="form-check-input me-2 select-article" id="article-<?= $item['id_article'] ?>" data-article-id="<?= $item['id_article'] ?>" style="margin-left:6px;margin-right:12px;">
                                                    <!-- Image produit -->
                                                    <img src="../ImagesBD/<?= $item['photo_article'] ?>" alt="Article" class="rounded" style="width:60px;height:60px;object-fit:cover;">

                                                    <!-- Infos produit -->
                                                    <div class="ms-3 flex-grow-1 minw-120" style="min-width:120px;">
                                                        <div class="fw-bold"><?= $item['nom_article'] ?></div>
                                                        <!-- Bloc prix & quantité, responsive -->
                                                        <div class="d-flex align-items-center mt-1">
                                                            <div class="small text-muted me-2 prix-article" style="font-size:1.1em;"><?= number_format($item['prix_article'], 0, '', ' ') ?> FCFA</div>
                                                            <!-- ========== Quantité +/- ========== -->
                                                            <div class="input-group input-group-sm d-inline-flex align-items-center" style="width:100px;">
                                                                <button class="btn btn-outline-secondary btn-minus-qty" type="button" aria-label="Diminuer la quantité" style="padding:0 10px;" data-action="minus"><i class="fas fa-minus"></i></button>
                                                                <input type="text" class="form-control text-center mx-1 input-qty" value="<?= $item['Quantite'] ?>" style="width:32px;min-width:32px;" readonly>
                                                                <button class="btn btn-outline-secondary btn-plus-qty" type="button" aria-label="Augmenter la quantité" data-action="plus" style="padding:0 10px;"><i class="fas fa-plus"></i></button>
                                                            </div>
                                                            <!-- ========== Fin Quantité ========== -->
                                                        </div>
                                                        <!-- Fin prix & quantités -->
                                                        <div class="small mt-1">
                                                            Vendu par : 
                                                            <a href="#" class="shop-link open-shop-link" data-bs-toggle="modal" data-bs-target="#modal-boutique"
                                                                data-boutique='{
                                                                    "id_commerçant":"<?= htmlspecialchars($item['id_commerçant']) ?>",
                                                                    "nom":"<?= htmlspecialchars($item['nom_boutique']) ?>",
                                                                    "photo":"../ImagesBD/<?= htmlspecialchars($item['profil_boutique']) ?>",
                                                                    "lat":<?= $lat ?>,
                                                                    "lng":<?= $lng ?>,
                                                                    "articles":<?= htmlspecialchars($articlesStringPan, ENT_QUOTES, "UTF-8") ?>
                                                                }'><?= $item['nom_boutique'] ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-outline-danger btn-sm ms-auto mt-2 mt-md-0 btn-remove-item" title="Retirer du panier"><i class="fas fa-trash"></i></button>
                                                </div>
                                        <?php if($index===count($articlesPanier)-1):?>
                                                <!-- ======= Fin Article du panier ======= -->
                                            </div>
                                            <!-- ========== Résumé & Paiement Boutique ========== -->
                                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mt-3">
                                                <span class="fw-bold boutique-total" style="font-size:1.03em;">Total de la boutique : <span class="boutique-total-price"></span></span>
                                                <button class="btn lb-btn-orange mt-2 mt-md-0 ms-md-auto" id="" data-bs-toggle="modal" data-bs-target="#paiementModal" data-boutique-id="<?= $item['id_commerçant'] ?>" data-article-id="<?= $item['id_article'] ?>">Valider votre panier</button>
                                            </div>
                                            <!-- ========== Fin paiement boutique ========== -->
                                        </div>
                                        <?php endif; ?>
                                    <?php endforeach; else:?>
                                        <p style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Votre panier est vide!</p>
                                    <?php endif; ?>

                            <!-- ======= Fin Bloc Boutique ======= -->        
                        </div>
                        <!-- ======================= Fin BLOC-SHOPPING-CART GROUPÉ PAR BOUTIQUE ======================= -->
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Total (toutes boutiques):</span>
                            <span class="fw-bold" id="grand-total-price" style="font-size:1.15em;">FCFA</span>
                        </div>
                        <!-- Sur mobile, le bouton paiement reste lié à chaque boutique, donc pas de global -->
                    </div>
                </div>

                                <!-- ============================================================
                                ======================= ONGLET COMMANDE =========================                                                      
                                ================================================================ -->

                <div class="tab-pane fade" id="commande">
                    <div class="lb-section">
                        <div class="lb-title"><i class="fas fa-clipboard-list accent-color me-1"></i> Ma commande</div>
                        <ul class="nav nav-pills mb-3" id="orderStatusTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="en-cours-tab" data-bs-toggle="pill" data-bs-target="#en-cours" type="button" role="tab" aria-controls="en-cours" aria-selected="true">En cours</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="livrees-tab" data-bs-toggle="pill" data-bs-target="#livrees" type="button" role="tab" aria-controls="livrees" aria-selected="false">Livrées</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="commandeStatusTabContent">
                            <!-- Commandes en cours -->
                            <div class="tab-pane fade show active" id="en-cours" role="tabpanel" aria-labelledby="en-cours-tab">
                                <div id="current-orders">
                                    <!-- Exemple statique de commande en cours -->
                                    <?php 
                                        foreach($commandeEnCours as $cmdC): 
                                            $stmtcmd=$conn->prepare("SELECT a.nom_article as name, dtl.quantite_cmd as qty, dtl.prix_unitaire as price, co.nom_boutique as seller, cmd.frais_livraison as fees
                                                                     FROM details_commande dtl, article a, commerçant co, commande cmd
                                                                     WHERE dtl.id_article=a.id_article AND dtl.id_commande=cmd.id_commande AND a.id_commerçant=co.id_commerçant AND dtl.id_commande=?");
                                            $stmtcmd->execute([$cmdC['id_commande']]);
                                            $articlesCmd=$stmtcmd->fetchAll(PDO::FETCH_ASSOC);

                                            $totalArticles=0;
                                            $montantTotalCalculé=0;
                                            foreach($articlesCmd as $art){
                                                $totalArticles+=$art['qty'];
                                                $montantTotalCalculé+=($art['price']*$art['qty']);
                                            }
                                            
                                            $jsonArticlesCmd=htmlspecialchars(json_encode($articlesCmd), ENT_QUOTES, 'UTF-8');

                                    ?>
                                        <div class="order-card p-3 mb-3 border rounded" data-order-id="<?= $cmdC['id_commande'] ?>"
                                            data-order-status="<?= $cmdC['statut'] ?>"
                                            data-order-total="<?= number_format($cmdC['Montant_commande'], 0, '', ' ') ?> FCFA"
                                            data-order-date="<?= date('d/m/Y', strtotime($cmdC['Date_commande'])); ?>"
                                            data-order-articles="<?= $jsonArticlesCmd ?>">
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-bold">Commande #<?= $cmdC['id_commande'] ?></span>
                                                <?php if($cmdC['statut']==='En attente'): ?>
                                                    <span class="badge bg-warning text-dark"><?= $cmdC['statut'] ?></span>
                                                <?php endif; ?>
                                                <?php if($cmdC['statut']==='Annulé'): ?>
                                                    <span class="badge bg-danger text-dark"><?= $cmdC['statut'] ?></span>
                                                <?php endif; ?>
                                                <?php if($cmdC['statut']==='En Préparation'): ?>
                                                    <span class="badge bg-info text-dark"><?= $cmdC['statut'] ?></span>
                                                <?php endif; ?>
                                                <?php if($cmdC['statut']==='Livré' && $cmdC['frais_livraison']!=0):?>
                                                    <span class="status-badge status-delivered"><?= $cmd['statut'] ?></span>
                                                <?php endif; ?>
                                                <?php if($cmdC['statut']==='Livré' && $cmdC['frais_livraison']==0): ?>
                                                    <span class="status-badge status-delivered">Retiré</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="mt-2">
                                                <div><strong>Articles:</strong> <?= $totalArticles ?></div>
                                                <div><strong>Total:</strong> <?= number_format($montantTotalCalculé, 0, '', ' ') ?>  FCFA</div>
                                                <div><strong>Date :</strong> <?= date('d/m/Y', strtotime($cmdC['Date_commande'])); ?></div>
                                                <?php if($cmdC['statut']==='Annulé'): ?>
                                                    <div><strong>Motif de l'annulation: </strong><?= $cmdC['motif'] ?></div>
                                                <?php else: ?>
                                                    <div><strong>Code Retrait:</strong> <?= $cmdC['Code_retrait'] ?></div>
                                                    <small style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Utiliser ce code lors du retrait ou de la livraison de vos articles, NE PAS LE DIVULGUEZ!</small>
                                                <?php endif; ?>
                                            </div>
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-outline-primary btn-order-details" data-bs-toggle="modal" data-bs-target="#modal-order-detail">Voir le détail</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if(empty($commandeEnCours)): ?>
                                    <p style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Vous n'avez pas de commande en cours!</p>
                                <?php endif; ?>
                            </div>
                            <!-- Commandes livrées -->
                            <div class="tab-pane fade" id="livrees" role="tabpanel" aria-labelledby="livrees-tab">
                                <div id="delivered-orders">
                                    <!-- Exemple statique de commande déjà livrée -->
                                    <?php 
                                        foreach($commandeLivrees as $cmdL): 
                                            $stmtcmdL=$conn->prepare("SELECT a.nom_article as name, dtl.quantite_cmd as qty, dtl.prix_unitaire as price, co.nom_boutique as seller
                                                                     FROM details_commande dtl, article a, commerçant co
                                                                     WHERE dtl.id_article=a.id_article AND a.id_commerçant=co.id_commerçant AND dtl.id_commande=?");
                                            $stmtcmdL->execute([$cmdL['id_commande']]);
                                            $articlesCmdL=$stmtcmdL->fetchAll(PDO::FETCH_ASSOC);

                                            $totalArticlesL=0;
                                            $montantTotalCalculéL=0;
                                            foreach($articlesCmdL as $artL){
                                                $totalArticlesL+=$artL['qty'];
                                                $montantTotalCalculéL+=($artL['price']*$artL['qty']);
                                            }
                                            
                                            $jsonArticlesCmdL=htmlspecialchars(json_encode($articlesCmdL), ENT_QUOTES, 'UTF-8');


                                    ?>
                                        <div class="order-card p-3 mb-3 border rounded" data-order-id="<?= $cmdL['id_commande'] ?>"
                                            data-order-status="<?= $cmdL['statut'] ?>"
                                            data-order-total="<?= number_format($cmdL['Montant_commande'], 0, '', ' ') ?> FCFA"
                                            data-order-date="<?= date('d/m/Y', strtotime($cmdL['Date_commande'])); ?>"
                                            data-order-articles="<?= $jsonArticlesCmdL ?>">
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-bold">Commande #<?= $cmdL['id_commande'] ?></span>
                                                <span class="badge bg-success"><?= htmlspecialchars($cmdL['statut']) ?></span>
                                            </div>
                                            <div class="mt-2">
                                                <div><strong>Articles:</strong> <?= $totalArticlesL ?></div>
                                                <div><strong>Total:</strong> <?= number_format($montantTotalCalculé, 0, '', ' ') ?> FCFA</div>
                                                <div><strong>Date :</strong> <?= date('d/m/Y', strtotime($cmdC['Date_commande'])); ?></div>
                                            </div>
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-outline-primary btn-order-details" data-bs-toggle="modal" data-bs-target="#modal-order-detail">Voir le détail</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if(empty($commandeLivrees)): ?> 
                                    <p style="color: gray; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i>Vous n'avez pas de commande Livrée(s)!</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="articles">
                                <!-- ============================================================
                                ======================== ONGLET ARTICLE =========================                                                      
                                ================================================================ -->
                    <div class="lb-section">
                        <div class="lb-title"><i class="fas fa-tags accent-color me-1"></i> Articles des commerçants</div>
                        <div id="seller-articles-list" class="row">
                            <!-- Exemples d'articles vendeurs statiques pour la démo -->
                            <?php foreach ($afficherArticles as $articles):
                                     
                                $coords=json_decode($articles['Quartier_boutique']??'', true);
                                $lat=$coords['lat']??0;
                                $lng=$coords['long']??0;

                                $id_vendeur=$articles['id_commerçant'];
                                $id_article_actuel=$articles['id_article'];
                                $stmtTroisArticles->execute([$id_vendeur, $id_article_actuel]);
                                $resultTrois=$stmtTroisArticles->fetchAll(PDO::FETCH_ASSOC);

                                $jsonArticles=[];
                                foreach($resultTrois as $item){
                                    $articlesModal[]=[
                                        "img"=>"../ImagesBD/".$item['photo_article'],
                                        "id"=>$item['id_article'],
                                        "titre"=>$item['nom_article'],
                                        "prix"=>number_format($item['prix_article'], 0, '.', ' ')." FCFA"
                                    ];
                                }
                                $articlesString=json_encode($articlesModal);
                                $heartClass=($articles['is_fav']>0)?'text-danger fa-solid':'fa-regular';
                                    
                            ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100">
                                        <img src="../ImagesBD/<?= htmlspecialchars($articles['photo_article']) ?>" class="card-img-top" alt="Produit du commerçant">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?= htmlspecialchars($articles['nom_article']) ?></h5>
                                            <p class="card-text mb-1"><?= htmlspecialchars($articles['nom_cat']).' - '.number_format($articles['prix_article'], 0, '.', ' ')." FCFA"  ; ?></p>
                                            <span class="text-muted small">Vendu par : <a href="#" class="shop-link open-shop-link" data-bs-toggle="modal" data-bs-target="#modal-boutique"
                                                data-boutique='{
                                                    "is_fav":"<?= $heartClass ?>",
                                                    "id_commerçant":"<?= htmlspecialchars($articles['id_commerçant']) ?>",
                                                    "nom":"<?= htmlspecialchars($articles['nom_boutique']) ?>",
                                                    "photo":"../ImagesBD/<?= htmlspecialchars($articles['profil_boutique']) ?>",
                                                    "lat":<?= $lat ?>,
                                                    "lng":<?= $lng ?>,
                                                    "articles":<?= htmlspecialchars($articlesString, ENT_QUOTES, "UTF-8") ?>
                                                }', data-fav='{
                                                              "is_fav":<?= $heartClass ?>}'><?= htmlspecialchars($articles['nom_boutique']) ?></a></span>
                                            <button class="btn-favori-article bg-light border-0 text-end" data-id-article="<?php echo $articles['id_article']; ?>"><i class="<?= $heartClass ?> fa-heart"></i></button>
                                            <div class="mt-auto">
                                                <button class="btn btn-outline-primary btn-sm w-100 mt-3 btn-add-to-cart" data-id-article="<?= $articles['id_article'] ?>">Ajouter au panier</button>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            <?php endforeach;  ?>
                            <!-- Plus d'articles commerçants ici -->
                        </div><br><br><br><br>
                        <div class="text-muted mt-3" id="empty-articles" style="display:none;">
                            <i class="far fa-tags"></i> Aucun article disponible pour le moment.
                        </div>
                    </div>
                </div>


                                <!-- ============================================================
                                =================== ONGLET NOTER BOUTIQUE =========================                                                      
                                ================================================================ -->

                <div class="tab-pane fade" id="avis">
                    <div class="lb-section">
                        <div class="lb-title"><i class="fas fa-star accent-color me-1"></i> Noter une boutique</div>
                        <form id="rating-form" method="post">
                            <input type="hidden" name="action_Avis" value="envoyer_note">
                            <input type="hidden" name="note_boutique" id="note_boutique_input" value="0">
                            <div class="mb-3">
                                <label for="boutiqueSelect" class="form-label">Choisissez une boutique à noter :</label>
                                <select class="form-select" id="boutiqueSelect" name="id_commerçant" required>
                                    <option value="" disabled selected>-- Sélectionner une boutique --</option>
                                    <?php  
                                        $query=$conn->query("SELECT id_commerçant, nom_boutique FROM commerçant ORDER BY  nom_boutique DESC");
                                        while($btq=$query->fetch()):
                                    ?>
                                            <option value="<?= $btq['id_commerçant'] ?>"><?= htmlspecialchars($btq['nom_boutique']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Votre note :</label>
                                <div>
                                    <span class="star-rating" style="font-size:1.8em;color:#f56c1c;">
                                        <i class="far fa-star" data-value="1"></i>
                                        <i class="far fa-star" data-value="2"></i>
                                        <i class="far fa-star" data-value="3"></i>
                                        <i class="far fa-star" data-value="4"></i>
                                        <i class="far fa-star" data-value="5"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Avis (optionnel):</label>
                                <textarea class="form-control" rows="2" placeholder="Votre message..." name="commentaire"></textarea>
                            </div>
                            <button type="submit" class="btn lb-btn-orange" onclick="submitFormAvis();">Envoyer l'avis</button>
                            <script>
                                function submitFormAvis(){
                                    console.log("Forçage de l'envoi...");
                                    var form=document.getElementById('rating-form');
                                    if(form){
                                        form.submit();
                                    }
                                    else{
                                        alert("LE FORMULAIRE EST INTROUVABLE");
                                    }
                                }
                            </script>
                        </form>
                        <?php if (!empty($_SESSION['errorMsg'])): ?>
                            <p id="JsErrorMsg" class="text-danger text-start fw-bold">
                        <?php 
                                echo $_SESSION['errorMsg'];
                                unset($_SESSION['errorMsg']);
                        ?>
                            </p>
                        <?php endif;  ?><br>
                        <?php if (!empty($_SESSION['successMsg'])): ?>
                            <p id="JsSuccessMsg" class="text-success text-start fw-bold">
                        <?php 
                                echo $_SESSION['successMsg'];
                                unset($_SESSION['successMsg']);
                        ?>
                            </p>
                        <?php endif;  ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==============================
            MODAL: PROFIL UTILISATEUR
        =============================== -->
        <div class="modal fade" id="modal-profil" tabindex="-1" aria-labelledby="modalProfilLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="form-profil" method="POST" action="">
                        <input type="hidden" name="action_Info" value="MAJInfo">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalProfilLabel"><i class="fas fa-user-circle accent-color"></i> Mon Profil</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3 text-center">
                                <i class="fas fa-user-circle" style="font-size:3em; color:#f56c1c;"></i>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nom</label>
                                <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($clientNom) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Prenom</label>
                                <input type="text" class="form-control" name="prenom" value="<?= htmlspecialchars($clientPrenom) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Téléphone</label>
                                <input type="number" class="form-control" name="tel" value="<?= htmlspecialchars($clientTel) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($clientemail) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mot de Passe</label>
                                <input type="text" class="form-control" name="pass" value="<?= htmlspecialchars($clientPass) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Confirmer votre mot de passe</label>
                                <input type="text" class="form-control" name="cpass" value="<?= htmlspecialchars($clientPass) ?>">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                            <button type="button" class="btn lb-btn-orange" onclick="submitForm();">Enregistrer les modifications</button>
                            <script>
                                function submitForm(){
                                    console.log("Forçage de l'envoi...");
                                    var form=document.getElementById('form-profil');
                                    if(form){
                                        form.submit();
                                    }
                                    else{
                                        alert("LE FORMULAIRE EST INTROUVABLE");
                                    }
                                }
                            </script>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ==============================
            MODAL: DETAIL D'UNE COMMANDE (NOUVEAU)
        =============================== -->
        <div class="modal fade" id="modal-order-detail" tabindex="-1" aria-labelledby="modal-order-detail-label" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-order-detail-label"><i class="fas fa-clipboard-list accent-color"></i> Détail de la commande</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <div id="order-detail-content">
                            <div class="text-center text-muted my-4">Chargement du détail...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==============================
            MODAL: Affiche détail d'une boutique lors d'un clic avec carte, articles, avis
        =============================== -->
        <div class="modal fade" id="modal-boutique" tabindex="-1" aria-labelledby="modal-boutique-label" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content" id="modal-boutique-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-boutique-label"><i class="fas fa-store accent-color"></i> <span id="modal-boutique-nom"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <img src="" id="modal-boutique-photo" alt="Photo de la boutique">
                            <div class="mt-2">
                                <span class="fw-bold" id="modal-boutique-nomhead"></span>
                                <span class="seller-distance-badge" id="modal-boutique-distance"></span>
                            </div>
                            <div id="modal-boutique-adresse" class="text-muted small mt-1"></div>
                        </div>
                        <div class="mb-3">
                            <div class="fw-semibold mb-1" id="distace"><i class="fas fa-map-marked-alt accent-color"></i> Distance &amp; Carte :</div>
                            <div id="boutique-map">
                                <div class="text-center text-muted my-4" id="boutique-map-loading">Chargement de la carte...</div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="fw-semibold mb-1"><i class="fas fa-boxes accent-color"></i> Quelques articles proposés:</div>
                            <div id="modal-boutique-articles" class="">
                            </div>
                        </div>
                        <hr>
                        <div class="boutique-rating-section mt-3">
                            <div class="fw-semibold"><i class="fas fa-star accent-color me-1"></i> Laisser un avis sur cette boutique</div>
                            <form id="modal-boutique-avis-form" method="post">
                                <input type="hidden" name="note_valeur" id="note-input" value="0">
                                <input type="hidden" name="id_commerçant_avis" id="id_commerçant" value="">
                                <input type="hidden" name="id_client_avis">
                                <div class="mb-2 mt-2">
                                    <span class="star-rating" style="font-size:1.5em;color:#f7b930;">
                                        <i class="far fa-star" data-value="1"></i>
                                        <i class="far fa-star" data-value="2"></i>
                                        <i class="far fa-star" data-value="3"></i>
                                        <i class="far fa-star" data-value="4"></i>
                                        <i class="far fa-star" data-value="5"></i>
                                    </span>
                                </div>
                                <div class="mb-2">
                                    <textarea id="modal-boutique-comment" class="form-control form-control-sm" placeholder="Votre commentaire sur la boutique..." rows="2" name="commentaire"></textarea>
                                </div>
                                <div class="mb-2 text-end">
                                    <button type="submit" class="btn btn-sm lb-btn-orange" name="envoyer_avis">Envoyer mon avis</button>
                                </div>
                                <div id="modal-boutique-avis-confirm" class="text-success small"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==============================
            MODAL: Paiement/livraison (lancé lors d'un achat)
        =============================== -->
        <div class="modal fade" id="paiementModal" tabindex="-1" aria-labelledby="paiementModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content" id="form-paiement">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paiementModalLabel"><i class="fas fa-credit-card accent-color"></i>Ma Commande</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer" id="btn-close-modal"></button>
                    </div>
                    <div class="modal-body" id="modal-scroll-body">
                        <form action="" id="form-final-commande">
                            <input type="hidden" name="id_boutique_modal" id="id_boutique_modal">
                            <input type="hidden" name="id_article_selectionne" id="modal-id-article">
                            <input type="hidden" name="id_client_modal" value="<?= $clientID ?>">
                            <div id="status-message" class="mb-3"></div>
                            <p class="text-muted small">Comment souhaitez-vous récupérer vos articles ?</p>
                            <div class="selection-card mb-3">
                                <input type="radio" name="type_achat" id="type_retrait" value="retrait" class="btn-check" checked>
                                <label for="type_retrait" class="btn btn-outline-secondary w-100 p-3 text-start d-flex align-items-center">
                                    <i class="fas fa-store fa-2x me-3"></i>
                                    <div>
                                        <div class="fw-bold">Retrait en boutique</div>
                                        <div class="small">Gratuit - Passez récupérer vos colis</div>
                                    </div>
                                </label>
                            </div>
                            <div class="selection-card mb-3">
                                <input type="radio" name="type_achat" id="type_livraison" value="livraison" class="btn-check">
                                <label for="type_livraison" class="btn btn-outline-secondary w-100 p-3 text-start d-flex align-items-center">
                                    <i class="fas fa-truck fa-2x me-3"></i>
                                    <div>
                                        <div class="fw-bold">Livraison à domicile</div>
                                        <div class="small text-orange fw-bold" id="display-frais">+ <span id="valeur-frais">...</span> FCFA</div>
                                    </div>
                                </label>
                            </div>
                            <div id="zone-adresse" class="mb-3 d-none">
                                <label for="" class="form-label small fw-bold">Adresse de livraison préçise :</label>
                                <textarea name="adresse_livraison" rows="2" class="form-control"  placeholder="Quartier, rue, indication particulière..." id="adresse_livraison"></textarea>
                                <label for="" class="form-label small fw-bold">Numéro de téléphone pour la livraison :</label>
                                <input type="text" class="form-control" name="telephone_livraison", id="telephone_livraison" value="<?php echo $tel_actuel; ?>">
                                <small class="text-muted"><i class="fa-solid fa-circle-exclamation"></i>Vous pouvez modifier ce numéro si besoin</small>
                            </div>
                            <div class="bg-light p-3 rounded mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Total articles :</span>
                                    <span id="modal-total-articles" class="fw-bold">0 FCFA</span>
                                </div>
                                <div class="d-flex justify-content-between text-orange d-none" id="row-frais">
                                    <span>Frais de livraison :</span>
                                    <span id="modal-frais-montant" class="fw-bold">0 FCFA</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between h5 mb-0">
                                    <span>Total à payer :</span>
                                    <span id="modal-total-final" class="text-primary fw-bold">0 FCFA</span>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn lb-btn-orange" id="btn-submit-order">Valider</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php include '../vendor/chatAI/discuss.php'; ?>

        <!-- ========================== 
            FOOTER GENERAL SITE
        ============================ -->
        <footer class="lb-footer-main mt-5 pt-4" id="footer">
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
        <!-- =========================
            SCRIPTS JS DE BASE (uniquement bootstrap et leaflet sans intégration démo)
        ============================ -->
        <script src="../js/bootstrap.min.js"></script>
        <script src="../vendor/dist/leaflet.js"></script>
        <!--<script src="https://unpkg.com/leaflet/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>-->

        <script>

            //GESTION DES FAVORIS
            document.addEventListener('click', function(e){
                try{
                    const btn=e.target.closest('.btn-favori-article');
                    if(!btn) return;

                    e.preventDefault();
                    e.stopImmediatePropagation();

                    const idArt=btn.getAttribute('data-id-article');
                    console.log("ID Article: ", idArt);
                    if(!idArt||idArt==="undefined")return;

                    const allbuttons=document.querySelectorAll('.btn-favori-article');
                    const itemContainer=btn.closest('.favori-item');
                    allbuttons.forEach(button=>{
                        if(button.getAttribute('data-id-article')===idArt){
                            const icon=button.querySelector('i, svg');
                            if(icon){
                                icon.classList.toggle('fa-regular');
                                icon.classList.toggle('fa-solid');
                                icon.classList.toggle('text-danger');
                            }
                            if(itemContainer){
                                itemContainer.style.transform="translateX(50px)";
                                itemContainer.style.opacity="0";
                                setTimeout(()=>itemContainer.remove(), 300);
                            }
                        }
                    });
                    const formData=new FormData();
                    formData.append('action_favori_article', 'true');
                    formData.append('id_article', idArt);

                    fetch('espaceclient.php',{
                        method: 'POST',
                        body: formData
                    }).then(response=>response.text()).then(data=>{
                            console.log("Reponse serveur: ",data.trim());  
                    }).catch(error=>{
                        console.log('Erreur: ', error);
                    })
                }
                catch(error){
                    console.log("Erreur dans le script favoris: ",error);
                }
            }) 

            document.addEventListener('click', function(e){
                try{
                    const btn=e.target.closest('.btn-add-to-cart');
                    if(!btn) return;

                    e.preventDefault();

                    const idArt=btn.getAttribute('data-id-article');
                    const originalText=btn.innerText;
                    btn.innerText="Ajout...";
                    btn.disable=true;

                    const fd=new FormData();
                    fd.append('ajouter_panier', 'true');
                    fd.append('id_article', idArt);

                    fetch('espaceclient.php',{
                        method: 'POST',
                        body: fd,
                    }).then(response=>response.text()).then(data=>{
                            console.log("Reponse serveur: ",data.trim());  
                            if(data.trim()==='insert'||data.trim()==='update'){
                                btn.innerText="Patienter...";

                                setTimeout(()=>{
                                    btn.innerText='Ajouté!';
                                    if(btn.style.bacgroundColor="#0d6efd"){
                                        btn.style.backgroundColor="#198754";
                                        btn.style.borderColor="#198754";
                                        btn.style.color="white";
                                    }
                                    else{
                                        btn.classList.replace('btn-outline-secondary', 'btn-success');
                                    }
                                    btn.style.pointerEvents="none";
                                    btn.style.opacity="0.8";
                                    btn.disable=false;
                                    setTimeout(()=>{location.reload();}, 2000);
                                }, 2000);
                            }
                    }).catch(error=>{
                        console.log('Erreur Panier: ', error);
                        btn.disable=false;
                    })
                }
                catch(error){
                    console.log("Erreur lors de l'ajout au panier: ",error);
                }
            })
        
            document.addEventListener('DOMContentLoaded', function() {
                // Affiche la bottom nav si affichage mobile (ou tablette <= 991px)
                function isMobileOrTablet() {
                    return window.matchMedia("(max-width: 991px)").matches;
                }

                function showBottomNavIfMobile() {
                    var bottomNav = document.getElementById('bottomTabNav');
                    if (isMobileOrTablet()) {
                        bottomNav.style.display = 'flex';
                    } else {
                        bottomNav.style.display = 'none';
                    }
                }
                showBottomNavIfMobile();
                window.addEventListener('resize', showBottomNavIfMobile);

                // Ajoute le comportement pour les boutons mobile
                var mobileButtons = document.querySelectorAll('#bottomTabNav .bottom-nav-link');
                var tabContent = document.querySelector('.tab-content');
                var panes = tabContent.querySelectorAll('.tab-pane');

                function setActiveTab(tabId) {
                    document.querySelectorAll('.nav-link.lb-nav-link').forEach(function(navLink) {
                        navLink.classList.remove('active');
                    });
                    mobileButtons.forEach(function(btn) {
                        btn.classList.remove('active');
                    });

                    panes.forEach(function(pane) {
                        pane.classList.remove('show', 'active');
                    });

                    // Active tab desktop
                    var tabNav = document.querySelector('.nav-link.lb-nav-link[href="#' + tabId + '"]');
                    if (tabNav) tabNav.classList.add('active');

                    // Mobile nav activate
                    var mBtn = document.querySelector('#bottomTabNav .bottom-nav-link[data-bs-target="#' + tabId + '"]');
                    if (mBtn) mBtn.classList.add('active');

                    var targetPane = document.getElementById(tabId);
                    if (targetPane) {
                        targetPane.classList.add('show', 'active');
                    }
                }

                mobileButtons.forEach(function(btn) {
                    btn.addEventListener('click', function(e) {
                        var target = btn.getAttribute('data-bs-target') || btn.getAttribute('href');
                        // test bouton profil (sans content pane) : ne fait rien de plus (modal automatique)
                        if (btn.id === 'mobile-profile') return;
                        if (!target) return;
                        var tabId = target.replace(/^#/, '');
                        setActiveTab(tabId);
                    });
                });

                // Desktop tab: switch content on click to show the correct pane
                document.querySelectorAll('.nav-link.lb-nav-link').forEach(function(tab) {
                    tab.addEventListener('click', function(e){
                        var href = tab.getAttribute('href');
                        if (!href || !href.startsWith('#')) return;
                        e.preventDefault();
                        var tabId = href.replace(/^#/, '');
                        setActiveTab(tabId);
                    });
                });

                // Sous-tabs "Commande": Affichage des onglets "En cours"/"Livrées"
                var orderStatusTabs = document.querySelectorAll('#orderStatusTabs .nav-link');
                var orderTabPanes = document.querySelectorAll('#commandeStatusTabContent .tab-pane');
                if(orderStatusTabs.length){
                    orderStatusTabs.forEach(function(tab){
                        tab.addEventListener('click', function(e){
                            e.preventDefault();
                            orderStatusTabs.forEach(function(t){t.classList.remove('active');});
                            tab.classList.add('active');
                            orderTabPanes.forEach(function(pane){
                                pane.classList.remove('show','active');
                            });
                            var tabId = tab.getAttribute('data-bs-target').replace(/^#/, '');
                            var activePane = document.getElementById(tabId);
                            if(activePane){
                                activePane.classList.add('show','active');
                            }
                        });
                    });
                }

                // Ajout JS : Gestion dynamique affichage champs selon moyen de paiement (panier)
                var moyenPaiement = document.getElementById('moyen-paiement');
                function hideAllPaymentFields() {
                    document.getElementById('field-mobilemoney').classList.add('d-none');
                    /*document.getElementById('field-cartebancaire').classList.add('d-none');
                    document.getElementById('field-virement').classList.add('d-none');
                    document.getElementById('field-autre').classList.add('d-none');*/
                }
                if(moyenPaiement){
                    moyenPaiement.addEventListener('change', function() {
                        hideAllPaymentFields();
                        var value = moyenPaiement.value;
                        if(value === "mobilemoney") {
                            document.getElementById('field-mobilemoney').classList.remove('d-none');
                        } /*else if(value === "cartebancaire") {
                            document.getElementById('field-cartebancaire').classList.remove('d-none');
                        } else if(value === "virement") {
                            document.getElementById('field-virement').classList.remove('d-none');
                        } else if(value === "autre") {
                            document.getElementById('field-autre').classList.remove('d-none');
                        }*/
                    });
                }

                // Support pour rating stars dans "Noter une boutique"
                var ratingStars = document.querySelectorAll('.star-rating .fa-star');
                if (ratingStars.length > 0) {
                    ratingStars.forEach(function(star) {
                        star.addEventListener('mouseenter', function() {
                            var starValue = parseInt(star.dataset.value);
                            ratingStars.forEach(function(s, idx) {
                                s.classList.toggle('fas', idx < starValue);
                                s.classList.toggle('far', idx >= starValue);
                            });
                        });
                        star.addEventListener('mouseleave', function() {
                            ratingStars.forEach(function(s) {
                                s.classList.remove('fas');
                                s.classList.add('far');
                            });
                        });
                        star.addEventListener('click', function() {
                            var starValue = parseInt(star.dataset.value);
                            document.getElementById('note_boutique_input').value=starValue;
                            ratingStars.forEach(function(s, idx) {
                                s.classList.toggle('fas', idx < starValue);
                                s.classList.toggle('far', idx >= starValue);
                                s.setAttribute('data-selected', idx < starValue ? '1' : '0');
                            });
                        });
                    });
                    // Reset star hover on mouse out
                    document.querySelector('.star-rating').addEventListener('mouseleave', function() {
                        var selected = 0;
                        ratingStars.forEach(function(s, idx) {
                            if (s.getAttribute('data-selected') === '1') selected = idx + 1;
                        });
                        ratingStars.forEach(function(s, idx) {
                            s.classList.toggle('fas', idx < selected);
                            s.classList.toggle('far', idx >= selected);
                        });
                    });
                }

                var profileForm = document.getElementById('form-profil');
                if(profileForm){
                    profileForm.addEventListener('submit', function(e){
                        e.preventDefault();
                        var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-profil'));
                        modal.hide();
                    });
                }

                // Ajout : Affichage du détail de la commande sur ouverture du modal
                function escapeHTML(str) {
                    return str.replace(/[&<>"']/g, function(m) {
                        return ({
                            '&':'&amp;',
                            '<':'&lt;',
                            '>':'&gt;',
                            '"':'&quot;',
                            "'":'&#39;'
                        })[m];
                    });
                }

                function showOrderDetailFromCard(orderCard) {
                    var orderId = orderCard.getAttribute('data-order-id') || '';
                    var orderStatus = orderCard.getAttribute('data-order-status') || '';
                    var orderTotal = orderCard.getAttribute('data-order-total') || '';
                    var orderDate = orderCard.getAttribute('data-order-date') || '';
                    var articlesRaw = orderCard.getAttribute('data-order-articles');
                    var articles = [];
                    try { 
                        if (articlesRaw) articles = JSON.parse(articlesRaw.replace(/&quot;/g,'"'));
                    } 
                    catch(e) { 
                        console.error("Erreur JSON:", e);
                        articles = []; 
                    }

                    var html = '';
                    html += `<div class="mb-2"><strong>Numéro :</strong> #${escapeHTML(orderId)}</div>`;
                    html += `<div class="mb-2"><strong>Date :</strong> ${escapeHTML(orderDate)}</div>`;
                    html += `<div class="mb-2"><strong>Statut :</strong> <span class="badge ${
                        orderStatus.includes('Livré') ? 'bg-success' :
                        orderStatus.includes('En attente') ? 'bg-warning text-dark':
                        orderStatus.includes('En Préparation')? 'bg-info text-dark':'bg-danger text-dark'
                    }">${escapeHTML(orderStatus)}</span></div>`;
                    html += `<div class="mt-4 mb-2 fw-semibold">Articles commandés :</div>`;
                    if (articles && articles.length) {
                        html += '<table class="table table-sm"><thead><tr><th>Produit</th><th>Vendeur</th><th>Qté</th><th>Prix</th></tr></thead><tbody>';
                        articles.forEach(function(a){
                            html += `<tr>
                                <td>${escapeHTML(a.name)}</td>
                                <td>${escapeHTML(a.seller)}</td>
                                <td class="text-center">${escapeHTML(a.qty+"")}</td>
                                <td>${escapeHTML(a.price)}</td>
                            </tr>`;
                        });
                        html += '</tbody></table>';
                    } 
                    else {
                        html += '<div>Aucun article trouvé pour cette commande.</div>';
                    }
                    html += `<div class="mt-3 mb-2 text-end"><strong>Total :</strong> <span>${escapeHTML(orderTotal)}</span></div>`;
                    document.getElementById('order-detail-content').innerHTML = html;
                }

                document.querySelectorAll('.btn-order-details').forEach(function(btn){
                    btn.addEventListener('click', function(){
                        var orderCard = btn.closest('.order-card');
                        var modal = document.getElementById('order-detail-content');
                        if(orderCard && modal){
                            // Affichage dynamique du détail dans le modal sur clic
                            showOrderDetailFromCard(orderCard);
                        }
                    });
                });

                // Optionnel : Nettoyage du détail lors de la fermeture, pour expérience UX propre
                var orderDetailModal = document.getElementById('modal-order-detail');
                if(orderDetailModal){
                    orderDetailModal.addEventListener('hidden.bs.modal', function(){
                        document.getElementById('order-detail-content').innerHTML = '<div class="text-center text-muted my-4">Chargement du détail...</div>';
                    });
                }

                // -------------------------------
                // MODAL BOUTIQUE : Dynamique JS
                // -------------------------------
                var currentBoutiqueLatLng = null; // Pour la carte
                if(typeof modalInitialisee==='undefined'){

                    //GESTIONS DES AVIS
                    window.initBoutiqueModal=function(boutiqueData) {

                        if(!boutiqueData||typeof boutiqueData!=='object' ||Object.keys(boutiqueData).length===0){
                            console.warn("Appel de la modal ignoré: données vides.");
                            return;
                        }
                        
                        const idArt=boutiqueData.articles[0].id;
                        const isFav=boutiqueData.is_fav;

                        const btnModal=document.querySelector('#modal-boutique .btn-favori-article');
                        const iconModal=btnModal ? btnModal.querySelector('i'):null;

                        if(btnModal && iconModal && idArt){
                            btnModal.setAttribute('data-id-article', idArt);
                            if(isFav===true||isFav===1){
                                iconModal.className='fa-solid fa-heart text-danger';
                            }
                            else{
                                iconModal.className='fa-regular fa-heart';
                            }
                        }

                        console.log("ID commerçant chargé:", boutiqueData.id_commerçant);
                        console.log("Donnée reçues: ", boutiqueData);
                        // Nom, photo, distance, adresse
                        document.getElementById('modal-boutique-nom').textContent = boutiqueData.nom || '';
                        document.getElementById('modal-boutique-nomhead').textContent = boutiqueData.nom || '';
                        document.getElementById('id_commerçant').value=boutiqueData.id_commerçant;
                        if(boutiqueData.photo){
                            document.getElementById('modal-boutique-photo').src = boutiqueData.photo;
                            document.getElementById('modal-boutique-photo').style.display = 'block';
                        } else {
                            document.getElementById('modal-boutique-photo').style.display = 'none';
                        }
                        // Articles   
                        var artDiv = document.getElementById('modal-boutique-articles');
                        if(artDiv){
                            artDiv.innerHTML = '';
                            console.log("Modal vidée");
                            if (Array.isArray(boutiqueData.articles) && boutiqueData.articles.length) {
                                boutiqueData.articles.forEach(function(item){
                                    const heartClass=item.is_fav?'text-danger fa-solid':'fa-regular';
                                    var card = document.createElement('div');
                                    card.className = 'seller-article-card d-flex align-items-center';
                                    card.innerHTML = `<img src="${item.img}" alt="" class="me-2">
                                        <div>
                                            <div class="seller-product-title">${item.titre}</div>
                                            <div class="small text-muted">${item.prix}</div>
                                        </div>
                                    `;
                                    artDiv.appendChild(card);
                                });

                            } 
                            else {
                                artDiv.innerHTML = '<div class="text-muted">Aucun article trouvé pour cette boutique.</div>';
                            }
                        }
                        // Carte
                        var mapDiv = document.getElementById('boutique-map');
                        mapDiv.innerHTML = '<div class="text-center text-muted my-4" id="boutique-map-loading">Chargement de la carte...</div>';
                        // Réglage de la carte sur le point
                        setTimeout(function(){
                            if(window.myMap!==undefined && window.myMap!==null){
                                window.myMap.remove();
                            }
                            
                            if(typeof L === "undefined") return;
                            mapDiv.innerHTML = ""; // clean
                            window.myMap = L.map(mapDiv).setView([boutiqueData.lat, boutiqueData.lng], 15);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: ''
                            }).addTo(window.myMap);
                            L.marker([boutiqueData.lat, boutiqueData.lng]).addTo(window.myMap).bindPopup(boutiqueData.nom||"Boutique").openPopup();
                            if(navigator.geolocation){
                                navigator.geolocation.getCurrentPosition(function(position){
                                    var clientLat=position.coords.latitude;
                                    var clientLng=position.coords.longitude;
                                    var clientLatlng=L.latLng(clientLat,clientLng);
                                    var boutiqueLatLng=L.latLng(boutiqueData.lat, boutiqueData.lng);
                                    var distanceMetres=clientLatlng.distanceTo(boutiqueLatLng);
                                    var distanceKm=(distanceMetres/10000).toFixed(1);
                                    var infoTexte=`Vous etes à ${distanceKm} Km de la boutique`;
                                    const distElem=document.getElementById('distance');
                                    if(distElem) distElem.innerText=infoTexte;
                                    document.getElementById('modal-boutique-distance').innerText = /*boutiqueData.distancekm!==undefined ? (boutiqueData.distancekm.toFixed(1) + " km") :*/ "Douala, Cameroun";
                                    console.log("Distance: ",infoTexte);
                                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${clientLat}&lon=${clientLng}`,{
                                        headers:{
                                            'Accept': 'application/json',
                                            'User-Agent': 'LeBoutiquier/1.0'
                                        }
                                    }).then(response=>response.json()).then(data=>{
                                        var ville=data.address.city||data.address.town||data.address.village||"Douala";
                                        var pays=data.address.country||"Cameroun";
                                    }).catch(err=>console.error("Erreur Nominatim: ", err));

                                    L.marker([clientLat, clientLng], { icon:L.icon({
                                        iconUrl:'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                                        shadowUrl:'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                                        iconSize: [25, 41],
                                        iconAnchor: [12, 41]
                                    })}).addTo(window.myMap).bindPopup(infoTexte).openPopup();

                                    var points=[
                                        [clientLat, clientLng],
                                        [boutiqueData.lat, boutiqueData.lng]
                                    ];

                                    var poly=L.polyline(points, {color: '#f56c1c', weight:5}).addTo(window.myMap);
                                    window.myMap.fitBounds(poly.getBounds(), {padding: [30, 30]});
                                    }, function(){
                                        console.log("L'utilisateur a refusé la géolocalisation");
                                })
                                const addrElem=document.getElementById('modal-boutique-distance');
                                if(addrElem){
                                    const country=(boutiqueData.address && boutiqueData.address.pays)?boutiqueData.pays:'Cameroun';
                                    const city=(boutiqueData.address && boutiqueData.address.ville)?boutiqueData.ville:'Douala';
                                    addrElem.textContent=`${city}, ${country}`;
                                }
                                console.log("Distance: ",boutiqueData.distanceKm);
                                console.log("Pays et Ville: ",boutiqueData.pays, boutiqueData.ville);
                            }
                            else{
                                alert("Pour une meilleur expérience, veuillez actualisé et accecpté la géolocalisation!");
                            }
                        },350);

                        // Réinitialise les étoiles avis
                        var stars = document.querySelectorAll('#modal-boutique-avis-form .star-rating .fa-star');
                        stars.forEach(function(s, idx){
                            s.classList.remove('fas'); s.classList.add('far'); s.setAttribute('data-selected','0');
                        });
                        document.getElementById('modal-boutique-comment').value = '';
                        document.getElementById('modal-boutique-avis-confirm').style.display = 'none';
                        currentBoutiqueLatLng = [boutiqueData.lat, boutiqueData.lng];

                    }

                    document.addEventListener('click', function(e){
                        const link=e.target.closest('.open-shop-link');
                        if(!link)return;
                            initBoutiqueModal();
                    });var modalInitialisee=true;

                }
                    
                document.querySelectorAll('.open-shop-link').forEach(function(link){
                    link.addEventListener('click',function(e){
                        var data = link.getAttribute('data-boutique');
                        if(data){
                            var obj;
                            try{ obj = JSON.parse(data); }catch(ex){ obj={}; }
                            initBoutiqueModal(obj);
                        }else{
                            // fallback : nom boutique
                            initBoutiqueModal({
                                nom: link.textContent||'Boutique',
                                photo:"", distancekm:0, adresse:"", articles:[],lat:5.34, lng:-4.03
                            });
                        }
                    });
                });

                // Gestion étoile rating pour avis sur la boutique (modal)
                (function(){
                    var stars = document.querySelectorAll('#modal-boutique-avis-form .star-rating .fa-star');
                    var noteValue = 0;
                    stars.forEach(function(star){
                        star.addEventListener('mouseenter',function(){
                            var val = parseInt(star.dataset.value);
                            stars.forEach(function(s, i){
                                s.classList.toggle('fas', i < val);
                                s.classList.toggle('far', i >= val);
                            });
                        });
                        star.addEventListener('mouseleave',function(){
                            stars.forEach(function(s, i){
                                s.classList.toggle('fas', i < noteValue);
                                s.classList.toggle('far', i >= noteValue);
                            });
                        });
                        star.addEventListener('click',function(){
                            noteValue = parseInt(star.dataset.value);
                            document.getElementById('note-input').value=noteValue;
                            stars.forEach(function(s, i){
                                s.classList.toggle('fas', i < noteValue);
                                s.classList.toggle('far', i >= noteValue);
                                s.setAttribute('data-selected', i < noteValue ? '1':'0');
                            });
                        });
                    });
                    // Sur mouseleave zone étoiles
                    var starZone = document.querySelector('#modal-boutique-avis-form .star-rating');
                    if(starZone) starZone.addEventListener('mouseleave',function(){
                        var sel=0;
                        stars.forEach(function(s,i){ if(s.getAttribute('data-selected')==='1')sel=i+1; });
                        noteValue = sel;
                        stars.forEach(function(s,i){
                            s.classList.toggle('fas', i < noteValue);
                            s.classList.toggle('far', i >= noteValue);
                        });
                    });
                })();

                var avisForm = document.getElementById('modal-boutique-avis-form');
                if(avisForm){
                    avisForm.addEventListener('submit',function(e){
                        e.preventDefault();
                        const confirmDiv=document.getElementById('modal-boutique-avis-confirm');
                        confirmDiv.style.display = 'block';
                        confirmDiv.innerHTML="Envoi en cours...";
                        confirmDiv.classList.replace('text-danger', 'text-success');
                        const formData=new FormData(this);
                        formData.append('envoyer_avis', 'true');
                        fetch('espaceclient.php', {
                            method:'POST',
                            body:formData
                        }).then(response=>response.text()).then(data=>{
                            console.log("Retour PHP: ", data);
                            confirmDiv.innerHTML=data;
                            if(data.includes("success")||data.includes("Merçi")){
                                confirmDiv.className='text-success small';
                                setTimeout(function(){
                                    confirmDiv.style.display='none';
                                    this.reset();
                                    confirmDiv.innerHTML="";
                                    document.getElementById('modal-boutique-avis-confirm').style.display = 'none';
                                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-boutique')).hide();
                                    location.reload();
                                }.bind(this),2000);
                            }
                            else{
                                confirmDiv.className='text-danger small';
                            }
                        },1700).catch(erro=>{
                            confirmDiv.classList.replace('tex-succes', 'tex-danger');
                            confirmDiv.innerHTML="Erreur lors de l'envoi...";
                        })
                        
                    });
                }
            });
            document.addEventListener('DOMContentLoaded', () => {
                // Initialisation
                if (typeof updateTotaux === 'function') updateTotaux();

                const panierContainer = document.getElementById('panier');
                if (!panierContainer) return;

                // --- GESTION DES CLICS SUR LE PANIER ---
                panierContainer.addEventListener('click', function(e) {
                    
                    // 1. Gestion des quantités (+/-)
                    const btnQty = e.target.closest('.btn-plus-qty, .btn-minus-qty');
                    if (btnQty) {
                        const card = btnQty.closest('.cart-item-card');
                        const input = card.querySelector('.input-qty');
                        const idArt = card.getAttribute('data-id-article');
                        let currentQty = parseInt(input.value);

                        if (btnQty.classList.contains('btn-plus-qty')) currentQty++;
                        else if (currentQty > 1) currentQty--;
                        else return;

                        const buttons = card.querySelectorAll('button');
                        buttons.forEach(btn => btn.disabled = true);

                        const fd = new FormData();
                        fd.append('update_qty', 'true');
                        fd.append('id_article', idArt);
                        fd.append('new_qty', currentQty);

                        fetch('espaceclient.php', { method: 'POST', body: fd })
                        .then(res => res.text())
                        .then(data => {
                            if (data.trim() === 'success') {
                                input.value = currentQty;
                                updateTotaux();
                            }
                        }).finally(() => {
                            buttons.forEach(btn => btn.disabled = false);
                        });
                    }

                    // 2. Sélectionner toute une boutique
                    if (e.target.classList.contains('select-boutique')) {
                        const bloc = e.target.closest('.boutique-cart-bloc');
                        bloc.querySelectorAll('.select-article').forEach(cb => {
                            cb.checked = e.target.checked;
                        });
                        updateTotaux();
                    }

                    // 3. Supprimer un article
                    const btnRemove = e.target.closest('.btn-remove-item');
                    if (btnRemove) {
                        if (confirm("Retirer cet article ?")) {
                            const card = btnRemove.closest('.cart-item-card');
                            const fd = new FormData();
                            fd.append('supprimer_article', 'true');
                            fd.append('id_article', card.getAttribute('data-id-article'));

                            fetch('espaceclient.php', { method: 'POST', body: fd })
                            .then(res => res.text())
                            .then(data => {
                                if (data.trim() === 'successDelete') {
                                    card.remove();
                                    updateTotaux();
                                }
                            });
                        }
                    }

                    // 4. OUVERTURE DU MODAL (Bouton Orange)
                    const btnValider = e.target.closest('.lb-btn-orange');
                    if (btnValider) {
                        const bloc = btnValider.closest('.boutique-cart-bloc');
                        const coches = bloc.querySelectorAll('.select-article:checked');

                        if (coches.length === 0) {
                            alert("Veuillez cocher au moins un article.");
                            return;
                        }

                        // Récupération des IDs cochés
                        const idsAchetes = Array.from(coches).map(cb => cb.value).join(',');
                        const idBoutique = btnValider.getAttribute('data-boutique-id');

                        // On remplit les champs cachés du modal
                        document.getElementById('id_article_selectionne').value = idsAchetes;
                        document.getElementById('id_boutique_modal').value = idBoutique;

                        // On affiche le modal (Bootstrap 5)
                        const myModal = new bootstrap.Modal(document.getElementById('modalAchat'));
                        myModal.show();
                    }
                });

                // --- ENVOI FINAL DE LA COMMANDE (DEPUIS LE MODAL) ---
                const btnConfirmAchat = document.getElementById('confirmAchat');
                if (btnConfirmAchat) {
                    btnConfirmAchat.addEventListener('click', function() {
                        const form = document.getElementById('formCommande');
                        const fd = new FormData(form);

                        // Désactiver le bouton pour éviter les doubles clics
                        btnConfirmAchat.disabled = true;
                        btnConfirmAchat.innerText = "Traitement...";

                        fetch('gestCmd.php', {
                            method: 'POST',
                            body: fd
                        })
                        .then(response => response.json()) // On attend du JSON
                        .then(data => {
                            if (data.status === 'success') {
                                // Succès : Redirection WhatsApp
                                const urlWa = `https://wa.me/${data.tel}?text=${encodeURIComponent(data.message)}`;
                                window.open(urlWa, '_blank');
                                location.reload();
                            } else {
                                // Erreur propre (plus de [object Object])
                                alert("Erreur : " + data.message);
                                btnConfirmAchat.disabled = false;
                                btnConfirmAchat.innerText = "Confirmer l'achat";
                            }
                        })
                        .catch(error => {
                            console.error('Erreur technique:', error);
                            alert("Une erreur technique est survenue.");
                            btnConfirmAchat.disabled = false;
                        });
                    });
                }

                // Recalculer les totaux si on coche manuellement
                panierContainer.addEventListener('change', (e) => {
                    if (e.target.classList.contains('select-article')) {
                        updateTotaux();
                    }
                });
            });
            function updateTotaux(){
                let globalTotal=0;
                document.querySelectorAll('.boutique-cart-bloc').forEach(bloc=>{
                    let shopTotal=0;
                    bloc.querySelectorAll('.cart-item-card').forEach(item=>{
                        const checkbox=item.querySelector('.select-article');
                        if(checkbox && checkbox.checked){
                            const prix =parseFloat(item.getAttribute('data-prix'));
                            const qty=parseInt(item.querySelector('.input-qty').value);
                            shopTotal+=(prix*qty);
                        }
                    });
                    const shopDisplay=bloc.querySelector('.boutique-total-price');
                    if(shopDisplay){
                        shopDisplay.innerText=new Intl.NumberFormat('fr-FR').format(shopTotal)+" FCFA";
                    }
                    globalTotal+=shopTotal;
                });
                const grandDisplay=document.getElementById('grand-total-price');
                if(grandDisplay){
                    grandDisplay.innerText= new Intl.NumberFormat('fr-FR').format(globalTotal);
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('paiementModal');
                const form = document.getElementById('form-final-commande');

                // 1. Initialisation du Modal au clic sur "Procéder au paiement"
                modal.addEventListener('show.bs.modal', function(event) {
                    const btn = event.relatedTarget;
                    const idBoutique = btn.getAttribute('data-boutique-id');
                    const bloc = btn.closest('.boutique-cart-bloc');

                    // ✅ Collecter tous les articles cochés dans ce bloc
                    const articlesCoches = bloc.querySelectorAll('.select-article:checked');
                    
                    // Si aucun coché, prendre tous les articles du bloc
                    const cibles = articlesCoches.length > 0 
                        ? articlesCoches 
                        : bloc.querySelectorAll('.select-article');

                    const ids = Array.from(cibles).map(cb => cb.getAttribute('data-article-id'));
                    
                    // Calcul du total basé sur les articles sélectionnés
                    let total = 0;
                    cibles.forEach(cb => {
                        const card = cb.closest('.boutique-article');
                        const prix = parseFloat(card.getAttribute('data-prix')) || 0;
                        const qty = parseInt(card.querySelector('.input-qty').value) || 1;
                        total += prix * qty;
                    });

                    document.getElementById('id_boutique_modal').value = idBoutique;
                    document.getElementById('modal-id-article').value = ids.join(',');
                    document.getElementById('modal-total-articles').innerText = 
                        new Intl.NumberFormat('fr-FR').format(total) + ' FCFA';
                    document.getElementById('modal-total-final').innerText = 
                        new Intl.NumberFormat('fr-FR').format(total) + ' FCFA';

                    const fraisDisplay = document.getElementById('modal-frais-montant');
                    if (fraisDisplay) fraisDisplay.innerText = "0 FCFA";
                });
                // 2. Gestion dynamique du choix Livraison/Retrait
                form.addEventListener('change', function(e) {
                    if(e.target.name === 'type_achat') {
                        const isLivraison = (e.target.value === 'livraison');
                        const totalBase = parseInt(document.getElementById('modal-total-articles').innerText.replace(/[^0-9]/g, ''));
                        const frais = isLivraison ? 1500 : 0;

                        const fraisDisplay=document.getElementById('modal-frais-montant');
                        if(fraisDisplay){
                            fraisDisplay.innerText=new Intl.NumberFormat('fr-FR').format(frais)+" FCFA";
                        }                    

                        document.getElementById('zone-adresse').classList.toggle('d-none', !isLivraison);
                        document.getElementById('row-frais').classList.toggle('d-none', !isLivraison);
                        document.getElementById('modal-total-final').innerText = new Intl.NumberFormat('fr-FR').format(totalBase + frais) + " FCFA";
                    }
                });

                // 3. ENVOI DES DONNÉES AVEC FETCH (MÉTHODE POST)
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // Empêche le rechargement de la page
                    
                    const btn = document.getElementById('btn-submit-order');
                    const status = document.getElementById('status-message');
                    const radioType=document.querySelector('input[name="type_achat"]:checked');
                    const champAdresse=document.querySelector('textarea[name="adresse_livraison"]');
                    const scrollContainer=document.getElementById('modal-scroll-body');
                    
                    const typeAchat=radioType?radioType.value:'retrait';
                    const adresse=champAdresse?champAdresse.value.trim():"";
                    const telephone=document.getElementById('telephone_livraison').value.trim();

                    if(typeAchat==='livraison' && adresse===""){
                        status.innerHTML=`
                            <div class="alert alert-warning border-0 shadow-sm mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Action requise :</strong> Veuillez remplir tout les champs!
                            </div>`;
                        if(scrollContainer) scrollContainer.scrollTop=0;
                        return;
                    }
                    
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                    const formData = new FormData(this);

                    fetch('gestCmd.php', {
                        method: 'POST', // On déclare le POST ici
                        body: formData
                    })
                    .then(response =>{
                        return response.text().then(text=>{
                            try{
                                return JSON.parse(text);
                            }
                            catch(err){
                                console.error("Réponse non-JSON reçue :", text);
                                throw new Error("Le serveur a renvoyé une erreur");
                            }
                        })
                    })
                    .then(result => {
                        if(result.status.trim() === 'success') {
                            btn.style.display="flex";
                            setTimeout(() =>{
                                status.innerHTML=`
                                <div class="alert alert-success border-0 shadow-sm mb-3">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Votre commande a été enregistrée avec success!
                                </div>`;
                                btn.disabled = false;
                                btn.innerHTML = '<i class="fab fa-whatsapp me-2"></i>Continuer sur wattsapp';
                                btn.style.backgroundColor="#25D366";
                                btn.style.color="white";
                                if(scrollContainer) scrollContainer.scrollTop=0;
                            }, 2000);
                            // Désactiver complètement le formulaire pour éviter toute re-soumission
                            form.querySelector('#btn-submit-order').disabled = true;
                            document.getElementById('modal-id-article').value = '';
                            document.getElementById('id_boutique_modal').value = '';

                            btn.onclick = function(){
                                const url = `https://wa.me/${result.tel}?text=${encodeURIComponent(result.message)}`;
                                window.open(url, '_blank');
                            }
                            document.getElementById('btn-close-modal').onclick = function(){
                                location.reload();
                            }
                            modal.addEventListener('hidden.bs.modal', function onModalHidden(){
                                modal.removeEventListener('hidden.bs.modal', onModalHidden);
                                location.reload();
                            });
                        } 
                        else{
                            btn.disabled = true;
                            btn.innerHTML = '<i class="fab fa-whatsapp me-2"></i>Continuer sur wattsapp';
                            btn.style.backgroundColor="#25D366";
                            btn.style.pointerEvents="none";
                        }
                    })
                    .catch(error => {
                        console.error('Erreur de format JSON:', error);
                        status.innerHTML = '<div class="alert alert-danger">Erreur de connexion au serveur.</div>';
                        btn.disabled = false;
                        btn.innerHTML = 'Réessayer';
                    });
                });
            });
            document.addEventListener("DOMContentLoaded", function(){
                const urlsParams=new URLSearchParams(window.location.search);
                if(urlsParams.get('tab')==='avis'){
                    var triggerEl=document.querySelector('#tab-avis');
                    if(triggerEl){
                        var tab=new bootstrap.Tab(triggerEl);
                        tab.show();
                        window.history.replaceState(null, null, window.location.pathname);
                    }
                }
                var scrollPos=localStorage.getItem('scrollPosition');
                if(scrollPos){
                    setTimeout(function(){
                        window.scrollTo(0, scrollPos);
                        localStorage.removeItem('scrollPosition');
                    }, 50);
                }
            });
            window.addEventListener("beforeunload", function(){
                localStorage.setItem('scollPosition', window.scrollY);
            });

            document.addEventListener("DOMContentLoaded", function(){
                const params=new URLSearchParams(window.location.search);
                if(params.get("modal")==="informations"){
                    const modal=new bootstrap.Modal(document.getElementById("modal-profil"));
                    modal.show();
                }
            });

            document.addEventListener('DOMContentLoaded', function(){
                const map=L.map('main-map').setView([4.05, 9.70], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                
                fetch('boutiques.php').then(response=>response.json()).then(data=>{
                    if(data.length===0){
                        console.warn("Aucune boutique trouvée avec des coordonnées valides.");
                        return;
                    }
                    data.forEach(btq=>{
                        const marker=L.marker([btq.lat, btq.lng]).addTo(map);

                        const popupContent=`<div class="btq-popup" style="width: 100px; text-align: center;">
                                                <img src="../ImagesBD/${btq.photo}" style='width: 100%;' alt="profil boutique">
                                                <h5 style="margin: 8px 0; font-size: 14px; color: #EA580C; font-weight: bold;">${btq.nom}</h5>
                                            </div>`;
                        marker.bindPopup(popupContent);
                    });
                }).catch(error=>console.error('erreur lors du chargement des boutiques: ', error));
            });

        </script>
    </body>
</html>
