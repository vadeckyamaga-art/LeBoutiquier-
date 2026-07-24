$limit=3;
$stmtAvisCom=$conn->prepare("SELECT avs.*, u.nom, u.prenom, u.id
                FROM avis avs, utilisateur u, client clt
                WHERE clt.id_client=avs.id_client AND clt.id=u.id AND avs.id_commerçant=?
                ORDER BY avs.date_avis DESC
                LIMIT ?");
$stmtAvisCom->bindValue(1, $id_commerçant, PDO::PARAM_STR);
$stmtAvisCom->bindValue(2, $limit, PDO::PARAM_INT);
$stmtAvisCom->execute();
$commentaires=$stmtAvisCom->fetchAll();

<div class="profile-card p-3 mb-4">
    <div class="seller-section-title"><i class="fas fa-star me-1"></i> Avis sur la boutique</div>
    <div id="avis-container"> 
        <?php foreach($commentaires as $com): 
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
        <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
        <button id="load-more-btn" class="btn btn-outline-primary rounded-pill px-4" data-offset="3" data-total="<?= $totalAvis ?>" data-idCom="<?= $id_commerçant ?>" data-mode="more">Afficher plus d'avis...</button>
    </div>
    <?php if(empty($com)): ?>
        <div class="text-center py-5">
            <div class="mb-4"><i class="fas fa-box-open text-muted" style="font-size: 5rem; opacity: 0.3;"></i></div>
            <h3 class="fw-bold text-secondary">Oups ! C'est encore un peu vide ici...</h3>
            <p class="text-muted mx-auto" style="max-width: 400px;">Aucun avis encore publié pour cette boutique; <strong>soyez peut-etre le premier!...</strong> à très bientot!</p>
            <a href="#" class="btn btn-primary rounded-pill px-4 py-2 mt-3 shadow"><i class="bi bi-arrow-left me-2"></i>Publier un avis</a>
        </div>
    <?php endif; ?>
</div>

 document.getElementById('load-more-btn').addEventListener('click', function(){
    let btn=this;
    let container=document.getElementById('avis-container');
    let mode=btn.getAttribute('data-mode');
    let total=parseInt(btn.getAttribute('data-total'));
    let idCom=btn.getAttribute('data-idCom');

    if(mode==="more"){
        let offset=parseInt(btn.getAttribute('data-offset'));

        fetch(`load_more_avis.php?id_commerçant=${idCom}&offset=${offset}`).then(respose=>respose.text()).then(data=>{
            if(data.trim()!=="EMPTY"){
                let newBlock=`<div class="avis-added-block">${data}</div>`;
                container.insertAdjacentHTML('beforeend', newBlock);

                let nextOffset=offset+3;
                btn.setAttribute('data-offset', nextOffset);

                if(nextOffset>=total){
                    btn.innerHTML="Voir moins...";
                    btn.setAttribute('data-mode', 'less');
                    btn.classList.replace('btn-outline-primary', 'btn-outline-secondary');
                }
            }
        });
    }
    else{
        let allGroups=container.querySelectorAll('.avis-added-block');
        allGroups.forEach(group=>group.remove());

        btn.innerHTML="Afficher plus d'avis";
        btn.setAttribute('data-mode', 'more');
        btn.setAttribute('data-offset', '3');
        btn.classList.replace('btn-outline-secondary', 'btn-outline-primary');

        container.scrollIntoView({behavior: 'smooth'});
    }
});

function modifierArt(){
    titre.innerHTML="Modifier un Article";
    btnPublier.innerHTML="Modifier";
    btnPublier.value="modifier";
    document.getElementById('nomArt').value=this.dataset.nom;
    document.getElementById('prixUArt').value=this.dataset.pu;
    document.getElementById('quantite').value=this.dataset.qnt;
    document.getElementById('id_article_invisible').value=this.dataset.id;
    document.getElementById('description').value=this.dataset.desc;
    document.getElementById('cat').value=this.dataset.cat;
    document.getElementById('photo').value=this.dataset.photo;
    document.getElementById('ancienne_photo').value=this.dataset.photo;
}

<button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" name="action_article" data-bs-target="#modalAjoutArticle" id="modifier"  
                                                        data-id="<?= htmlspecialchars($article['id_article']); ?>"
                                                        data-nom="<?= htmlspecialchars($article['nom_article']); ?>"
                                                        data-pu="<?= htmlspecialchars($article['prix_article']); ?>"
                                                        data-qnt="<?= htmlspecialchars($article['quantite_stock']); ?>"
                                                        data-desc="<?= htmlspecialchars($article['desc_article']); ?>"
                                                        data-cat="<?= htmlspecialchars($article['nom_cat']); ?>"
                                                        data-photo="'../ImagesBD/<?= htmlspecialchars($article['photo_article']); ?>'" onclick="modifierArt.call(this)"><i class="fas fa-pen"></i>
                                                    </button>



                            <div class="col-lg-8">
                                <div class="card p-4">
                                    <h5 class="fw-bold mb-3">Statistiques</h5>
                                     
                                </div>
                            </div>
    