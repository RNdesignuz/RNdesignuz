<?php
/**
 * ShakarqamishMFY.uz - Murojaat (Appeal) Page
 * Online appeal submission form for citizens
 */

// Load configuration and core functions
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/core/functions.php';

// Track visitor
trackVisitor('/appeal');

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $message = 'Xavfsizlik xatosi. Iltimos qaytadan urinib ko\'ring.';
        $messageType = 'danger';
    } elseif (!checkRateLimit('appeal_submit', 3, 3600)) {
        $message = 'Siz juda ko\'p murojaat yubordingiz. Iltimos 1 soat kuting.';
        $messageType = 'warning';
    } else {
        // Sanitize inputs
        $fullName = sanitizeInput($_POST['full_name'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $address = sanitizeInput($_POST['address'] ?? '');
        $subject = sanitizeInput($_POST['subject'] ?? '');
        $appealMessage = sanitizeInput($_POST['message'] ?? '');
        $appealType = $_POST['appeal_type'] ?? 'shikoyat';
        $priority = $_POST['priority'] ?? 'medium';
        $isAnonymous = isset($_POST['is_anonymous']) ? 1 : 0;
        
        // Validation
        $errors = [];
        if (empty($phone)) {
            $errors[] = 'Telefon raqami majburiy';
        }
        if (empty($subject)) {
            $errors[] = 'Mavzu majburiy';
        }
        if (empty($appealMessage)) {
            $errors[] = 'Murojaat matni majburiy';
        }
        if (!preg_match('/^[+]?[0-9\s\-()]+$/', $phone)) {
            $errors[] = 'Telefon raqami noto\'g\'ri formatda';
        }
        
        if (empty($errors)) {
            try {
                $db = getDB();
                
                // Handle file upload
                $attachmentPath = null;
                if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                    $validation = validateFileUpload($_FILES['attachment'], ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'], 5242880);
                    if ($validation['success']) {
                        $uploadDir = dirname(__DIR__) . '/public/uploads/appeals/';
                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        
                        $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
                        $filename = generateTicketId() . '.' . $ext;
                        $attachmentPath = 'appeals/' . $filename;
                        
                        if (!move_uploaded_file($_FILES['attachment']['tmp_name'], dirname(__DIR__) . '/public/uploads/' . $attachmentPath)) {
                            $attachmentPath = null;
                        }
                    }
                }
                
                // Generate ticket ID
                $ticketId = generateTicketId();
                
                // Insert appeal
                $stmt = $db->prepare("INSERT INTO appeals (ticket_id, full_name, phone, address, subject, message, appeal_type, priority, is_anonymous, attachment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $ticketId,
                    $isAnonymous ? null : $fullName,
                    $phone,
                    $address,
                    $subject,
                    $appealMessage,
                    $appealType,
                    $priority,
                    $isAnonymous,
                    $attachmentPath
                ]);
                
                $appealId = $db->lastInsertId();
                
                // Send notification to admins
                $adminStmt = $db->query("SELECT id FROM admins WHERE status = 1 AND role IN ('super_admin', 'rais', 'operator')");
                $admins = $adminStmt->fetchAll();
                
                foreach ($admins as $admin) {
                    sendNotification(
                        $admin['id'],
                        'Yangi murojaat',
                        "Yangi murojaat kelib tushdi (Ticket: {$ticketId})",
                        'warning',
                        '/admin/appeals.php'
                    );
                }
                
                $message = "Murojaatingiz qabul qilindi! Ticket ID: <strong>{$ticketId}</strong>. Ushbu ID orqali murojaat holatini tekshirishingiz mumkin.";
                $messageType = 'success';
                
                // Log activity
                logActivity(null, 'appeal_submitted', "Yangi murojaat: {$ticketId}", 'appeals', $appealId);
                
            } catch (Exception $e) {
                error_log("Appeal submission error: " . $e->getMessage());
                $message = 'Murojaat yuborishda xatolik yuz berdi. Iltimos qaytadan urinib ko\'ring.';
                $messageType = 'danger';
            }
        } else {
            $message = implode('<br>', $errors);
            $messageType = 'danger';
        }
    }
}

// Fetch appeal types
$appealTypes = [
    'shikoyat' => 'Shikoyat',
    'taklif' => 'Taklif',
    'ariza' => 'Ariza',
    'muammo' => 'Muammo',
    'minnatdorchilik' => 'Minnatdorchilik',
    'kommunal' => 'Kommunal xizmat',
    'yoshlar' => 'Yoshlar masalasi',
    'ayollar' => 'Ayollar masalasi'
];

// Fetch priorities
$priorities = [
    'low' => 'Past',
    'medium' => 'O\'rta',
    'high' => 'Yuqori',
    'urgent' => 'Zudlik bilan'
];

// Set page metadata
$pageTitle = 'Online Murojaat - ' . getSetting('site_name');
$pageDescription = 'Mahallaga online murojaat yuboring';
$activePage = 'appeal';

// Generate CSRF token
$csrfToken = generateCSRFToken();

// Start output buffering
ob_start();
?>

