<?php
    // 1. On lance la session en tout premier  
    if (session_status()===PHP_SESSION_NONE){
        session_start();
    }

    $host="localhost:3306";
    $db="leboutiquier";
    $password="";
    $users="root";

    try{
        $pdo_options[PDO::ATTR_ERRMODE]=PDO::ERRMODE_EXCEPTION;
        $conn=new Pdo("mysql:host=$host; dbname=$db", $users, $password);
        $conn->exec("SET sql_mode=''");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 2. Une fois la connexion établie, on inclut la vérification du cookie
        // On vérifie si l'utilisateur n'est pas déjà connecté en session
        if (!isset($_SESSION['id']) && isset($_COOKIE['remember_me'])) {
            $data=explode(':', $_COOKIE['remember_me']);
            if (count($data)===2) {
                $user_id=$data[0];
                $token=$data[1];

                // On cherche l'utilisateur avec un jeton précis
                $stmt_auth=$conn->prepare("SELECT * FROM utilisateur WHERE id=? AND remember_token=?");
                $stmt_auth->execute([$user_id, $token]);
                $user=$stmt_auth->fetch(PDO::FETCH_ASSOC);

                if($user){
                    // Reconnexion automatique : on remplit la session
                    $_SESSION['id']=$user['id'];
                    $_SESSION['compte']=$user['compte'];
                    $_SESSION['nom']=$user['nom'];
                    $_SESSION['prenom']=$user['prenom'];
                    $_SESSION['email']=$user['email'];
                } 
                else{
                    // Jeton invalide ou expiré : on détruit le cookie
                    setcookie('remember_me', '', time() - 3600, '/');
                }
            }
        }

    } 
    catch(PDOException $e) {
        echo "erreur:" . $e->getMessage();
        die();
    }
?>