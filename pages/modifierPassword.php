<?php

    session_start();
    include 'connexionBD.php';
    $errorMsg='';
    $successMsg='';
    $show_form = false;
    $conn->exec("SET time_zone = '+01:00'");
    date_default_timezone_set('Africa/Douala'); // Force l'heure du Cameroun pour le calcul
    $temps_restant_secondes = 0;
    $token = $_GET['token'];

    if (isset($_GET['token'])) {
        // On cherche l'utilisateur et on vérifie si le token n'est pas expiré
        $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE reset_token = ? AND token_expires > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if($user){
            $show_form = true;
            // CALCUL DU TEMPS RÉEL (PHP -> JS)
            $expiration = new DateTime($user['token_expires']);
            $maintenant = new DateTime();
            $diff = $expiration->getTimestamp() - $maintenant->getTimestamp();
            $temps_restant_secondes = ($diff > 0) ? $diff : 0;
        } else {
            $_SESSION['errorMsg']="<i class='fa-solid fa-triangle-exclamation' style='color: gold;'></i> Lien expiré ou invalide!</a>";
        }
    } 
    else{
        header("Location: connexion.php");
        exit();
    }

    if(isset($_POST['submit_new_pass'])){
        $pass = $_POST['pass'];
        $cpass = $_POST['cpass'];

        if(empty($pass)||empty($cpass)){
            $_SESSION['errorMsg']="<i class='fa-solid fa-triangle-exclamation' style='color: gold;'></i> Veuillez remplir tous les champs";
        }
        else{
            if($pass===$cpass){
                if(strlen($pass)>=6){
                
                    $update=$conn->prepare("UPDATE utilisateur SET pass=?, reset_token=NULL, token_expires=NULL WHERE reset_token=?");
                    $update->execute([$pass, $token]);
    
                    $_SESSION['successMsg']=$_SESSION['successMsg']="<i class='fa-solid fa-check-circle' style='color: green;'></i> Modification réussie, veuillez-vous connecter!";
                    $show_form = false;
                    
                    header('Location: connexion.php');
                    exit();
                }
                else{
                    $_SESSION['errorMsg']="<i class='fa-solid fa-triangle-exclamation' style='color: gold;'></i> Le nouveau mot de passe doit faire au moins 06 caractères";
                }
            } 
            else{
                $_SESSION['errorMsg']="<i class='fa-solid fa-triangle-exclamation' style='color: gold;'></i> Les deux mots de passe doivent etre identiques";
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Réinitialiser votre mot de passe || LeBoutiquier</title>
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

        <?php include 'splash.php'; ?>

        <video class="video-bg" id="video" autoplay muted loop playsinline>
            <source src="../Image/animation.mp4" type="video/mp4">
            Votre navigateur ne supporte pas les vidéos HTML5.
        </video>
    
        <div class="leb-login-wrapper">
            <nav class="leb-navbar d-flex align-items-center p-2 px-3 mb-0">
                <button class="btn btn-lg me-2" type="button" onclick="history.back()">
                    <i class="fas fa-arrow-left" style="color: var(--leb-orange);"></i>
                </button>
                <a href="../accueil.php" class="text-decoration-none leb-navbar-brand flex-grow-1 text-center m-0 p-0" style="font-size:1.7rem">Leboutiquier</a>
                <button id="togglebutton" class="btn btn-light pause-btn border ms-auto"><i class="fas fa-pause"></i></button>
            </nav>
    
            <div class="leb-login-center py-5">
                <div class="leb-login-form-container">
                    <h3 class="text-center mb-4">Entrez vos nouvelles informations</h3>
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
                            <label for="log-email" class="form-label">Nouveau Mot de passe*</label>
                            <input type="password" class="form-control form-control-lg" style="border: solid #222 2px; border-radius: 17px;" name="pass" id="log-email" required>
                        </div>
                        <div class="mb-3">
                            <label for="log-email" class="form-label">Confirmer mot de passe*</label>
                            <input type="password" class="form-control form-control-lg" style="border: solid #222 2px; border-radius: 17px;" name="cpass" id="log-email" required>
                        </div>
                        <button class="leb-btn-orange w-100 mb-2" name="submit_new_pass" type="submit">Modifier le mot de passe</button>
                    </form><br>
                    <div class="alert alert-info text-center">
                        Temps restant pour modifier : <strong id="timer">--:--</strong>
                    </div>
                </div>
            </div>
        </div>
    
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

            // On définit le temps en secondes (5 minutes = 300 secondes)
            let timeLeft=<?php echo $temps_restant_secondes; ?>; 
            const timerElement=document.getElementById('timer');

            const countdown=setInterval(function(){
                timeLeft--;

                // Calcul des minutes et secondes
                let minutes=Math.floor(timeLeft/60);
                let seconds=timeLeft%60;

                // Ajout d'un zéro devant si < 10 (ex: 09 au lieu de 9)
                seconds=seconds <10 ? '0'+ seconds : seconds;
                minutes = minutes < 10 ? '0' + minutes : minutes;

                // Affichage
                timerElement.innerHTML=minutes + ":" + seconds;

                // Si le temps est écoulé
                if(timeLeft<=0){
                    clearInterval(countdown);
                    alert("Le délai de 5 minutes est dépassé. Vous allez être redirigé.");
                    window.location.href="resetPassword.php"; // Redirection vers la demande
                }

                // Change la couleur en rouge quand il reste moins d'une minute
                if(timeLeft<60) {
                    timerElement.style.color ="red";
                }
            }, 1000);

        </script>
        <script src="../js/bootstrap.min.js"></script>   
    </body>
</html>