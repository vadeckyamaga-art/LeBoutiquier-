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

    function verifierExistenceReelle($email) {
        //1. Extraction du domaine
        $domaine=substr(strrchr($email, "@"), 1);
        
        //2. Vérification des serveurs MX (Mail Exchange)
        if(!getmxrr($domaine, $mx_hosts)) {
            return false;// Le domaine n'existe pas ou ne gère pas de mails
        }

        $host=$mx_hosts[0];// On prend le serveur prioritaire
        $existence=false;

        //3. Connexion au serveur de mail de l'entreprise (Port 25)
        //Note : fsockopen peut être bloqué par certains FAI en local
        $connect=@fsockopen($host, 25, $errno, $errstr, 5);
        
        if($connect){
            if(preg_match("/^220/", fgets($connect))){
                fputs($connect, "HELO localhost\r\n");
                fgets($connect);
                fputs($connect, "MAIL FROM: <verificateur@leboutiquier.cm>\r\n");
                fgets($connect);
                fputs($connect, "RCPT TO: <$email>\r\n");
                $reponse=fgets($connect);
                
                //Si le serveur répond avec le code 250, l'adresse existe
                if(preg_match("/^250/", $reponse)) {
                    $existence=true;
                }
                
                fputs($connect, "QUIT\r\n");
                fclose($connect);
            }
        }
        else{
            //Si le port 25 est bloqué, on se rabat sur une validation DNS simple
            $existence=checkdnsrr($domaine, "MX");
        }
        return $existence;
    }

    if($_SERVER["REQUEST_METHOD"]=="POST") {
        $email_user=filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $nom=htmlspecialchars($_POST['nom']);
        $message_brut=htmlspecialchars($_POST['message']);

        //Vérification de l'adresse
        $est_valide=verifierExistenceReelle($email_user);

        //Préparation de la mention d'alerte
        if($est_valide){
            $mention_securite = "<div style='color: #2ecc71; padding: 10px; border: 1px solid #2ecc71; margin-bottom: 20px;'>
                                    ✅ L'adresse <strong>$email_user</strong> a été vérifiée comme EXISTANTE chez le fournisseur.
                                </div>";
        }
        else{
            $mention_securite = "<div style='color: #e74c3c; padding: 15px; border: 2px solid #e74c3c; background-color: #fdeaea; margin-bottom: 20px; font-weight: bold;'>
                                    ⚠️ ATTENTION : L'adresse <strong>$email_user</strong> n'a pas pu être validée (elle n'existe probablement pas). 
                                    Il est déconseillé de répondre à cet email.
                                </div>";
        }
        $mail=new PHPMailer(true);

        try{
            //Configuration Serveur SMTP
            $mail->isSMTP();
            $mail->Host='smtp.gmail.com';
            $mail->SMTPAuth=true;
            $mail->Username='leboutiquier2026.VADTECH@gmail.com'; 
            $mail->Password='cmylkqmpktwgmbam'; 
            $mail->SMTPSecure=PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port=587;

            // Paramètres de l'email
            $mail->setFrom('leboutiquier2026.VADTECH@gmail.com', 'LeBoutiquier');
            $mail->addAddress('vadeckyamaga@gmail.com');  
            $mail->addReplyTo($email_user);  

            $mail->isHTML(true);
            $mail->Subject="Nouveau message de $nom sur LeBoutiquier.com";
            
            // Corps du mail
            $mail->Body="
                <html>
                    <body style='font-family: Arial, sans-serif; line-height: 1.6;'>
                        $mention_securite
                        <h2>Détails de la requête :</h2>
                        <p><strong>Message de la part de :</strong> $nom</p>
                        <p><strong>Adresse email :</strong> $email_user</p>
                        <hr>
                        <p><strong>Message :</strong></p>
                        <p style='background: #f9f9f9; padding: 15px; border-left: 4px solid #EA580C;'>
                            " . nl2br($message_brut) . "
                        </p>
                    </body>
                </html>";

            $mail->send();
            $_SESSION['successMsg']='<i class="fa-solid fa-check-circle" style="color: green;"></i> Merci pour votre message! 
                                     </br>
                                     <p class="text-center"><a href="accueil.php">Retour à l\'accueil</a></p>';
            header("Location: Contact.php");
            exit();
        } 
        catch(Exception $e){
            $_SESSION['errorMsg']="<i class='fa-solid fa-triangle-exclamation' style='color: gold;'></i> Connexion internet perdue!<br> <a href='Contact.php' style='text-align: center;'>Veuillez réessayer</a>";
            header("Location: Contact.php");
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contactez-nous || LeBoutiquier</title>
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
                    <h2 class="text-center mb-4">Contactez-nous</h2>
                    <hr>
                    <p class="text-muted text-center">Si vous avez rencontrer un problème lors de l'utilisation de la plateforme, n'hésitez pas à nous contacter en <strong>remplissant le formulaire ci-dessous; </strong>
                        nous serrons ravis de repondre à vos questions...
                    </p>
                    <hr>
                    <h5 class="text-center mb-4">Veuillez remplir ce formulaire</h5>
                    <form action="" method="post" id="contactForm">
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
                            <label for="log-email" class="form-label">Votre nom</label>
                            <input type="text" value="<?php echo isset($_SESSION['nom']) ? $_SESSION['nom'] : ''; ?>" class="form-control form-control-lg" style="border: solid #222 2px; border-radius: 17px;" name="nom" id="name" placeholder="LeBoutiquier..." required>
                        </div>
                        <div class="mb-3">
                            <label for="log-email" class="form-label">Votre adresse email</label>
                            <input type="email" class="form-control form-control-lg" style="border: solid #222 2px; border-radius: 17px;" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>" name="email" id="email" placeholder="adresse@gmail.com..." required>
                        </div>
                        <div class="mb-3">
                            <label for="log-email" class="form-label">Votre message</label>
                            <textarea class="form-control form-control-lg" style="border: solid #222 2px; border-radius: 17px;" name="message" id="message" placeholder="description..." required></textarea>
                        </div>
                        <button class="leb-btn-orange w-100 mb-2" name="submit_new_pass" type="submit" id="btnEnvoyer"> 
                            <span id="btnText">Envoyer au support</span>
                            <div id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status"></div>
                        </button>
                    </form><hr>
                </div>
            </div>
        </div>
        <?php include '../vendor/chatAI/discuss.php'; ?>
    
        <script src="../js/bootstrap.min.js"></script>   
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

            document.querySelector('form').addEventListener('submit', function(e){
                const btn=document.getElementById('btnEnvoyer');
                const btnText=document.getElementById('btnText');
                const btnSpinner=document.getElementById('btnSpinner');

                btn.disabled=true;
                btnText.innerText = "Envoi en cours...";  
                btnSpinner.classList.remove('d-none');    
            });
        </script>
    </body>
</html>
