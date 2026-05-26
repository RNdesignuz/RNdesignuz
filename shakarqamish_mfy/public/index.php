<?php
/**
 * ShakarqamishMFY.uz - Home Page
 * Main landing page with all key features
 */

// Load configuration and core functions
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/core/functions.php';

// Track visitor
trackVisitor('/');

// Fetch latest news
$db = getDB();
$newsStmt = $db->query("SELECT p.*, c.name_uz as category_name, c.color, a.full_name as author_name 
    FROM posts p 
    LEFT JOIN categories c ON p.category_id = c.id 
    LEFT JOIN admins a ON p.author_id = a.id 
    WHERE p.status = 'published' 
    ORDER BY p.published_at DESC 
    LIMIT 6");
$latestNews = $newsStmt->fetchAll();

// Fetch featured news
$featuredStmt = $db->query("SELECT p.*, c.name_uz as category_name 
    FROM posts p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.status = 'published' AND p.is_featured = 1 
    ORDER BY p.published_at DESC 
    LIMIT 3");
$featuredNews = $featuredStmt->fetchAll();

// Fetch staff members
$staffStmt = $db->query("SELECT * FROM staff_members WHERE status = 1 ORDER BY sort_order LIMIT 8");
$staffMembers = $staffStmt->fetchAll();

// Fetch announcements
$announcementStmt = $db->query("SELECT p.* FROM posts p 
    INNER JOIN categories c ON p.category_id = c.id 
    WHERE p.status = 'published' AND c.slug = 'elonlar' 
    ORDER BY p.published_at DESC 
    LIMIT 5");
$announcements = $announcementStmt->fetchAll();

// Statistics
$statsStmt = $db->query("SELECT SUM(page_views) as total_views, COUNT(DISTINCT id) as total_visitors FROM statistics");
$stats = $statsStmt->fetch();

$visitorsStmt = $db->query("SELECT COUNT(*) as total FROM visitors WHERE visited_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$monthlyVisitors = $visitorsStmt->fetch()['total'] ?? 0;

$appealsStmt = $db->query("SELECT COUNT(*) as total FROM appeals WHERE status = 'resolved'");
$resolvedAppeals = $appealsStmt->fetch()['total'] ?? 0;

$postsStmt = $db->query("SELECT COUNT(*) as total FROM posts WHERE status = 'published'");
$totalPosts = $postsStmt->fetch()['total'] ?? 0;

// Fetch sliders
$sliderStmt = $db->query("SELECT * FROM sliders WHERE status = 1 ORDER BY sort_order");
$sliders = $sliderStmt->fetchAll();

// Set page metadata
$pageTitle = getSetting('site_name', 'Shakarqamish MFY') . ' - Bosh sahifa';
$pageDescription = getSetting('site_description', 'Mahalla fuqarolar yig\'ini rasmiy sayti');
$activePage = 'home';

// Start output buffering
ob_start();
?>

<!-- Hero Section with Slider -->
<?php if (count($sliders) > 0): ?>
<section class="hero-slider">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <?php foreach ($sliders as $index => $slider): ?>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $index ?>" <?= $index === 0 ? 'class="active"' : '' ?>></button>
            <?php endforeach; ?>
        </div>
        <div class="carousel-inner">
            <?php foreach ($sliders as $index => $slider): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                    <img src="/public/uploads/<?= e($slider['image']) ?>" class="d-block w-100" alt="<?= e($slider['title']) ?>" style="height: 600px; object-fit: cover;">
                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-4">
                        <h2 class="display-4 fw-bold"><?= e($slider['title']) ?></h2>
                        <p class="lead"><?= e($slider['caption']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>
<?php else: ?>
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7" data-aos="fade-right">
                <h1><?= e(getSetting('site_name', 'Shakarqamish MFY')) ?></h1>
                <p class="lead mb-4"><?= e(getSetting('site_description', 'Mahalla fuqarolar yig\'ini rasmiy portaliga xush kelibsiz!')) ?></p>
                <div class="d-flex gap-3">
                    <a href="/public/appeal.php" class="btn btn-light btn-lg px-4">
                        <i class="bi bi-envelope"></i> Murojaat yuborish
                    </a>
                    <a href="/public/news.php" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-newspaper"></i> Yangiliklar
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-center" data-aos="fade-left">
                <img src="/public/assets/images/hero-illustration.svg" alt="Illustration" class="img-fluid" onerror="this.style.display='none'">
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Statistics Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="0">
                <div class="stats-card text-center">
                    <div class="icon bg-primary bg-opacity-10 text-primary mx-auto">
                        <i class="bi bi-eye"></i>
                    </div>
                    <div class="number" data-count="<?= $stats['total_views'] ?? 0 ?>"><?= formatNumber($stats['total_views'] ?? 0) ?></div>
                    <div class="label">Ko'rishlar soni</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stats-card text-center">
                    <div class="icon bg-success bg-opacity-10 text-success mx-auto">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="number" data-count="<?= $monthlyVisitors ?>"><?= formatNumber($monthlyVisitors) ?></div>
                    <div class="label">Oylik tashriflar</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stats-card text-center">
                    <div class="icon bg-info bg-opacity-10 text-info mx-auto">
                        <i class="bi bi-file-text"></i>
                    </div>
                    <div class="number" data-count="<?= $totalPosts ?>"><?= formatNumber($totalPosts) ?></div>
                    <div class="label">Yangiliklar</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stats-card text-center">
                    <div class="icon bg-warning bg-opacity-10 text-warning mx-auto">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="number" data-count="<?= $resolvedAppeals ?>"><?= formatNumber($resolvedAppeals) ?></div>
                    <div class="label">Yechildi</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured News Section -->
<?php if (count($featuredNews) > 0): ?>
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Muhim yangiliklar</h2>
            <p class="section-subtitle">Eng dolzarb va muhim xabarlar</p>
        </div>
        <div class="row g-4">
            <?php foreach ($featuredNews as $news): ?>
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="<?= $loop->index * 100 ?>">
                <div class="news-card">
                    <?php if ($news['featured_image']): ?>
                        <img src="/public/uploads/<?= e($news['featured_image']) ?>" class="card-img-top" alt="<?= e($news['title']) ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <?php if ($news['category_name']): ?>
                            <span class="category-badge mb-2 d-inline-block" style="background: <?= e($news['color']) ?>; color: white;">
                                <?= e($news['category_name']) ?>
                            </span>
                        <?php endif; ?>
                        <h5 class="card-title">
                            <a href="/public/news-detail.php?slug=<?= e($news['slug']) ?>" class="text-decoration-none text-dark">
                                <?= e($news['title']) ?>
                            </a>
                        </h5>
                        <p class="card-text"><?= e(mb_substr(strip_tags($news['excerpt'] ?? $news['content']), 0, 120)) ?>...</p>
                        <div class="meta">
                            <span><i class="bi bi-calendar"></i> <?= formatDateUZ($news['published_at'], 'short') ?></span>
                            <span><i class="bi bi-eye"></i> <?= formatNumber($news['views']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Quick Services -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Tezkor xizmatlar</h2>
            <p class="section-subtitle">Fuqarolar uchun qulay xizmatlar</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="0">
                <a href="/public/appeal.php" class="text-decoration-none">
                    <div class="stats-card text-center h-100">
                        <div class="icon bg-primary bg-opacity-10 text-primary mx-auto">
                            <i class="bi bi-envelope-paper"></i>
                        </div>
                        <h5 class="mt-3 mb-2">Online murojaat</h5>
                        <p class="text-muted">Ariza va shikoyatlaringizni online tarzda yuboring</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <a href="/public/staff.php" class="text-decoration-none">
                    <div class="stats-card text-center h-100">
                        <div class="icon bg-success bg-opacity-10 text-success mx-auto">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h5 class="mt-3 mb-2">Kengash a'zolari</h5>
                        <p class="text-muted">Mahalla kengashi a'zolari bilan tanishing</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <a href="/public/documents.php" class="text-decoration-none">
                    <div class="stats-card text-center h-100">
                        <div class="icon bg-info bg-opacity-10 text-info mx-auto">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </div>
                        <h5 class="mt-3 mb-2">Hujjatlar</h5>
                        <p class="text-muted">Kerakli hujjat va blankalarni yuklab oling</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Latest News Section -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5" data-aos="fade-up">
            <div>
                <h2 class="section-title mb-0">So'nggi yangiliklar</h2>
                <p class="section-subtitle mb-0">Barcha yangiliklarni ko'rish</p>
            </div>
            <a href="/public/news.php" class="btn btn-primary">
                Barchasi <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="row g-4">
            <?php foreach ($latestNews as $news): ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up">
                <div class="news-card">
                    <?php if ($news['featured_image']): ?>
                        <img src="/public/uploads/<?= e($news['featured_image']) ?>" class="card-img-top" alt="<?= e($news['title']) ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <?php if ($news['category_name']): ?>
                            <span class="category-badge mb-2 d-inline-block" style="background: <?= e($news['color']) ?>; color: white;">
                                <?= e($news['category_name']) ?>
                            </span>
                        <?php endif; ?>
                        <h5 class="card-title">
                            <a href="/public/news-detail.php?slug=<?= e($news['slug']) ?>" class="text-decoration-none text-dark">
                                <?= e($news['title']) ?>
                            </a>
                        </h5>
                        <p class="card-text"><?= e(mb_substr(strip_tags($news['excerpt'] ?? $news['content']), 0, 100)) ?>...</p>
                        <div class="meta">
                            <span><i class="bi bi-calendar"></i> <?= formatDateUZ($news['published_at'], 'short') ?></span>
                            <span><i class="bi bi-eye"></i> <?= formatNumber($news['views']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Announcements Section -->
<?php if (count($announcements) > 0): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8" data-aos="fade-right">
                <h2 class="section-title mb-4">E'lonlar</h2>
                <div class="list-group">
                    <?php foreach ($announcements as $index => $announcement): ?>
                    <a href="/public/news-detail.php?slug=<?= e($announcement['slug']) ?>" class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                        <div class="flex-shrink-0 bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <span class="fw-bold"><?= $index + 1 ?></span>
                        </div>
                        <div>
                            <h6 class="mb-0"><?= e($announcement['title']) ?></h6>
                            <small class="text-muted"><i class="bi bi-calendar"></i> <?= formatDateUZ($announcement['published_at'], 'short') ?></small>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-4" data-aos="fade-left">
                <div class="stats-card">
                    <h5 class="mb-3"><i class="bi bi-clock-history"></i> Qabul kunlari</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="py-2 border-bottom">
                            <strong>Dushanba:</strong> 09:00 - 12:00
                        </li>
                        <li class="py-2 border-bottom">
                            <strong>Seshanba:</strong> 14:00 - 17:00
                        </li>
                        <li class="py-2 border-bottom">
                            <strong>Chorshanba:</strong> 09:00 - 12:00
                        </li>
                        <li class="py-2 border-bottom">
                            <strong>Payshanba:</strong> 14:00 - 17:00
                        </li>
                        <li class="py-2">
                            <strong>Juma:</strong> 09:00 - 12:00
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Staff Preview Section -->
<?php if (count($staffMembers) > 0): ?>
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Mahalla kengashi a'zolari</h2>
            <p class="section-subtitle">Bizning jamoa bilan tanishing</p>
        </div>
        <div class="row g-4">
            <?php foreach ($staffMembers as $member): ?>
            <div class="col-md-6 col-lg-3" data-aos="fade-up">
                <div class="staff-card">
                    <?php if ($member['photo']): ?>
                        <img src="/public/uploads/<?= e($member['photo']) ?>" alt="<?= e($member['full_name']) ?>" class="avatar">
                    <?php else: ?>
                        <div class="avatar bg-light d-flex align-items-center justify-content-center mx-auto">
                            <i class="bi bi-person fs-1 text-muted"></i>
                        </div>
                    <?php endif; ?>
                    <h5 class="name"><?= e($member['full_name']) ?></h5>
                    <p class="position"><?= e($member['position']) ?></p>
                    <?php if ($member['phone']): ?>
                        <p class="contact-info">
                            <i class="bi bi-telephone"></i> <?= e($member['phone']) ?>
                        </p>
                    <?php endif; ?>
                    <a href="/public/staff-detail.php?id=<?= $member['id'] ?>" class="btn btn-sm btn-outline-primary mt-2">
                        Batafsil <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="/public/staff.php" class="btn btn-primary btn-lg">
                Barcha xodimlarni ko'rish <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Map Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Joylashuv</h2>
            <p class="section-subtitle">Bizni xaritada toping</p>
        </div>
        <div class="map-container" data-aos="zoom-in">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2998.5!2d69.2495!3d41.3115!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDHCsDE4JzQxLjQiTiA2OcKwMTQnNTguMiJF!5e0!3m2!1suz!2s!4v1234567890" 
                width="100%" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/includes/header.php';
