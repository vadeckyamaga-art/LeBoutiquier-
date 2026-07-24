# Documentation EasyCar — Guide complet

## Table des matières
1. [Vue d'ensemble du projet](#1-vue-densemble)
2. [Architecture MVC](#2-architecture-mvc)
3. [Base de données](#3-base-de-données)
4. [Fichiers partagés (includes)](#4-fichiers-partagés)
5. [Modèles](#5-modèles)
6. [Contrôleurs](#6-contrôleurs)
7. [Vues — Pages publiques](#7-vues--pages-publiques)
8. [Module Admin](#8-module-admin)
9. [Flux complets pas à pas](#9-flux-complets-pas-à-pas)
10. [Sécurité](#10-sécurité)

---

## 1. Vue d'ensemble

EasyCar est une application web de réservation de billets de bus inter-villes au Cameroun.
Les villes desservies sont : **Douala, Bafia, Nkongsamba**.

**Technologies utilisées :**
- PHP 8+ (backend, logique métier)
- MySQL / MariaDB (base de données)
- PDO (accès base de données sécurisé)
- Tailwind CSS via CDN (styles)
- Leaflet + OpenStreetMap (carte interactive)
- OSRM (calcul de distance et tracé routier réel)
- Sessions PHP (authentification)

**Rôles utilisateurs :**
| Rôle | Accès |
|------|-------|
| `client` | Recherche, réservation, suivi, profil |
| `admin` | Tout + panneau d'administration |
| `chauffeur` | Compte uniquement (pas d'interface dédiée) |


---

## 2. Architecture MVC

Le projet suit le pattern **MVC (Modèle - Vue - Contrôleur)** :

```
src/
├── modele/          ← Classes PHP qui parlent à la base de données
├── controleur/      ← Scripts PHP qui traitent les formulaires POST
├── includes/        ← Fragments réutilisables (header, footer, alertes, config)
├── admin/           ← Module administration (vues + contrôleur admin)
│   ├── includes/    ← Header et sidebar admin
│   └── AdminCtrl.php
├── assets/          ← CSS/JS statiques
├── database/        ← Fichier SQL de création de la base
└── *.php            ← Pages vues (accueil, connexion, réservation, etc.)
```

**Principe de fonctionnement :**
1. L'utilisateur visite une **vue** (ex: `index.php`)
2. Il soumet un formulaire vers un **contrôleur** (ex: `controleur/ReservationCtrl.php`)
3. Le contrôleur instancie un **modèle** (ex: `new Voyage()`) et appelle ses méthodes
4. Le modèle exécute la requête SQL via PDO et retourne les données
5. Le contrôleur redirige vers une vue avec un message de succès/erreur en session

---

## 3. Base de données

Fichier : `src/database/easycar.sql`

### Tables

#### `Utilisateur`
Stocke tous les comptes (clients, admins, chauffeurs).

| Colonne | Type | Description |
|---------|------|-------------|
| `idUtilisateur` | INT AUTO_INCREMENT | Clé primaire |
| `nom` | VARCHAR(100) | Nom de famille |
| `prenom` | VARCHAR(100) | Prénom |
| `sexe` | VARCHAR(10) | masculin / feminin |
| `email` | VARCHAR(150) UNIQUE | Email de connexion |
| `telephone` | VARCHAR(20) | Numéro de téléphone |
| `motDePasse` | VARCHAR(255) | Hash bcrypt |
| `adresse` | VARCHAR(255) | Adresse postale |
| `num_cni` | VARCHAR(50) | Numéro CNI |
| `resetPass` | VARCHAR(100) | Token de réinitialisation MDP |
| `resetTime` | DATETIME | Expiration du token (1h) |
| `dateInscription` | DATETIME | Date de création du compte |
| `role` | ENUM | `client`, `admin`, `chauffeur` |
| `statut` | ENUM | `actif`, `archive` |


#### `Bus`
Représente les véhicules de la flotte.

| Colonne | Type | Description |
|---------|------|-------------|
| `idBus` | INT AUTO_INCREMENT | Clé primaire |
| `immatriculation` | VARCHAR(50) UNIQUE | Plaque d'immatriculation |
| `capacite` | INT | Nombre de places |
| `type` | ENUM | `Standard`, `VIP`, `Executive` |
| `statut` | ENUM | `disponible`, `en_route`, `maintenance` |
| `latitude` | DECIMAL(10,7) | Position GPS actuelle |
| `longitude` | DECIMAL(10,7) | Position GPS actuelle |
| `vitesse` | INT | Vitesse en km/h |

#### `Chauffeur`
Extension de `Utilisateur` pour les chauffeurs.

| Colonne | Type | Description |
|---------|------|-------------|
| `idChauffeur` | INT AUTO_INCREMENT | Clé primaire |
| `idUtilisateur` | INT FK | Lien vers `Utilisateur` |
| `numeroPermis` | VARCHAR(50) | Numéro de permis de conduire |
| `experience` | VARCHAR(50) | Débutant / Intermédiaire / Expérimenté |
| `photo` | VARCHAR(255) | Chemin vers la photo |
| `statut` | ENUM | `actif`, `inactif` |

#### `Voyage`
Un trajet planifié entre deux villes.

| Colonne | Type | Description |
|---------|------|-------------|
| `idVoyage` | INT AUTO_INCREMENT | Clé primaire |
| `idBus` | INT FK | Bus assigné |
| `idChauffeur` | INT FK | Chauffeur assigné |
| `villeDepart` | VARCHAR(100) | Ville de départ |
| `villeArrive` | VARCHAR(100) | Ville d'arrivée |
| `heureDepart` | TIME | Heure de départ |
| `dateVoyage` | DATE | Date du voyage |
| `duree` | VARCHAR(20) | Durée estimée (ex: "4h") |
| `prixVoyage` | DECIMAL(10,2) | Prix par passager en FCFA |
| `placesTotal` | INT | Capacité totale |
| `placesDisponibles` | INT | Places restantes (décrémentées à chaque réservation) |
| `statutVoyage` | ENUM | `planifie`, `en_cours`, `termine`, `annule` |

#### `Reservation`
Une réservation d'un utilisateur sur un voyage.

| Colonne | Type | Description |
|---------|------|-------------|
| `idReservation` | INT AUTO_INCREMENT | Clé primaire |
| `idUtilisateur` | INT FK | Client qui réserve |
| `idVoyage` | INT FK | Voyage réservé |
| `codeReservation` | VARCHAR(20) UNIQUE | Code lisible (ex: EC-A3F2B1-42) |
| `nomPassager` | VARCHAR(200) | Nom du passager (peut différer du compte) |
| `telephonePassager` | VARCHAR(20) | Téléphone du passager |
| `cniPassager` | VARCHAR(50) | CNI du passager |
| `siege` | VARCHAR(10) | Siège attribué (ex: 1A, 2B) |
| `moyenPaiement` | ENUM | `orange_money`, `mobile_money` |
| `numeroPaiement` | VARCHAR(20) | Numéro utilisé pour payer |
| `montantTotal` | DECIMAL(10,2) | Montant payé |
| `statutPaiement` | ENUM | `en_attente`, `confirme`, `rembourse` |
| `statutReservation` | ENUM | `active`, `annulee`, `terminee` |
| `dateReservation` | DATETIME | Date de création |
| `delaiAnnulation` | DATETIME | Limite d'annulation (+24h) |

#### `Ticket`
Ticket généré après confirmation de paiement.

| Colonne | Type | Description |
|---------|------|-------------|
| `idTicket` | INT AUTO_INCREMENT | Clé primaire |
| `idReservation` | INT FK | Réservation associée |
| `codeTicket` | VARCHAR(30) UNIQUE | Code ticket (ex: TK-A1B2C3D4) |
| `dateEmission` | DATETIME | Date de génération |
| `statut` | ENUM | `valide`, `utilise`, `annule` |

### Relations entre tables
```
Utilisateur ──< Chauffeur
Utilisateur ──< Reservation
Bus ──< Voyage
Chauffeur ──< Voyage
Voyage ──< Reservation
Reservation ──< Ticket
```


---

## 4. Fichiers partagés

### `src/includes/config.php`
Définit la constante globale des villes desservies.
```php
define('VILLES', ['Douala', 'Bafia', 'Nkongsamba']);
```
Utilisée dans les formulaires (boucle `foreach`) et dans les contrôleurs pour valider que les villes soumises sont autorisées (`in_array($ville, VILLES)`).

### `src/controleur/connexionBD.php`
Crée la connexion PDO à MySQL et la stocke dans `$conn`.
```php
$conn = new PDO("mysql:host=localhost;dbname=easycar;charset=utf8mb4", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
```
- `ERRMODE_EXCEPTION` : toute erreur SQL lance une exception PHP (plus facile à déboguer)
- `FETCH_ASSOC` : les résultats sont des tableaux associatifs (`$row['nom']` au lieu de `$row[0]`)
- Inclus dans **chaque page** qui a besoin de la base de données

### `src/includes/header.php`
Barre de navigation commune à toutes les pages publiques.
- Lit `$_SESSION['user']` pour savoir si l'utilisateur est connecté
- Affiche les liens "Mes Réservations" et "Suivi Bus" seulement si connecté
- Affiche un lien "Administration" si `$user['role'] === 'admin'`
- Le menu déroulant contient : Profil, Mes Réservations, Déconnexion
- La déconnexion soumet un formulaire POST vers `controleur/ProfilCtrl.php` avec `action=deconnexion`

### `src/includes/footer.php`
Pied de page statique avec liens et formulaire d'abonnement (non fonctionnel, décoratif).

### `src/includes/alerts.php`
Affiche les messages flash stockés en session.
- `$_SESSION['success']` → bandeau vert en haut à droite
- `$_SESSION['error']` → bandeau rouge en haut à droite
- Les messages disparaissent automatiquement après 5 secondes (JavaScript `setTimeout`)
- Après affichage, la variable session est détruite (`unset`)

---

## 5. Modèles

Les modèles sont des classes PHP dans `src/modele/`. Chaque méthode reçoit `$conn` (la connexion PDO) en paramètre et exécute des requêtes SQL préparées.

### `Utilisateur.php`

**`authentifier($conn, $email, $motDePasse)`**
- Cherche l'utilisateur par email avec statut `actif`
- Vérifie le mot de passe avec `password_verify()` (bcrypt)
- Retourne le tableau utilisateur ou `false`

**`inscrire($conn, $data)`**
- Vérifie que l'email n'existe pas déjà
- Hache le mot de passe avec `password_hash($password, PASSWORD_BCRYPT)`
- Insère le nouvel utilisateur avec rôle `client`
- Retourne `true` ou `false`

**`modifier($conn, $id, $data)`**
- Met à jour nom, prénom, téléphone, adresse, num_cni
- L'email n'est pas modifiable (sécurité)

**`changerMotDePasse($conn, $id, $nouveau)`**
- Hache le nouveau mot de passe et met à jour la base

**`getById($conn, $id)`**
- Retourne toutes les colonnes d'un utilisateur par son ID

**`sauvegarderTokenReset($conn, $email, $token)`**
- Stocke un token aléatoire + expiration (+1h) dans `resetPass` et `resetTime`

**`validerTokenReset($conn, $token)`**
- Vérifie que le token existe ET que `resetTime > NOW()` (non expiré)


### `Voyage.php`

**`rechercher($conn, $depart, $arrivee, $date, $type)`**
- Cherche les voyages avec `placesDisponibles > 0` et `statutVoyage = 'planifie'`
- Fait des JOIN sur Bus, Chauffeur, Utilisateur pour récupérer toutes les infos en une requête
- Le paramètre `$type` (Standard/VIP/Executive) est optionnel

**`getById($conn, $id)`**
- Retourne un voyage avec toutes ses infos (bus, chauffeur, coordonnées GPS)

**`getAll($conn)`**
- Retourne tous les voyages triés par date décroissante (pour l'admin)

**`creer($conn, $data)`**
- Insère un nouveau voyage. `placesDisponibles` est initialisé à la même valeur que `placesTotal`

**`mettreAJourStatut($conn, $id, $statut)`**
- Change le statut d'un voyage (planifie → en_cours → termine / annule)

**`decrementerPlaces($conn, $id)`**
- Réduit `placesDisponibles` de 1. La condition `AND placesDisponibles > 0` évite les valeurs négatives

**`incrementerPlaces($conn, $id)`**
- Augmente `placesDisponibles` de 1 (lors d'une annulation)

**`annulerExpires($conn)`**
- Requête UPDATE qui passe tous les voyages `planifie` dont `CONCAT(dateVoyage, ' ', heureDepart) < NOW()` en statut `annule`
- Appelée au chargement de `index.php`, `reservation.php`, `admin/voyages.php`, `admin/dashboard.php`

### `reservation.php` (classe `Reservation`)

**`creer($conn, $data)`**
- Génère un code unique `EC-XXXXXX-XX` via `md5(uniqid())`
- Appelle `attribuerSiege()` pour trouver le premier siège libre
- Insère la réservation avec délai d'annulation à +24h
- Retourne `['idReservation' => ..., 'code' => ..., 'siege' => ...]` ou `false`

**`attribuerSiege($conn, $idVoyage)` (privée)**
- Récupère tous les sièges déjà pris pour ce voyage
- Parcourt les combinaisons 1A, 1B, 1C... jusqu'à trouver un siège libre

**`confirmerPaiement($conn, $id)`**
- Passe `statutPaiement` à `confirme` (simule la validation du paiement mobile)

**`annuler($conn, $id, $idUtilisateur)`**
- Vérifie que la réservation appartient bien à l'utilisateur et est `active`
- Passe le statut à `annulee` et remet la place disponible dans le voyage

**`getByUtilisateur($conn, $idUtilisateur)`**
- Retourne toutes les réservations d'un client avec les infos du voyage, bus et ticket

**`getById($conn, $id)`**
- Retourne une réservation complète avec toutes les jointures (pour le suivi et le ticket)

**`getAll($conn)`**
- Retourne toutes les réservations (pour l'admin)

### `Ticket.php`

**`generer($conn, $idReservation)`**
- Vérifie si un ticket existe déjà (idempotent)
- Génère un code `TK-XXXXXXXX` et l'insère
- Retourne le tableau ticket

**`getByReservation($conn, $idReservation)`**
- Retourne le ticket avec toutes les infos du voyage, bus, chauffeur (pour affichage)

**`getByCode($conn, $code)`**
- Recherche un ticket par son code (pour validation externe)

### `bus.php` (classe `Bus`)

**`getAll($conn)`** — Tous les bus triés par date d'enregistrement

**`creer($conn, $data)`** — Insère un nouveau bus

**`modifier($conn, $id, $data)`** — Met à jour les infos d'un bus

**`mettreAJourPosition($conn, $id, $lat, $lng, $vitesse)`** — Met à jour la position GPS (non utilisé en production, prévu pour intégration GPS)

**`getDisponibles($conn)`** — Retourne uniquement les bus avec statut `disponible`

### `chauffeur.php` (classe `Chauffeur`)

**`getAll($conn)`** — Tous les chauffeurs avec leurs infos utilisateur (JOIN)

**`getById($conn, $id)`** — Un chauffeur par son ID

**`getDisponibles($conn)`** — Chauffeurs avec statut `actif`


---

## 6. Contrôleurs

Les contrôleurs sont des scripts PHP dans `src/controleur/`. Ils ne produisent pas de HTML — ils traitent les données POST, appellent les modèles, puis redirigent avec `header("Location: ...")`.

### `Authentification.php`
Traite le formulaire de connexion (`connexion.php`).

Flux :
1. Vérifie que la méthode est POST
2. Valide que email et mot de passe ne sont pas vides
3. Appelle `$utilisateur->authentifier($conn, $email, $motDePasse)`
4. Si succès : stocke l'utilisateur dans `$_SESSION['user']`, redirige vers `admin/dashboard.php` (admin) ou `index.php` (client)
5. Si échec : stocke l'erreur en session, redirige vers `connexion.php`

### `Inscription.php`
Traite le formulaire d'inscription (`inscription.php`).

Flux :
1. Vérifie que tous les champs sont remplis (nom, prénom, email, sexe, tel, cni, password)
2. Valide le format email avec `filter_var($email, FILTER_VALIDATE_EMAIL)`
3. Appelle `$utilisateur->inscrire($conn, $data)`
4. Si succès : redirige vers `connexion.php` avec message de succès
5. Si échec (email déjà utilisé) : redirige vers `inscription.php` avec erreur

### `ReservationCtrl.php`
Gère toutes les actions liées aux réservations. Protégé : redirige vers connexion si non connecté.

**`action = rechercher`**
- Valide les villes avec `in_array($ville, VILLES)`
- Vérifie que départ ≠ arrivée
- Stocke les critères dans `$_SESSION['recherche']`
- Redirige vers `reservation.php`

**`action = reserver`**
- Vérifie que le voyage existe et a des places
- Crée la réservation via `$reservationModel->creer()`
- Décrémente les places du voyage
- Simule la confirmation du paiement
- Génère le ticket
- Redirige vers `ticket.php?id=...`

**`action = annuler`**
- Appelle `$reservationModel->annuler()` (vérifie l'appartenance)
- Redirige vers `mes-reservations.php`

### `ProfilCtrl.php`
Gère les actions du profil. Protégé : redirige si non connecté.

**`action = modifier`** — Met à jour les infos personnelles et rafraîchit la session

**`action = changer_mdp`** — Vérifie l'ancien mot de passe, valide la confirmation, met à jour

**`action = deconnexion`** — Appelle `session_destroy()` et redirige vers `connexion.php`

### `MotDePasseOublie.php`
Gère la réinitialisation du mot de passe.

**`action = envoyer`**
- Génère un token aléatoire avec `bin2hex(random_bytes(32))`
- Sauvegarde le token en base (valide 1h)
- En mode démo : affiche le lien directement dans la page (en production, il faudrait envoyer un email)

**`action = reinitialiser`**
- Valide le token (non expiré)
- Vérifie que les deux mots de passe correspondent et font au moins 6 caractères
- Met à jour le mot de passe et invalide le token (`resetPass = NULL`)


---

## 7. Vues — Pages publiques

### `connexion.php`
Page de connexion. Accessible uniquement si non connecté.

Points clés :
```php
// Empêche le retour navigateur après connexion
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");

// Redirige si déjà connecté
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
```
- Formulaire POST vers `controleur/Authentification.php`
- Bouton "œil" pour afficher/masquer le mot de passe (JavaScript `togglePwd()`)
- Design deux colonnes : panneau bleu à gauche (décoratif), formulaire à droite

### `inscription.php`
Page d'inscription. Même protection anti-retour que `connexion.php`.
- Formulaire POST vers `controleur/Inscription.php`
- Champs : nom, prénom, email, téléphone, CNI, sexe, mot de passe
- Validation HTML5 (`required`, `minlength="6"`, `type="email"`)

### `index.php`
Page d'accueil avec formulaire de recherche de voyage.

Points clés :
```php
$voyageModel->annulerExpires($conn); // Annule les voyages expirés à chaque chargement
$villes = VILLES; // ['Douala', 'Bafia', 'Nkongsamba']
```
- Formulaire de recherche POST vers `controleur/ReservationCtrl.php` avec `action=rechercher`
- Les selects départ/arrivée sont synchronisés : si on choisit "Douala" au départ, "Douala" disparaît des options d'arrivée (JavaScript `syncOptions()`)
- Section avantages et CTA (call-to-action)

**JavaScript `syncOptions()` :**
```javascript
function syncOptions() {
    const val = depart.value;
    Array.from(arrivee.options).forEach(opt => {
        opt.hidden = opt.value !== '' && opt.value === val;
    });
    if (arrivee.value === val) arrivee.value = '';
}
depart.addEventListener('change', syncOptions);
```

### `reservation.php`
Page de réservation en 3 étapes (stepper).

**Étape 1 — Choisir le bus**
- Si `$_SESSION['recherche']` existe, affiche les voyages correspondants
- Si aucun voyage trouvé, affiche un message et un lien retour
- Clic sur une carte voyage → `selectionnerVoyage()` met à jour le récapitulatif sidebar et active le bouton "Continuer"

**Étape 2 — Informations passager**
- Champs pré-remplis avec les données du compte connecté
- Validation JS avant de passer à l'étape 3

**Étape 3 — Paiement**
- Choix entre Orange Money et MoMo (images depuis `public/`)
- Saisie du numéro de paiement
- `soumettre()` copie toutes les valeurs dans un formulaire caché et le soumet

**Stepper visuel :**
- Les indicateurs passent de gris → bleu (actif) → vert (complété)
- Les lignes de connexion entre étapes passent de gris à bleu

**Formulaire caché :**
```html
<form id="form-reservation" action="controleur/ReservationCtrl.php" method="POST">
    <input type="hidden" name="action" value="reserver">
    <!-- Tous les champs remplis par JS avant soumission -->
</form>
```

### `mes-reservations.php`
Liste toutes les réservations de l'utilisateur connecté.
- Filtres par statut (Tous / Actives / Annulées / Terminées) via JavaScript `filtrer()`
- Chaque carte affiche : trajet, date, heure, siège, code réservation, prix
- Boutons contextuels : "Ticket" (si ticket généré), "Annuler" (si active et future), "Suivi" (si active et future)

### `ticket.php`
Affiche le ticket de voyage après réservation.
- Reçoit `?id=idReservation` en GET
- Appelle `$ticketModel->getByReservation($conn, $id)`
- Design ticket avec séparateur en pointillés (style billet physique)
- Bouton "Imprimer" utilise `window.print()` avec CSS `@media print` qui masque les éléments non imprimables

### `profil.php`
Page de profil avec deux onglets.

**Onglet "Informations"** — Formulaire de modification des données personnelles
**Onglet "Sécurité"** — Formulaire de changement de mot de passe

- Sidebar avec avatar (initiales), stats (total voyages, actives, date d'inscription)
- Navigation par onglets en JavaScript `showTab()`
- L'email est affiché mais non modifiable (`disabled`)

### `suivi.php`
Page de suivi en temps réel du bus.

**Logique PHP :**
1. Cherche la réservation active de l'utilisateur (ou celle passée en `?id=`)
2. Appelle l'API OSRM pour obtenir distance réelle, durée et géométrie de la route :
```php
$osrmGeoUrl = "https://router.project-osrm.org/route/v1/driving/{lng1},{lat1};{lng2},{lat2}?overview=full&geometries=geojson";
```
3. Si OSRM répond : utilise les vraies valeurs. Sinon : utilise les valeurs hardcodées (fallback)
4. Calcule la position du bus par interpolation linéaire selon l'heure :
```php
$ratio = min(1, $elapsed / $dureeSecondes); // 0 = au départ, 1 = à l'arrivée
$latBus = $latDepart + ($latArrivee - $latDepart) * $ratio;
```

**Carte Leaflet :**
- Tuiles OpenStreetMap (gratuit, sans clé API)
- 3 marqueurs : départ (🚉 bleu), arrivée (📍 vert), bus (🚌 bleu)
- Tracé de la route : géométrie OSRM si disponible, sinon ligne droite en pointillés
- `map.fitBounds()` ajuste le zoom pour voir tout le trajet

**Stats affichées :** distance en km, durée en heures, heure d'arrivée estimée (calculée en JS)

**Rafraîchissement :** `setInterval(() => location.reload(), 30000)` — recharge toutes les 30 secondes

### `mot-de-passe-oublie.php` et `reinitialiser-mdp.php`
Flux de réinitialisation du mot de passe.
- `mot-de-passe-oublie.php` : saisie de l'email → génère un token → affiche le lien (démo)
- `reinitialiser-mdp.php` : reçoit `?token=...`, valide le token, affiche le formulaire de nouveau mot de passe


---

## 8. Module Admin

Accessible uniquement aux utilisateurs avec `role = 'admin'`. Chaque page vérifie :
```php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../connexion.php"); exit();
}
```

### `admin/includes/admin_header.php`
Barre de navigation admin avec :
- Logo + lien vers le dashboard
- Lien "Site" pour revenir au site public
- Avatar de l'admin connecté
- Bouton de déconnexion

### `admin/includes/admin_sidebar.php`
Menu latéral avec 5 liens :
- Tableau de bord (`dashboard.php`)
- Voyages (`voyages.php`)
- Réservations (`reservations.php`)
- Bus (`bus.php`)
- Utilisateurs (`utilisateurs.php`)

Le lien actif est mis en surbrillance bleue via `basename($_SERVER['PHP_SELF'])`.

### `admin/dashboard.php`
Vue d'ensemble avec 4 cartes de statistiques :
- Nombre de clients inscrits
- Voyages planifiés
- Réservations actives
- Revenus totaux (somme des paiements confirmés)

Deux tableaux en bas :
- 5 dernières réservations
- 5 prochains voyages

### `admin/voyages.php`
Gestion des voyages.
- Tableau de tous les voyages avec statut coloré
- Boutons "Démarrer" (planifie → en_cours) et "Annuler" (planifie → annule)
- Bouton "Terminer" (en_cours → termine)
- Modal de création de voyage avec 3 lignes de 3 champs :
  - Ligne 1 : Départ / Arrivée / Date
  - Ligne 2 : Heure / Bus / Chauffeur
  - Ligne 3 : Prix / Durée / Nb places
- Synchronisation départ/arrivée (même logique que l'accueil)

### `admin/reservations.php`
Gestion des réservations.
- Filtres par statut via liens GET (`?statut=active`, etc.)
- Tableau avec : code, client, trajet, date, montant, statut paiement, statut réservation
- Bouton "Annuler" pour les réservations actives

### `admin/bus.php`
Gestion de la flotte.
- Affichage en grille de cartes (3 colonnes)
- Chaque carte a un select pour changer le statut (disponible / en_route / maintenance)
- Le changement de statut soumet automatiquement le formulaire (`onchange="this.form.submit()"`)
- Modal d'ajout de bus : immatriculation, capacité, type

### `admin/utilisateurs.php`
Gestion des utilisateurs.
- Filtres par rôle via liens GET (`?role=client`, etc.)
- Tableau avec avatar (initiale), nom, email, téléphone, rôle, statut, date d'inscription
- Bouton "Archiver" / "Réactiver" (sauf pour soi-même)
- Modal d'ajout avec sélection de rôle (Admin ou Chauffeur)
  - Si Chauffeur : champs supplémentaires (permis, expérience) affichés/masqués via `toggleChauffeurFields()`

### `admin/AdminCtrl.php`
Contrôleur unique pour toutes les actions admin. Protégé par vérification de rôle.

Actions disponibles :
| Action | Description |
|--------|-------------|
| `creer_voyage` | Valide les villes, insère le voyage |
| `voyage_statut` | Change le statut d'un voyage |
| `creer_bus` | Insère un nouveau bus |
| `bus_statut` | Change le statut d'un bus |
| `annuler_reservation` | Annule une réservation et remet la place |
| `creer_utilisateur` | Crée un admin ou chauffeur (avec entrée dans `Chauffeur` si besoin) |
| `toggle_statut_user` | Archive ou réactive un utilisateur (pas soi-même) |


---

## 9. Flux complets pas à pas

### Flux 1 : Inscription d'un nouveau client

```
1. Utilisateur visite inscription.php
2. Remplit le formulaire et clique "Créer mon compte"
3. POST → controleur/Inscription.php
4. Inscription.php vérifie les champs (tous remplis, email valide)
5. Appelle $utilisateur->inscrire($conn, $data)
   → Vérifie que l'email n'existe pas (SELECT)
   → Hash le mot de passe (password_hash)
   → INSERT INTO Utilisateur
6. $_SESSION['success'] = "Inscription réussie !"
7. Redirige vers connexion.php
```

### Flux 2 : Connexion

```
1. Utilisateur visite connexion.php
   → Si déjà connecté ($_SESSION['user'] existe) : redirige vers index.php
2. Remplit email + mot de passe
3. POST → controleur/Authentification.php
4. Appelle $utilisateur->authentifier($conn, $email, $motDePasse)
   → SELECT WHERE email = ? AND statut = 'actif'
   → password_verify($motDePasse, $hash)
5. Si OK : $_SESSION['user'] = $user
   → Admin : redirige vers admin/dashboard.php
   → Client : redirige vers index.php
6. Si KO : $_SESSION['error'] = "Email ou mot de passe incorrect"
   → Redirige vers connexion.php
```

### Flux 3 : Recherche et réservation d'un voyage

```
1. index.php : l'utilisateur choisit départ, arrivée, date, classe
2. POST → controleur/ReservationCtrl.php (action=rechercher)
   → Valide les villes (in_array VILLES)
   → $_SESSION['recherche'] = ['depart', 'arrivee', 'date', 'type']
   → Redirige vers reservation.php

3. reservation.php charge les voyages :
   $voyages = $voyageModel->rechercher($conn, ...)
   → JOIN Bus + Chauffeur + Utilisateur
   → WHERE placesDisponibles > 0 AND statutVoyage = 'planifie'

4. Étape 1 : l'utilisateur clique sur un voyage
   → selectionnerVoyage() met à jour le récapitulatif
   → Active le bouton "Continuer"

5. Étape 2 : infos passager (pré-remplies depuis la session)

6. Étape 3 : choix du moyen de paiement + numéro
   → soumettre() copie tout dans le formulaire caché et le soumet

7. POST → controleur/ReservationCtrl.php (action=reserver)
   → Vérifie que le voyage a encore des places
   → $reservationModel->creer() :
      - Génère codeReservation (EC-XXXXXX-XX)
      - Attribue un siège (premier libre)
      - INSERT INTO Reservation
   → $voyageModel->decrementerPlaces()
   → $reservationModel->confirmerPaiement() (UPDATE statutPaiement='confirme')
   → $ticketModel->generer() (INSERT INTO Ticket avec code TK-XXXXXXXX)
   → Redirige vers ticket.php?id=idReservation

8. ticket.php affiche le billet avec tous les détails
```

### Flux 4 : Suivi du bus

```
1. suivi.php?id=idReservation (ou sans paramètre → prend la première réservation active)
2. Récupère la réservation avec getById() (JOIN complet)
3. Appel OSRM :
   GET https://router.project-osrm.org/route/v1/driving/{lng1},{lat1};{lng2},{lat2}?overview=full&geometries=geojson
   → Récupère distance (km), durée (h), coordonnées de la route
   → Si OSRM indisponible : utilise les valeurs hardcodées
4. Calcule la position du bus :
   ratio = (maintenant - heureDepart) / dureeSecondes
   latBus = latDepart + (latArrivee - latDepart) * ratio
5. Passe les données à JavaScript
6. Leaflet initialise la carte, place les 3 marqueurs, trace la route
7. Calcule l'ETA : heureDepart + dureeH * 60 minutes
8. Recharge la page toutes les 30 secondes
```

### Flux 5 : Annulation d'une réservation

```
1. mes-reservations.php : bouton "Annuler" (visible si active + future)
2. POST → controleur/ReservationCtrl.php (action=annuler)
3. $reservationModel->annuler($conn, $id, $idUtilisateur)
   → SELECT WHERE idReservation=? AND idUtilisateur=? AND statutReservation='active'
   → Si trouvé : UPDATE Reservation SET statutReservation='annulee'
   → UPDATE Voyage SET placesDisponibles = placesDisponibles + 1
4. $_SESSION['success'] = "Réservation annulée..."
5. Redirige vers mes-reservations.php
```

### Flux 6 : Création d'un chauffeur par l'admin

```
1. admin/utilisateurs.php : bouton "Ajouter un utilisateur"
2. Modal s'ouvre, rôle "Chauffeur" sélectionné par défaut
   → toggleChauffeurFields(true) affiche les champs permis/expérience
3. POST → admin/AdminCtrl.php (action=creer_utilisateur)
   → Valide les champs obligatoires
   → Vérifie unicité de l'email
   → INSERT INTO Utilisateur (role='chauffeur')
   → Si role='chauffeur' : INSERT INTO Chauffeur (idUtilisateur, numeroPermis, experience)
4. $_SESSION['success'] = "Chauffeur créé avec succès."
5. Redirige vers utilisateurs.php
```

---

## 10. Sécurité

### Mots de passe
- Tous les mots de passe sont hachés avec `password_hash($password, PASSWORD_BCRYPT)`
- La vérification utilise `password_verify()` (résistant aux attaques timing)
- Jamais de mot de passe en clair en base de données

### Requêtes SQL
- Toutes les requêtes utilisent des **requêtes préparées PDO** avec des paramètres nommés (`:param`)
- Aucune concaténation directe de variables dans le SQL → protection contre les injections SQL
```php
// CORRECT (préparé)
$stmt = $conn->prepare("SELECT * FROM Utilisateur WHERE email = :email");
$stmt->execute([':email' => $email]);

// DANGEREUX (jamais fait dans ce projet)
$stmt = $conn->query("SELECT * FROM Utilisateur WHERE email = '$email'");
```

### Affichage HTML
- Toutes les données affichées passent par `htmlspecialchars()` → protection XSS
```php
echo htmlspecialchars($user['nom']); // Échappe <, >, ", &
```

### Contrôle d'accès
- Pages protégées : vérifient `isset($_SESSION['user'])` en début de fichier
- Pages admin : vérifient en plus `$_SESSION['user']['role'] === 'admin'`
- Un utilisateur ne peut annuler que ses propres réservations (vérification `idUtilisateur` en SQL)
- Un admin ne peut pas s'archiver lui-même

### Anti-retour navigateur
- `connexion.php` et `inscription.php` envoient des headers `Cache-Control: no-store` pour forcer le rechargement et déclencher la vérification de session

### Validation des données
- Les villes sont validées côté serveur avec `in_array($ville, VILLES)` (pas seulement côté client)
- Les statuts sont validés avec des listes blanches (`$allowed = ['planifie', 'en_cours', ...]`)
- Les IDs sont castés en entier `(int)$_POST['id']` pour éviter les injections

### Token de réinitialisation
- Généré avec `bin2hex(random_bytes(32))` (cryptographiquement sûr)
- Expire après 1 heure (`resetTime > NOW()`)
- Invalidé après utilisation (`resetPass = NULL`)

---

*Documentation générée pour EasyCar — Projet PHP MVC*
