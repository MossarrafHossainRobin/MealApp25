<?php
session_start();

class Auth
{
    private $db;

    public function __construct()
    {
        require_once 'database.php';
        $this->db = new Database();
    }

    public function login($email, $password)
    {
        $connection = $this->db->getConnection();
        $stmt = $connection->prepare("SELECT * FROM admins WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // For plain text passwords
        if ($admin && $password === $admin['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['login_time'] = time();
            return true;
        }
        return false;
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }

    public function logout()
    {
        session_destroy();
        // Redirect to the same page (admin.php) which will show login form
        header("Location: admin.php");
        exit;
    }

    public function checkSession()
    {
        if (!$this->isLoggedIn()) {
            // Don't redirect, just let admin.php handle showing the login form
            return;
        }

        // Session timeout after 1 hour
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > 3600)) {
            $this->logout();
        }

        // Update session time
        $_SESSION['login_time'] = time();
    }
}
?>