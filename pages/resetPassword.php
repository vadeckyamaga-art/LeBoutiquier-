<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    require '../vendor/PHPMailer/src/Exception.php'; 
    require '../vendor/PHPMailer/src/PHPMailer.php';
    require '../vendor/PHPMailer/src/SMTP.php';
    
    session_start();
    include 'connexionBD.php';
    $errorMsg='';
    $successMsg='';
    date_default_timezone_set('Africa/Douala');

    if (isset($_POST['send_link'])) {
        $email = htmlspecialchars($_POST['reset-email']);
        
        $stmt = $conn->prepare("SELECT id FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);

        if($stmt->rowCount() > 0) {
            $token = bin2hex(random_bytes(32)); // Génère un jeton unique
            $expires = date("Y-m-d H:i:s", time() + 300); // Expire dans 5 min

            $update = $conn->prepare("UPDATE utilisateur SET reset_token=?, token_expires=? WHERE email=?");
            $update->execute([$token, $expires, $email]);

            $link = "http://localhost/LeBoutiquier/pages/modifierPassword.php?token=".$token;

            $mail = new PHPMailer(true);

            try {
                // Configuration SMTP pour Gmail
                $mail->SMTPDebug = SMTP::DEBUG_OFF;
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'leboutiquier2026.VADTECH@gmail.com'; 
                $mail->Password   = 'cmylkqmpktwgmbam';  
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->CharSet    = 'UTF-8';

                // Ignore la recherche du fichier cacert.pem sur le disque dur, qui vérifie que Google est bien Google.
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );

                // Destinataire
                $mail->setFrom('leboutiquier2026.VADTECH@gmail.com', 'LeBoutiquier');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Réinitialisation de mot de passe';
                $mail->Body    = "
                    <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
                        <h2 style='color: #EA580C;'>LeBoutiquier</h2>
                        <p>Vous avez demandé à réinitialiser votre mot de passe.</p>
                        <p>Cliquez sur le bouton ci-dessous. Attention, ce lien expire dans <strong>5 minutes</strong>.</p>
                        <a href='$link' style='display:inline-block; background:#EA580C; color:white; padding:12px 25px; text-decoration:none; border-radius:5px;'>Réinitialiser mon mot de passe</a>
                        <p style='margin-top:20px; color:#888; font-size:12px;'>Si vous n'avez pas fait cette demande, ignorez cet e-mail.</p>
                    </div>";
                $mail->send();
                header("Location: confirmation.php");
                exit();
                
            }catch(Exception $e) {
                $_SESSION['errorMsg']="<i class='fa-solid fa-triangle-exclamation' style='color: gold;'></i>Erreur d'envoi d'email.<br> <a href='resetPassword.php' style='text-align: center;'>Cliquez ici pour réessayer</a>";
                header("Location: resetPassword.php");
                exit();
            }    
        }
        else{
            $_SESSION['errorMsg']='<i class="fa-solid fa-triangle-exclamation" style="color: gold;"></i>Cette addresse email n\'existe pas!';
            header("Location: resetPassword.php");
            exit();
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
                    <h3 class="text-center mb-4">Reinitialiser votre mot de passe</h3>
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
                                <div id="JsErrorMsg" class="alert alert-warning alert-dismissible text-center">
                                    <?php 
                                        echo $_SESSION['successMsg'];
                                        unset($_SESSION['successMsg']);
                                    ?>
                                </div>
                        <?php endif;  ?> 
                        <div class="mb-3">
                            <label for="log-email" class="form-label">E-mail*</label>
                            <input type="email" class="form-control form-control-lg" style="border: solid #222 2px; border-radius: 17px;" name="reset-email" id="log-email" >
                        </div>
                        <button class="leb-btn-orange w-100 mb-2" name="send_link" type="submit">Envoyer le lien</button>
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