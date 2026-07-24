<?php
    session_start();
    include 'connexionBD.php';

    // --- ACTION : QUITTER LE MODE SIMULATION ---
    if (isset($_GET['action']) && $_GET['action'] === 'logoutAdmin') {
        if (isset($_SESSION['admin_id_backup'])) {
            $_SESSION['id'] = $_SESSION['admin_id_backup'];
            $_SESSION['compte'] = 'admin';
            unset($_SESSION['admin_mode'], $_SESSION['admin_id_backup'], $_SESSION['active_merchant_id']);
            header('Location: dashboard.php');
            exit();
        }
    }

    // --- ACTION : ACCÉDER AU COMPTE COMMERÇANT ---
    if (isset($_SESSION['id']) && $_SESSION['compte'] === 'admin' && isset($_GET['id'])) {
        $merchantId = $_GET['id']; // ex: COM001

        // On récupère les infos du commerçant ET son compte utilisateur lié
        $stmt = $conn->prepare("SELECT co.*, u.nom, u.prenom 
                                FROM commerçant co 
                                JOIN utilisateur u ON co.id = u.id 
                                WHERE co.id_commerçant = ?");
        $stmt->execute([$merchantId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user){
            // 1. On sauvegarde l'identité réelle de l'admin
            $_SESSION['admin_mode'] = true;
            $_SESSION['admin_id_backup'] = $_SESSION['id'];
            
            // 2. On adopte l'identité du commerçant pour tromper le système de sécurité
            $_SESSION['id'] = $user['id']; // On prend l'ID numérique du commerçant
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['compte'] = 'boutiquier'; 
            
            // 3. On stocke l'ID technique pour les redirections
            $_SESSION['active_merchant_id'] = $merchantId; 

            header('Location: Espacecommerçant.php?id=' . $merchantId);
            exit();
        } else {
            // Si le lien entre utilisateur et commerçant est brisé en base
            die("Erreur : Ce commerçant n'a pas de compte utilisateur lié (Vérifiez la colonne 'id' dans la table 'commerçant').");
        }
    }

    header('Location: connexion.php');  
    exit();
?>