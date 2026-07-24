<?php

    session_start();
    include 'connexionBD.php';
    $errorMsg='';

    if(!isset($_SESSION['id'])){
        header('Location: connexion.php');
        exit();
    }
    if(isset($_POST['finaliser'])){
        $userID=$_SESSION['id'];
        $date=htmlspecialchars(trim($_POST['Dborn']?? ''));
        $sexe=htmlspecialchars(trim($_POST['sexe']?? ''));
        $compte=htmlspecialchars(trim($_POST['compte']?? ''));
        $numero=$_POST['numero']??'';
        $pass=$_POST['sing-pass']?? '';
        $cpass=$_POST['sing-cpass']?? '';
        $localisation=$_POST['localisation']??'';

        if(empty($date) ||empty($sexe) ||empty($compte) ||empty($pass) ||empty($cpass) ||empty($numero)){
            $_SESSION['errorMsg']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Veuillez remplir tous les champs!';
        }
        elseif($pass!==$cpass){
            $_SESSION['errorMsg']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Les deux mots de passe ne correspondent pas!';
        }

        $verifNumero=$conn->prepare("SELECT id FROM utilisateur WHERE tel=? AND id !=?");
        $verifNumero->execute([$numero, $userID]);
        if($verifNumero->fetch()){
            $_SESSION['errorMsg']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Ce numéro de téléphone est déja utilisé par un autre compte!';
        }
        if(empty($_SESSION['errorMsg'])){
            try{
                $conn->beginTransaction();
                $stmt=$conn->prepare("UPDATE utilisateur SET pass=?, tel=?, compte=?, localisation=?, dateNaiss=? WHERE id=?");
                $stmt->execute([$pass, $numero, $compte, $localisation, $date, $userID]);

                if($compte==='client'){
                    $idClt="CLT-".date("Y")."-".random_int(1000, 9999);
                    $sqlclt="INSERT INTO client (id_client, id) VALUES (?, ?)";
                    $stmtclt=$conn->prepare($sqlclt);
                    $stmtclt->execute([$idClt, $userID]);
                }
                elseif($compte==='boutiquier'){
                    $idBtq="BTQ-".date("Y")."-".random_int(1000, 9999);
                    $sqlbtq="INSERT INTO commerçant (id_commerçant, id) VALUES (?, ?)";
                    $stmtbtq=$conn->prepare($sqlbtq);
                    $stmtbtq->execute([$idBtq, $userID]);
                }
                $conn->commit();
                $_SESSION['compte']=$compte;
                header('Location: accueil.php');
            }
            catch(PDOException $e){
                $conn->rollBack();
                $_SESSION['errorMsg']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i> Erreur: ' .htmlspecialchars($e->getMessage());
            }
        }
    }

