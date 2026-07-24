<?php
    session_start();
    include 'connexionBD.php';
    header('Content-Type: application/json');
 
    ini_set('display_errors', 0); 
 
    $usersID=$_SESSION['id'] ?? null;
    if(!$usersID) {
        echo json_encode(['status'=>'error', 'message'=>'Session expirée']);
        exit();
    }
 
    $queryclient=$conn->prepare("SELECT id_client FROM client WHERE id=?");
    $queryclient->execute([$usersID]);
    $clientRow=$queryclient->fetch();
    $clientID=$clientRow['id_client'] ?? null;
 
    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['type_achat'])){
        $id_commerçant=$_POST['id_boutique_modal']??'';
        $ids_bruts=$_POST['id_article_selectionne']??'';
        $articles_selectionnes=explode(',', $ids_bruts);
        $type_achat=$_POST['type_achat'];
        $adresse=($type_achat==='livraison')?htmlspecialchars($_POST['adresse_livraison']):'Retrait en boutique';
        $frais=($type_achat==='livraison')?1500:0;
        $telLivraison=htmlspecialchars(trim($_POST['telephone_livraison'] ?? ''));
 
        try{
            $conn->beginTransaction();
 
            // 1. Mise à jour du téléphone
            $updateTel = $conn->prepare("UPDATE utilisateur SET tel=? WHERE id=?");
            $updateTel->execute([$telLivraison, $usersID]);
 
            // 2. Récupérer les articles avec jointure pour avoir les noms directement
            $placeholders=implode(',', array_fill(0, count($articles_selectionnes), '?'));
            $sql="SELECT p.id_article, p.Quantite, a.prix_article, a.nom_article 
                  FROM panier p 
                  JOIN article a ON p.id_article = a.id_article 
                  WHERE p.id_client=? AND a.id_commerçant=? AND p.id_article IN ($placeholders)";
            
            $params=array_merge([$clientID, $id_commerçant], $articles_selectionnes);
            $stmt=$conn->prepare($sql);
            $stmt->execute($params);
            $items=$stmt->fetchAll();
 
            if(empty($items)) throw new Exception("Aucun article trouvé.");
 
            $total_brut=0;
            foreach($items as $item){ 
                $total_brut+=($item['prix_article']*$item['Quantite']); 
            }
            $total_final=$total_brut+$frais;
 
            // 3. Création de la commande
            $id_cmd="CMD-".date("Y")."-". random_int(1000, 9999);
            $code_retrait="CDR-".date("Y")."-".random_int(1000, 9999);
            
            $qCmd=$conn->prepare("INSERT INTO commande (id_commande, Code_retrait, type_achat, id_client, frais_livraison, statut, Montant_commande, numero_livraison, adresse_livraison, id_commerçant) VALUES (?, ?, ?, ?, ?, 'En attente', ?, ?, ?, ?)");
            $qCmd->execute([$id_cmd, $code_retrait, $type_achat, $clientID, $frais, $total_final, $telLivraison, $adresse, $id_commerçant]);
 
            // 4. Détails et suppression du panier
            foreach ($items as $item) {
                $id_det="DTL-".date("Y")."-".random_int(1000, 9999);
                $qDet=$conn->prepare("INSERT INTO details_commande (id_detail, quantite_cmd, prix_unitaire, id_article, id_commande) VALUES (?, ?, ?, ?, ?)");
                $qDet->execute([$id_det, $item['Quantite'], $item['prix_article'], $item['id_article'], $id_cmd]);
                
                $qDel=$conn->prepare("DELETE FROM panier WHERE id_client=? AND id_article=?");
                $qDel->execute([$clientID, $item['id_article']]);
            }
 
            // 5. Tel Vendeur
            $stmtV=$conn->prepare("SELECT u.tel FROM utilisateur u JOIN commerçant c ON u.id=c.id WHERE c.id_commerçant=?");
            $stmtV->execute([$id_commerçant]);
            $tel_v=preg_replace('/[^0-9]/', '', $stmtV->fetchColumn());
 
            $conn->commit();
 
            // 6. Construction du message WhatsApp
            $message="*NOUVELLE COMMANDE SUR LEBOUTIQUIER*\n\n";
            $message.="*Commande:* #" . $id_cmd . "\n";
            $message.="--------------------------------------\n";
            $message.="*Détails de la commande:*\n";
 
            foreach ($items as $ligne) {
                $message.="•".$ligne['nom_article']." (X".$ligne['Quantite'].")\n";
            }
 
            $message.="--------------------------------------\n";
            $message.="*TOTAL À PERCEVOIR:* ". number_format($total_final, 0, '.', ' '). " FCFA\n";
            $message.="*Lieu de livraison:* ". $adresse."\n\n";
            $message.="_Merci de traiter cette commande rapidement._";
 
            echo json_encode([
                'status'=>'success',
                'tel'=>$tel_v,
                'message'=>$message
            ]);
 
        }
        catch(Exception $e){
            if($conn->inTransaction()) $conn->rollBack();
            echo json_encode(['status'=>'error', 'message'=> $e->getMessage()]);
        }
        exit();
    }
?>