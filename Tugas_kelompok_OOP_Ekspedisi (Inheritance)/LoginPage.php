<?php
require_once 'Base.php';

class LoginPage extends Base {
    private $error_message = '';
    
    public function __construct() {
        parent::__construct("Login - Mitsuki's Place");
        $this->handleLogin();
    }
    
    private function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if ($this->login($username, $password)) {
                header('Location: menu.php');
                exit;
            } else {
                $this->error_message = 'Username atau password salah!';
            }
        }
        
        // Jika sudah login, redirect ke menu
        if ($this->isLoggedIn()) {
            header('Location: menu.php');
            exit;
        }
    }
    
    public function render() {
        echo $this->renderHeader();
        echo $this->renderLoginForm();
        echo $this->renderFooter();
    }
    
    private function renderLoginForm() {
        $errorDiv = '';
        if ($this->error_message) {
            $errorDiv = '<div class="error-message">' . $this->error_message . '</div>';
        }
        
        return '
        <div class="form-container">
            <h2 style="text-align: center; color: #4CAF50; margin-bottom: 30px;">
                Selamat Datang di Mitsuki\'s Place
            </h2>
            ' . $errorDiv . '
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required 
                           placeholder="Masukkan username Anda">
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Masukkan password Anda">
                </div>
                
                <input type="submit" value="Login">
            </form>
        </div>';
    }
}

// Jalankan halaman login
$loginPage = new LoginPage();
$loginPage->render();
?>