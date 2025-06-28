<?php
/**
 * GitHub Webhook Deployment Script - Public Repo Version
 * Otomatik deployment için GitHub webhook handler (Secret key gerektirmez)
 * 
 * @author GitHub Copilot & User
 * @version 1.1
 */

// Hata raporlamayı aç (production'da kapatılabilir)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Konfigürasyon
define('REPO_URL', 'https://github.com/ademcaniyik/kisisel_qr.git');
define('TARGET_DIR', '/home/acd1f4ftwarecom/acdisoftware.com.tr/kisisel_qr');
define('LOG_FILE', '/home/acd1f4ftwarecom/logs/deployment.log');
define('BACKUP_DIR', '/home/acd1f4ftwarecom/backups');
define('ALLOWED_BRANCHES', ['main', 'master']); // İzin verilen branch'ler
define('ALLOWED_REPOS', ['kisisel_qr']); // İzin verilen repo isimleri

// Korunacak dosya ve dizinler (deployment sırasında silinmeyecek)
define('PROTECTED_PATHS', [
    '.env',
    '.env.local',
    '.env.production',
    'logs/',
    'public/uploads/',
    'public/qr_codes/',
    'vendor/',
    '.htaccess',
    'config/local.php',
    'storage/',
    'cache/'
]);

/**
 * Log mesajı yaz
 */
function writeLog($message, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
    
    // Log dizini yoksa oluştur
    $logDir = dirname(LOG_FILE);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents(LOG_FILE, $logMessage, FILE_APPEND | LOCK_EX);
    
    // Console'a da yazdır (debug için)
    echo $logMessage;
}

/**
 * GitHub'dan gelen request mi kontrol et
 */
function isValidGitHubRequest() {
    // User-Agent kontrolü
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (!str_contains($userAgent, 'GitHub-Hookshot')) {
        writeLog("Geçersiz User-Agent: $userAgent", 'WARNING');
        return false;
    }
    
    // GitHub event header kontrolü
    $event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
    if ($event !== 'push') {
        writeLog("Push event değil: $event", 'WARNING');
        return false;
    }
    
    return true;
}

/**
 * Backup oluştur
 */
function createBackup() {
    $backupName = 'backup_' . date('Y-m-d_H-i-s');
    $backupPath = BACKUP_DIR . '/' . $backupName;
    
    // Backup dizini yoksa oluştur
    if (!is_dir(BACKUP_DIR)) {
        mkdir(BACKUP_DIR, 0755, true);
    }
    
    // Sadece son 5 backup'ı tut
    $backups = glob(BACKUP_DIR . '/backup_*');
    if (count($backups) >= 5) {
        // En eski backup'ları sil
        usort($backups, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });
        
        for ($i = 0; $i < count($backups) - 4; $i++) {
            exec("rm -rf " . escapeshellarg($backups[$i]));
            writeLog("Eski backup silindi: " . basename($backups[$i]));
        }
    }
    
    // Mevcut dizini backup'la (sadece dizin varsa)
    if (is_dir(TARGET_DIR)) {
        $command = "cp -r " . escapeshellarg(TARGET_DIR) . " " . escapeshellarg($backupPath);
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0) {
            writeLog("Backup oluşturuldu: $backupName");
            return $backupPath;
        } else {
            writeLog("Backup oluşturulamadı!", 'ERROR');
            return false;
        }
    } else {
        writeLog("İlk deployment - backup gerekmiyor");
        return true;
    }
}

/**
 * Korumalı dosyaları geçici olarak kaydet
 */
