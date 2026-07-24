<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hors Ligne || LeBoutiquier</title>
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
        <a href="../accueil.php" class="text-decoration-none leb-navbar-brand flex-grow-1 text-center m-0 p-0" style="font-size:1.7rem">Leboutiquier</a>
        <button id="togglebutton" class="btn btn-light pause-btn border ms-auto"><i class="fas fa-pause"></i></button>
      </nav>
      <div class="leb-login-center py-5">
        <div class="leb-login-form-container">
          <h2 class="text-center mb-4">Vous etes Hors-ligne</h2>
          <hr>
          <p class="text-muted text-center"> La connexion internet semble indisponible. Certaines fonctionnalités peuvent etre limitées.
            Réessayez plus tard ou utiliser l'application installée si disponible
          </p>
          <hr>
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