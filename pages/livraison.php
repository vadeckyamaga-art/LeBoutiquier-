<?php

    session_start();
    include 'connexionBD.php';

?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
        <title>Dashboard Livreur - Gestion des Livraisons</title>
        <link rel="stylesheet" href="../fontawesome/css/all.min.css">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../style/livraison.css">
        <!-- PWA -->
        <link rel="manifest" href="../vendor/manifest.json">
        <meta name="theme-color" content="#EA580C">
        <link rel="icon" href="../Image/favoicon.ico" sizes="48x48">
        <link rel="apple-touch-icon" href="../Image/logo.png">
    </head>
    <body>
        <?php include 'splash.php'; ?>
        <!-- === NAVBAR FIXE EN HAUT === -->
        <nav class="fixed-top-navbar">
            <button class="btn-back" onclick="goBack();" title="Retour">
                <i class="fas fa-arrow-left"></i>
            </button>
            <div class="navbar-main-title" id="navbarTitle">Dashboard Livreur</div>
            <button class="notif-bell-top" id="notifBellTop" type="button" aria-expanded="false">
                <i class="fas fa-bell"></i>
                <span class="notif-dot" id="notif-count-top">3</span>
            </button>
            <ul class="dropdown-menu-notif-top" id="notifDropdownMenuTop">
                <li class="notif-title">Notifications</li>
                <li class="notif-item unread">
                    <i class="fas fa-package text-success me-1"></i>
                    <span><strong>Nouvelle livraison</strong> - Commande #CMD-001</span>
                    <small class="text-muted">il y a 5 min</small>
                </li>
                <li class="notif-item unread">
                    <i class="fas fa-check-circle text-info me-1"></i>
                    <span><strong>Livraison confirmée</strong> - Admin a accepté</span>
                    <small class="text-muted">il y a 15 min</small>
                </li>
                <li class="notif-item unread">
                    <i class="fas fa-message text-warning me-1"></i>
                    <span><strong>Nouveau message</strong> de l'admin</span>
                    <small class="text-muted">il y a 30 min</small>
                </li>
            </ul>
        </nav>

        <!-- === BARRE DE NAVIGATION BASSE (MOBILE) === -->
        <nav class="leb-bottom-navbar d-lg-none" id="mobileNavbar">
            <a class="nav-link active" href="#" data-tab="dashboard">
                <i class="fas fa-chart-line"></i>
                <span class="nav-label">Dashboard</span>
            </a>
            <a class="nav-link" href="#" data-tab="deliveries">
                <i class="fas fa-box"></i>
                <span class="nav-label">Livraisons</span>
            </a>
            <a class="nav-link" href="#" data-tab="messages">
                <i class="fas fa-envelope"></i>
                <span class="nav-label">Messages</span>
            </a>
            <a class="nav-link" href="#" data-tab="profile">
                <i class="fas fa-user"></i>
                <span class="nav-label">Profil</span>
            </a>
        </nav>

        <!-- === CONTENU MOBILE === -->
        <div id="mobile-content-panels" class="main-content d-lg-none">
            <!-- Panel Dashboard (Mobile) -->
            <div id="mobile-dashboard-panel" class="mobile-panel active">
                <div class="section-title">
                    <i class="fas fa-chart-line"></i>
                    Aperçu
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="card p-2 text-center">
                            <div class="h5 text-orange fw-bold">8</div>
                            <small class="text-muted">Livraisons</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card p-2 text-center">
                            <div class="h5 text-orange fw-bold">6</div>
                            <small class="text-muted">En attente</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card p-2 text-center">
                            <div class="h5 text-orange fw-bold">2</div>
                            <small class="text-muted">En cours</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card p-2 text-center">
                            <div class="h5 text-orange fw-bold">4.8</div>
                            <small class="text-muted">Note</small>
                        </div>
                    </div>
                </div>

                <div class="section-title">
                    <i class="fas fa-box"></i>
                    Récentes
                </div>
                <div class="delivery-card" onclick="openDeliveryDetails('LIV-001', 'CMD-001', 'Boutique Dupont', 'Jean Dupont', '123 Rue de Paris', '45 000 FCFA', 'En attente', 'Livraison à domicile', '15/02/2026')">
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                            <div class="delivery-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <div style="min-width: 0;">
                                <h6 class="mb-0 fw-bold" style="font-size: 0.9rem;">#LIV-001</h6>
                                <small class="text-muted" style="display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Boutique Dupont</small>
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="fw-bold mb-1" style="font-size: 0.85rem;">45K FCFA</div>
                            <span class="status-badge status-pending">En attente</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Livraisons (Mobile) -->
            <div id="mobile-deliveries-panel" class="mobile-panel">
                <div class="section-title">
                    <i class="fas fa-box"></i>
                    Livraisons
                </div>
                <div class="delivery-card" onclick="openDeliveryDetails('LIV-001', 'CMD-001', 'Boutique Dupont', 'Jean Dupont', '123 Rue de Paris', '45 000 FCFA', 'En attente', 'Livraison à domicile', '15/02/2026')">
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                            <div class="delivery-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <div style="min-width: 0;">
                                <h6 class="mb-0 fw-bold" style="font-size: 0.9rem;">#LIV-001</h6>
                                <small class="text-muted" style="display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Boutique Dupont • 15/02</small>
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="fw-bold mb-1" style="font-size: 0.85rem;">45K FCFA</div>
                            <span class="status-badge status-pending">En attente</span>
                        </div>
                    </div>
                </div>

                <div class="delivery-card" onclick="openDeliveryDetails('LIV-002', 'CMD-002', 'Boutique Martin', 'Marie Martin', '456 Avenue Principale', '62 000 FCFA', 'Acceptée', 'Livraison à domicile', '14/02/2026')">
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                            <div class="delivery-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <div style="min-width: 0;">
                                <h6 class="mb-0 fw-bold" style="font-size: 0.9rem;">#LIV-002</h6>
                                <small class="text-muted" style="display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Boutique Martin • 14/02</small>
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="fw-bold mb-1" style="font-size: 0.85rem;">62K FCFA</div>
                            <span class="status-badge status-accepted">Acceptée</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Messages (Mobile) -->
            <div id="mobile-messages-panel" class="mobile-panel">
                <div class="section-title">
                    <i class="fas fa-envelope"></i>
                    Messages
                </div>
                <div class="message-item unread" onclick="openMessage('MSG-001', 'Admin', 'Votre demande pour la livraison #LIV-001 a été acceptée', 'il y a 15 min')">
                    <div class="message-from">Admin</div>
                    <div class="message-preview">Votre demande pour la livraison #LIV-001 a été acceptée</div>
                    <div class="message-time">il y a 15 min</div>
                </div>

                <div class="message-item" onclick="openMessage('MSG-002', 'Admin', 'Nouvelle livraison disponible: #LIV-002', 'il y a 30 min')">
                    <div class="message-from">Admin</div>
                    <div class="message-preview">Nouvelle livraison disponible: #LIV-002</div>
                    <div class="message-time">il y a 30 min</div>
                </div>
            </div>

            <!-- Panel Profil (Mobile) -->
            <div id="mobile-profile-panel" class="mobile-panel">
                <div class="profile-header">
                    <div class="profile-avatar">JD</div>
                    <div class="profile-name">Jean Dupont</div>
                    <div class="profile-status">Livreur Actif</div>
                    <div class="profile-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                        <span class="small text-muted ms-1">4.8/5.0</span>
                    </div>
                </div>

                <div class="card p-3 mb-3">
                    <h6 class="fw-bold mb-2" style="font-size: 0.95rem;">Informations</h6>
                    <div class="mb-2">
                        <small class="text-muted">Email</small>
                        <div class="fw-bold" style="font-size: 0.9rem;">jean@example.com</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Téléphone</small>
                        <div class="fw-bold" style="font-size: 0.9rem;">+33 6 12 34 56 78</div>
                    </div>
                    <div>
                        <small class="text-muted">Statut</small>
                        <div class="fw-bold"><span class="badge badge-success">Actif</span></div>
                    </div>
                </div>

                <div class="card p-3">
                    <h6 class="fw-bold mb-2" style="font-size: 0.95rem;">Statistiques</h6>
                    <div class="mb-2">
                        <small class="text-muted">Livraisons effectuées</small>
                        <div class="fw-bold" style="font-size: 0.9rem;">45</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">En attente</small>
                        <div class="fw-bold" style="font-size: 0.9rem;">6</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Taux de réussite</small>
                        <div class="fw-bold" style="font-size: 0.9rem;">98%</div>
                    </div>
                    <div>
                        <small class="text-muted">Revenus</small>
                        <div class="fw-bold text-orange" style="font-size: 0.9rem;">2.45M FCFA</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- === CONTENU DESKTOP === -->
        <div class="desktop-content">
            <div class="main-content">
                <!-- Onglets Desktop -->
                <ul class="nav nav-tabs" role="tablist" id="desktopTabs">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-dashboard" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab">
                            <i class="fas fa-chart-line me-2"></i>Dashboard
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-deliveries" data-bs-toggle="tab" data-bs-target="#deliveries" type="button" role="tab">
                            <i class="fas fa-box me-2"></i>Livraisons
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-messages" data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab">
                            <i class="fas fa-envelope me-2"></i>Messages
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-reviews" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
                            <i class="fas fa-star me-2"></i>Avis
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-profile" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                            <i class="fas fa-user me-2"></i>Profil
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-4">
                    <!-- Tab Dashboard -->
                    <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="card p-4 text-center">
                                    <div class="h3 text-orange fw-bold">8</div>
                                    <small class="text-muted">Livraisons</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card p-4 text-center">
                                    <div class="h3 text-orange fw-bold">6</div>
                                    <small class="text-muted">En attente</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card p-4 text-center">
                                    <div class="h3 text-orange fw-bold">2</div>
                                    <small class="text-muted">En cours</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card p-4 text-center">
                                    <div class="h3 text-orange fw-bold">4.8</div>
                                    <small class="text-muted">Note moyenne</small>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-lg-8">
                                <div class="card p-4">
                                    <h5 class="fw-bold mb-3">Livraisons récentes</h5>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="card delivery-card p-3" onclick="openDeliveryDetails('LIV-001', 'CMD-001', 'Boutique Dupont', 'Jean Dupont', '123 Rue de Paris', '45 000 FCFA', 'En attente', 'Livraison à domicile', '15/02/2026')">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <div class="delivery-icon">
                                                            <i class="fas fa-box"></i>
                                                        </div>
                                                        <div class="ms-3">
                                                            <h6 class="mb-0 fw-bold">#LIV-001</h6>
                                                            <small class="text-muted">Boutique Dupont • 15/02/2026</small>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="fw-bold mb-1">45 000 FCFA</div>
                                                        <span class="status-badge status-pending">En attente</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="card delivery-card p-3" onclick="openDeliveryDetails('LIV-002', 'CMD-002', 'Boutique Martin', 'Marie Martin', '456 Avenue Principale', '62 000 FCFA', 'Acceptée', 'Livraison à domicile', '14/02/2026')">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <div class="delivery-icon">
                                                            <i class="fas fa-box"></i>
                                                        </div>
                                                        <div class="ms-3">
                                                            <h6 class="mb-0 fw-bold">#LIV-002</h6>
                                                            <small class="text-muted">Boutique Martin • 14/02/2026</small>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="fw-bold mb-1">62 000 FCFA</div>
                                                        <span class="status-badge status-accepted">Acceptée</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="card p-4">
                                    <h5 class="fw-bold mb-3">Notifications</h5>
                                    <div class="message-item unread">
                                        <div class="message-from">Admin</div>
                                        <div class="message-preview">Nouvelle livraison disponible</div>
                                        <div class="message-time">il y a 5 min</div>
                                    </div>
                                    <div class="message-item unread">
                                        <div class="message-from">Admin</div>
                                        <div class="message-preview">Livraison confirmée</div>
                                        <div class="message-time">il y a 15 min</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Livraisons -->
                    <div class="tab-pane fade" id="deliveries" role="tabpanel">
                        <div class="card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold">Gestion des Livraisons</h5>
                                <span class="badge badge-info">Total: 8 livraison(s)</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="card delivery-card p-3" onclick="openDeliveryDetails('LIV-001', 'CMD-001', 'Boutique Dupont', 'Jean Dupont', '123 Rue de Paris', '45 000 FCFA', 'En attente', 'Livraison à domicile', '15/02/2026')">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="delivery-icon">
                                                    <i class="fas fa-box"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="mb-0 fw-bold">#LIV-001</h6>
                                                    <small class="text-muted">Boutique Dupont • 15/02/2026</small>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold mb-1">45 000 FCFA</div>
                                                <span class="status-badge status-pending">En attente</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="card delivery-card p-3" onclick="openDeliveryDetails('LIV-002', 'CMD-002', 'Boutique Martin', 'Marie Martin', '456 Avenue Principale', '62 000 FCFA', 'Acceptée', 'Livraison à domicile', '14/02/2026')">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="delivery-icon">
                                                    <i class="fas fa-box"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="mb-0 fw-bold">#LIV-002</h6>
                                                    <small class="text-muted">Boutique Martin • 14/02/2026</small>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold mb-1">62 000 FCFA</div>
                                                <span class="status-badge status-accepted">Acceptée</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="card delivery-card p-3" onclick="openDeliveryDetails('LIV-003', 'CMD-003', 'Boutique Durand', 'Pierre Durand', '789 Boulevard Saint-Germain', '38 500 FCFA', 'En cours', 'Retrait en boutique', '13/02/2026')">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="delivery-icon">
                                                    <i class="fas fa-box"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="mb-0 fw-bold">#LIV-003</h6>
                                                    <small class="text-muted">Boutique Durand • 13/02/2026</small>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold mb-1">38 500 FCFA</div>
                                                <span class="status-badge status-in-progress">En cours</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Messages -->
                    <div class="tab-pane fade" id="messages" role="tabpanel">
                        <div class="card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold">Messages avec l'Admin</h5>
                                <span class="badge badge-info">2 non lus</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="message-item unread" onclick="openMessage('MSG-001', 'Admin', 'Votre demande pour la livraison #LIV-001 a été acceptée. Vous pouvez commencer la livraison.', 'il y a 15 min')">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="message-from">Admin</div>
                                                <div class="message-preview">Votre demande pour la livraison #LIV-001 a été acceptée...</div>
                                            </div>
                                            <div class="message-time">il y a 15 min</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="message-item unread" onclick="openMessage('MSG-002', 'Admin', 'Nouvelle livraison disponible: #LIV-002 - Boutique Martin', 'il y a 30 min')">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="message-from">Admin</div>
                                                <div class="message-preview">Nouvelle livraison disponible: #LIV-002...</div>
                                            </div>
                                            <div class="message-time">il y a 30 min</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="message-item" onclick="openMessage('MSG-003', 'Admin', 'Merci pour votre travail sur la livraison #LIV-003. Bien fait!', 'il y a 2 h')">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="message-from">Admin</div>
                                                <div class="message-preview">Merci pour votre travail sur la livraison #LIV-003...</div>
                                            </div>
                                            <div class="message-time">il y a 2 h</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Avis -->
                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                        <div class="card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold">Avis et Évaluations</h5>
                                <div>
                                    <span class="badge badge-success">4.8/5.0</span>
                                    <span class="badge badge-info">24 avis</span>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="review-author">Jean Dupont</div>
                                            <div class="review-date">15/02/2026</div>
                                        </div>
                                        <div class="review-rating">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <span class="small text-muted ms-1">5.0</span>
                                        </div>
                                        <div class="review-text">Excellent livreur ! Très professionnel et ponctuel. Je recommande vivement !</div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="review-author">Marie Martin</div>
                                            <div class="review-date">14/02/2026</div>
                                        </div>
                                        <div class="review-rating">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                            <span class="small text-muted ms-1">4.5</span>
                                        </div>
                                        <div class="review-text">Très bon service, livraison rapide. Petit délai mais rien de grave.</div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="review-author">Pierre Durand</div>
                                            <div class="review-date">13/02/2026</div>
                                        </div>
                                        <div class="review-rating">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <span class="small text-muted ms-1">4.0</span>
                                        </div>
                                        <div class="review-text">Bon service, mais aurait pu être plus courtois. Livraison effectuée correctement.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Profil -->
                    <div class="tab-pane fade" id="profile" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="card p-4">
                                    <div class="profile-header">
                                        <div class="profile-avatar">JD</div>
                                        <div class="profile-name">Jean Dupont</div>
                                        <div class="profile-status">Livreur Actif</div>
                                        <div class="profile-rating">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                            <span class="small text-muted ms-1">4.8/5.0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="card p-4 mb-4">
                                    <h5 class="fw-bold mb-3">Informations Personnelles</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">Nom complet</small>
                                            <div class="fw-bold">Jean Dupont</div>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">Email</small>
                                            <div class="fw-bold">jean@example.com</div>
                                        </div>
                                        <div class="col-md-6 mt-3">
                                            <small class="text-muted">Téléphone</small>
                                            <div class="fw-bold">+33 6 12 34 56 78</div>
                                        </div>
                                        <div class="col-md-6 mt-3">
                                            <small class="text-muted">Adresse</small>
                                            <div class="fw-bold">123 Avenue des Livreurs, Paris</div>
                                        </div>
                                        <div class="col-md-6 mt-3">
                                            <small class="text-muted">Statut</small>
                                            <div class="fw-bold"><span class="badge badge-success">Actif</span></div>
                                        </div>
                                        <div class="col-md-6 mt-3">
                                            <small class="text-muted">Membre depuis</small>
                                            <div class="fw-bold">01/01/2024</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card p-4">
                                    <h5 class="fw-bold mb-3">Statistiques</h5>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="stat-card">
                                                <div class="stat-number">45</div>
                                                <div class="stat-label">Livraisons effectuées</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-card">
                                                <div class="stat-number">6</div>
                                                <div class="stat-label">En attente</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-card">
                                                <div class="stat-number">98%</div>
                                                <div class="stat-label">Taux de réussite</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-card">
                                                <div class="stat-number">2.45M</div>
                                                <div class="stat-label">Revenus (FCFA)</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- === MODAL DÉTAILS LIVRAISON === -->
        <div class="modal fade" id="deliveryDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deliveryTitle">Livraison #LIV-001</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Informations Livraison -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Informations de la Livraison</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Boutique</small>
                                    <div class="fw-bold" id="deliveryShop">Boutique Dupont</div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Client</small>
                                    <div class="fw-bold" id="deliveryClient">Jean Dupont</div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <small class="text-muted">Adresse de livraison</small>
                                    <div class="fw-bold" id="deliveryAddress">123 Rue de Paris</div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <small class="text-muted">Date de commande</small>
                                    <div class="fw-bold" id="deliveryDate">15/02/2026</div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <small class="text-muted">Montant</small>
                                    <div class="fw-bold text-orange" id="deliveryAmount">45 000 FCFA</div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <small class="text-muted">Type de livraison</small>
                                    <div class="fw-bold" id="deliveryType">Livraison à domicile</div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Articles de la Livraison -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Articles à livrer</h6>
                            <div class="articles-list" id="deliveryItemsList">
                                <div class="article-item">
                                    <div class="article-img">
                                        <i class="fas fa-image"></i>
                                    </div>
                                    <div class="article-details">
                                        <div class="article-name">Produit A</div>
                                        <div class="article-meta">Qté: 2 • 15 000 FCFA</div>
                                    </div>
                                    <div class="article-price">30 000 FCFA</div>
                                </div>
                                <div class="article-item">
                                    <div class="article-img">
                                        <i class="fas fa-image"></i>
                                    </div>
                                    <div class="article-details">
                                        <div class="article-name">Produit B</div>
                                        <div class="article-meta">Qté: 1 • 15 000 FCFA</div>
                                    </div>
                                    <div class="article-price">15 000 FCFA</div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Statut et Actions -->
                        <div id="section-status-pending" class="mb-4">
                            <h6 class="fw-bold mb-3">Actions</h6>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Statut actuel:</strong> En attente de votre décision
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-danger flex-grow-1" onclick="rejectDelivery()">Refuser</button>
                                <button type="button" class="btn btn-outline-secondary flex-grow-1" data-bs-dismiss="modal">Fermer</button>
                                <button type="button" class="btn btn-orange flex-grow-1" onclick="acceptDelivery()">Accepter</button>
                            </div>
                        </div>

                        <!-- Demande Envoyée -->
                        <div id="section-request-sent" class="alert alert-info" style="display: none;">
                            <i class="fas fa-paper-plane me-2"></i>
                            <strong>Demande envoyée!</strong> Votre demande a été transmise à l'admin. En attente de confirmation...
                        </div>

                        <!-- Livraison Confirmée -->
                        <div id="section-delivery-confirmed" style="display: none;">
                            <div class="alert alert-success mb-3">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Livraison confirmée!</strong> L'admin a accepté votre demande. Vous pouvez commencer la livraison.
                            </div>

                            <h6 class="fw-bold mb-3">Modifier l'état de la livraison</h6>
                            <div class="mb-3">
                                <label class="form-label">État actuel</label>
                                <select class="form-select" id="deliveryStatus" onchange="updateDeliveryStatus()">
                                    <option value="confirmed">Confirmée</option>
                                    <option value="in-progress">En cours</option>
                                    <option value="delivered">Livrée</option>
                                    <option value="failed">Échouée</option>
                                </select>
                            </div>

                            <div id="section-delivery-notes" class="mb-3" style="display: none;">
                                <label class="form-label">Notes (si échouée)</label>
                                <textarea class="form-control" id="deliveryNotes" rows="3" placeholder="Expliquez pourquoi la livraison a échoué..."></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary flex-grow-1" data-bs-dismiss="modal">Fermer</button>
                                <button type="button" class="btn btn-orange flex-grow-1" onclick="saveDeliveryStatus()">Enregistrer</button>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div id="section-timeline" style="display: none;">
                            <hr>
                            <h6 class="fw-bold mb-3">Historique</h6>
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-time">15/02/2026 10:30</div>
                                    <div class="timeline-content">Livraison publiée par l'admin</div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-time">15/02/2026 10:35</div>
                                    <div class="timeline-content">Vous avez accepté la livraison</div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-time">15/02/2026 10:40</div>
                                    <div class="timeline-content">Admin a confirmé votre demande</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- === MODAL MESSAGE === -->
        <div class="modal fade" id="messageModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="messageTitle">Message de Admin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <small class="text-muted">De:</small>
                            <div class="fw-bold" id="messageFrom">Admin</div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Date:</small>
                            <div class="fw-bold" id="messageDate">il y a 15 min</div>
                        </div>
                        <hr>
                        <div class="mb-4">
                            <div id="messageContent">Votre demande pour la livraison #LIV-001 a été acceptée. Vous pouvez commencer la livraison.</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary flex-grow-1" data-bs-dismiss="modal">Fermer</button>
                            <button type="button" class="btn btn-orange flex-grow-1" onclick="replyToMessage()">Répondre</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include '../vendor/chatAI/discuss.php'; ?>
        <script src="../js/bootstrap.min.js"></script>
        <script>
            // === GESTION DES ONGLETS MOBILES ===
            document.querySelectorAll('.nav-link[data-tab]').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const tab = link.dataset.tab;
                    
                    // Masquer tous les panneaux
                    document.querySelectorAll('.mobile-panel').forEach(panel => {
                        panel.classList.remove('active');
                    });
                    
                    // Afficher le panneau sélectionné
                    const panel = document.getElementById(`mobile-${tab}-panel`);
                    if (panel) {
                        panel.classList.add('active');
                    }
                    
                    // Mettre à jour les onglets actifs
                    document.querySelectorAll('.nav-link[data-tab]').forEach(l => {
                        l.classList.remove('active');
                    });
                    link.classList.add('active');
                });
            });

            // === GESTION DES NOTIFICATIONS ===
            const notifBell = document.getElementById('notifBellTop');
            const notifDropdown = document.getElementById('notifDropdownMenuTop');

            notifBell.addEventListener('click', () => {
                notifDropdown.classList.toggle('show');
            });

            document.addEventListener('click', (e) => {
                if (!notifBell.contains(e.target) && !notifDropdown.contains(e.target)) {
                    notifDropdown.classList.remove('show');
                }
            });

            // === NAVIGATION ===
            function goBack() {
                window.history.back();
            }

            // === MODALES LIVRAISONS ===
            function openDeliveryDetails(id, cmd, shop, client, address, amount, status, type, date) {
                document.getElementById('deliveryTitle').textContent = 'Livraison #' + id;
                document.getElementById('deliveryShop').textContent = shop;
                document.getElementById('deliveryClient').textContent = client;
                document.getElementById('deliveryAddress').textContent = address;
                document.getElementById('deliveryAmount').textContent = amount;
                document.getElementById('deliveryType').textContent = type;
                document.getElementById('deliveryDate').textContent = date;

                // Réinitialiser les sections
                document.getElementById('section-status-pending').style.display = 'block';
                document.getElementById('section-request-sent').style.display = 'none';
                document.getElementById('section-delivery-confirmed').style.display = 'none';
                document.getElementById('section-timeline').style.display = 'none';

                const modal = new bootstrap.Modal(document.getElementById('deliveryDetailsModal'));
                modal.show();
            }

            function acceptDelivery() {
                document.getElementById('section-status-pending').style.display = 'none';
                document.getElementById('section-request-sent').style.display = 'block';

                setTimeout(() => {
                    document.getElementById('section-request-sent').style.display = 'none';
                    document.getElementById('section-delivery-confirmed').style.display = 'block';
                    document.getElementById('section-timeline').style.display = 'block';
                }, 2000);
            }

            function rejectDelivery() {
                if (confirm('Êtes-vous sûr de vouloir refuser cette livraison ?')) {
                    document.getElementById('section-status-pending').style.display = 'none';
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-danger';
                    alert.innerHTML = '<i class="fas fa-times-circle me-2"></i><strong>Refusée!</strong> Vous avez refusé cette livraison.';
                    document.querySelector('.modal-body').insertBefore(alert, document.querySelector('.modal-body').firstChild);
                    
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('deliveryDetailsModal')).hide();
                    }, 2000);
                }
            }

            function updateDeliveryStatus() {
                const status = document.getElementById('deliveryStatus').value;
                const notesSection = document.getElementById('section-delivery-notes');
                
                if (status === 'failed') {
                    notesSection.style.display = 'block';
                } else {
                    notesSection.style.display = 'none';
                }
            }

            function saveDeliveryStatus() {
                const status = document.getElementById('deliveryStatus').value;
                const statusText = {
                    'confirmed': 'Confirmée',
                    'in-progress': 'En cours',
                    'delivered': 'Livrée',
                    'failed': 'Échouée'
                };

                const alert = document.createElement('div');
                alert.className = 'alert alert-success mt-3';
                alert.innerHTML = `<i class="fas fa-check-circle me-2"></i><strong>Succès!</strong> État de la livraison mis à jour: ${statusText[status]}`;
                
                document.querySelector('.modal-body').appendChild(alert);
                
                setTimeout(() => {
                    bootstrap.Modal.getInstance(document.getElementById('deliveryDetailsModal')).hide();
                }, 2000);
            }

            // === MODALES MESSAGES ===
            function openMessage(id, from, content, time) {
                document.getElementById('messageTitle').textContent = 'Message de ' + from;
                document.getElementById('messageFrom').textContent = from;
                document.getElementById('messageContent').textContent = content;
                document.getElementById('messageDate').textContent = time;

                const modal = new bootstrap.Modal(document.getElementById('messageModal'));
                modal.show();
            }

            function replyToMessage() {
                alert('Vous pouvez maintenant répondre à ce message. Une interface de réponse s\'affichera ici.');
            }

            // === INITIALISATION ===
            document.addEventListener('DOMContentLoaded', () => {
                if (window.innerWidth < 992) {
                    document.getElementById('mobile-dashboard-panel').classList.add('active');
                }
            });

            // === RESPONSIVE ===
            window.addEventListener('resize', () => {
                if (window.innerWidth < 992) {
                    document.querySelector('.desktop-content').style.display = 'none';
                } else {
                    document.querySelector('.desktop-content').style.display = 'block';
                }
            });
        </script>
    </body>
</html>