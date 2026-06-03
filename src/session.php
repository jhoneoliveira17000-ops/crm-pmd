<?php
// PMDCRM/src/session.php
// Custom database session handler for TiDB Cloud

require_once __DIR__ . '/db.php';

class DatabaseSessionHandler implements SessionHandlerInterface {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function open($savePath, $sessionName): bool {
        return true;
    }

    public function close(): bool {
        return true;
    }

    public function read($id): string {
        try {
            $stmt = $this->pdo->prepare("SELECT data FROM sessoes WHERE id = ? AND expires_at > ?");
            $stmt->execute([$id, time()]);
            $data = $stmt->fetchColumn();
            return $data ? $data : '';
        } catch (\Exception $e) {
            error_log("Session read error: " . $e->getMessage());
            return '';
        }
    }

    public function write($id, $data): bool {
        try {
            $expires = time() + 31536000; // 1 year session lifetime
            $stmt = $this->pdo->prepare("
                INSERT INTO sessoes (id, data, expires_at) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE data = VALUES(data), expires_at = VALUES(expires_at)
            ");
            return $stmt->execute([$id, $data, $expires]);
        } catch (\Exception $e) {
            error_log("Session write error: " . $e->getMessage());
            return false;
        }
    }

    public function destroy($id): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM sessoes WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (\Exception $e) {
            error_log("Session destroy error: " . $e->getMessage());
            return false;
        }
    }

    public function gc($maxLifetime): int|false {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM sessoes WHERE expires_at < ?");
            $stmt->execute([time()]);
            return $stmt->rowCount();
        } catch (\Exception $e) {
            error_log("Session gc error: " . $e->getMessage());
            return false;
        }
    }
}

// Register database session handler before session starts
if (session_status() === PHP_SESSION_NONE) {
    $handler = new DatabaseSessionHandler($pdo);
    session_set_save_handler($handler, true);
}
