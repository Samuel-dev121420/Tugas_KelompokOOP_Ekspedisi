<?php
require_once 'Base.php';

class Pesanan {
    public $nama;
    public $alamat;
    public $jarak;
    public $jangkauan;
    public $jenis_barang;
    public $ukuran_barang;
    public $tanggal;
    public $akun;
    
    public function __construct($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}

class ShippingCalculator {
    private $shipping_costs = [
        "Lokal" => 10000,
        "Domestik" => 30000,
        "Internasional" => 100000
    ];
    
    private $account_discounts = [
        "Reguler" => 0.05,
        "Instant" => 0.07,
        "VIP" => 0.12
    ];
    
    private $size_multipliers = [
        "Kecil" => 1.0,
        "Sedang" => 1.5,
        "Besar" => 2.0
    ];
    
    private $type_multipliers = [
        "Elektronik" => 1.2,
        "Makanan" => 1.0,
        "Benda Hidup" => 1.8
    ];
    
    public function calculateTotal($pesanan) {
        $base_cost = $this->shipping_costs[$pesanan->jangkauan];
        
        // Kalkulasi berdasarkan jarak (tambahan per km)
        $distance_cost = ($pesanan->jarak > 10) ? ($pesanan->jarak - 10) * 500 : 0;
        
        // Kalkulasi berdasarkan ukuran dan jenis barang
        $size_multiplier = $this->size_multipliers[$pesanan->ukuran_barang];
        $type_multiplier = $this->type_multipliers[$pesanan->jenis_barang];
        
        $total_before_discount = ($base_cost + $distance_cost) * $size_multiplier * $type_multiplier;
        
        // Diskon berdasarkan akun
        $discount_rate = $this->account_discounts[$pesanan->akun];
        $discount_amount = $total_before_discount * $discount_rate;
        
        $final_total = $total_before_discount - $discount_amount;
        
        return [
            'total_awal' => $total_before_discount,
            'diskon_akun' => $discount_amount,
            'total_akhir' => $final_total
        ];
    }
    
    public function getShippingCosts() {
        return $this->shipping_costs;
    }
}

class ShippingPage extends Base {
    private $calculator;
    private $pesanan = null;
    private $calculation_result = null;
    
    public function __construct() {
        parent::__construct("Form Pengiriman - Mitsuki's Place");
        
        // Cek apakah user sudah login
        if (!$this->isLoggedIn()) {
            header('Location: index.php');
            exit;
        }
        
        $this->calculator = new ShippingCalculator();
        $this->handleFormSubmission();
    }
    
