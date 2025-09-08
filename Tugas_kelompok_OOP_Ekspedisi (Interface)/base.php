<?php
session_start();

// Kelas dasar untuk aplikasi
abstract class Base {
    protected $title;
    protected $db_users;
    
    public function __construct($title = "Mitsuki's Place") {
        $this->title = $title;
        $this->initializeUsers();
    }
    
    // Inisialisasi data user (dalam aplikasi nyata, ini dari database)
    private function initializeUsers() {
        $this->db_users = [
            'Akira' => ['password' => 'admin123', 'role' => 'User'],
            'Fatih' => ['password' => 'Fatih123', 'role' => 'User'],
            'Denis' => ['password' => 'Denis123', 'role' => 'User'],
            'Syahrul' => ['password' => 'Syahrul123', 'role' => 'User']
        ];
    }
    
    // Method untuk render header HTML
    protected function renderHeader() {
        return '<!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . $this->title . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background: linear-gradient(#b8f5b8, white);
                    width: 100%;
                    height: 100%;
                    text-align: center;
                    padding: 20px;
                    margin: 0;
                }
                
                .container {
                    max-width: 800px;
                    margin: 0 auto;
                }
                
                .navbar {
                    background-color: #4CAF50;
                    padding: 15px;
                    border-radius: 10px;
                    margin-bottom: 20px;
                }
                
                .navbar a {
                    color: white;
                    text-decoration: none;
                    margin: 0 15px;
                    padding: 10px 15px;
                    border-radius: 5px;
                    transition: background-color 0.3s;
                }
                
                .navbar a:hover {
                    background-color: #45a049;
                }
                
                .form-container {
                    background-color: white;
                    padding: 30px;
                    margin-top: 10px;
                    border-radius: 15px;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                    text-align: left;
                }
                
                .form-group {
                    margin-bottom: 15px;
                }
                
                label {
                    display: block;
                    margin-bottom: 6px;
                    font-weight: bold;
                }
                
                input, select {
                    width: 100%;
                    padding: 10px;
                    box-sizing: border-box;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                }
                
                input[type="submit"], button {
                    background-color: #4CAF50;
                    color: white;
                    border-radius: 5px;
                    cursor: pointer;
                    border: none;
                    font-size: 16px;
                    width: 100%;
                    margin-top: 10px;
                    padding: 12px;
                    transition: background-color 0.3s;
                }
                
                input[type="submit"]:hover, button:hover {
                    background-color: #45a049;
                }
                
                .success-message {
                    color: #4CAF50;
                    font-weight: bold;
                    font-size: 18px;
                    text-align: center;
                    background-color: #d4edda;
                    padding: 15px;
                    border-radius: 5px;
                    margin: 20px 0;
                }
                
                .error-message {
                    color: #721c24;
                    background-color: #f8d7da;
                    padding: 15px;
                    border-radius: 5px;
                    margin: 20px 0;
                    text-align: center;
                }
                
                .delivery-type {
                    display: flex;
                    justify-content: space-around;
                    margin: 20px 0;
                    text-align: center;
                }
                
                .delivery-type div {
                    flex: 1;
                    margin: 0 10px;
                }
                
                .delivery-type img {
                    width: 100px;
                    height: 100px;
                    object-fit: cover;
                    border-radius: 10px;
                    border: 2px solid #4CAF50;
                }
                
                .user-info {
                    background-color: #e8f5e8;
                    padding: 10px;
                    border-radius: 5px;
                    margin-bottom: 20px;
                    text-align: right;
                }

                .btn-shipping {
                    display: inline-block;
                    padding: 15px 30px;
                    font-size: 18px;
                    font-weight: bold;
                    color: #333;
                    text-decoration: none;
                    border-radius: 10px;
                    background: linear-gradient(#b8f5b8, white);
                    transition: all 0.3s ease;
                    border: 2px solid #4CAF50;
                }

                .btn-shipping:hover {
                    background: linear-gradient(white, #b8f5b8);
                    color: #000;
                    transform: scale(1.05);
                    }

            </style>
        </head>
        <body>
            <div class="container">
                <h1>' . $this->title . '</h1>';
    }
    
    // Method untuk render footer HTML
    protected function renderFooter() {
        return '</div></body></html>';
    }
    
    // Method untuk cek apakah user sudah login
    public function isLoggedIn() {
        return isset($_SESSION['username']) && isset($_SESSION['role']);
    }
    
    // Method untuk login user
    public function login($username, $password) {
        if (isset($this->db_users[$username]) && 
            $this->db_users[$username]['password'] === $password) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $this->db_users[$username]['role'];
            return true;
        }
        return false;
    }
    
    // Method untuk logout
    public function logout() {
        session_unset();
        session_destroy();
    }
    
    // Method untuk render navbar
    protected function renderNavbar() {
        if (!$this->isLoggedIn()) return '';
        
        $username = $_SESSION['username'];
        $role = $_SESSION['role'];
        
        return '<div class="navbar">
            <a href="menu.php">Menu Utama</a>
            <a href="shipping.php">Form Pengiriman</a>
            <a href="logout.php">Logout</a>
            <span style="float: right; color: white;">
                Selamat datang, ' . htmlspecialchars($username) . ' (' . htmlspecialchars($role) . ')
            </span>
        </div>';
    }
    
    // Method abstract yang harus diimplementasi oleh child class
    abstract public function render();
}