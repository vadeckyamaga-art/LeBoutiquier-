<?php 

    session_start();
    include'connexionBD.php';
    
    $data=json_decode(file_get_contents('php://input'), true);

    if(!empty($data['email'])){
        $email=$data['email'];
        $nom=$data['last_name'];
        $prenom=$data['first_name'];

        $stmt=$conn->prepare("SELECT id, compte FROM utilisateur WHERE email=?");
        $stmt->execute([$email]);
        $user=$stmt->fetch();

        if($user){
            $_SESSION['id']=$user['id'];
            $_SESSION['compte']=$user['compte'];
            $incomplet=empty($user['tel']);
        }
        else{
            $newID="USER-".date('Y')."-".random_int(1000, 9999);
            $conn->prepare("INSERT INTO utilisateur (id, nom, prenom, email) VALUES (?, ?, ?, ?)" )->execute([$newID, $nom, $prenom, $email]);

            $_SESSION['id']=$newID;
            $incomplet=true;
        }
        echo json_encode(['success'=>true, 'redirect_to_complete'=>$incomplet]);
    }

?>