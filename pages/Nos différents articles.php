<?php 

    session_start();
    include 'connexionBD.php';
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    file_put_contents('debug_log.txt', print_r($_POST, true));
    $errorMsg='';
    $successMsg='';
    $id_cat=isset($_GET['id_cat'])?$_GET['id_cat']:'';
    $search=$_GET['search']?? null;

    if(isset($_SESSION['id'] )){
        $usersID=$_SESSION['id'];
        if($_SESSION['compte']==='client'){
            $queryclient=$conn->prepare("SELECT * FROM client WHERE id=?");
            $queryclient->execute([$usersID]);
            $clientRow=$queryclient->fetch(PDO::FETCH_ASSOC);
            if($clientRow){
                $client=$clientRow;
                $clientID=$clientRow['id_client'];
                $visite=$clientID;
            }
        }
        elseif($_SESSION['compte']==='boutiquier'){
            $stmtshow=$conn->prepare("SELECT * FROM commerçant WHERE id=?");
            $stmtshow->execute([$usersID]);
            $rowBtq=$stmtshow->fetch(PDO::FETCH_ASSOC);
            if($rowBtq){
                $Btq=$rowBtq;
                $BtqID=$rowBtq['id_commerçant'];
                $visite=$rowBtq['id_commerçant'];
            }
        }
        elseif($_SESSION['compte']==='livreur'){
            $stmtshow=$conn->prepare("SELECT * FROM utilisateur WHERE id=? AND compte='livreur'");
            $stmtshow->execute([$usersID]);
            $rowBtq=$stmtshow->fetch(PDO::FETCH_ASSOC);
            if($rowBtq){
                $visite=$rowBtq['id'];
            }
        }
        else{
            $stmtshow=$conn->prepare("SELECT * FROM utilisateur WHERE id=? AND compte='admin'");
            $stmtshow->execute([$usersID]);
            $rowBtq=$stmtshow->fetch(PDO::FETCH_ASSOC);
            if($rowBtq){
                $visite=$rowBtq['id'];
            }
        }
    }

    /*try{
        $sqlCat=$conn->prepare("SELECT a.*, c.id_cat, co.nom_boutique, (SELECT COUNT(*) FROM details_commande dtl WHERE dtl.id_article=a.id_article) as nbr_commande
                                 FROM article a, catégorie c, commerçant co 
                                 WHERE a.id_cat=c.id_cat AND a.id_commerçant=co.id_commerçant AND c.id_cat=?");
        $params=[];
        if(isset($_GET['search']) && !empty($_GET['search'])){
            $search="%".$_GET['search']."%";
            $sqlCat.=" AND a.nom_article LIKE ? ORDER BY a.prix_article ASC";
            $params[]=$search;
        }
        $stmtArt=$conn->prepare($sqlCat);
        $stmtArt->execute($params);
        $articlesCat=$stmtArt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e){
        die("Erreur: ".$e->getMessage());
    }*/
     
    try{
        $articlesCat=[];
        if($id_cat){
            $sqlCat="SELECT a.*, c.id_cat, c.nom_cat, co.nom_boutique, co.id_commerçant, 
                            (SELECT COUNT(*) FROM details_commande dtl WHERE dtl.id_article=a.id_article) as nbr_commande,
                            (SELECT COUNT(*) FROM favoris f WHERE f.id_article=a.id_article AND id_client=?) as is_fav
                    FROM article a, catégorie c, commerçant co 
                    WHERE a.id_cat=c.id_cat AND a.id_commerçant=co.id_commerçant AND c.id_cat=?";
            $params=[$visite, $id_cat];
            if(!empty($search)){
                $sqlCat.=" AND a.nom_article LIKE ?";
                $params[]="%" . $search . "%";
            }
            $sqlCat.=" ORDER BY a.prix_article ASC";
            $stmtCat=$conn->prepare($sqlCat);
            $stmtCat->execute($params);
            $articlesCat=$stmtCat->fetchAll(PDO::FETCH_ASSOC);

            if(!empty($articlesCat)){
                $titrePage="Articles ".$articlesCat[0]['nom_cat'];
            }
            else{
                $stmtNomCat=$conn->prepare("SELECT nom_cat FROM catégorie WHERE id_cat=?");
                $stmtNomCat->execute([$id_cat]);
                $catInfo=$stmtNomCat->fetch();

                if($catInfo){ 
                    $titrePage="Articles ".$catInfo['nom_cat'];
                }
            }
        }

    }
    catch(PDOException $e){
        die("Erreur: ".$e->getMessage());
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

?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Liste des produits - Leboutiquier</title>
        <link rel="stylesheet" href="../fontawesome/css/all.min.css">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../style/articles.css">
        <link rel="stylesheet" href="../style/Espacecommerçant.css">
        <!-- PWA -->
        <link rel="manifest" href="../vendor/manifest.json">
        <meta name="theme-color" content="#EA580C">
        <link rel="icon" href="../Image/favoicon.ico" sizes="48x48">
        <link rel="apple-touch-icon" href="../Image/logo.png">
    </head>
    <body>
        <?php include 'splash.php'; ?>
        <nav class="fixed-top-navbar">
            <button class="btn-back" onclick="window.history.back();" title="Retour"><i class="fas fa-arrow-left"></i></button>
            <div class="navbar-main-title">Le boutiquier</div>
        </nav>

        <div class="container" style="margin-top:72px; margin-bottom:48px;">
            <?php if(empty($search)): ?>
                <h3 class="text-center mb-4 mt-3" style="font-weight: bold;"><?= htmlspecialchars($titrePage) ?></h3>
            <?php else: ?>
                <h3 class="text-center mb-4 mt-3" style="font-weight: bold;">Résultat de la recherche</h3>
            <?php endif; ?>
            <!-- Barre de recherche ajoutée ici -->
            <form class="lb-search-bar mb-4" autocomplete="on" method="get">
                <input type="hidden" name="id_cat" value="<?= htmlspecialchars($id_cat) ?>">
                <div class="input-group">
                    <input id="searchProduit" name="search" type="text" class="form-control" placeholder="Rechercher un article..." aria-label="Rechercher" value="<?= htmlspecialchars($search) ?>">
                    
                    <?php if(empty($search)): ?>
                        <button type="submit" class="btn btn-primary" tabindex="-1" style="cursor: pointer;">
                            <i class="fas fa-search"></i>
                        </button>
                    <?php else: ?>
                        <a href="Nos différents articles.php?id_cat=<?= urlencode($id_cat) ?>" class="btn btn-danger px-4 d-flex align-items-center"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                </div>
            </form>
            <div class="row g-3" id="demo-products-list">
                <!-- Les cartes produits seront insérées ici en JS -->
                <?php foreach ($articlesCat as $artCat): 
                    $heartClass=($artCat['is_fav']>0)?'text-danger fa-solid':'fa-regular';
                ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card product-card p-2 h-100 shadow-sm">
                            <div class="d-flex flex-row align-items-center gap-2">
                                <img src="../ImagesBD/<?= $artCat['photo_article'] ?>" alt="Produit" class="product-img flex-shrink-0" width="120" height="120">
                                <div class="flex-grow-1 ms-1">
                                    <div class="product-title fw-bold mb-1"><?= $artCat['nom_article'] ?></div>
                                    <?php if(isset($_SESSION['id'])): ?>
                                        <div class="boutique-name mb-1"><i class="fa-store fa-fw fa"></i><a href="profil boutique.php?id_article=<?= urlencode($artCat['id_article']) ?>&id_commerçant=<?= urlencode($artCat['id_commerçant']) ?>" style="text-decoration: none; text-transformation: none; color: #EA580C;"><?= $artCat['nom_boutique'] ?></a></div>
                                    <?php else: ?>
                                        <div class="boutique-name mb-1"><i class="fa-store fa-fw fa"></i><a href="connexion.php?redirect=profil boutique.php?id_article=<?= urlencode($artCat['id_article']) ?>&id_commerçant=<?= urlencode($artCat['id_commerçant']) ?>" style="text-decoration: none; text-transformation: none; color: #EA580C;" title="Visitez la boutique"><?= $artCat['nom_boutique'] ?></a></div>
                                    <?php endif; ?>
                                    <div><span class="fw-bold" style="color:#EA580C;"><?= number_format($artCat['prix_article'], 0, '.', ' ')." FCFA" ?></span></div>
                                    <div class="product-meta mt-1 mb-1"><i class='fas fa-shopping-bag me-1'></i>Commandé <span class="fw-bold"><?= $artCat['nbr_commande'] ?></span> fois</div>
                                    <div class="product-meta"><i class='far fa-calendar-alt me-1'></i>Publié le <?= date('d/m/Y', strtotime($artCat['date_ajout'])); ?></div>
                                    <?php if($_SESSION['compte']==='client'): ?>
                                        <button class="<?= $heartClass ?> fa-heart btn-favori-article  bg-light border-0 text-end" data-id-article="<?= $artCat['id_article'] ?>" style="cursor: pointer;" title="mettre en favoris" onclick="location.reload();"></button>
                                    <?php else: ?>
                                        <button class="fa-regular fa-heart btn-favori-article  bg-light border-0 text-end" style="cursor: pointer;"  title="Bloqué"></button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if(empty($articlesCat)): ?>
                    <?php if(!empty($search)): ?>
                        <div class="empty-state text-center" style="padding: 80px 20px; background: #f8f9fa; border-radius: 20px; border: 2px dashed #dee2e6;">
                            <div class="mb-3 display-1 text-warning"><i class="fas fa-search text-muted"></i></div>
                                <h3 class="fw-bold">Aucun résultat trouvé pour "<?= htmlspecialchars($search) ?>"</h3>
                                <p class="text-muted">Nous n'avons rien trouvé qui correspondt à votre recherche dans cette catégorie!</p>
                                <a href="Nos différents articles.php?id_cat=<?= $id_cat ?>" class="btn btn-primary rounded-pill px-4 py-2 mt-3 shadow"><i class="bi bi-arrow-left me-2"></i> Voir tous les articles</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="mb-4"><i class="fas fa-box-open text-muted" style="font-size: 5rem; opacity: 0.3;"></i></div>
                            <h3 class="fw-bold text-secondary">Oups ! C'est encore un peu vide ici...</h3>
                            <p class="text-muted mx-auto" style="max-width: 400px;">Nos rayons pour cette catégorie sont en cours de <strong>ravitaillement...</strong> à très bientot!</p>
                            <a href="accueil.php" class="btn btn-primary rounded-pill px-4 py-2 mt-3 shadow"><i class="bi bi-arrow-left me-2"></i> Découvrez d'autres pépites...</a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php include '../vendor/chatAI/discuss.php'; ?>

        <!-- Footer -->
        <footer class="lb-footer-main mt-5 pt-4">
            <div class="container pb-2">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h5 class="footer-title mb-2">À propos</h5>
                        <ul class="list-unstyled">
                            <li><a href="#" class="text-decoration-none text-dark">Qui sommes-nous</a></li>
                            <li><a href="#" class="text-decoration-none text-dark">Notre mission</a></li>
                            <li><a href="#" class="text-decoration-none text-dark">Aide & FAQ</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h5 class="footer-title mb-2">Nos services</h5>
                        <ul class="list-unstyled">
                            <li><a href="#" class="text-decoration-none text-dark">Vendre un article</a></li>
                            <li><a href="#" class="text-decoration-none text-dark">Trouver une boutique</a></li>
                            <li><a href="#" class="text-decoration-none text-dark">Conseils de sécurité</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h5 class="footer-title mb-2">Mentions</h5>
                        <ul class="list-unstyled">
                            <li><a href="#" class="text-decoration-none text-dark">Conditions générales</a></li>
                            <li><a href="#" class="text-decoration-none text-dark">Vie privée</a></li>
                            <li><a href="#" class="text-decoration-none text-dark">Accessibilité</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h5 class="footer-title mb-2">Contact</h5>
                        <ul class="list-unstyled">
                            <li><a href="mailto:contact@leboutiquier.com" class="text-decoration-none text-dark"><i class="fas fa-envelope me-1 accent-color"></i> Nous contacter</a></li>
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

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script>

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
            });

        </script>
    </body>
</html>
