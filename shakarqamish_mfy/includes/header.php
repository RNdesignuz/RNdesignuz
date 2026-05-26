<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? getSetting('site_name', 'Shakarqamish MFY')) ?></title>
    <meta name="description" content="<?= e($pageDescription ?? getSetting('site_description', 'Mahalla fuqarolar yig\'ini rasmiy sayti')) ?>">
    <meta name="keywords" content="<?= e($pageKeywords ?? 'mahalla, shakarqamish, yangiliklar, elonlar') ?>">
    
    <!-- SEO Meta Tags -->
    <meta property="og:title" content="<?= e($pageTitle ?? getSetting('site_name')) ?>">
    <meta property="og:description" content="<?= e($pageDescription ?? getSetting('site_description')) ?>">
    <meta property="og:image" content="<?= getSetting('site_url') ?>/public/assets/images/og-image.jpg">
    <meta property="og:type" content="website">
    
    <!-- Favicon -->
    <link rel="icon" href="/public/assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/public/assets/css/style.css">
    
    <?= $extraHead ?? '' ?>
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <!-- Top Bar -->
        <div class="top-bar bg-primary text-white py-2">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 d-none d-md-block">
                        <small><i class="bi bi-geo-alt"></i> <?= e(getSetting('address', 'Toshkent viloyati')) ?></small>
                        <span class="mx-2">|</span>
                        <small><i class="bi bi-clock"></i> 09:00 - 18:00</small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="tel:<?= e(getSetting('phone')) ?>" class="text-white text-decoration-none me-3">
                            <i class="bi bi-telephone"></i> <?= e(getSetting('phone')) ?>
                        </a>
                        <a href="<?= e(getSetting('telegram')) ?>" class="text-white me-2"><i class="bi bi-telegram"></i></a>
                        <a href="<?= e(getSetting('facebook')) ?>" class="text-white me-2"><i class="bi bi-facebook"></i></a>
                        <a href="<?= e(getSetting('instagram')) ?>" class="text-white"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="/">
                    <?php if (getSetting('logo')): ?>
                        <img src="/public/uploads/<?= e(getSetting('logo')) ?>" alt="Logo" height="50">
                    <?php else: ?>
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-building fs-4"></i>
                        </div>
                    <?php endif; ?>
                    <span class="ms-2 fw-bold text-primary"><?= e(getSetting('site_name', 'Shakarqamish MFY')) ?></span>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link <?= ($activePage ?? '') === 'home' ? 'active' : '' ?>" href="/">Bosh sahifa</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($activePage ?? '') === 'about' ? 'active' : '' ?>" href="/public/about.php">Mahalla haqida</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($activePage ?? '') === 'staff' ? 'active' : '' ?>" href="/public/staff.php">Kengash a'zolari</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= ($activePage ?? '') === 'news' ? 'active' : '' ?>" href="#" data-bs-toggle="dropdown">Yangiliklar</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/public/news.php">Barcha yangiliklar</a></li>
                                <li><a class="dropdown-item" href="/public/news.php?category=elonlar">E'lonlar</a></li>
                                <li><a class="dropdown-item" href="/public/news.php?category=tadbirlar">Tadbirlar</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($activePage ?? '') === 'gallery' ? 'active' : '' ?>" href="/public/gallery.php">Galereya</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($activePage ?? '') === 'documents' ? 'active' : '' ?>" href="/public/documents.php">Hujjatlar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($activePage ?? '') === 'contact' ? 'active' : '' ?>" href="/public/contact.php">Aloqa</a>
                        </li>
                    </ul>
                    <a href="/public/appeal.php" class="btn btn-primary ms-lg-3">
                        <i class="bi bi-envelope"></i> Murojaat
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="site-footer bg-dark text-white pt-5 pb-3">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <h5 class="fw-bold mb-3"><?= e(getSetting('site_name')) ?></h5>
                    <p class="text-white-50"><?= e(getSetting('site_description')) ?></p>
                    <div class="social-links mt-3">
                        <a href="<?= e(getSetting('telegram')) ?>" class="text-white me-3"><i class="bi bi-telegram fs-5"></i></a>
                        <a href="<?= e(getSetting('facebook')) ?>" class="text-white me-3"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="<?= e(getSetting('instagram')) ?>" class="text-white"><i class="bi bi-instagram fs-5"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold mb-3">Tezkor havolalar</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/" class="text-white-50 text-decoration-none">Bosh sahifa</a></li>
                        <li class="mb-2"><a href="/public/about.php" class="text-white-50 text-decoration-none">Mahalla haqida</a></li>
                        <li class="mb-2"><a href="/public/staff.php" class="text-white-50 text-decoration-none">Kengash a'zolari</a></li>
                        <li class="mb-2"><a href="/public/news.php" class="text-white-50 text-decoration-none">Yangiliklar</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-bold mb-3">Foydali linklar</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/public/appeal.php" class="text-white-50 text-decoration-none">Online murojaat</a></li>
                        <li class="mb-2"><a href="/public/gallery.php" class="text-white-50 text-decoration-none">Galereya</a></li>
                        <li class="mb-2"><a href="/public/documents.php" class="text-white-50 text-decoration-none">Hujjatlar</a></li>
                        <li class="mb-2"><a href="/admin" class="text-white-50 text-decoration-none">Admin panel</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-bold mb-3">Aloqa ma'lumotlari</h6>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i> <?= e(getSetting('address')) ?></li>
                        <li class="mb-2"><i class="bi bi-telephone me-2"></i> <?= e(getSetting('phone')) ?></li>
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i> <?= e(getSetting('email')) ?></li>
                        <li class="mb-2"><i class="bi bi-clock me-2"></i> Dushanba - Juma: 09:00 - 18:00</li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4 border-secondary">
            
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-white-50">&copy; <?= date('Y') ?> <?= e(getSetting('site_name')) ?>. Barcha huquqlar himoyalangan.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 text-white-50">Developed with <i class="bi bi-heart-fill text-danger"></i> by Shakarqamish Team</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top" id="backToTop">
        <i class="bi bi-arrow-up"></i>
    </a>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Custom JS -->
    <script src="/public/assets/js/main.js"></script>
    
    <?= $extraScripts ?? '' ?>
</body>
</html>