function saveProtectedFiles() {
    $tempDir = sys_get_temp_dir() . '/deployment_protected_' . uniqid();
    mkdir($tempDir, 0755, true);
    
    $savedFiles = [];
    
    foreach (PROTECTED_PATHS as $path) {
        $fullPath = TARGET_DIR . '/' . $path;
        
        if (file_exists($fullPath)) {
            $tempPath = $tempDir . '/' . $path;
            $tempPathDir = dirname($tempPath);
            
            if (!is_dir($tempPathDir)) {
                mkdir($tempPathDir, 0755, true);
            }
            
            if (is_dir($fullPath)) {
                $command = "cp -r " . escapeshellarg($fullPath) . " " . escapeshellarg($tempPath);
            } else {
                $command = "cp " . escapeshellarg($fullPath) . " " . escapeshellarg($tempPath);
            }
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0) {
                $savedFiles[] = $path;
                writeLog("Korumalı dosya kaydedildi: $path");
            }
        }
    }
    
    return ['temp_dir' => $tempDir, 'saved_files' => $savedFiles];
}

/**
 * Korumalı dosyaları geri yükle
 */
function restoreProtectedFiles($protectedData) {
    $tempDir = $protectedData['temp_dir'];
    $savedFiles = $protectedData['saved_files'];
    
    foreach ($savedFiles as $path) {
        $tempPath = $tempDir . '/' . $path;
        $fullPath = TARGET_DIR . '/' . $path;
        
        if (file_exists($tempPath)) {
            $fullPathDir = dirname($fullPath);
            
            if (!is_dir($fullPathDir)) {
                mkdir($fullPathDir, 0755, true);
            }
            
            if (is_dir($tempPath)) {
                // Hedef dizin varsa sil
                if (is_dir($fullPath)) {
                    exec("rm -rf " . escapeshellarg($fullPath));
                }
                $command = "cp -r " . escapeshellarg($tempPath) . " " . escapeshellarg($fullPath);
            } else {
                $command = "cp " . escapeshellarg($tempPath) . " " . escapeshellarg($fullPath);
            }
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0) {
                writeLog("Korumalı dosya geri yüklendi: $path");
            } else {
                writeLog("Korumalı dosya geri yüklenemedi: $path", 'ERROR');
            }
        }
    }
    
    // Geçici dizini temizle
    exec("rm -rf " . escapeshellarg($tempDir));
}

/**
 * Git deployment yap
 */
function deployFromGit($branch = 'main') {
    writeLog("Deployment başlıyor - Branch: $branch");
    
    // 1. Backup oluştur
    $backupPath = createBackup();
    if ($backupPath === false) {
        return false;
    }
    
    // 2. Korumalı dosyaları kaydet
    $protectedData = saveProtectedFiles();
    
    try {
        // 3. Hedef dizine git
        if (!is_dir(TARGET_DIR)) {
            // İlk kez clone
            $parentDir = dirname(TARGET_DIR);
            $dirName = basename(TARGET_DIR);
            
            // Parent dizin yoksa oluştur
            if (!is_dir($parentDir)) {
                mkdir($parentDir, 0755, true);
            }
            
            $command = "cd " . escapeshellarg($parentDir) . " && git clone " . 
                      escapeshellarg(REPO_URL) . " " . escapeshellarg($dirName) . " 2>&1";
            
            writeLog("İlk kez clone yapılıyor: $command");
        } else {
            // Mevcut repo'yu güncelle
            $commands = [
                "cd " . escapeshellarg(TARGET_DIR),
                "git fetch origin",
                "git reset --hard origin/$branch",
                "git clean -fd"
            ];
            
            $command = implode(' && ', $commands) . ' 2>&1';
            writeLog("Repo güncelleniyor: $command");
        }
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception("Git komutu başarısız: " . implode("\n", $output));
        }
        
        writeLog("Git komutu başarılı: " . implode(" | ", $output));
        
        // 4. Korumalı dosyaları geri yükle
        restoreProtectedFiles($protectedData);
        
        // 5. Dosya izinlerini düzelt
        exec("find " . escapeshellarg(TARGET_DIR) . " -type f -name '*.php' -exec chmod 644 {} \; 2>/dev/null");
        exec("find " . escapeshellarg(TARGET_DIR) . " -type d -exec chmod 755 {} \; 2>/dev/null");
        
        // 6. Özel dizinler için izin düzeltme
        $specialDirs = ['public/uploads', 'public/qr_codes', 'logs', 'cache'];
        foreach ($specialDirs as $dir) {
            $fullPath = TARGET_DIR . '/' . $dir;
            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0777, true);
            } else {
                chmod($fullPath, 0777);
            }
        }
        
        writeLog("Deployment başarıyla tamamlandı!");
        return true;
        
    } catch (Exception $e) {
        writeLog("Deployment hatası: " . $e->getMessage(), 'ERROR');
        
        // Rollback yap (sadece backup varsa)
        if ($backupPath && is_dir($backupPath)) {
            writeLog("Rollback yapılıyor...", 'WARNING');
            exec("rm -rf " . escapeshellarg(TARGET_DIR));
            exec("mv " . escapeshellarg($backupPath) . " " . escapeshellarg(TARGET_DIR));
            writeLog("Rollback tamamlandı", 'WARNING');
        }
        
        return false;
    }
}