<!-- Page Header -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-2"><i class="bi bi-envelope-paper"></i> Online Murojaat</h1>
                <p class="lead mb-0 opacity-75">Ariza, shikoyat va takliflaringizni online tarzda yuboring</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="bg-white bg-opacity-10 rounded p-3 d-inline-block">
                    <small><i class="bi bi-clock"></i> Javob muddati: 3-5 kun</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Appeal Form Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <?php if ($message): ?>
                    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                        <i class="bi bi-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                        <?= $message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="appeal-form">
                    <h3 class="mb-4"><i class="bi bi-pencil-square"></i> Murojaat formasini to'ldiring</h3>
                    
                    <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                        
                        <div class="row g-4">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <label for="fullName" class="form-label">Ism sharifingiz</label>
                                <input type="text" class="form-control" id="fullName" name="full_name" placeholder="To'liq ismingizni kiriting">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Telefon raqami <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="+998 90 123 45 67" required>
                                <div class="invalid-feedback">Telefon raqami majburiy</div>
                            </div>
                            
                            <div class="col-12">
                                <label for="address" class="form-label">Manzil</label>
                                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Yashash manzilingiz"></textarea>
                            </div>
                            
                            <!-- Appeal Details -->
                            <div class="col-md-6">
                                <label for="appealType" class="form-label">Murojaat turi</label>
                                <select class="form-select" id="appealType" name="appeal_type">
                                    <?php foreach ($appealTypes as $value => $label): ?>
                                        <option value="<?= e($value) ?>"><?= e($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="priority" class="form-label">Prioritet</label>
                                <select class="form-select" id="priority" name="priority">
                                    <?php foreach ($priorities as $value => $label): ?>
                                        <option value="<?= e($value) ?>" <?= $value === 'medium' ? 'selected' : '' ?>><?= e($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label for="subject" class="form-label">Mavzu <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="subject" name="subject" placeholder="Murojaat mavzusi" required>
                                <div class="invalid-feedback">Mavzu majburiy</div>
                            </div>
                            
                            <div class="col-12">
                                <label for="message" class="form-label">Murojaat matni <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="6" placeholder="Murojaatingizni batafsil yozing..." required></textarea>
                                <div class="invalid-feedback">Murojaat matni majburiy</div>
                                <div class="form-text">Maksimal 2000 belgi</div>
                            </div>
                            
                            <div class="col-12">
                                <label for="attachment" class="form-label">Ilova (fayl)</label>
                                <input type="file" class="form-control" id="attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <div class="form-text">Ruxsat etilgan formatlar: PDF, JPG, PNG, DOC, DOCX. Maksimal hajm: 5MB</div>
                                <div id="filePreview"></div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="isAnonymous" name="is_anonymous">
                                    <label class="form-check-label" for="isAnonymous">
                                        Maxfiy yuborish (ismim yashirin bo'lsin)
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> 
                                    <strong>Eslatma:</strong> Murojaatingiz 3-5 kun ichida ko'rib chiqiladi. Javob SMS orqali yoki telefon orqali yuboriladi.
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-send"></i> Murojaatni yuborish
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Additional Information -->
                <div class="row g-4 mt-4">
                    <div class="col-md-4">
                        <div class="stats-card text-center h-100">
                            <div class="icon bg-primary bg-opacity-10 text-primary mx-auto">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <h5 class="mt-3">Javob muddati</h5>
                            <p class="text-muted mb-0">3-5 ish kuni ichida javob qaytariladi</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card text-center h-100">
                            <div class="icon bg-success bg-opacity-10 text-success mx-auto">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h5 class="mt-3">Maxfiylik</h5>
                            <p class="text-muted mb-0">Ma'lumotlaringiz himoyalangan</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card text-center h-100">
                            <div class="icon bg-info bg-opacity-10 text-info mx-auto">
                                <i class="bi bi-ticket-perforated"></i>
                            </div>
                            <h5 class="mt-3">Ticket ID</h5>
                            <p class="text-muted mb-0">Har bir murojaatga unikal ID beriladi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Ko'p beriladigan savollar</h2>
            <p class="section-subtitle">Murojaat jarayoni haqida</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Murojaatim qancha vaqt ichida ko'rib chiqiladi?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Murojaatlar odatda 3-5 ish kuni ichida ko'rib chiqiladi. Murakkab holatlarda bu muddat 15 kungacha uzaytirilishi mumkin.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Murojaat holatini qanday bilsam bo'ladi?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Murojaat yuborganingizdan keyin sizga Ticket ID beriladi. Ushbu ID orqali admin panel orqali yoki telefon orqali murojaat holatini bilishingiz mumkin.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Anonim murojaat yubora olamanmi?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Ha, "Maxfiy yuborish" opsiyasini tanlasangiz, ismingiz yashirin bo'ladi. Lekin telefon raqamingiz javob yuborish uchun zarur.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Qanday fayllarni ilova qilish mumkin?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                PDF, JPG, PNG, DOC, DOCX formatidagi fayllarni yuklashingiz mumkin. Maksimal hajm 5MB.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/includes/header.php';
