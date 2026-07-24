<?php

    include 'connexionBD.php';
    $id_commerçant=isset($_GET['id_commerçant'])?$_GET['id_commerçant']:'';
    $offset=(int)$_GET['offset'];

    $stmtAvisComSuite=$conn->prepare("SELECT avs.*, u.nom, u.prenom, u.id
                                      FROM avis avs, utilisateur u, client clt
                                      WHERE clt.id_client=avs.id_client AND clt.id=u.id AND avs.id_commerçant=?
                                      ORDER BY avs.date_avis DESC
                                      LIMIT 3 OFFSET ?");
    $stmtAvisComSuite->bindValue(1, $id_commerçant, PDO::PARAM_STR);
    $stmtAvisComSuite->bindValue(2, $offset, PDO::PARAM_INT);
    $stmtAvisComSuite->execute();
    $commentaires=$stmtAvisComSuite->fetchAll(PDO::FETCH_ASSOC);

    if(empty($commentaires)){
        echo"EMPTY";
    }
    else{
        foreach($commentaires as $com){
            $nom=trim($com['nom']);
            $initialeNom=mb_strtoupper(mb_substr($nom, 0, 1)).'.';
            $prenomMaj=ucfirst(mb_strtolower(htmlspecialchars($com['prenom'])));

            $noteActive=$com['note'];
            ?>
                <div class="mb-3 d-flex align-items-start gap-2 flex-wrap">
                    <img src="../Image/boisson.jpg" alt="user" class="review-user-avatar">
                    <div>
                        <div class="fw-bold"><?=$prenomMaj.' '.$initialeNom?><span class="star-orange ms-2">
                            <?php for($i=1; $i<=5; $i++):  
                                if($i<=$noteActive):
                            ?>
                                    <i class="fas fa-star"></i>
                                <?php else: ?>
                                    <i class="far fa-star"></i>
                                <?php endif;  ?>
                            <?php endfor;  ?></span>
                        </div>
                        <div><?= $com['commentaire'] ?></div>
                        <div class="date-light mt-1"><i class="far fa-clock"></i> <?= date('d/m/Y', strtotime($com['date_avis'])); ?></div>
                    </div>
                </div>
            <?php
        }
    }

?>