<?php 
    session_start();
    include 'connexionBD.php';   

    if (isset($_SESSION['id'])) {
        $id=$_SESSION['id'];
        $stmt=$conn->prepare("UPDATE utilisateur SET remember_token=NULL WHERE id=?");
        $stmt->execute([$id]);
    }

    $_SESSION=array();
    session_unset();
    session_destroy();

    if (isset($_COOKIE['remember_me'])) {
        setcookie('remember_me', '', time() - 3600, '/');
    }

    header('Location: connexion.php');
    exit();
?>