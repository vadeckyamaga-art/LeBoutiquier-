/*==============================================================*/
/* Nom de SGBD :  MySQL 5.0                                     */
/* Date de création :  13/02/2026 17:46:30                      */
/*==============================================================*/


drop table if exists Article;

drop table if exists Avis;

drop table if exists Categorie;

drop table if exists Client;

drop table if exists Commande;

drop table if exists Commercant;

drop table if exists DetailCmd;

drop table if exists Favoris;

drop table if exists Panier;

drop table if exists Utilisateur;

/*==============================================================*/
/* Table : Article                                              */
/*==============================================================*/
create table Article
(
   idArticle            varchar(254) not null,
   id                   varchar(254) not null,
   idCommercant         varchar(254) not null,
   nomArticle           varchar(254),
   descArticle          varchar(254),
   prixArticle          int,
   dateAjout            datetime,
   photoArticle         varchar(254),
   photo2               varchar(254),
   photo3               varchar(254),
   statut               varchar(254),
   primary key (idArticle)
);

/*==============================================================*/
/* Table : Avis                                                 */
/*==============================================================*/
create table Avis
(
   idAvis               varchar(254) not null,
   Com_id               varchar(254) not null,
   idCommercant         varchar(254) not null,
   id                   varchar(254) not null,
   idClient             varchar(254) not null,
   note                 int,
   commentaire          varchar(254),
   dateAvis             datetime,
   primary key (idAvis)
);

/*==============================================================*/
/* Table : Categorie                                            */
/*==============================================================*/
create table Categorie
(
   idCat                varchar(254) not null,
   idArticle            varchar(254),
   nomCat               varchar(254),
   image_cat            varchar(254),
   primary key (idCat)
);

/*==============================================================*/
/* Table : Client                                               */
/*==============================================================*/
create table Client
(
   id                   varchar(254) not null,
   idClient             varchar(254) not null,
   primary key (id, idClient)
);

/*==============================================================*/
/* Table : Commande                                             */
/*==============================================================*/
create table Commande
(
   idCommande           varchar(254) not null,
   id                   varchar(254) not null,
   idClient             varchar(254) not null,
   Com_id               varchar(254) not null,
   idCommercant         varchar(254) not null,
   codeRetrait          varchar(254),
   dateCommande         datetime,
   typeAchat            varchar(254),
   fraisLivraison       int,
   motif                varchar(254),
   statut               varchar(254),
   montantCommande      numeric(8,0),
   numeroLivraison      varchar(254),
   adresseLivraison     varchar(254),
   primary key (idCommande)
);

/*==============================================================*/
/* Table : Commercant                                           */
/*==============================================================*/
create table Commercant
(
   id                   varchar(254) not null,
   idCommercant         varchar(254) not null,
   nomCommercant        varchar(254),
   nomBoutique          varchar(254),
   descriptionBoutique  varchar(254),
   statut               varchar(254),
   profilBoutique       varchar(254),
   primary key (id, idCommercant)
);

/*==============================================================*/
/* Table : DetailCmd                                            */
/*==============================================================*/
create table DetailCmd
(
   idCommande           varchar(254) not null,
   idArticle            varchar(254) not null,
   idDetail             varchar(254) not null,
   prixUnitaire         numeric(8,0),
   quantite             int,
   primary key (idCommande, idArticle, idDetail)
);

/*==============================================================*/
/* Table : Favoris                                              */
/*==============================================================*/
create table Favoris
(
   idFavoris            varchar(254) not null,
   id                   varchar(254) not null,
   idClient             varchar(254) not null,
   idArticle            varchar(254) not null,
   dateAjout            datetime,
   primary key (idFavoris)
);

/*==============================================================*/
/* Table : Panier                                               */
/*==============================================================*/
create table Panier
(
   idPanier             varchar(254) not null,
   id                   varchar(254) not null,
   idClient             varchar(254) not null,
   quantite             int,
   dateAjout            varchar(254),
   primary key (idPanier)
);

/*==============================================================*/
/* Table : Utilisateur                                          */
/*==============================================================*/
create table Utilisateur
(
   id                   varchar(254) not null,
   nom                  varchar(254),
   prenom               varchar(254),
   email                varchar(254),
   pass                 varchar(254),
   tel                  varchar(254),
   localisation         varchar(254),
   dateNaiss            datetime,
   dateInsc             datetime,
   compte               varchar(254),
   primary key (id)
);

alter table Article add constraint FK_poster foreign key (id, idCommercant)
      references Commercant (id, idCommercant) on delete restrict on update restrict;

alter table Avis add constraint FK_publier foreign key (id, idClient)
      references Client (id, idClient) on delete restrict on update restrict;

alter table Avis add constraint FK_recevoir foreign key (Com_id, idCommercant)
      references Commercant (id, idCommercant) on delete restrict on update restrict;

alter table Categorie add constraint FK_appartenir foreign key (idArticle)
      references Article (idArticle) on delete restrict on update restrict;

alter table Client add constraint FK_Generalisation_2 foreign key (id)
      references Utilisateur (id) on delete restrict on update restrict;

alter table Commande add constraint FK_passer foreign key (id, idClient)
      references Client (id, idClient) on delete restrict on update restrict;

alter table Commande add constraint FK_recevoir foreign key (Com_id, idCommercant)
      references Commercant (id, idCommercant) on delete restrict on update restrict;

alter table Commercant add constraint FK_Generalisation_1 foreign key (id)
      references Utilisateur (id) on delete restrict on update restrict;

alter table DetailCmd add constraint FK_contenir foreign key (idArticle)
      references Article (idArticle) on delete restrict on update restrict;

alter table DetailCmd add constraint FK_contenir foreign key (idCommande)
      references Commande (idCommande) on delete restrict on update restrict;

alter table Favoris add constraint FK_avoir foreign key (id, idClient)
      references Client (id, idClient) on delete restrict on update restrict;

alter table Favoris add constraint FK_contenir foreign key (idArticle)
      references Article (idArticle) on delete restrict on update restrict;

alter table Panier add constraint FK_avoir foreign key (id, idClient)
      references Client (id, idClient) on delete restrict on update restrict;

