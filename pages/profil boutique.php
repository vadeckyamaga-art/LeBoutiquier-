<?php

    session_start();
    include 'connexionBD.php';
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    file_put_contents('debug_log.txt', print_r($_POST, true));
    $errorMsg='';
    $successMsg='';
    $usersID=$_SESSION['id'];
    $id_article=isset($_GET['id_article'])?$_GET['id_article']:'';
    $id_commerçant=isset($_GET['id_commerçant'])?$_GET['id_commerçant']:'';

    if(isset($_SESSION['id'] )){
        if($_SESSION['compte']==='client'){
            $queryclient=$conn->prepare("SELECT * FROM client WHERE id=?");
            $queryclient->execute([$usersID]);
            $clientRow=$queryclient->fetch();
            if($clientRow){
                $clientID=$clientRow['id_client'];
            }
        }
        elseif($_SESSION['compte']==='boutiquier'){
            $stmtcom=$conn->prepare("SELECT id_commerçant FROM commerçant WHERE id=?");
            $stmtcom->execute([$usersID]);
            $commmerçant=$stmtcom->fetch();
            if($commmerçant){
                $IDcom=$commmerçant['id_commerçant'];
            }
        }
        elseif($_SESSION['compte']==='admin'){
            $stmtadmin=$conn->prepare("SELECT id FROM utilisateur WHERE compte='admin'");
            $admin=$stmtadmin->fetch();
            if($admin){
                $IDadmin=$admin['id'];
            }
        }
        elseif($_SESSION['compte']==='livreur'){
            $stmtlivreur=$conn->prepare("SELECT id FROM utilisateur WHERE compte='livreur'");
            $livreur=$stmtlivreur->fetch();
            if($livreur){
                $IDlivreur=$livreur['id'];
            }
        }
        else{
            header('Location: connexion.php');
            exit();
        }
    }
    else{
        header('Location: connexion.php');
        exit();
    }

                                                             /* Insérer panier */
    if(isset($_POST['ajouter_panier'])){
        $id_article=$_POST['id_art'];
        $quantite=$_POST['quantite'];
        try{
            $checkPanier=$conn->prepare("SELECT id_panier FROM panier WHERE id_article=? AND id_client=?");
            $checkPanier->execute([$id_article, $clientID]);

            if($checkPanier->rowCount()>0){
                $updatePanier=$conn->prepare("UPDATE panier SET Quantite=Quantite+? WHERE id_article=? AND id_client=?");
                $updatePanier->execute([$quantite ,$id_article, $clientID]);
                echo"update";
            }
            else{
                $id_panier="PAN-".date("Y")."-".random_int(1000, 9999);
                $insertPanier=$conn->prepare("INSERT INTO panier (id_panier, Quantite, id_article, id_client) VALUES (?, ?, ?, ?)");
                $insertPanier->execute([$id_panier, $quantite, $id_article, $clientID]);
                echo"insert";
            }
            exit;
        }
        catch(PDOException $e){
            die("Erreur: ".$e->getMessage());
        }
        header('Location: profil boutique.php');
        exit();
    }

    $sqlArticle="SELECT a.*, co.nom_boutique, co.id_commerçant, (SELECT COUNT(*) FROM details_commande dtl WHERE dtl.id_article=a.id_article) as nbr_commande
                 FROM article a, commerçant co 
                 WHERE a.id_commerçant=co.id_commerçant AND a.id_article=?";
    $stmtArticle=$conn->prepare($sqlArticle);
    $stmtArticle->execute([$id_article]);
    $articleChoisi=$stmtArticle->fetchAll(PDO::FETCH_ASSOC);

                                                        /* Affichage des avis */
    $limit=3;
    $stmtAvisCom=$conn->prepare("SELECT avs.*, u.nom, u.prenom, u.id
                 FROM avis avs, utilisateur u, client clt
                 WHERE clt.id_client=avs.id_client AND clt.id=u.id AND avs.id_commerçant=?
                 ORDER BY avs.date_avis DESC
                 LIMIT ?");
    $stmtAvisCom->bindValue(1, $id_commerçant, PDO::PARAM_STR);
    $stmtAvisCom->bindValue(2, $limit, PDO::PARAM_INT);
    $stmtAvisCom->execute();
    $commentaires=$stmtAvisCom->fetchAll();

    $stmtTotalAvis=$conn->prepare("SELECT COUNT(*)
                                   FROM avis WHERE id_commerçant=?");
    $stmtTotalAvis->execute([$id_commerçant]);
    $totalAvis=$stmtTotalAvis->fetchColumn();

    $stmtTopVente=$conn->prepare("SELECT a.*, SUM(dtl.quantite_cmd) as total_vendu
                                  FROM article a, details_commande dtl
                                  WHERE dtl.id_article=a.id_article AND a.id_commerçant=?
                                  GROUP BY a.id_article
                                  ORDER BY total_vendu DESC
                                  LIMIT 3");
    $stmtTopVente->execute([$id_commerçant]);
    $bestSellers=$stmtTopVente->fetchAll();

    $stmtInfoCommerçant=$conn->prepare("SELECT u.*, co.nom_boutique, co.description_boutique, co.profil_boutique
                                        FROM utilisateur u, commerçant co
                                        WHERE u.id=co.id AND co.id_commerçant=?");
    $stmtInfoCommerçant->execute([$id_commerçant]);
    $infoCommerçant=$stmtInfoCommerçant->fetchAll();

                                                /* Debut bloc d'attribution de la certification */
    $stmtStars=$conn->prepare("SELECT AVG(note)
                               FROM avis 
                               WHERE id_commerçant=?");
    $stmtStars->execute([$id_commerçant]);
    $moyenneEtoile=$stmtStars->fetchColumn()?:0;

    $stmtVentes=$conn->prepare("SELECT SUM(dtl.quantite_cmd)
                                FROM details_commande dtl, article a
                                WHERE a.id_article=dtl.id_article AND a.id_commerçant=?");
    $stmtVentes->execute([$id_commerçant]);
    $totalVendus=$stmtVentes->fetchColumn()?:0;

    $stmtAnciennete=$conn->prepare("SELECT u.*, co.profil_boutique, co.description_boutique, co.Quartier_boutique, co.nom_boutique
                                    FROM utilisateur u, commerçant co
                                    WHERE u.id=co.id AND co.id_commerçant=?");
    $stmtAnciennete->execute([$id_commerçant]);
    $ancienneteCheck=$stmtAnciennete->fetch();

    $dateInscription=new DateTime($ancienneteCheck['dateInsc']);
    $aujourdhui=new DateTime();
    $intervalle=$dateInscription->diff($aujourdhui);
    $ancienneteOk=($intervalle->m>=1||$intervalle->y>=1);

    $infoRemplies=(!empty($ancienneteCheck));

    $estCertifie=($moyenneEtoile>1 && $totalVendus>5 && $ancienneteOk && $infoRemplies);


                                            /* Fin bloc d'attribution de la certification */


?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profil du Vendeur - Leboutiquier</title>
        <link rel="stylesheet" href="../fontawesome/css/all.min.css">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../style/Espacecommerçant.css">
        <link rel="stylesheet" href="../style/profil boutique.css">
        <!-- PWA -->
        <link rel="manifest" href="../vendor/manifest.json">
        <meta name="theme-color" content="#EA580C">
        <link rel="icon" href="../Image/favoicon.ico" sizes="48x48">
        <link rel="apple-touch-icon" href="../Image/logo.png">
    </head>
    <body style="background:#fbfbfb;">
        <?php include 'splash.php'; ?>
        <nav class="fixed-top-navbar">
            <button class="btn-back" onclick="window.history.back();" title="Retour"><i class="fas fa-arrow-left"></i></button>
            <div class="navbar-main-title">Le boutiquier</div>
        </nav>

        <div class="container" style="margin-top:80px; margin-bottom:50px; max-width:950px;">
            <?php foreach($articleChoisi as $artC): ?>
                <div class="profile-header profile-card p-3 mb-3">
                    <img src="../ImagesBD/<?= $artC['photo_article'] ?>" class="main-product-img shadow-sm" alt="Produit sélectionné">
                    <div class="product-profile-details">
                        <h2 class="fw-bold mb-1"><?= $artC['nom_article'] ?></h2>
                        <div class="boutique-name mb-2" style="font-size:1.01rem;"><i class="fa fa-store"></i> <?= $artC['nom_boutique'] ?></div>
                        <div class="mb-2">
                            <span class="fw-bold" style="color:#EA580C; font-size:1.12rem;" data-prix="<?= $artC['prix_article'] ?>" id="prix"><?= number_format($artC['prix_article'], 0, '.', ' ')." FCFA" ?></span>
                            <span class="badge bg-secondary ms-2">Commandé <?= $artC['nbr_commande'] ?> fois</span>
                        </div>
                        <div class="product-meta mb-2"><i class="far fa-calendar-alt me-1"></i>Publié le <?= date('d/m/Y', strtotime($artC['date_ajout'])); ?></div>
                        <div class="mb-3">
                            <span class="form-label fw-bold">Quantité à commander :</span>
                            <div class="input-group align-items-center" style="width:160px;max-width:100%;">
                                <button class="btn-qty" onclick="changerQuantite(-1)" type="button">-</button>
                                <input type="text" id="productQty" name="quantite_principale" class="qty-input mx-1" value="1" min="1" readonly>
                                <button class="btn-qty" onclick="changerQuantite(1)" type="button">+</button>
                            </div>
                        </div>
                        <?php if($_SESSION['compte']==='client'): ?>
                            <button class="btn btn-primary px-4 btn-add-to-cart" type="button" data-id-article="<?= $_GET['id_article']; ?>" style="background:#EA580C;border:none;"><i class="fas fa-shopping-cart"></i> Ajouter au panier</button>
                        <?php else: ?>
                            <button class="btn btn-primary px-4 btn-add-to-cart" disabled type="button" data-id-article="<?= $_GET['id_article']; ?>" style="background:#EA580C;border:none;"><i class="fas fa-shopping-cart"></i> Ajouter au panier</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Avis clients -->
            <div class="profile-card p-3 mb-4">
                <div class="seller-section-title"><i class="fas fa-star me-1"></i> Avis sur la boutique</div>
                <div id="avis-container"> 
                    <?php foreach($commentaires as $com): 
                        $nom=trim($com['nom']);
                        $initialeNom=mb_strtoupper(mb_substr($nom, 0, 1)).'.';
                        $prenomMaj=ucfirst(mb_strtolower(htmlspecialchars($com['prenom'])));

                        $noteActive=$com['note'];
                    ?>
                        <div class="mb-3 d-flex align-items-start gap-2 flex-wrap">
                            <img src="../Image/boisson.jpg" alt="user" class="review-user-avatar">
                            <div>
                                <div class="fw-bold"><?=$prenomMaj.' '.$initialeNom?><span class="star-orange ms-2">
                                    <?php for($i=1; $i<=5; $i++):  
                                        if($i<=$noteActive):
                                    ?>
                                            <i class="fas fa-star"></i>
                                        <?php else: ?>
                                            <i class="far fa-star"></i>
                                        <?php endif;  ?>
                                    <?php endfor;  ?></span>
                                </div>
                                <div><?= $com['commentaire'] ?></div>
                                <div class="date-light mt-1"><i class="far fa-clock"></i> <?= date('d/m/Y', strtotime($com['date_avis'])); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-center mt-4">
                    <button id="load-more-btn" class="btn btn-outline-primary rounded-pill px-4" data-offset="3" data-total="<?= $totalAvis ?>" data-idCom="<?= $id_commerçant ?>" data-mode="more">Afficher plus d'avis...</button>
                </div>
                <?php if(empty($com)): ?>
                    <div class="text-center py-5">
                        <div class="mb-4"><i class="fas fa-box-open text-muted" style="font-size: 5rem; opacity: 0.3;"></i></div>
                        <h3 class="fw-bold text-secondary">Oups ! C'est encore un peu vide ici...</h3>
                        <p class="text-muted mx-auto" style="max-width: 400px;">Aucun avis encore publié pour cette boutique; <strong>soyez peut-etre le premier!...</strong> à très bientot!</p>
                        <a href="#" class="btn btn-primary rounded-pill px-4 py-2 mt-3 shadow"><i class="bi bi-arrow-left me-2"></i>Publier un avis</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Best sellers du vendeur -->
            <div class="profile-card p-3 mb-4">
                <div class="seller-section-title"><i class="fas fa-fire me-1"></i> Produits les plus vendus</div>
                <div class="row best-products-list g-3">
                <?php foreach($bestSellers as $best): ?>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="card card-bestrel shadow-sm p-2 h-100">
                            <button class="border-0 badge-voir" title="Voir ce produit" data-product="<?= htmlspecialchars(json_encode([
                                                                                                                        "id"=>$best["id_article"],
                                                                                                                        "nom"=>$best["nom_article"],
                                                                                                                        "prix"=>$best["prix_article"],
                                                                                                                        "photo"=>$best["photo_article"],
                                                                                                                      "desc"=>$best["desc_article"]??"Pas de description pour cet article!"
                                                                                                                        ])) ?>" onclick="preparerModal(this)"><i class="fas fa-eye"></i> Voir
                            </button>
                            <img src="../ImagesBD/<?= $best['photo_article'] ?>" alt="" class="mb-2">
                            <div class="fw-bold" style="font-size:.97rem;"><?=  $best['nom_article'] ?></div>
                            <div style="color: #EA580C;"><?= number_format($best['prix_article'], 0, '.', ' ')." FCFA" ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
                <?php if(empty($bestSellers)): ?>
                    <div class="col-12 text-center py-5">
                        <div class="no-sales-card p-5 rounded-4 shadow-sm bg-white border-dashed">
                            <div class="mb-4">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-circle fa-stack-2x text-light"></i>
                                    <i class="fas fa-rocket fa-stack-1x-muted opacity-50"></i>
                                </span>
                            </div>
                            <h5 class="fw-bold mb-2">Une boutique prete à décoller !</h5>
                            <p class="text-muted mb-4 mx-auto" style="max-width: 450px;">Les produits de cette boutique n'ont pas encore de classement officiel. C'est l'occasion idéale pour etre parmis les premiers clients à laisser une trace!</p>
                            <a href="#" class="btn btn-outline-primary rounded-pill px-4 transition-all"><i class="fas fa-shopping-bag me-2"></i>Découvrir la collection</a>
                        </div>

                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Profil complet du boutiquier -->
            <div class="profile-card p-3 mb-2">
                <div class="seller-section-title"><i class="fas fa-user me-1"></i> Profil du boutiquier</div>
                <?php foreach($infoCommerçant as $info): 
                    $nom=trim($info['nom']);
                    $initialeNom=mb_strtoupper(mb_substr($nom, 0, 1)).'.';
                    $prenomMaj=ucfirst(mb_strtolower(htmlspecialchars($info['prenom'])));
                ?>
                    <div class="row g-2">
                        <div class="col-12 col-md-3 text-center"><img src="../ImagesBD/<?= $info['profil_boutique'] ?>" alt="avatar" class="boutique-avatar mb-2"></div>
                        <div class="col-12 col-md-9">
                            <div class="fw-bold" style="font-size:1.01rem;"><?= $prenomMaj.' '.$initialeNom ?></div>
                            <?php if($estCertifie): ?>
                                <div class="mb-1"><i class="fa fa-store me-1"></i><?= $info['nom_boutique'] ?><span class="ms-2 shadow-sm d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle" style="width: 15px; height: 15px; font-size: 12px;" title="certifié"><i class="fas fa-check"></i></span></div>
                            <?php else: ?>
                                <div class="mb-1"><i class="fa fa-store me-1"></i><?= $info['nom_boutique'] ?><span class="ms-2 shadow-sm d-inline-flex align-items-center justify-content-center bg-warning text-white rounded-circle" style="width: 15px; height: 15px; font-size: 12px;" title="En cours de certification"><i class="fas fa-check"></i></span></div>
                            <?php  endif; ?>
                            <div class="mb-2"><i class="fas fa-map-marker-alt me-1"></i> Douala, Cameroun</div>
                            <div class="mb-1"><i class="fas fa-envelope me-1"></i><?= $info['email'] ?></div>
                            <div class="mb-2"><span class="badge bg-light text-dark border" style="font-weight:600;">Inscrit depuis le <?= date('d/m/Y', strtotime($info['dateInsc'])); ?></span></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php foreach($infoCommerçant as $info):?>
                <?php if(!empty($info['description_boutique'])): ?>
                    <div class="profile-card p-3 mb-2">
                        <div class="seller-section-title"><i class="fas fa-info-circle me-1"></i> Description de la boutique</div>
                        <div><?= $info['description_boutique'] ?><br></div>
                    </div>
                <?php else: ?>
                    <p style="color: gray; text-align:center; font-style: italic;"><i class="fa-solid fa-circle-exclamation"></i> Le boutiquier n'a publié aucune description pour sa boutique!</p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="location.reload();"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6 text-center">
                                <img src="" alt="photo article" id="modalImg" class="img-fluid rounded-4 shadow-sm" style="max-height: 350px; object-fit: contain;">
                            </div>
                            <div class="col-md-6">
                                <h2 id="modalTitle" class="fw-bold mb-2"></h2>
                                <h4 id="modalPrice" class="fw-bold mb-4" style="color: #EA580C;"></h4>
                                <p id="modalDesc" class="text-muted mb-4"></p>
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <span class="fw-bold">Quantité :</span>
                                    <div class="input-group" style="width: 140px;">
                                        <button class="btn btn-outline-secondary rounded-start-pill" type="button" onclick="changeQty(-1)"><i class="fas fa-minus"></i></button>
                                        <input type="text" id="modalQty" name="quantite" class="form-control text-center border-secondary" style="outline: none;" value="1" min="1" readonly>
                                        <button class="btn btn-outline-secondary rounded-end-pill" type="button" onclick="changeQty(+1)"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-lg-between align-items-center mb-4 p-3 bg-light rounded-3">
                                    <span class="text-muted small fw-bold text-uppercase">Total à payer : </span>
                                    <span id="modalTotal" class="fs-4 fw-bold text-dark"> 0 FCFA</span>
                                </div>
                                <?php if($_SESSION['compte']==='client'): ?>
                                    <button onclick="" class="btn btn-dark w-100 py-3 border-0 rounded-pill fw-bold shadow btn-add-to-cart" data-id-article="" id="btn-confirm-add-modal"><i class="fas fa-shopping-cart me-2"></i>Ajouter au panier</button>
                                <?php else: ?>
                                    <button onclick="" class="btn btn-dark w-100 py-3 border-0 rounded-pill fw-bold shadow btn-add-to-cart" data-id-article="" id="btn-confirm-add-modal" disabled><i class="fas fa-shopping-cart me-2"></i>Ajouter au panier</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
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
        
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script>
            // Gestion quantité commander
            const showPrice=document.getElementById('prix');
            let prix=showPrice.dataset.prix;
            function changerQuantite(delta) {
                var input = document.getElementById('productQty');
                var val = parseInt(input.value);
                if (isNaN(val)) val = 1;
                val += delta;
                if (val < 1) val = 1;
                if (val > 50) val = 50;
                input.value = val;
                showTotal(val);
            }
            function showTotal(qty){
                let total=qty*prix;
                showPrice.innerText=new Intl.NumberFormat().format(total)+" FCFA";
            }

            document.getElementById('load-more-btn').addEventListener('click', function(){
                let btn=this;
                let container=document.getElementById('avis-container');
                let mode=btn.getAttribute('data-mode');
                let total=parseInt(btn.getAttribute('data-total'));
                let idCom=btn.getAttribute('data-idCom');

                if(mode==="more"){
                    let offset=parseInt(btn.getAttribute('data-offset'));

                    fetch(`load_more_avis.php?id_commerçant=${idCom}&offset=${offset}`).then(respose=>respose.text()).then(data=>{
                        if(data.trim()!=="EMPTY"){
                            let newBlock=`<div class="avis-added-block">${data}</div>`;
                            container.insertAdjacentHTML('beforeend', newBlock);

                            let nextOffset=offset+3;
                            btn.setAttribute('data-offset', nextOffset);

                            if(nextOffset>=total){
                                btn.innerHTML="Voir moins...";
                                btn.setAttribute('data-mode', 'less');
                                btn.classList.replace('btn-outline-primary', 'btn-outline-secondary');
                            }
                        }
                    });
                }
                else{
                    let allGroups=container.querySelectorAll('.avis-added-block');
                    allGroups.forEach(group=>group.remove());

                    btn.innerHTML="Afficher plus d'avis";
                    btn.setAttribute('data-mode', 'more');
                    btn.setAttribute('data-offset', '3');
                    btn.classList.replace('btn-outline-secondary', 'btn-outline-primary');

                    container.scrollIntoView({behavior: 'smooth'});
                }
            });

            let currentProductId=null;
            let prixUnitaire=0;
            function openModal(data){
                currentProductId=data.id;
                prixUnitaire=data.prix;
                document.getElementById('modalTitle').innerText=data.nom;
                document.getElementById('modalPrice').innerText=new Intl.NumberFormat().format(data.prix)+" FCFA";
                document.getElementById('modalImg').src="../ImagesBD/"+data.photo;
                document.getElementById('modalDesc').innerText=data.desc;
                document.getElementById('modalQty').value=1;

                const btnModal=document.getElementById('btn-confirm-add-modal');
                btnModal.setAttribute('data-id-article', data.id);

                calculerTotal(1);

                var myModal=new bootstrap.Modal(document.getElementById('productModal'));
                myModal.show();
            }

            function preparerModal(element){
                try{
                    const jsonStr= element.getAttribute('data-product')
                    const data= JSON.parse(jsonStr);
                    openModal(data);
                }
                catch(e){
                    console.error("Erreur de lecture de données produits: ", e)
                }
            }

            function changeQty(val){
                let input=document.getElementById('modalQty');
                let newValue=parseInt(input.value)+val;
                if(newValue>=1){
                    input.value=newValue;
                    calculerTotal(newValue);
                }  
            }
            function calculerTotal(qty){
                let total=qty*prixUnitaire;
                document.getElementById('modalTotal').innerText=new Intl.NumberFormat().format(total)+" FCFA";
            }

            document.addEventListener('click', function(e){
                const inputQty=document.getElementById('modalQty')||document.querySelector('input[name="quantite_principale"]');
                const Qty=inputQty?inputQty.value:1;

                try{
                    const btn=e.target.closest('.btn-add-to-cart');
                    if(!btn) return;

                    const idArt=btn.getAttribute('data-id-article');
                    if(!idArt){
                        console.log("ERREUR: l'id est manquant sur le bouton");
                        return;
                    }
                  
                    btn.innerText="Ajout...";
                    btn.disable=true;

                    const fd=new FormData();
                    fd.append('ajouter_panier', 'true');
                    fd.append('id_art', idArt);
                    fd.append('quantite', Qty);

                    fetch('profil boutique.php',{
                        method: 'POST',
                        body: fd,
                    }).then(response=>response.text()).then(data=>{
                            console.log("Reponse serveur: ",data.trim());  
                            if(data.trim()==='insert'||data.trim()==='update'){
                                btn.innerText="Patienter...";
                                btn.style.pointerEvents="none";
                                btn.style.opacity="0.8";
 
                                setTimeout(()=>{
                                    btn.innerText="Ajouté!";
                                    if(btn.style.backgroundColor="#EA580C"){
                                        btn.style.backgroundColor="#198754";
                                    }
                                    else{
                                        btn.classList.replace('btn-dark', 'btn-success');
                                    }
                                    btn.disable=false;
                                    btn.style.pointerEvents="none";
                                    btn.style.opacity="0.8";
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
            });

        </script>
    </body>
</html>
