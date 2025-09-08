<?php
require_once 'Base.php';

class LogoutPage extends Base {
    
    public function __construct() {
        parent::__construct("Logout - Mitsuki's Place");
        $this->handleLogout();
    }
    
    private function handleLogout() {
        // Simpan username sebelum logout untuk pesan
        $username = $_SESSION['username'] ?? 'Guest';
        
        // Lakukan logout
        $this->logout();
        
        // Set pesan logout
        session_start();
        $_SESSION['logout_message'] = $username;
        
        // Redirect setelah 3 detik atau langsung klik link
        header("refresh:2;url=index.php");
    }
    
    public function render() {
        echo $this->renderHeader();
        echo $this->renderLogoutMessage();
        echo $this->renderFooter();
    }
    
    private function renderLogoutMessage() {
        $username = $_SESSION['logout_message'] ?? 'Guest';
        unset($_SESSION['logout_message']); // Hapus pesan setelah digunakan
        
        return '
        <div class="form-container" style="text-align: center;">
            <h2 style="color: #4CAF50; margin-bottom: 30px;">
                Logout Berhasil!
            </h2>
            
            <div style="background-color: #d4edda; padding: 25px; border-radius: 10px; margin-bottom: 30px;">
                <h3 style="color: #155724; margin-top: 0;">
                    Terima kasih, ' . htmlspecialchars($username) . '!
                </h3>
                <p style="color: #155724; font-size: 16px; line-height: 1.6;">
                    Anda telah berhasil keluar dari sistem Mitsuki\'s Place.<br>
                    Semua sesi Anda telah dihapus dengan aman.
                </p>
            </div>
            
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
                <p style="color: #6c757d; margin: 0;">
                    🔄 Anda akan diarahkan ke halaman login dalam <span id="countdown">2</span> detik...
                </p>
            </div>
            
            <div>
                <a href="index.php" style="background-color: #4CAF50; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-size: 16px; display: inline-block;">
                    🔑 Login Kembali
                </a>
            </div>
            
            <div style="margin-top: 30px; padding: 20px; background-color: #e3f2fd; border-radius: 10px;">
                <h4 style="color: #1976d2; margin-top: 0;">🌟 Sampai jumpa lagi!</h4>
                <p style="color: #1565c0; margin-bottom: 0;">
                    Mitsuki\'s Place selalu siap melayani kebutuhan pengiriman Anda. 
                    Terima kasih telah menggunakan layanan kami!
                </p>
            </div>
        </div>
        
        <script>
            let countdown = 2;
            const countdownElement = document.getElementById("countdown");
            
            const timer = setInterval(() => {
                countdown--;
                countdownElement.textContent = countdown;
                
                if (countdown <= 0) {
                    clearInterval(timer);
                    window.location.href = "index.php";
                }
            }, 1000);
        </script>';
    }
}

// Jalankan halaman logout
$logoutPage = new LogoutPage();
$logoutPage->render();
?>