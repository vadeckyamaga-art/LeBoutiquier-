<?php 

    session_start();
    include 'connexionBD.php';
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    file_put_contents('debug_log.txt', print_r($_POST, true));
    $errorMsg='';
    $successMsg='';

    // if (isset($_SESSION['compte']) && $_SESSION['compte']==='admin') {
    //     header('Location: dashboard.php');
    //     exit();
    // }
    if(isset($_SESSION['id'] )){
        $usersID=$_SESSION['id'];
        if($_SESSION['compte']==='client'){
            $queryclient=$conn->prepare("SELECT * FROM client WHERE id=?");
            $queryclient->execute([$usersID]);
            $clientRow=$queryclient->fetch(PDO::FETCH_ASSOC);
            if($clientRow){
                $client=$clientRow;
                $clientID=$clientRow['id_client'];
            }
        }
        else{
            $rowBtq = null;
            // On vérifie d'abord si on est un admin qui simule
            if (isset($_SESSION['admin_mode']) && isset($_SESSION['active_merchant_id'])) {
                $stmtshow = $conn->prepare("SELECT * FROM commerçant WHERE id_commerçant = ?");
                $stmtshow->execute([$_SESSION['active_merchant_id']]);
            } 
            else {
                // Procédure normale pour un vrai commerçant
                $stmtshow = $conn->prepare("SELECT * FROM commerçant WHERE id = ?");
                $stmtshow->execute([$usersID]);
            }

            $rowBtq = $stmtshow->fetch(PDO::FETCH_ASSOC);
            if($rowBtq){
                $Btq = $rowBtq;
                $BtqID = $rowBtq['id_commerçant'];
            } 
            else{
                // Si on ne trouve rien, c'est là qu'on risque la redirection
                // On force l'ID si on est admin pour éviter d'être jeté
                if(isset($_SESSION['admin_mode'])) {
                    header('Location: Espacecommerçant.php?id=' . $_SESSION['active_merchant_id']);
                    exit();
                }
            }

        }
       
    }

    $selectCategorie=$conn->prepare("SELECT * FROM catégorie ORDER BY nom_cat ASC");
    $selectCategorie->execute();
    $categorie=$selectCategorie->fetchAll(PDO::FETCH_ASSOC);

    try{
        $sql=$conn->prepare("SELECT a.*, 
                            COUNT(DISTINCT avs.id_avis) as com, 
                            ROUND(AVG(avs.note), 1) as note, co.nom_commerçant, co.profil_boutique, 
                            COUNT(dtl.id_article) as total_ventes
                            FROM article a, details_commande dtl, avis avs, commerçant co
                            WHERE a.id_article=dtl.id_article AND co.id_commerçant=avs.id_commerçant AND a.id_commerçant=co.id_commerçant
                            GROUP BY a.id_article
                            ORDER BY total_ventes DESC
                            LIMIT 3");
        $sql->execute();
        $articles_populaires=$sql->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e){
        die("Erreur SQL: ".$e->getMessage());
    }
     
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Découvres notre sélection d'articles</title>
        <link rel="stylesheet" href="../fontawesome/css/all.min.css">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../style/accueil.css">
        <link rel="stylesheet" href="../style/accueil2.css">
        <link rel="stylesheet" href="../style/promo.css">
        <!-- PWA -->
        <link rel="manifest" href="../vendor/manifest.json">
        <meta name="theme-color" content="#EA580C">
        <link rel="icon" href="../Image/favoicon.ico" sizes="48x48">
        <link rel="apple-touch-icon" href="../Image/logo.png">
    </head>
    <body>
        <?php include 'splash.php'; ?>
        <!-- Navbar Leboncoin style -->
        <nav class="navbar navbar-expand-lg leb-navbar fixed-top">
            <div class="container-lg">
                <button class="btn d-lg-none me-3" data-bs-toggle="offcanvas" data-bs-target="#lebOffcanvas" aria-controls="lebOffcanvas"><i class="fas fa-bars"></i></button>
                <a class="navbar-brand leb-navbar-brand ms-lg-0 ms-2" >Leboutiquier</a>
                <?php  
                    if(isset($_SESSION['id'])):
                        if($_SESSION['compte']==='client'):
                ?>
                            <div class="d-none d-lg-flex gap-3 ms-auto align-items-center">
                                <a href="espaceclient.php" class="text-secondary fs-6"><i class="fa-solid fa-user"></i> Mon compte</a>
                                <a href="deconnexion.php" class="text-secondary fs-6"><i class="fa-solid fa-right-from-bracket"></i> Deconnexion</a>
                            </div>
                <?php elseif($_SESSION['compte']==='boutiquier'): ?>
                            <div class="d-none d-lg-flex gap-3 ms-auto align-items-center">
                                <button id="publier-mobile" data-id="<?= $BtqID ?>" class="text-secondary fs-6 border-0" style="background-color: transparent; text-decoration: underline;"><i class="fa-solid fa-user"></i> Mon compte</button>
                                <a href="deconnexion.php" class="text-secondary fs-6"><i class="fa-solid fa-right-from-bracket"></i> Deconnexion</a>
                                <button class="btn btn-leb-orange" id="publier-mobile" data-id="<?= $BtqID ?>"><i class="fas fa-plus-square"></i> Publier un article</button>
                            </div>
                <?php elseif($_SESSION['compte']==='livreur'): ?>
                            <div class="d-none d-lg-flex gap-3 ms-auto align-items-center">
                                <a href="livraison.php" class="text-secondary fs-6"><i class="fa-solid fa-user"></i> Mon compte</a>
                                <a href="deconnexion.php" class="text-secondary fs-6"><i class="fa-solid fa-right-from-bracket"></i> Deconnexion</a>
                            </div>
                <?php else: ?>
                            <div class="d-none d-lg-flex gap-3 ms-auto align-items-center">
                                <a href="dashboard.php" class="text-secondary fs-6"><i class="fa-solid fa-user"></i> Mon compte</a>
                                <a href="deconnexion.php" class="text-secondary fs-6"><i class="fa-solid fa-right-from-bracket"></i> Deconnexion</a>
                            </div>
                <?php endif;?>
                <?php else: ?>
                    <div class="d-none d-lg-flex gap-3 ms-auto align-items-center">
                        <a onclick="redirectionCompte();" style="cursor: pointer;" class="text-secondary fs-6"><i class="fa-solid fa-user"></i> Mon compte</a>
                        <a href="connexion.php" class="text-secondary fs-6"><i class="fas fa-user"></i> Se connecter</a>
                        <button class="btn btn-leb-orange" onclick="redirectionCompte();" style="cursor: pointer;" ><i class="fas fa-plus-square"></i> Publier un article</button>
                    </div>
                <?php endif;?>
            </div>
        </nav>

        <!-- Offcanvas menu mobile -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="lebOffcanvas" aria-labelledby="lebOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title leb-navbar-brand" id="lebOffcanvasLabel">Leboutiquier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fermer"></button>
            </div>
            <div class="offcanvas-body">
                <?php  
                    if(isset($_SESSION['id'])):
                        if($_SESSION['compte']==='client'):
                ?>
                            <ul class="nav flex-column gap-2">
                                <li><a href="espaceclient.php#favoris" class="nav-link text-dark"><i class="fas fa-heart"></i> Mes Favoris</a></li>
                                <li><a href="espaceclient.php#avis" class="nav-link text-dark"><i class="fas fa-star"></i> Notez une boutique</a></li>
                                <li><a href="espaceclient.php#panier" class="nav-link text-dark"><i class="fas fa-shopping-cart"></i> Mon panier</a></li>
                                <li><a href="espaceclient.php#commande" class="nav-link text-dark"><i class="fas fa-shopping-bag"></i> Mes commandes</a></li>
                                <li><a href="espaceclient.php?modal=informations" class="nav-link text-dark"><i class="fas fa-user-circle"></i> informations personnelles</a></li>
                                <li><hr></li>
                                <li><span class="text-secondary ps-1">Catégories</span></li>
                                <?php foreach($categorie as $cat):?>
                                    <li><a onclick="redirectionCategorie('<?= $cat['id_cat'] ?>');" style="cursor: pointer;" class="nav-link text-secondary"><?= $cat['nom_cat'] ?></a></li>
                                <?php endforeach; ?>
                                <li><hr></li>
                                <li><a href="deconnexion.php" class="nav-link text-dark"><i class="fa-solid fa-right-from-bracket"></i> Deconnexion</a></li>
                                <li><a href="espaceclient.php" class="nav-link text-dark"><i class="fa-solid fa-user"></i> Mon Compte</a></li>
                            </ul>
                <?php   elseif($_SESSION['compte']==='livreur'): ?>
                            <ul class="nav flex-column gap-2">
                                <li><a href="livraison.php" class="nav-link text-dark"><i class="fas fa-chart-line"></i> Tableau de bord</a></li>
                                <li><a href="livraison.php" class="nav-link text-dark"><i class="fas fa-box"></i> Livraisons</a></li>
                                <li><a href="livraison.php" class="nav-link text-dark"><i class="fas fa-bell"></i> Notifications</a></li>
                                <li><a href="livraison.php" class="nav-link text-dark"><i class="fas fa-star"></i> Avis</a></li>
                                <li><a href="livraison.php" class="nav-link text-dark"><i class="fas fa-user-circle"></i> informations personnelles</a></li>
                                <li><hr></li>
                                <li><span class="text-secondary ps-1">Catégories</span></li>
                                <?php foreach($categorie as $cat):?>
                                    <li><a onclick="redirectionCategorie('<?= $cat['id_cat'] ?>');" style="cursor: pointer;" class="nav-link text-secondary"><?= $cat['nom_cat'] ?></a></li>
                                <?php endforeach; ?>
                                <li><hr></li>
                                <li><a href="deconnexion.php" class="nav-link text-dark"><i class="fa-solid fa-right-from-bracket"></i> Deconnexion</a></li>
                                <li><a href="livreur.php" class="nav-link text-dark"><i class="fa-solid fa-user"></i> Mon Compte</a></li>
                            </ul>    
                <?php   elseif($_SESSION['compte']==='boutiquier'): ?>
                            <ul class="nav flex-column gap-2">
                                <li><a href="Espacecommerçant.php" class="nav-link text-dark"><i class="fas fa-plus-square"></i>publier un article</a></li>
                                <li><a href="Espacecommerçant.php" class="nav-link text-dark"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
                                <li><a href="page not found.php" class="nav-link text-dark"><i class="fas fa-star"></i> Notez-nous</a></li>
                                <li><hr></li>
                                <li><span class="text-secondary ps-1">Catégories</span></li>
                                <?php foreach($categorie as $cat):?>
                                    <li><a onclick="redirectionCategorie('<?= $cat['id_cat'] ?>');" style="cursor: pointer;" class="nav-link text-secondary"><?= $cat['nom_cat'] ?></a></li>
                                <?php endforeach; ?>
                                <li><hr></li>
                                <li><a href="deconnexion.php" class="nav-link text-dark"><i class="fa-solid fa-right-from-bracket"></i> Deconnexion</a></li>
                                <li><a href="Espacecommerçant.php" class="nav-link text-dark"><i class="fa-solid fa-user"></i> Mon Compte</a></li>
                            </ul>
                <?php   else: ?>
                            <ul class="nav flex-column gap-2">
                                <li><a href="dashboard.php" class="nav-link text-dark"><i class="fas fa-chart-line"></i> Tableau de bord</a></li>
                                <li><a href="dashboard.php" class="nav-link text-dark"><i class="fas fa-store"></i> Gestion des comptes comptes</a></li>
                                <li><a href="dashboard.php" class="nav-link text-dark"><i class="fas fa-shopping-bag"></i> Commande</a></li>
                                <li><a href="dashboard.php" class="nav-link text-dark"><i class="fas fa-bell"></i> Notifications</a></li>
                                <li><a href="dashboard.php" class="nav-link text-dark"><i class="fas fa-truck"></i> Gestion des livreurs</a></li>
                                <li><hr></li>
                                <li><span class="text-secondary ps-1">Catégories</span></li>
                                <?php foreach($categorie as $cat):?>
                                    <li><a onclick="redirectionCategorie('<?= $cat['id_cat'] ?>');" style="cursor: pointer;" class="nav-link text-secondary"><?= $cat['nom_cat'] ?></a></li>
                                <?php endforeach; ?>
                                <li><hr></li>
                                <li><a href="deconnexion.php" class="nav-link text-dark"><i class="fa-solid fa-right-from-bracket"></i> Deconnexion</a></li>
                                <li><a href="dashboard.php" class="nav-link text-dark"><i class="fa-solid fa-user"></i> Mon Compte</a></li>
                            </ul>    
                <?php   endif;?>
            <?php   else: ?>
                        <ul class="nav flex-column gap-2">
                            <li><a onclick="redirectionCompte();" style="cursor: pointer;" class="nav-link text-dark"><i class="fas fa-plus-square"></i> Publier un article</a></li>
                            <li><a onclick="redirectionCompte();" style="cursor: pointer;" class="nav-link text-dark"><i class="fas fa-heart"></i> Mes Favoris</a></li>
                            <li><a href="page not found.php" class="nav-link text-dark"><i class="fas fa-star"></i> Notez-nous</a></li>
                            <li><hr></li>
                            <li><span class="text-secondary ps-1">Catégories</span></li>
                            <?php foreach($categorie as $cat):?>
                                <li><a  onclick="redirectionCategorie('<?= $cat['id_cat'] ?>');" style="cursor: pointer;" class="nav-link text-secondary"><?= $cat['nom_cat'] ?></a></li>
                            <?php endforeach; ?>
                            <li><hr></li>
                            <li><a href="connexion.php" class="nav-link text-dark"><i class="fas fa-user"></i> Se connecter</a></li>
                        </ul>
                <?php endif;?>
            </div>
        </div>

        <main class="container-lg" style="padding-top:82px; max-width:1260px;">
            <!-- Search & Categories -->
            <div class="row mt-3">
                <div class="col-12 col-md-10 offset-md-1 px-0 mb-2 position-relative">
                    <div class="leb-search-wrapper">
                        <select name="type" class="leb-search-type">
                            <option value="article">Articles</option>
                            <option value="categorie">Catégories</option>
                        </select>
                        <input class="leb-searchbox" name="query" autocomplete="off" style="outline: none;" type="text" placeholder="Rechercher sur Leboutiquier">
                        <button type="submit" class="leb-btn-submit"><span class="d-none d-md-inline">Rechercher</span><i class="fas fa-search d-md-none"></i></button>
                    </div>
                    <div id="searchSuggestions" class="search-suggestions-box"></div>
                </div>
                <div class="col-12 col-md-10 offset-md-1">
                    <div class="leb-main-cats px-2 py-1 d-flex flex-nowrap mb-3">
                        <?php foreach($categorie as $cat):?>
                            <a onclick="redirectionCategorie('<?= $cat['id_cat'] ?>');"  class="nav-link text-secondary" style="cursor: pointer;"><?= $cat['nom_cat'] ?></a> 
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <?php include '../vendor/ADS/Ads.php'; ?> 

            <!-- Nos catégories -->
            <div class="row mt-4 mb-2">
                <div class="col-12 col-md-10 offset-md-1">
                    <h2 class="h5 mb-3" style="color: #EA580C; font-weight: bold;">Nos catégories</h2>
                    <div class="leb-cards-grid">
                        <?php foreach($categorie as $cat): ?>
                            <div class="leb-card" onclick="redirectionCategorie('<?= $cat['id_cat'] ?>');">
                                <img src="../ImagesBD/<?= $cat['image_cat'] ?>" alt="<?= $cat['nom_cat'] ?>" class="leb-card-img">
                                <div class="leb-card-body">
                                    <div class="leb-card-title mb-2"><?= $cat['nom_cat'] ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div><br><br>

            <section class="delegation-section py-5">
                <div class="container">
                    <div class="row align-items-center bg-white rounded-4 shadow-lg overflow-hidden delegation-card">
                        <div class="col-lg-7 p-5">
                            <span class="badge bg-primary-soft text-primary mb-3">Service Exclusif</span>
                            <h2 class="display-5 fw-bold mb-4">La technologie ne doit plus être un <span class="text-gradient">frein</span>.</h2>
                            <p class="lead text-muted mb-4">
                                Vous êtes un expert dans votre domaine, mais l'informatique vous intimide ? 
                                Confiez-nous les clés de votre boutique numérique. Nos administrateurs s'occupent de tout.
                            </p>
                            
                            <div class="row g-4 mb-5">
                                <div class="col-sm-6 d-flex align-items-start anim-slide">
                                    <div class="icon-box me-3"><i class="fas fa-upload"></i></div>
                                    <div>
                                        <h6 class="fw-bold mb-1">Mise en ligne</h6>
                                        <small class="text-muted">On publie vos articles pour vous.</small>
                                    </div>
                                </div>
                                <div class="col-sm-6 d-flex align-items-start anim-slide delay-1">
                                    <div class="icon-box me-3"><i class="fas fa-chart-line"></i></div>
                                    <div>
                                        <h6 class="fw-bold mb-1">Gestion de Stock</h6>
                                        <small class="text-muted">Inventaire et prix mis à jour.</small>
                                    </div>
                                </div>
                            </div>
                            <button class="btn-delegation" id="demander-gestion">
                                <span>Confier ma gestion maintenant</span>
                                <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>

                        <div class="col-lg-5 d-none d-lg-block p-0 position-relative bg-gradient-blue text-center py-5">
                            <div class="floating-icons">
                                <div class="float-item"><i class="fas fa-store fa-3x"></i></div>
                                <div class="float-item"><i class="fas fa-user-check fa-3x"></i></div>
                                <div class="float-item"><i class="fas fa-shield-alt fa-3x"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

             
             
            <!-- Tendance -->
            <div class="row">
                <div class="col-12 col-md-10 offset-md-1 mb-2">
                    <h2 class="h5 mb-3" style="color: #EA580C; font-weight: bold;">Le plus acheté en ce moment</h2>
                    <div class="leb-cards-grid">
                        <div class="leb-card" id="bureau" onclick="window.location.href='page not found.php';">
                            <img src="../Image/bureau.jpg" alt="" class="leb-card-img">
                            <div class="leb-card-body">
                                <div class="leb-card-title mb-2">Accessoires bureau</div>
                                <div class="leb-card-actions">
                                    <span class="small text-muted">à partir de</span>
                                    <span class="leb-card-price">5 000 FCFA</span>
                                </div>
                            </div>
                        </div>
                        <div class="leb-card" id="auto" onclick="window.location.href='page not found.php';">
                            <img src="../Image/pièce.jpg" alt="" class="leb-card-img">
                            <div class="leb-card-body">
                                <div class="leb-card-title mb-2">La pièce manquante</div>
                                <div class="leb-card-actions">
                                    <span class="small text-muted">à partir de</span>
                                    <span class="leb-card-price">12 000 FCFA</span>
                                </div>
                            </div>
                        </div>
                        <div class="leb-card" onclick="window.location.href='page not found.php';">
                            <img src="../Image/bio.jpg" alt="" class="leb-card-img">
                            <div class="leb-card-body">
                                <div class="leb-card-title mb-2">Bio/naturel</div>
                                <div class="leb-card-actions">
                                    <span class="small text-muted">à partir de</span>
                                    <span class="leb-card-price">950 FCFA</span>
                                </div>
                            </div>
                        </div>
                        <div class="leb-card" onclick="window.location.href='page not found.php';">
                            <img src="../Image/circuit.jpg" alt="" class="leb-card-img">
                            <div class="leb-card-body">
                                <div class="leb-card-title mb-2">Local/circuit court</div>
                                <div class="leb-card-actions">
                                    <span class="small text-muted">à partir de</span>
                                    <span class="leb-card-price">3 000 FCFA</span>
                                </div>
                            </div>
                        </div>
                        <div class="leb-card" onclick="window.location.href='page not found.php';">
                            <img src="../Image/cuisine.jpg" alt="" class="leb-card-img">
                            <div class="leb-card-body">
                                <div class="leb-card-title mb-2">Cuisine du monde</div>
                                <div class="leb-card-actions">
                                    <span class="small text-muted">à partir de</span>
                                    <span class="leb-card-price">2 200 FCFA</span>
                                </div>
                            </div>
                        </div>
                        <div class="leb-card" onclick="window.location.href='page not found.php';">
                            <img src="../Image/nouveauté%20%282%29.jpg" alt="" class="leb-card-img">
                            <div class="leb-card-body">
                                <div class="leb-card-title mb-2">Best-sellers</div>
                                <div class="leb-card-actions">
                                    <span class="small text-muted">à partir de</span>
                                    <span class="leb-card-price">990 FCFA</span>
                                </div>
                            </div>
                        </div>
                        <div class="leb-card" onclick="window.location.href='page not found.php';">
                            <img src="../Image/nouveauté.jpg" alt="" class="leb-card-img">
                            <div class="leb-card-body">
                                <div class="leb-card-title mb-2">Nouveauté</div>
                                <div class="leb-card-actions">
                                    <span class="small text-muted">à partir de</span>
                                    <span class="leb-card-price">6 500 FCFA</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><br><br>

            <div class="row mt-4">
                <div class="col-12 col-md-10 offset-md-1">
                    <h2 class="h5 mb-3" style="color: #EA580C; font-weight: bold;">Les plus achetés</h2>
                    <div class="row g-3">
                        <?php foreach($articles_populaires as $art): 
                            $img1=!empty($art['photo_article']);
                            $img2=!empty($art['photo2'])?$art['photo2']:$art['photo_article'];
                            $img3=!empty($art['photo3'])?$art['photo3']:$art['photo_article'];
                        ?>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="leb-carousel-card">
                                    <div class="d-flex align-items-center w-100 mb-2">
                                        <img src="../ImagesBD/<?= $art['profil_boutique'] ?>" width="32" height="32" class="rounded-circle me-2 border" alt="">
                                        <span class="fw-bold"><?= $art['nom_commerçant'].'  ' ?><i class="fas fa-star" style="color: #EA580C;"></i> <?= $art['note'] ?>(<?= $art['com'] ?>)</span>
                                    </div>
                                    <div id="car1" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner rounded">
                                            <div class="carousel-item active"><img src="../ImagesBD/<?= $art['photo_article'] ?>" class="d-block w-100" alt="photo article"></div>
                                            <div class="carousel-item"><img src="../ImagesBD/<?= $img2 ?>" class="d-block w-100" alt="photo2"></div>
                                            <div class="carousel-item"><img src="../ImagesBD/<?= $img3 ?>" class="d-block w-100" alt="photo3"></div>
                                        </div>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#car1" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#car1" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
                                    </div>
                                    <div class="carousel-caption">
                                        <p class="mb-0 fw-bold"><?= $art['nom_article'] ?></p>
                                        <p class="mb-0 text-muted"><?= htmlspecialchars($art['desc_article']) ?></p>
                                        <div class="d-flex align-items-center justify-content-center gap-2 mt-2">
                                            <span class="fw-bold text-dark"><?= number_format($art['prix_article'], 0, '.', ' ')." FCFA" ?></span>
                                            <i class="fas fa-heart leb-fav-button" data-id="0"></i>
                                        </div>
                                        <div class="badge bg-light text-orange mt-2" style="color:var(--leb-orange);font-weight:500">livraison possible</div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if(empty($articles_populaires)): ?>
                            <p style="color: gray; text-align:center; font-style: italic;"><i class="fa-solid fa-circle-exclamation"></i> Le boutiquier n'a publié aucune description pour sa boutique!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="container">
                <section class="trust-bar">
                    <div class="trust-item">
                        <div class="icon-box">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <div class="trust-text">
                            <h5>Livraison Rapide</h5>
                            <p>Chez vous en 1h</p>
                        </div>
                    </div>

                    <div class="trust-item">
                        <div class="icon-box">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="trust-text">
                            <h5>Support 24h/7j</h5>
                            <p>Une équipe à l'écoute</p>
                        </div>
                    </div>

                    <div class="trust-item">
                        <div class="icon-box">
                            <i class="fas fa-shield"></i>
                        </div>
                        <div class="trust-text">
                            <h5>Sécurité</h5>
                            <p>Protection des informations</p>
                        </div>
                    </div>

                    <div class="trust-item">
                        <div class="icon-box">
                            <i class="fas fa-undo"></i>
                        </div>
                        <div class="trust-text">
                            <h5>Retour Simple</h5>
                            <p>Satisfait ou remboursé</p>
                        </div>
                    </div>

                </section>
            </div>

            <?php include '../vendor/chatAI/discuss.php'; ?>

            <footer class="leb-footer mt-5">
                <div class="container-lg">
                    <div class="row">
                        <div class="col-lg-4 leb-footer-section mb-3 mb-lg-0">
                            <strong class="leb-footer-brand">Leboutiquier</strong>
                            <p style="font-size:.98rem;">
                                Service innovant de petites annonces locales et nationales.<br>
                                Retrouvez vos produits favoris et achetez chez vous en toute confiance, proche de chez vous ou partout !
                            </p>
                        </div>
                        <div class="col-lg-4 leb-footer-section mb-3 mb-lg-0">
                            <h6>À propos</h6>
                            <ul class="list-unstyled" style="font-size:.99rem;">
                                <li><a href="LeBoutiquier_Description.php" class="text-secondary text-decoration-none">Qui sommes-nous ?</a></li>
                                <li><a href="Conditions_Générales.php" class="text-secondary text-decoration-none">Conditions générales</a></li>
                                <li><a href="Contact.php" class="text-secondary text-decoration-none">Contact</a></li>
                            </ul>
                        </div>
                        <div class="col-lg-4 leb-footer-section">
                            <h6>Suivez-nous</h6>
                            <div class="d-flex gap-3">
                                <a href="page not found.php" class="text-secondary fs-4"><i class="fab fa-facebook-f"></i></a>
                                <a href="page not found.php" class="text-secondary fs-4"><i class="fab fa-whatsapp"></i></a>
                                <a href="page not found.php" class="text-secondary fs-4"><i class="fab fa-instagram"></i></a>
                                <a href="page not found.php" class="text-secondary fs-4"><i class="fab fa-twitter"></i></a>
                            </div>
                            <div class="text-muted small mt-3">&copy; Leboutiquier 2025</div>
                        </div>
                    </div>
                </div>
            </footer>
        </main>
        <!-- Script Interactions -->
        <script src="../js/bootstrap.min.js"></script>
        <script>
            // Favori interaction façon Leboncoin, localStorage
            document.addEventListener('DOMContentLoaded', function(){
                document.querySelectorAll('.leb-fav-button').forEach(function(icon){
                    var id = "lebfav_"+(icon.dataset.id||"");
                    if(localStorage.getItem(id) === '1') icon.classList.add('active');
                    icon.addEventListener('click', function(e){
                        icon.classList.toggle('active');
                        localStorage.setItem(id, icon.classList.contains('active') ? '1' : '0');
                    });
                });
            });
            
            const estConnecte=<?php echo json_encode(isset($_SESSION['id'])); ?>;
            const typeCompte=<?php echo json_encode(isset($_SESSION['compte']))??''; ?>;
            const inputRecherche=document.querySelector('.leb-searchbox');
            const suggestionsBox = document.getElementById('searchSuggestions');
            if(inputRecherche){
                inputRecherche.addEventListener('input', function(){
                    const query = this.value;
                    const type = document.querySelector('.leb-search-type').value;

                    if (query.length > 2) {
                        fetch(`recherche.php?query=${encodeURIComponent(query)}&type=${type}`)
                            .then(res => res.text())
                            .then(text => {
                                try{
                                    const data=JSON.parse(text);
                                    renderSuggestions(data, type);
                                }
                                catch(err){
                                    console.error("Erreur de format JSON. Texte reçu :", text);
                                }
                            });
                    } 
                    else {
                        suggestionsBox.style.display = 'none';
                    }
                });
            }
            function renderSuggestions(data, type){
                console.log('Données reçues pour l\'affichage: ', data);
                suggestionsBox.style.display = 'block';
                suggestionsBox.innerHTML = '';
                if (data && data.length > 0) {
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'suggestion-item';
                        // On affiche le nom et une petite icône selon le type
                        div.innerHTML = `<span>${item.nom}</span> <i class="fas fa-chevron-right"></i>`;
                        div.onclick = () => {
                            let destination="";

                            if(type==='categorie'){
                                destination=`Nos différents articles.php?id_cat=${item.id_categorie}`;
                            }
                            else{
                                destination=`profil boutique.php?id_article=${item.id_article}&id_commerçant=${item.id_commerçant}`;
                            }

                            if(estConnecte){
                                window.location.href=destination;
                            }
                            else{
                                window.location.href=`connexion.php?redirect=${encodeURIComponent(destination)}`;
                            }
                        };
                        suggestionsBox.appendChild(div);
                    });
                } 
                else {
                    const noResult=document.createElement('div');
                    noResult.className='suggestion-item';
                    noResult.style.padding="15px";
                    noResult.style.textAlign="center";
                    noResult.style.backgroundColor="#fff";
                    if(type==='categorie'){
                        noResult.innerHTML=`<p style="color: gray; text-align: center;"><i class="fa-solid fa-circle-exclamation"></i> Aucune ${type} ne correspond à votre recherche</p>`;
                    }
                    else{
                        noResult.innerHTML=`<p style="color: gray; text-align: center;"><i class="fa-solid fa-circle-exclamation"></i> Aucun ${type} ne correspond à votre recherche</p>`;
                    }
                    suggestionsBox.appendChild(noResult);
                    suggestionsBox.style.pointerEvents="none";
                }
            }
            // Fermer la liste si on clique ailleurs
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.position-relative')) {
                    document.getElementById('searchSuggestions').style.display = 'none';
                }
            });

            function redirectionCategorie(id_cat){
                const  destination=`Nos différents articles.php?id_cat=${id_cat}`;
                if(estConnecte){
                    window.location.href=destination;
                }
                else{
                    window.location.href=`connexion.php?redirect=${encodeURIComponent(destination)}`;
                }
            }
            function redirectionCompte(){
                const  destinationClient=`espaceclient.php`;
                const  destinationBoutiquier=`espaceclient.php`;
                const  destinationAdmin=`dashboard.php`;
                const  destinationLivreur=`livraison.php`;
                if(estConnecte){
                    if(typeCompte==="boutiquier"){
                        window.location.href=destinationBoutiquier;
                    }
                    else if(typeCompte==="client"){
                        window.location.href=destinationClient;
                    }
                    else if(typeCompte==="livreur"){
                        window.location.href=destinationLivreur;
                    }
                    else{
                        window.location.href=destinationAdmin;
                    }
                }
                else{
                    window.location.href="connexion.php?redirect=compte";
                }
            }

            // Rediriger "publier" bouton (desktop et mobile)
            document.querySelectorAll('#publier, #publier-mobile').forEach(function(btn){
                btn && btn.addEventListener('click', function(){
                    const merchantId = this.getAttribute('data-id');
                    const destination = merchantId ? `Espacecommerçant.php?id=${merchantId}` : 'accueil.php';
                    if(estConnecte){
                        window.location.href=destination;
                    }
                    else{
                        window.location.href=`connexion.php?redirect=${encodeURIComponent(destination)}`;
                    }
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                const btnWhatsapp = document.getElementById('demander-gestion');
                if (btnWhatsapp) {
                    btnWhatsapp.addEventListener('click', function() {
                        const numeroTel = "237692238528";  
                        const message = "Bonjour ! Je visite LeBoutiquier et j'aimerais avoir plus d'informations sur votre service de gestion déléguée. Je ne maîtrise pas bien l'outil informatique et j'aimerais qu'un expert m'accompagne. Merci !";

                        // Encodage du message pour l'URL
                        const url = `https://wa.me/${numeroTel}?text=${encodeURIComponent(message)}`;

                        // Ouverture dans un nouvel onglet
                        window.open(url, '_blank');
                    });
                }
            });

            if('serviceWorker' in navigator){
                window.addEventListener('load', function(){
                    navigator.serviceWorker.register('../vendor/sw.js')
                    .then(function(reg){console.log('Service Worker enregistré!', reg);})
                    .catch(function(err){console.warn('Erreur d\'enregistrement SW: ', err)});
                });
            }
        </script>
    </body>
</html>