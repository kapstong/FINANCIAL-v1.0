<?php
session_start();
require_once '../config/database.php';

class Auth {
    private $db;
    private $maxAttempts = 5;
    private $lockoutTime = 60; // 60 seconds

    public function __construct() {
        $this->db = new Database();
    }

    public function checkLockout() {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['lockout_time'] = 0;
        }

        if ($_SESSION['lockout_time'] > 0 && time() < $_SESSION['lockout_time']) {
            $remaining = $_SESSION['lockout_time'] - time();
            return ['locked' => true, 'remaining' => $remaining];
        }

        if ($_SESSION['lockout_time'] > 0 && time() >= $_SESSION['lockout_time']) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['lockout_time'] = 0;
        }

        return ['locked' => false, 'remaining' => 0];
    }

    public function login($username, $password) {
        $lockout = $this->checkLockout();
        if ($lockout['locked']) {
            return ['success' => false, 'error' => 'Account locked', 'lockout' => $lockout];
        }

        $user = $this->db->fetchOne(
            "SELECT u.*, r.name as role_name FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE u.username = ?", 
            [$username]
        );

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['role_name'] = $user['role_name'];
            $_SESSION['login_attempts'] = 0;
            $_SESSION['lockout_time'] = 0;
            return ['success' => true, 'user' => $user];
        }

        $_SESSION['login_attempts']++;
        if ($_SESSION['login_attempts'] >= $this->maxAttempts) {
            $_SESSION['lockout_time'] = time() + $this->lockoutTime;
        }

        return ['success' => false, 'error' => 'Invalid credentials', 'attempts' => $_SESSION['login_attempts']];
    }

    public function register($username, $password, $role_id = 2) {
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }

        $existing = $this->db->fetchOne("SELECT id FROM users WHERE username = ?", [$username]);
        if ($existing) {
            return ['success' => false, 'message' => 'Username already exists'];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $this->db->execute(
            "INSERT INTO users (username, password_hash, role_id, created_at) VALUES (?, ?, ?, NOW())",
            [$username, $hash, $role_id]
        );

        return ['success' => true, 'message' => 'User created successfully'];
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin() {
        return $this->isLoggedIn() && $_SESSION['role_id'] == 1;
    }

    public function getCurrentUser() {
        if (!$this->isLoggedIn()) return null;
        
        $user = $this->db->fetchOne(
            "SELECT u.*, r.name as role_name FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE u.id = ?", 
            [$_SESSION['user_id']]
        );
        
        return $user;
    }

    public function updateProfileImage($userId, $imagePath) {
        try {
            $this->db->execute(
                "UPDATE users SET profile_image = ? WHERE id = ?",
                [$imagePath, $userId]
            );
            return ['success' => true, 'message' => 'Profile image updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to update profile image: ' . $e->getMessage()];
        }
    }

    public function changePassword($userId, $currentPassword, $newPassword) {
        $user = $this->db->fetchOne("SELECT password_hash FROM users WHERE id = ?", [$userId]);
        
        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'New password must be at least 6 characters'];
        }
        
        try {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->db->execute(
                "UPDATE users SET password_hash = ? WHERE id = ?",
                [$hash, $userId]
            );
            return ['success' => true, 'message' => 'Password changed successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to change password: ' . $e->getMessage()];
        }
    }
    
    public function updateUserPreferences($userId, $preferences) {
        try {
            $this->db->execute(
                "UPDATE users SET 
                 email_notifications = ?, 
                 auto_save = ?, 
                 system_alerts = ?, 
                 financial_reports = ?, 
                 security_alerts = ?,
                 updated_at = NOW()
                 WHERE id = ?",
                [
                    $preferences['email_notifications'] ?? 0,
                    $preferences['auto_save'] ?? 0,
                    $preferences['system_alerts'] ?? 0,
                    $preferences['financial_reports'] ?? 0,
                    $preferences['security_alerts'] ?? 0,
                    $userId
                ]
            );
            return ['success' => true, 'message' => 'Preferences updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to update preferences: ' . $e->getMessage()];
        }
    }
    
    public function getUserPreferences($userId) {
        $user = $this->db->fetchOne(
            "SELECT email_notifications, auto_save, system_alerts, financial_reports, security_alerts 
             FROM users WHERE id = ?", 
            [$userId]
        );
        
        if ($user) {
            return [
                'email_notifications' => (bool)$user['email_notifications'],
                'auto_save' => (bool)$user['auto_save'],
                'system_alerts' => (bool)$user['system_alerts'],
                'financial_reports' => (bool)$user['financial_reports'],
                'security_alerts' => (bool)$user['security_alerts']
            ];
        }
        
        return [
            'email_notifications' => true,
            'auto_save' => true,
            'system_alerts' => true,
            'financial_reports' => true,
            'security_alerts' => true
        ];
    }
    
    public function getDashboardData() {
        try {
            // Get current month and year
            $currentMonth = date('Y-m');
            $currentYear = date('Y');
            
            // 1. Cash Balance (from General Ledger)
            $cashBalance = $this->db->fetchOne(
                "SELECT 
                    COALESCE(SUM(CASE WHEN je.type = 'debit' THEN je.amount ELSE 0 END), 0) -
                    COALESCE(SUM(CASE WHEN je.type = 'credit' THEN je.amount ELSE 0 END), 0) as cash_balance
                FROM journal_entries je
                JOIN accounts a ON je.account_id = a.id
                WHERE a.name = 'Cash' AND je.status = 'posted'"
            );
            
            // 2. Accounts Receivable Outstanding
            $arOutstanding = $this->db->fetchOne(
                "SELECT COALESCE(SUM(amount), 0) as ar_outstanding
                FROM accounts_receivable 
                WHERE status = 'open' AND due_date >= CURDATE()"
            );
            
            // 3. Accounts Payable Outstanding
            $apOutstanding = $this->db->fetchOne(
                "SELECT COALESCE(SUM(amount), 0) as ap_outstanding
                FROM accounts_payable 
                WHERE status = 'open' AND due_date >= CURDATE()"
            );
            
            // 4. Revenue Month-to-Date
            $revenueMTD = $this->db->fetchOne(
                "SELECT COALESCE(SUM(amount), 0) as revenue_mtd
                FROM journal_entries je
                JOIN accounts a ON je.account_id = a.id
                WHERE a.type = 'revenue' 
                AND je.type = 'credit'
                AND je.status = 'posted'
                AND DATE_FORMAT(je.entry_date, '%Y-%m') = ?",
                [$currentMonth]
            );
            
            // 5. Recent Transactions (last 10)
            $recentTransactions = $this->db->fetchAll(
                "SELECT 
                    je.entry_date as date,
                    je.description,
                    CASE 
                        WHEN je.type = 'credit' AND a.type = 'revenue' THEN 'Income'
                        WHEN je.type = 'debit' AND a.type = 'expense' THEN 'Expense'
                        ELSE 'Transaction'
                    END as type,
                    je.amount,
                    je.type as entry_type
                FROM journal_entries je
                JOIN accounts a ON je.account_id = a.id
                WHERE je.status = 'posted'
                ORDER BY je.entry_date DESC
                LIMIT 10"
            );
            
            // 6. Sales Trend Data (last 30 days)
            $salesTrend = $this->db->fetchAll(
                "SELECT 
                    DATE(je.entry_date) as date,
                    COALESCE(SUM(CASE WHEN je.type = 'credit' AND a.type = 'revenue' THEN je.amount ELSE 0 END), 0) as sales
                FROM journal_entries je
                JOIN accounts a ON je.account_id = a.id
                WHERE a.type = 'revenue' 
                AND je.status = 'posted'
                AND je.entry_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY DATE(je.entry_date)
                ORDER BY date DESC"
            );
            
            // 7. Monthly Summary
            $monthlySummary = $this->db->fetchOne(
                "SELECT 
                    COALESCE(SUM(CASE WHEN je.type = 'credit' AND a.type = 'revenue' THEN je.amount ELSE 0 END), 0) as total_revenue,
                    COALESCE(SUM(CASE WHEN je.type = 'debit' AND a.type = 'expense' THEN je.amount ELSE 0 END), 0) as total_expenses,
                    COALESCE(SUM(CASE WHEN je.type = 'credit' AND a.type = 'revenue' THEN je.amount ELSE 0 END), 0) -
                    COALESCE(SUM(CASE WHEN je.type = 'debit' AND a.type = 'expense' THEN je.amount ELSE 0 END), 0) as net_income
                FROM journal_entries je
                JOIN accounts a ON je.account_id = a.id
                WHERE je.status = 'posted'
                AND DATE_FORMAT(je.entry_date, '%Y-%m') = ?",
                [$currentMonth]
            );
            
            return [
                'success' => true,
                'data' => [
                    'cash_balance' => $cashBalance['cash_balance'] ?? 0,
                    'ar_outstanding' => $arOutstanding['ar_outstanding'] ?? 0,
                    'ap_outstanding' => $apOutstanding['ap_outstanding'] ?? 0,
                    'revenue_mtd' => $revenueMTD['revenue_mtd'] ?? 0,
                    'recent_transactions' => $recentTransactions,
                    'sales_trend' => $salesTrend,
                    'monthly_summary' => $monthlySummary,
                    'current_month' => $currentMonth,
                    'current_year' => $currentYear
                ]
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to fetch dashboard data: ' . $e->getMessage(),
                'data' => [
                    'cash_balance' => 0,
                    'ar_outstanding' => 0,
                    'ap_outstanding' => 0,
                    'revenue_mtd' => 0,
                    'recent_transactions' => [],
                    'sales_trend' => [],
                    'monthly_summary' => ['total_revenue' => 0, 'total_expenses' => 0, 'net_income' => 0],
                    'current_month' => $currentMonth ?? date('Y-m'),
                    'current_year' => $currentYear ?? date('Y')
                ]
            ];
        }
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        session_write_close();
        setcookie(session_name(), '', 0, '/');
        session_regenerate_id(true);
        return true;
    }

    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
    }

    public function requireAdmin() {
        $this->requireAuth();
        if (!$this->isAdmin()) {
            header('Location: dashboard.php');
            exit();
        }
    }
}
?>
