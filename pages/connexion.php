<?php
    session_start();
    include 'connexionBD.php';
    $errorMsg='';
    $successMsg='';
    if($_SERVER['REQUEST_METHOD']=='POST'){
        try{
            $email=htmlspecialchars(trim($_POST['log-email']))??'';
            $pass=$_POST['log-pass'];

            if(empty($email)||empty($pass)){
                $_SESSION['errorMsg']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Adresse email ou mot de passe manquant!';
                header('Location: connexion.php');
                exit();
            }
            elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $_SESSION['errorMsg']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Adresse email invalide!';
                header('Location: connexion.php');
                exit();
            }
            else{
                $sql="SELECT * FROM utilisateur WHERE email=? AND pass=?";
                $stmt=$conn->prepare($sql);
                $stmt->execute([$email, $pass]);
                $user=$stmt->fetch(PDO::FETCH_ASSOC);
                if($user){
                    $_SESSION['id']=$user['id'];
                    $_SESSION['compte']=$user['compte'];
                    $_SESSION['nom']=$user['nom'];
                    $_SESSION['prenom']=$user['prenom'];
                    $_SESSION['email']=$user['email'];

                    // --- DEBUT AJOUT : SE SOUVENIR DE MOI ---
                    if(isset($_POST['remember'])){
                        // Générer un jeton aléatoire
                        $token=bin2hex(random_bytes(32));
                        
                        $updateSql="UPDATE utilisateur SET remember_token=? WHERE id=?";
                        $updateStmt=$conn->prepare($updateSql);
                        $updateStmt->execute([$token, $user['id']]);

                        // Créer le cookie (30 jours) : "ID:TOKEN"
                        $cookieValue=$user['id'] . ':' . $token;
                        setcookie('remember_me', $cookieValue, [
                            'expires'=>time()+(30*24*3600),
                            'path'=>'/',
                            'httponly'=>true,
                            'secure'=>false,  
                            'samesite'=>'Lax'
                        ]);
                    }
                    // --- FIN AJOUT ---

                    if(isset($_GET['redirect']) && $_GET['redirect']==='compte'){
                        if ($user['compte']==='admin') {
                            header('Location: dashboard.php');
                        }
                        elseif ($user['compte']==='livreur') {
                            header('Location: livraison.php');
                        }
                        elseif ($user['compte']==='client') {
                            header('Location: espaceclient.php');
                        }
                        elseif ($user['compte']==='boutiquier') {
                            header('Location: Espacecommerçant.php');
                        }
                        else{
                            header('Location: accueil.php');
                        }
                    }
                    elseif(isset($_GET['redirect'])){
                        $destination=$_GET['redirect'];
                        header("Location: ". $destination);
                    }
                    else{
                        header('Location: accueil.php');
                    }
                    exit();
                }
                else{
                    $_SESSION['errorMsg']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Adresse email ou mot de passe incorrect!';  
                    header('Location: connexion.php');
                    exit();
                }
            }
        }
        catch(PDOException $e){
            $_SESSION['errorMsg']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Erreur: ' .htmlspecialchars($e->getMessage());
            header('Location: connexion.php');
            exit();
        }
    }