/**
 * Ana webhook handler
 */
function handleWebhook() {
    // POST isteği kontrolü
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        writeLog("Geçersiz HTTP metodu: " . $_SERVER['REQUEST_METHOD'], 'ERROR');
        die('Method not allowed');
    }
    
    // GitHub request kontrolü
    if (!isValidGitHubRequest()) {
        http_response_code(403);
        writeLog("GitHub'dan gelmeyen request", 'ERROR');
        die('Not from GitHub');
    }
    
    // Payload'ı al
    $payload = file_get_contents('php://input');
    if (empty($payload)) {
        http_response_code(400);
        writeLog("Boş payload", 'ERROR');
        die('Empty payload');
    }
    
    // JSON decode
    $data = json_decode($payload, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        writeLog("Geçersiz JSON: " . json_last_error_msg(), 'ERROR');
        die('Invalid JSON');
    }
    
    // Push event kontrolü
    if (!isset($data['ref']) || !isset($data['repository'])) {
        writeLog("Push event değil veya eksik data", 'WARNING');
        die('Not a push event');
    }
    
    // Repository kontrolü
    $repoName = $data['repository']['name'] ?? '';
    if (!in_array($repoName, ALLOWED_REPOS)) {
        writeLog("İzin verilmeyen repository: $repoName", 'WARNING');
        die("Repository not allowed: $repoName");
    }
    
    // Branch kontrolü
    $branch = str_replace('refs/heads/', '', $data['ref']);
    if (!in_array($branch, ALLOWED_BRANCHES)) {
        writeLog("İzin verilmeyen branch: $branch", 'WARNING');
        die("Branch not allowed: $branch");
    }
    
    writeLog("Webhook alındı - Repo: $repoName, Branch: $branch");
    writeLog("Commit: " . ($data['head_commit']['id'] ?? 'unknown'));
    writeLog("Commit message: " . ($data['head_commit']['message'] ?? 'unknown'));
    
    // Deployment yap
    $success = deployFromGit($branch);
    
    if ($success) {
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'Deployment completed successfully',
            'repository' => $repoName,
            'branch' => $branch,
            'commit' => $data['head_commit']['id'] ?? 'unknown',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Deployment failed',
            'repository' => $repoName,
            'branch' => $branch,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}

// Script başlangıcı
writeLog("=== Webhook Script Başlatıldı ===");
writeLog("IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
writeLog("User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'));

// Ana fonksiyonu çalıştır
try {
    handleWebhook();
} catch (Exception $e) {
    writeLog("Kritik hata: " . $e->getMessage(), 'CRITICAL');
    http_response_code(500);
    die('Internal server error');
} catch (Error $e) {
    writeLog("Fatal error: " . $e->getMessage(), 'CRITICAL');
    http_response_code(500);
    die('Fatal error');
}

writeLog("=== Webhook Script Tamamlandı ===");
?>