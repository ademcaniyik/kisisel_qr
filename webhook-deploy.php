<?php
/**
 * GitHub Webhook Deployment Script - Timeout Optimized
 * Hızlı deployment için optimize edilmiş webhook handler
 * 
 * @author GitHub Copilot & User
 * @version 1.2
 */

// Zaman limitini artır ve hızlı başlangıç
set_time_limit(0); // Unlimited execution time
ignore_user_abort(true); // Continue even if user disconnects

// Hata raporlamayı kapat (production)
error_reporting(0);
ini_set('display_errors', 0);

// Konfigürasyon
define('REPO_URL', 'https://github.com/ademcaniyik/kisisel_qr.git');
define('TARGET_DIR', '/home/acd1f4ftwarecom/acdisoftware.com.tr/kisisel_qr');
define('LOG_FILE', '/home/acd1f4ftwarecom/logs/deployment.log');
define('BACKUP_DIR', '/home/acd1f4ftwarecom/backups');
define('ALLOWED_BRANCHES', ['main', 'master']);
define('ALLOWED_REPOS', ['kisisel_qr']);

// Korunacak dosya ve dizinler
define('PROTECTED_PATHS', [
    '.env',
    '.env.local',
    '.env.production',
    'logs/',
    'public/uploads/',
    'public/qr_codes/',
    'vendor/',
    '.htaccess',
    'config/local.php'
]);

/**
 * Hızlı log yazma
 */
function quickLog($message, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$level] $message\n";
    
    // Async log yazma - blocking olmayan
    file_put_contents(LOG_FILE, $logMessage, FILE_APPEND | LOCK_EX);
}

/**
 * Hızlı GitHub doğrulama
 */
function quickValidateGitHub() {
    // Method check
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        die('Method not allowed');
    }
    
    // User-Agent check
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (!str_contains($userAgent, 'GitHub-Hookshot')) {
        http_response_code(403);
        die('Not from GitHub');
    }
    
    // Event check
    $event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
    if ($event !== 'push') {
        http_response_code(200);
        die('Not a push event');
    }
    
    return true;
}

/**
 * Hızlı payload işleme
 */
function quickProcessPayload() {
    $payload = file_get_contents('php://input');
    if (empty($payload)) {
        http_response_code(400);
        die('Empty payload');
    }
    
    $data = json_decode($payload, true);
    if (!$data) {
        http_response_code(400);
        die('Invalid JSON');
    }
    
    // Repository check
    $repoName = $data['repository']['name'] ?? '';
    if (!in_array($repoName, ALLOWED_REPOS)) {
        http_response_code(200);
        die("Repository not allowed: $repoName");
    }
    
    // Branch check
    $branch = str_replace('refs/heads/', '', $data['ref'] ?? '');
    if (!in_array($branch, ALLOWED_BRANCHES)) {
        http_response_code(200);
        die("Branch not allowed: $branch");
    }
    
    return ['repository' => $repoName, 'branch' => $branch, 'data' => $data];
}

/**
 * Background deployment başlat
 */
function startBackgroundDeployment($branch, $commitId) {
    // PHP scriptini background'da çalıştır
    $command = "nohup php -f " . __FILE__ . " deploy $branch $commitId > /dev/null 2>&1 &";
    exec($command);
    
    quickLog("Background deployment başlatıldı - Branch: $branch, Commit: $commitId");
}

/**
 * Actual deployment (background'da çalışır)
 */
function performActualDeployment($branch, $commitId) {
    quickLog("=== DEPLOYMENT BAŞLADI ===");
    quickLog("Branch: $branch, Commit: $commitId");
    
    try {
        // 1. Hızlı korumalı dosya backup
        $protectedBackup = quickBackupProtectedFiles();
        
        // 2. Git operations
        if (!is_dir(TARGET_DIR)) {
            // İlk clone
            $parentDir = dirname(TARGET_DIR);
            $dirName = basename(TARGET_DIR);
            
            if (!is_dir($parentDir)) {
                mkdir($parentDir, 0755, true);
            }
            
            $command = "cd " . escapeshellarg($parentDir) . " && git clone --depth 1 --branch $branch " . 
                      escapeshellarg(REPO_URL) . " " . escapeshellarg($dirName) . " 2>&1";
            
            quickLog("Clone: $command");
        } else {
            // Hızlı update
            $commands = [
                "cd " . escapeshellarg(TARGET_DIR),
                "git fetch origin $branch",
                "git reset --hard origin/$branch",
                "git clean -fd"
            ];
            
            $command = implode(' && ', $commands) . ' 2>&1';
            quickLog("Update: $command");
        }
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception("Git failed: " . implode("\n", $output));
        }
        
        // 3. Korumalı dosyaları geri yükle
        quickRestoreProtectedFiles($protectedBackup);
        
        // 4. Hızlı permission fix
        exec("chmod -R 755 " . escapeshellarg(TARGET_DIR) . " 2>/dev/null");
        exec("chmod -R 777 " . escapeshellarg(TARGET_DIR . '/public/uploads') . " 2>/dev/null");
        exec("chmod -R 777 " . escapeshellarg(TARGET_DIR . '/public/qr_codes') . " 2>/dev/null");
        
        quickLog("=== DEPLOYMENT BAŞARILI ===");
        
    } catch (Exception $e) {
        quickLog("DEPLOYMENT HATASI: " . $e->getMessage(), 'ERROR');
    }
}