    private function handleFormSubmission() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->pesanan = new Pesanan($_POST);
            $this->calculation_result = $this->calculator->calculateTotal($this->pesanan);
        }
    }
    
    public function render() {
        echo $this->renderHeader();
        echo $this->renderNavbar();
        
        if ($this->pesanan) {
            echo $this->renderResult();
        } else {
            echo $this->renderForm();
        }
        
        echo $this->renderFooter();
    }
    
    private function renderForm() {
        $shipping_costs = $this->calculator->getShippingCosts();
        
        return '
        <div class="form-container">
            <h2 style="text-align: center; color: #4CAF50; margin-bottom: 30px;">
            Form Pengiriman Barang
            </h2>
            
            <form method="POST">
                <div class="form-group">
                    <label for="nama">Nama Pengirim:</label>
                    <input type="text" id="nama" name="nama" required placeholder="Masukkan nama lengkap">
                </div>

                <div class="form-group">
                    <label for="alamat">Tujuan:</label>
                    <input type="text" id="alamat" name="alamat" required placeholder="Masukkan alamat lengkap tujuan">
                </div>

                <div class="form-group">
                    <label for="jarak">Jarak (km):</label>
                    <input type="number" id="jarak" name="jarak" min="1" required placeholder="Masukkan jarak dalam kilometer">
                </div>

                <div class="form-group">
                    <label for="jangkauan">Jangkauan Pengiriman:</label>
                    <select id="jangkauan" name="jangkauan" required>
                        <option value="">Pilih jangkauan pengiriman</option>
                        <option value="Lokal">Ekspedisi Lokal (Rp ' . number_format($shipping_costs['Lokal'], 0, ",", ".") . ')</option>
                        <option value="Domestik">Ekspedisi Domestik (Rp ' . number_format($shipping_costs['Domestik'], 0, ",", ".") . ')</option>
                        <option value="Internasional">Ekspedisi Internasional (Rp ' . number_format($shipping_costs['Internasional'], 0, ",", ".") . ')</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="jenis_barang">Jenis Barang:</label>
                    <select id="jenis_barang" name="jenis_barang" required>
                        <option value="">Pilih jenis barang</option>
                        <option value="Elektronik">Elektronik (+ 20% biaya)</option>
                        <option value="Makanan">Bahan Makanan (biaya normal)</option>
                        <option value="Benda Hidup">Benda Hidup (+ 80% biaya)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="ukuran_barang">Ukuran Barang:</label>
                    <select id="ukuran_barang" name="ukuran_barang" required>
                        <option value="">Pilih ukuran barang</option>
                        <option value="Kecil">Kecil (biaya normal)</option>
                        <option value="Sedang">Sedang (+ 50% biaya)</option>
                        <option value="Besar">Besar (+ 100% biaya)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tanggal"Tanggal Pengiriman:</label>
                    <input type="date" id="tanggal" name="tanggal" required min="' . date('Y-m-d') . '">
                </div>

                <div class="form-group">
                    <label for="akun">Tipe Akun:</label>
                    <select id="akun" name="akun" required>
                        <option value="">Pilih tipe akun</option>
                        <option value="Reguler"> Reguler (Diskon 5%)</option>
                        <option value="Instant"> Instant (Diskon 7%, Pesanan Anda menjadi Prioritas)</option>
                        <option value="VIP"> VIP (Diskon 12%)</option>
                    </select>
                </div>

                <!-- Gambar Tipe Akun -->
                <div class="delivery-type">
                <div>
               <img src="Reguler.jpg" alt="VIP" style="width: 100px; height: 100px; border-radius: 10px; display: block; margin: 0 auto; border: 2px solid black;">

                <p style="margin-top: 10px; font-weight: bold;">Reguler<br><small>(Diskon 5%)</small></p>
                </div>

                <div>
               <img src="Instant.jpg" alt="VIP" style="width: 100px; height: 100px; border-radius: 10px; display: block; margin: 0 auto; border: 2px solid black;">

                <p style="margin-top: 10px; font-weight: bold;">Instant<br><small>(Diskon 7%, Pesanan Anda menjadi Prioritas)</small></p>
                </div>
                
                <div>
             <img src="King.jpg" alt="VIP" style="width: 100px; height: 100px; border-radius: 10px; display: block; margin: 0 auto; border: 2px solid black;">

                <p style="margin-top: 10px; font-weight: bold;">VIP<br><small>(Diskon 12%)</small></p>
                </div>
                </div>

                <input type="submit" value=" Konfirmasi Pengiriman">
            </form>
        </div>';
    }
    
    private function renderResult() {
        $result = $this->calculation_result;
        
        return '
        <div class="form-container">
            <h2 style="text-align: center; color: #4CAF50; margin-bottom: 30px;">
                ✅ Konfirmasi Pengiriman Berhasil
            </h2>
            
            <div style="background-color: #e8f5e8; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                <h3 style="color: #4CAF50; margin-top: 0;">📋 Detail Pengiriman:</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <p><strong> Nama:</strong> ' . htmlspecialchars($this->pesanan->nama) . '</p>
                    <p><strong> Alamat:</strong> ' . htmlspecialchars($this->pesanan->alamat) . '</p>
                    <p><strong> Jarak:</strong> ' . htmlspecialchars($this->pesanan->jarak) . ' km</p>
                    <p><strong> Jangkauan:</strong> ' . htmlspecialchars($this->pesanan->jangkauan) . '</p>
                    <p><strong> Jenis Barang:</strong> ' . htmlspecialchars($this->pesanan->jenis_barang) . '</p>
                    <p><strong> Ukuran:</strong> ' . htmlspecialchars($this->pesanan->ukuran_barang) . '</p>
                    <p><strong> Tanggal:</strong> ' . date('d/m/Y', strtotime($this->pesanan->tanggal)) . '</p>
                    <p><strong> Akun:</strong> ' . htmlspecialchars($this->pesanan->akun) . '</p>
                </div>
            </div>
            
            <div style="background-color: #fff3cd; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                <h3 style="color: #856404; margin-top: 0;">💰 Rincian Biaya:</h3>
                <p><strong>💵 Total Sebelum Diskon:</strong> Rp ' . number_format($result['total_awal'], 0, ",", ".") . '</p>
                <p><strong>🎁 Diskon Akun (' . htmlspecialchars($this->pesanan->akun) . '):</strong> -Rp ' . number_format($result['diskon_akun'], 0, ",", ".") . '</p>
                <hr style="border: 1px solid #856404; margin: 15px 0;">
                <h2 style="color: #4CAF50; margin-bottom: 0;">🎯 Total Bayar: Rp ' . number_format($result['total_akhir'], 0, ",", ".") . '</h2>
            </div>
            
            <div class="success-message">
                🎉 Terima kasih! Pengiriman Anda telah dikonfirmasi dan akan diproses segera.
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="shipping.php" style="background-color: #4CAF50; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; margin-right: 10px; display: inline-block;">
                    📦 Kirim Lagi
                </a>
                <a href="menu.php" style="background-color: #6c757d; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block;">
                    🏠 Kembali ke Menu
                </a>
            </div>
        </div>';
    }
}

// Jalankan halaman pengiriman
$shippingPage = new ShippingPage();
$shippingPage->render();
?>