<?php
// admin/api/log_action.php
session_start();
require_once __DIR__ . '/../../includes/utilities.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);
    exit();
}
$action = $_POST['action'] ?? '';
$details = $_POST;
unset($details['action']);
Utilities::logAdminAction($action, $details);
echo json_encode(['success' => true]);