/**
 * Hızlı protected files backup
 */
function quickBackupProtectedFiles() {
    $tempDir = sys_get_temp_dir() . '/deploy_' . uniqid();
    mkdir($tempDir, 0755);
    
    $saved = [];
    foreach (PROTECTED_PATHS as $path) {
        $fullPath = TARGET_DIR . '/' . $path;
        if (file_exists($fullPath)) {
            $tempPath = $tempDir . '/' . $path;
            $tempDir2 = dirname($tempPath);
            
            if (!is_dir($tempDir2)) {
                mkdir($tempDir2, 0755, true);
            }
            
            if (is_dir($fullPath)) {
                exec("cp -r " . escapeshellarg($fullPath) . " " . escapeshellarg($tempPath));
            } else {
                exec("cp " . escapeshellarg($fullPath) . " " . escapeshellarg($tempPath));
            }
            $saved[] = $path;
        }
    }
    
    return ['temp_dir' => $tempDir, 'saved' => $saved];
}

/**
 * Hızlı protected files restore
 */
function quickRestoreProtectedFiles($backup) {
    foreach ($backup['saved'] as $path) {
        $tempPath = $backup['temp_dir'] . '/' . $path;
        $fullPath = TARGET_DIR . '/' . $path;
        
        if (file_exists($tempPath)) {
            $fullDir = dirname($fullPath);
            if (!is_dir($fullDir)) {
                mkdir($fullDir, 0755, true);
            }
            
            if (is_dir($tempPath)) {
                if (is_dir($fullPath)) {
                    exec("rm -rf " . escapeshellarg($fullPath));
                }
                exec("cp -r " . escapeshellarg($tempPath) . " " . escapeshellarg($fullPath));
            } else {
                exec("cp " . escapeshellarg($tempPath) . " " . escapeshellarg($fullPath));
            }
        }
    }
    
    // Cleanup
    exec("rm -rf " . escapeshellarg($backup['temp_dir']));
}

// ===== MAIN EXECUTION =====

// Log dizini oluştur
$logDir = dirname(LOG_FILE);
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

// Command line argument kontrolü (background deployment için)
if (isset($argv[1]) && $argv[1] === 'deploy') {
    $branch = $argv[2] ?? 'main';
    $commitId = $argv[3] ?? 'unknown';
    performActualDeployment($branch, $commitId);
    exit(0);
}

// Normal webhook işleme
try {
    // Hemen response gönder (GitHub timeout'unu önle)
    quickValidateGitHub();
    $payload = quickProcessPayload();
    
    // GitHub'a hızlı başarı response'u gönder
    http_response_code(200);
    echo json_encode([
        'status' => 'accepted',
        'message' => 'Deployment started in background',
        'repository' => $payload['repository'],
        'branch' => $payload['branch'],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
    // Output buffer'ı flush et
    if (ob_get_level()) {
        ob_end_flush();
    }
    flush();
    
    // Artık GitHub'a response gönderildi, background deployment başlat
    $commitId = $payload['data']['head_commit']['id'] ?? 'unknown';
    startBackgroundDeployment($payload['branch'], $commitId);
    
    quickLog("Webhook processed successfully - Background deployment started");
    
} catch (Exception $e) {
    quickLog("Webhook error: " . $e->getMessage(), 'ERROR');
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Internal error']);
} catch (Error $e) {
    quickLog("Fatal error: " . $e->getMessage(), 'CRITICAL');
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Fatal error']);
}
?>