?>
 
 
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Connecter vous sur notre site || LeBoutiquier</title>
        <link rel="stylesheet" href="../fontawesome/css/all.min.css">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../style/connexion.css">
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
                    alert("Aucune connexion internet, veuillez réessayer plus tard!");
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
                            window.location.href="accueil.php";
                        }
                    }, {scope: 'public_profile, email'});
            }

            function onGoogleSignIn(response){
                const base64Url=response.credential.split('.')[1];
                const base64=base64Url.replace(/-/g, '+').replace(/_/g, '/');
                const data=JSON.parse(window.atob(base64));

                fetch('API_Google.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        email: data.email,
                        nom: data.family_name,
                        prenom: data.given_name
                    })
                }).then(res=>res.json()).then(res=>{
                    if(res.success){
                        if(res.redirect_to_complete){
                            window.location.href="accueil.php";
                        }
                        else{
                            window.location.href="completerProfil.php";
                        }
                    }
                    else{
                        alert("Erreur lors de l'authentification avec Google.");
                    }
                }).catch(err=>console.error("Erreur Fecth :", err));
            }

        </script>

        <?php include 'splash.php'; ?>
        
        <video class="video-bg" id="video" autoplay muted loop playsinline>
            <source src="../Image/animation.mp4" type="video/mp4">
            Votre navigateur ne supporte pas les vidéos HTML5.
        </video>
    
        <div class="leb-login-wrapper">
            <nav class="leb-navbar d-flex align-items-center p-2 px-3 mb-0">
                <button class="btn btn-lg me-3" type="button" onclick="window.location.href='accueil.php';" title="Accueil">
                    <i class="fas fa-home" style="color: var(--leb-orange);"></i>
                </button>
                <a class="text-decoration-none leb-navbar-brand flex-grow-1 text-center m-0 p-0" style="font-size:1.7rem">Leboutiquier</a>
                <button id="togglebutton" class="btn btn-light pause-btn border ms-auto"><i class="fas fa-pause"></i></button>
            </nav>
    
            <div class="leb-login-center py-5">
                <div class="leb-login-form-container">
                    <h3 class="text-center mb-4">Connectez-vous ou créez votre compte Leboutiquier</h3>
                    <form action="" method="post">
                        <?php if (!empty($_SESSION['errorMsg'])): ?>
                                <div id="JsErrorMsg" class="alert alert-danger alert-dismissible text-center">
                                    <?php 
                                        echo $_SESSION['errorMsg'];
                                        unset($_SESSION['errorMsg']);
                                    ?>
                                </div>
                        <?php endif;  ?> 
                        <?php if (!empty($_SESSION['successMsg'])): ?>
                                <div id="JsErrorMsg" class="alert alert-success alert-dismissible text-center">
                                    <?php 
                                        echo $_SESSION['successMsg'];
                                        unset($_SESSION['successMsg']);
                                    ?>
                                </div>
                        <?php endif;  ?> 
                        <div class="mb-3">
                            <label for="log-email" class="form-label">E-mail*</label>
                            <input type="email" class="form-control form-control-lg" style="border: solid #222 2px; border-radius: 17px;" name="log-email" id="log-email">
                        </div>
                        <div class="mb-3">
                            <label for="log-pass" class="form-label">Mot de passe*</label>
                            <input type="password" class="form-control form-control-lg" style="border: solid #222 2px; border-radius: 17px;" name="log-pass" id="log-pass">
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" style="border: 1px solid #222; cursor: pointer;">
                                    <label class="form-check-label text-primary" for="remember" style="font-size: 12px; font-style: italic;">Se souvenir de moi</label>
                                </div>
                                <a href="resetPassword.php" style="font-style: italic; font-size: 10px;">mot de passe oublié?</a>
                            </div>
                        </div>
                         
                        <button class="leb-btn-orange w-100 mb-2" type="submit">Connexion</button>
    
                        <div class="leb-separator">
                            <hr>
                            <span>Ou</span>
                            <hr>
                        </div>
                        <button type="button" class="leb-btn-outline w-100 mb-2" onclick="loginFB()" style="height: 40px;">
                            <i class="fa-brands fa-facebook" style="font-size: 25px; color:#1877F2;"></i>Continuer avec Facebook
                        </button>
                        <script src="https://accounts.google.com/gsi/client" async defer></script>
                        <div id="g_id_onload"
                            data-client_id="414178828420-2uduc9ak7okilh66ic5823krtjuo39oi.apps.googleusercontent.com"
                            data-callback="onGoogleSignIn"
                            data-context="signin">
                        </div>
                        <div class="col-12 mb-4 d-flex justify-content-center">
                            <div class="g_id_signin"
                                data-type="standard"
                                data-shape="pill"
                                data-theme="outline"
                                data-text="continue_with"
                                data-size="large"
                                data-width="340"></div>
                        </div>
                        <p class="text-center mt-2 mb-0" style="font-size: 15px; font-style: italic;">
                            Pas de compte ? <a href="inscription.php" style="color: var(--leb-orange); font-weight: 500;">S'inscrire</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <?php include '../vendor/chatAI/discuss.php'; ?>
    
        <script>
            pause=document.getElementById('togglebutton');
            video=document.getElementById('video');
            pause.addEventListener('click', arret);
            function arret(){
                if(video.paused){
                    video.play();
                    pause.innerHTML='<i class="fas fa-pause"></i>';
                }
                else{
                    video.pause();
                    pause.innerHTML='<i class="fas fa-play"></i>';
                }
            }
        </script>
        <script src="../js/bootstrap.min.js"></script>
    </body>
 </html>