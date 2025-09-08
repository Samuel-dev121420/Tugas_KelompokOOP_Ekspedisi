<?php
require_once 'Base.php';

class MenuPage extends Base {
    public function __construct() {
        parent::__construct("Menu Utama - Mitsuki's Place");

        // Cek apakah user sudah login
        if (!$this->isLoggedIn()) {
            header('Location: index.php');
            exit;
        }
    }

    public function render() {
        echo $this->renderHeader();
        echo $this->renderNavbar();

        echo '
        <div class="container">
            <h2 style="text-align:center; color:black; margin-top: 100px; margin-bottom:30px;">
                Selamat Datang di Menu Utama
            </h2>
            <div style="text-align:center;">
                <a href="shipping.php" class="btn-shipping"> Masuk Form Pengiriman</a>
            </div>
        </div>
        ';

        echo $this->renderFooter();
    }
}

$menuPage = new MenuPage();
$menuPage->render();
