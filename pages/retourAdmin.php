<?php
    session_start();
    include 'connexionBD.php';

    if(isset($_SESSION['admin_id_backup'])) {
        $_SESSION['id'] = $_SESSION['admin_id_backup'];
        $_SESSION['compte'] = 'admin';

        unset($_SESSION['admin_id_backup']);
        unset($_SESSION['admin_mode']);
        unset($_SESSION['active_merchant_id']);

        header('Location: dashboard.php');
        exit();
    } 
    else{
        header('Location: connexion.php');
        exit();
    }
?>