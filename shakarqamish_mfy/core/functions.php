<?php
/**
 * ShakarqamishMFY.uz - Core Functions & Security
 * Professional helper functions with security features
 */

// Start secure session
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
        ini_set('session.cookie_samesite', 'Strict');
        session_start();
    }
}

// CSRF Token Generation
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF Token Verification
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// XSS Protection - Sanitize Output
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// SQL Injection Protection - Already handled by PDO prepared statements
// This is additional sanitization for inputs
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return trim(strip_tags($input));
}

// Password Hashing
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

// Password Verification
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Rate Limiting
function checkRateLimit($action, $limit = 5, $timeframe = 300) {
    $key = 'rate_' . $action . '_' . $_SERVER['REMOTE_ADDR'];
    $attempts = $_SESSION[$key] ?? ['count' => 0, 'reset' => time() + $timeframe];
    
    if (time() > $attempts['reset']) {
        $attempts = ['count' => 0, 'reset' => time() + $timeframe];
    }
    
    $attempts['count']++;
    $_SESSION[$key] = $attempts;
    
    return $attempts['count'] <= $limit;
}

// IP Address Logging
function getClientIP() {
    $ip = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

// User Agent Sanitization
function getUserAgent() {
    return substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);
}

// Redirect Helper
function redirect($url) {
    header("Location: " . $url);
    exit;
}

// JSON Response
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// File Upload Validation
function validateFileUpload($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'], $maxSize = 5242880) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }
    
    // Check file size
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File too large (max 5MB)'];
    }
    
    // Check file extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowedMimes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    
    if (!in_array($mimeType, $allowedMimes)) {
        return ['success' => false, 'message' => 'Invalid MIME type'];
    }
    
    return ['success' => true];
}

// Generate Unique ID
function generateTicketId() {
    return 'APPEAL-' . strtoupper(substr(uniqid(), -6)) . '-' . time();
}

// Slug Generation (for SEO URLs)
function createSlug($string) {
    $string = mb_strtolower(trim($string), 'UTF-8');
    $string = preg_replace('/[^a-z0-9-]/u', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

// Date Formatting (Uzbek)
function formatDateUZ($date, $format = 'long') {
    $months = [
        'January' => 'Yanvar', 'February' => 'Fevral', 'March' => 'Mart',
        'April' => 'Aprel', 'May' => 'May', 'June' => 'Iyun',
        'July' => 'Iyul', 'August' => 'Avgust', 'September' => 'Sentyabr',
        'October' => 'Oktyabr', 'November' => 'Noyabr', 'December' => 'Dekabr'
    ];
    
    $timestamp = strtotime($date);
    
    if ($format === 'short') {
        return date('d.m.Y', $timestamp);
    } elseif ($format === 'time') {
        return date('H:i', $timestamp);
    } else {
        $month = date('F', $timestamp);
        return date('d', $timestamp) . ' ' . ($months[$month] ?? $month) . ' ' . date('Y', $timestamp);
    }
}

// Number Formatting
function formatNumber($number) {
    return number_format($number, 0, '.', ' ');
}

// Activity Logging
function logActivity($adminId, $action, $description = '', $tableName = null, $recordId = null) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO activity_logs (admin_id, action, description, ip_address, user_agent, table_name, record_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $adminId,
        $action,
        $description,
        getClientIP(),
        getUserAgent(),
        $tableName,
        $recordId
    ]);
}

// Send Notification
function sendNotification($adminId, $title, $message, $type = 'info', $link = null) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO notifications (admin_id, title, message, type, link) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$adminId, $title, $message, $type, $link]);
}

// Track Visitor
function trackVisitor($pageUrl = null) {
    $db = getDB();
    $userAgent = getUserAgent();
    
    // Parse user agent
    $browser = 'Unknown';
    $os = 'Unknown';
    $device = 'Desktop';
    
    if (strpos($userAgent, 'Chrome') !== false) $browser = 'Chrome';
    elseif (strpos($userAgent, 'Firefox') !== false) $browser = 'Firefox';
    elseif (strpos($userAgent, 'Safari') !== false) $browser = 'Safari';
    elseif (strpos($userAgent, 'Opera') !== false) $browser = 'Opera';
    elseif (strpos($userAgent, 'IE') !== false) $browser = 'Internet Explorer';
    
    if (strpos($userAgent, 'Windows') !== false) $os = 'Windows';
    elseif (strpos($userAgent, 'Mac') !== false) $os = 'MacOS';
    elseif (strpos($userAgent, 'Linux') !== false) $os = 'Linux';
    elseif (strpos($userAgent, 'Android') !== false) { $os = 'Android'; $device = 'Mobile'; }
    elseif (strpos($userAgent, 'iOS') !== false) { $os = 'iOS'; $device = 'Mobile'; }
    
    $stmt = $db->prepare("INSERT INTO visitors (ip_address, user_agent, page_url, browser, os, device) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([getClientIP(), $userAgent, $pageUrl ?? $_SERVER['REQUEST_URI'], $browser, $os, $device]);
    
    // Update daily statistics
    $today = date('Y-m-d');
    $stmt = $db->prepare("INSERT INTO statistics (stat_date, page_views, unique_visitors, total_visits) VALUES (?, 1, 1, 1) ON DUPLICATE KEY UPDATE page_views = page_views + 1, total_visits = total_visits + 1");
    $stmt->execute([$today]);
}

// Admin Authentication Check
function requireAdmin() {
    startSecureSession();
    if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
        redirect('/admin/login.php');
    }
}

// Check Admin Role
function hasRole($allowedRoles) {
    if (!isset($_SESSION['admin_role'])) {
        return false;
    }
    if (!is_array($allowedRoles)) {
        $allowedRoles = [$allowedRoles];
    }
    return in_array($_SESSION['admin_role'], $allowedRoles);
}

// Get Site Settings
function getSetting($key, $default = '') {
    static $settings = null;
    
    if ($settings === null) {
        $db = getDB();
        $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    
    return $settings[$key] ?? $default;
}

// Initialize core functions
startSecureSession();
