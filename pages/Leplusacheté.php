<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits les plus achetés en ce moment</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../style/accueil.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/Leplusacheté.css">
    <!-- PWA -->
    <link rel="manifest" href="../vendor/manifest.json">
    <meta name="theme-color" content="#EA580C">
    <link rel="icon" href="../Image/favoicon.ico" sizes="48x48">
    <link rel="apple-touch-icon" href="../Image/logo.png">
</head>
<body>
    <?php include 'splash.php'; ?>
    <!-- HEADER / NAVBAR -->
    <nav class="navbar navbar-light bg-light py-3 px-2 shadow-sm">
        <div class="container-fluid">
            <button class="btn btn-light me-2 px-2" onclick="history.back()" style="border-radius:50%; border:1px solid #f2eae5;">
                <i class="fas fa-arrow-left accent-color"></i>
            </button>
            <span class="navbar-brand mx-auto p-0 fw-bold accent-color" id="logo" style="font-size:2rem;letter-spacing:1px;">
                Leboutiquier
            </span>
            <span style="width:38px;" class="d-none d-md-inline"></span>
        </div>
    </nav>

    <!-- SEARCH BAR AU MILIEU -->
    <div class="container my-4">
        <div class="row justify-content-center align-items-center" style="min-height:70px;">
            <div class="col-12 d-flex justify-content-center">
                <div class="position-relative" style="width:100%; max-width:480px;">
                    <i class="fas fa-search position-absolute" style="top:50%; left:18px; transform:translateY(-50%); color:#c2c2c2;"></i>
                    <input type="text" class="form-control search-bar ps-5" id="search" placeholder="Choisir une localisation">
                </div>
            </div>
        </div>
    </div>

    <!-- MODALE LOCALISATION -->
    <div class="modal fade" id="modalLocalisation" tabindex="-1" aria-labelledby="modalLocalisationLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
          <div class="modal-header border-0">
            <h5 class="modal-title accent-color mx-auto" id="modalLocalisationLabel">Choisir une localisation</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body">
            <input type="text" class="form-control mb-3" placeholder="Ajouter une localisation" id="localisation_search">
            <div>
                <h6 class="">Suggestions pour vous</h6>
                <p class="text-secondary mb-0" style="cursor:pointer;" id="all">
                    <i class="fas fa-map-marker-alt me-1"></i>Tout le quartier
                </p>
                <p class="text-secondary mb-0" style="cursor:pointer;" id="moi">
                    <i class="fas fa-crosshairs me-1"></i>Autour de moi
                </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- CONTENU PRODUITS -->
    <div class="container mb-5">
        <h3 class="accent-color mt-3 mb-0 fw-bold" style="letter-spacing:0.02em;">Produits les plus achetés</h3>
        <p class="text-secondary" style="font-size:1.1em;">1500 publications</p>
        <div class="row g-4 py-0">
            <!-- Produit 1 -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
                <div class="card-produit w-100 d-flex flex-column">
                    <div id="carouselProduit1" class="carousel slide produit-img-carousel" data-bs-ride="carousel">
                        <div class="carousel-inner rounded-top">
                            <div class="carousel-item active">
                                <img src="../Image/fourniture18.jpg" class="d-block w-100" alt="Cahiers 200pages">
                            </div>
                            <div class="carousel-item">
                                <img src="../Image/fourniture19.jpg" class="d-block w-100" alt="Cahiers 200pages">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center px-3 profil-ligne">
                        <img src="../Image/1.JPG" alt="vendeur" class="seller-img rounded-circle me-2" />
                        <span class="fw-bold">Laurent <i class="fas fa-star accent-color"></i> 5(1)</span>
                    </div>
                    <div class="px-3 pb-2" style="padding-top:8px !important;">
                        <h5 class="fw-bold mb-1 mt-1 accent-color">Cahiers 200 pages</h5>
                        <p class="mb-2 small">Lorem ipsum dolor sit amet consectetur adipisicing elit. Odit, iusto. Sunt quis eum necessitatibus quasi beatae eaque qui tempore nulla.</p>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold">2000 FCFA</span>
                            <i class="fas fa-heart icon-clickable fav-button" data-id="1" style="background:transparent; cursor:pointer;font-size:1.2em;"></i>
                        </div>
                        <div class="livraison text-center mb-2">Livraison possible</div>
                    </div>
                </div>
            </div>
            <!-- Produit 2 -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
                <div class="card-produit w-100 d-flex flex-column">
                    <div id="carouselProduit2" class="carousel slide produit-img-carousel" data-bs-ride="carousel">
                        <div class="carousel-inner rounded-top">
                            <div class="carousel-item active">
                                <img src="../Image/fourniture4.jpg" class="d-block w-100" alt="Crayons de couleur">
                            </div>
                            <div class="carousel-item">
                                <img src="../Image/fourniture11.jpg" class="d-block w-100" alt="Crayons de couleur">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center px-3 profil-ligne">
                        <img src="../Image/1.JPG" alt="vendeur" class="seller-img rounded-circle me-2" />
                        <span class="fw-bold">Laurent <i class="fas fa-star accent-color"></i> 5(1)</span>
                    </div>
                    <div class="px-3 pb-2" style="padding-top:8px !important;">
                        <h5 class="fw-bold mb-1 mt-1 accent-color">Crayons de couleur</h5>
                        <p class="mb-2 small">Consectetur adipisicing elit. Officiis, iste temporibus quibusdam id voluptate provident nihil dignissimos.</p>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold">4500 FCFA</span>
                            <i class="fas fa-heart icon-clickable fav-button" data-id="2" style="background:transparent; cursor:pointer;font-size:1.2em;"></i>
                        </div>
                        <div class="livraison text-center mb-2">Livraison possible</div>
                    </div>
                </div>
            </div>
            <!-- Produit 3 -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
                <div class="card-produit w-100 d-flex flex-column">
                    <div id="carouselProduit3" class="carousel slide produit-img-carousel" data-bs-ride="carousel">
                        <div class="carousel-inner rounded-top">
                            <div class="carousel-item active">
                                <img src="../Image/fourniture17.jpg" class="d-block w-100" alt="Boites académiques">
                            </div>
                            <div class="carousel-item">
                                <img src="../Image/fourniture20.jpg" class="d-block w-100" alt="Boites académiques">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center px-3 profil-ligne">
                        <img src="../Image/1.JPG" alt="vendeur" class="seller-img rounded-circle me-2" />
                        <span class="fw-bold">Laurent <i class="fas fa-star accent-color"></i> 5(1)</span>
                    </div>
                    <div class="px-3 pb-2" style="padding-top:8px !important;">
                        <h5 class="fw-bold mb-1 mt-1 accent-color">Boites académiques</h5>
                        <p class="mb-2 small">Dolorum hic ex excepturi, praesentium ab illo vel non rerum provident recusandae optio eveniet!</p>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold">4500 FCFA</span>
                            <i class="fas fa-heart icon-clickable fav-button" data-id="3" style="background:transparent; cursor:pointer;font-size:1.2em;"></i>
                        </div>
                        <div class="livraison text-center mb-2">Livraison possible</div>
                    </div>
                </div>
            </div>
            <!-- Produit 4 -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
                <div class="card-produit w-100 d-flex flex-column">
                    <div id="carouselProduit4" class="carousel slide produit-img-carousel" data-bs-ride="carousel">
                        <div class="carousel-inner rounded-top">
                            <div class="carousel-item active">
                                <img src="../Image/fourniture2.jpg" class="d-block w-100" alt="Accessoire scolaire">
                            </div>
                            <div class="carousel-item">
                                <img src="../Image/fourniture7.jpg" class="d-block w-100" alt="Accessoire scolaire">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center px-3 profil-ligne">
                        <img src="../Image/1.JPG" alt="vendeur" class="seller-img rounded-circle me-2" />
                        <span class="fw-bold">Laurent <i class="fas fa-star accent-color"></i> 5(1)</span>
                    </div>
                    <div class="px-3 pb-2" style="padding-top:8px !important;">
                        <h5 class="fw-bold mb-1 mt-1 accent-color">Accessoire scolaire</h5>
                        <p class="mb-2 small">Alias veritatis totam incidunt, assumenda et dicta nulla vitae aliquid officia asperiores.</p>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold">4500 FCFA</span>
                            <i class="fas fa-heart icon-clickable fav-button" data-id="4" style="background:transparent; cursor:pointer;font-size:1.2em;"></i>
                        </div>
                        <div class="livraison text-center mb-2">Livraison possible</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../vendor/chatAI/discuss.php'; ?>

    <!-- FOOTER identique à accueil.php (style leboncoin / Leboutiquier) -->
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
    <script src="../js/bootstrap.min.js"></script>
    <script>
        // Gestion ouverture modale localisation
        document.getElementById('search').addEventListener('focus', function(){
            var myModal = new bootstrap.Modal(document.getElementById('modalLocalisation'));
            myModal.show();
        });

        function remplirLocalisation(val) {
            document.getElementById('search').value = val;
            bootstrap.Modal.getInstance(document.getElementById('modalLocalisation')).hide();
        }
        document.getElementById('all').onclick = function(){ remplirLocalisation("Tout le quartier"); }
        document.getElementById('moi').onclick = function(){ remplirLocalisation("Autour de moi"); }

        // Favoris (LS)
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('.icon-clickable.fav-button').forEach(function(icon){
                var iconId = icon.dataset.id;
                if(localStorage.getItem('fav_'+iconId)==='true'){
                    icon.classList.add('active');
                }
                icon.addEventListener('click', function(){
                    icon.classList.toggle('active');
                    localStorage.setItem('fav_'+iconId, icon.classList.contains('active'));
                });
            });
        });
    </script>
</body>
</html>