?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inscrivez-vous sur Leboutiquier</title>
        <link rel="stylesheet" href="../fontawesome/css/all.min.css">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../style/inscription.css">
        <!-- PWA -->
        <link rel="manifest" href="../vendor/manifest.json">
        <meta name="theme-color" content="#EA580C">
        <link rel="icon" href="../Image/favoicon.ico" sizes="48x48">
        <link rel="apple-touch-icon" href="../Image/logo.png">
    </head>
    <body>
        <script async defer crossorigin="anonymous" src="https:/connect.facebook.net/fr_FR/sdk.js"></script>
        <script>
            window.fbAsyncInit=function(){
                FB.init({appId:'807203322351495', cookie:true, xfbml:true, version:'v18.0'});
            };
            function loginFB(){
                if(typeof FB==='undefined'){
                    alert("Le SDK de Facebook n'est pas encore chargé, veuillez patienter!");
                }
                FB.login(function(reponse){
                    if(reponse.status==='connected'){
                        FB.api('/me', {fields:'last_name, first_name, email'}, function(data){
                            envoiServeur(data);
                        });
                    }
                },{scope: 'public_profile, email'});
            }
            function envoiServeur(data){
                fetch('API_Facebook.php', {
                    method: 'POST',
                    headers:{'Content-Type': 'application/json'},
                    body: JSON.stringify(data)}).then(res=>res.json()).then(res=>{
                        if(res.success){
                            if(res.redirect_to_complete){
                                window.location.href="completerProfil.php";
                            }
                            else{
                                window.location.href="accueil.php";
                            }
                        }
                    },{scope: 'public_profile, email'});
            }
        </script>

        <?php include 'splash.php'; ?>

        <video class="video-bg" id="video" autoplay muted loop playsinline>
            <source src="../Image/animation.mp4" type="video/mp4">
            Votre navigateur ne supporte pas les vidéos HTML5.
        </video>

        <nav class="leb-navbar p-3 mb-4 d-flex align-items-center position-relative">
            <button class="btn btn-lg me-3" type="button" onclick="history.back()">
                <i class="fas fa-arrow-left" style="color: var(--leb-orange);"></i>
            </button>
            <span class="leb-navbar-brand mx-auto d-block text-center" id="logo">Leboutiquier</span>
            <button id="togglebutton" class="btn btn-light pause-btn border ms-auto"><i class="fas fa-pause"></i></button>
        </nav>

        <div class="leb-form-wrapper container d-flex justify-content-center align-items-center">
            <div class="leb-form-container w-100">
                <h3 class="text-center mb-4">Terminer votre Inscription sur Leboutiquier</h3>
                <form action="" class="row" method="post">
                    <?php if (!empty($_SESSION['errorMsg'])): ?>
                            <div id="JsErrorMsg" class="alert alert-danger alert-dismissible text-center">
                                <?php 
                                    echo $_SESSION['errorMsg'];
                                    unset($_SESSION['errorMsg']);
                                ?>
                            </div>
                    <?php endif;  ?><br>
                    <?php if (!empty($_SESSION['successMsg'])): ?>
                            <div id="JsErrorMsg" class="alert alert-success alert-dismissible text-center">
                                <?php 
                                    echo $_SESSION['successMsg'];
                                    unset($_SESSION['successMsg']);
                                ?>
                            </div>
                    <?php endif;  ?><br>
                    <input type="hidden" name="form">
                    <input type="hidden" name="localisation" id="localisation"> 
                    <div class="col-md-6 mb-3">
                        <label for="Dborn" class="form-label">Date de naissance</label>
                        <input type="date" class="form-control form-control-lg" style="border: 2px solid #000; border-radius: 20px;" name="Dborn" id="Dborn">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sexe" class="form-label">Sexe</label>
                        <select name="sexe" id="sexe" class="form-select" style="border: 2px solid #000; border-radius: 20px;">
                            <option value="" disabled selected>Votre sexe</option>
                            <option value="masculin">Masculin</option>
                            <option value="feminin">Feminin</option>
                            <option value="autres">Autre</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="compte" class="form-label">Numéro de téléphone</label>
                        <input type="number" class="form-control form-control-lg" style="border: 2px solid #000; border-radius: 20px;" name="numero" id="numero">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="compte" class="form-label">Type de compte</label>
                        <select name="compte" id="compte" class="form-select" style="border: 2px solid #000; border-radius: 20px;">
                            <option value="" disabled selected>Votre type de compte</option>
                            <option value="client">Habitant</option>
                            <option value="boutiquier">Commerçant</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sing-pass" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control form-control-lg" style="border: 2px solid #000; border-radius: 20px;" name="sing-pass" id="sing-pass">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sing-cpass" class="form-label">Confirmer votre mot de passe</label>
                        <input type="password" class="form-control form-control-lg" style="border: 2px solid #000; border-radius: 20px;" name="sing-cpass" id="sing-cpass">
                    </div>
                    <div class="col-12 mb-3">
                        <input type="checkbox" class="form-check-input me-2" name="remember" id="remember">
                        <label for="remember" class="text-primary" style="font-size: 15px; font-style: italic;">Se souvenir de moi</label>
                    </div>
                    <div class="col-12 mb-3">
                        <button type="submit" id="btnInscrire" name="finaliser" class="btn w-100 text-white" style="background-color:var(--leb-orange); border-radius: 20px; border:none; height:50px;">S'inscrire</button>
                    </div>
                    <div class="col-12">
                        <p class="text-center" style="font-size: 15px; font-style: italic;">Vous avez un compte ?
                            <a href="connexion.php"style="color: var(--leb-orange); font-weight: 500;">Connexion</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
        <?php include '../vendor/chatAI/discuss.php'; ?>

        <script>
            const pause = document.getElementById('togglebutton');
            const video = document.getElementById('video');
            pause.addEventListener('click', function arret() {
                if (video.paused) {
                    video.play();
                    pause.innerHTML = '<i class="fas fa-pause"></i>';
                } else {
                    video.pause();
                    pause.innerHTML = '<i class="fas fa-play"></i>';
                }
            });
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
             
        </script>
        <script src="../js/bootstrap.min.js"></script>
    </body>
</html>