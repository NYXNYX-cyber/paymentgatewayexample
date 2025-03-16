<?php
// functions.php (Jika Anda memisahkan fungsi-fungsi)
//include 'functions.php';


// Data Produk (Biasanya dari database, ini contoh statis)
$products = [
    [
        'id' => 1,
        'type' => 'hosting',
        'name' => 'Paket Hosting Basic',
        'description' => '1GB Storage, 1 Domain, Unlimited Bandwidth',
        'price' => 50000,
        'features' => ['1GB Storage', '1 Domain', 'Unlimited Bandwidth', 'cPanel']
    ],
    [
        'id' => 2,
        'type' => 'hosting',
        'name' => 'Paket Hosting Pro',
        'description' => '5GB Storage, 5 Domains, Unlimited Bandwidth, Free SSL',
        'price' => 100000,
        'features' => ['5GB Storage', '5 Domains', 'Unlimited Bandwidth', 'cPanel', 'Free SSL']
    ],
    [
        'id' => 3,
        'type' => 'vps',
        'name' => 'VPS Starter',
        'description' => '1 vCPU, 2GB RAM, 20GB SSD, 1TB Bandwidth',
        'price' => 150000,
         'features' => ['1 vCPU', '2GB RAM', '20GB SSD', '1TB Bandwidth', 'Full Root Access']
    ],
     [
        'id' => 4,
        'type' => 'vps',
        'name' => 'VPS Bisnis',
        'description' => '2 vCPU, 4GB RAM, 50GB SSD, 2TB Bandwidth',
        'price' => 300000,
         'features' => ['2 vCPU', '4GB RAM', '50GB SSD', '2TB Bandwidth', 'Full Root Access', 'Dedicated IP']
    ],

];

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemesanan Hosting & VPS</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-6kt2QJrovzyxl9c2"></script>
    </head>
<body>
    <header>
        <div class="logo">
            <img src="assets/logo.png" alt="Logo Perusahaan" width="50" height="50">
             <h1>Nama Perusahaan Hosting</h1>
        </div>
        <nav>
            <ul>
                <li><a href="#">Beranda</a></li>
                <li><a href="#">Hosting</a></li>
                <li><a href="#">VPS</a></li>
                <li><a href="#">Kontak</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="products">
            <h2>Pilih Paket Hosting/VPS</h2>
            <div class="product-list">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <h3><?php echo $product['name']; ?></h3>
                        <p class="product-type"><?php echo strtoupper($product['type']); ?></p>
                        <p><?php echo $product['description']; ?></p>
                        <p class="price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?> / bulan</p>

                        <ul class="features">
                            <?php foreach ($product['features'] as $feature): ?>
                                <li><?php echo $feature; ?></li>
                            <?php endforeach; ?>
                        </ul>


                        <button class="order-button" data-product-id="<?php echo $product['id']; ?>">Pesan Sekarang</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

       <section id="order-form" style="display: none;">
            <h2>Formulir Pemesanan</h2>
            <form id="order-form-data">
                <input type="hidden" id="product-id" name="product_id" value="">
                <label for="customer_name">Nama Lengkap:</label>
                <input type="text" id="customer_name" name="customer_name" required>

                <label for="customer_email">Email:</label>
                <input type="email" id="customer_email" name="customer_email" required>

                 <label for="domain_name">Nama Domain (Jika Hosting):</label>
                <input type="text" id="domain_name" name="domain_name">


                <label for="payment_method">Metode Pembayaran:</label>
                <select id="payment_method" name="payment_method">
                    <option value="midtrans">All Payment</option>
                    <option value="manual">Transfer Manual</option>  </select>
                <button type="button" id="submit-order">Proses Pesanan</button>
                <button type="button" id="cancel-order">Batal</button>
            </form>
        </section>

        <div id="payment-result" style="display: none;">
            <h2>Hasil Pembayaran</h2>
            <pre id="payment-result-json"></pre>
        </div>

    </main>

    <footer>
        <p>&copy; 2023 Nama Perusahaan Anda</p>
    </footer>

    <script>
        // --- Event Listener untuk Tombol "Pesan Sekarang" ---
        document.querySelectorAll('.order-button').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                document.getElementById('product-id').value = productId;
                document.getElementById('products').style.display = 'none';
                document.getElementById('order-form').style.display = 'block';
                document.getElementById('order-form').scrollIntoView({ behavior: 'smooth' });
            });
        });

        // --- Event Listener untuk Tombol "Batal" ---
        document.getElementById('cancel-order').addEventListener('click', function(){
            document.getElementById('products').style.display = 'block';
            document.getElementById('order-form').style.display = 'none';
        });

        // --- Event Listener untuk Tombol "Proses Pesanan" ---
        document.getElementById('submit-order').addEventListener('click', function() {
            const formData = new FormData(document.getElementById('order-form-data'));

            fetch('order.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.snapToken) {
                    // --- Tampilkan Popup Pembayaran Midtrans ---
                    snap.pay(data.snapToken, {
                        onSuccess: function(result){
                            displayPaymentResult(result);
                        },
                        onPending: function(result){
                            displayPaymentResult(result);
                        },
                        onError: function(result){
                            displayPaymentResult(result);
                        },
                        onClose: function(){
                            alert('Anda menutup popup tanpa menyelesaikan pembayaran.');
                        }
                    });

                    document.getElementById('order-form').style.display = 'none'; // Sembunyikan form

                } else if (data.error) {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memproses pesanan.');
            });
        });


        // --- Fungsi untuk Menampilkan Hasil Pembayaran ---
        function displayPaymentResult(result) {
            document.getElementById('payment-result-json').innerText = JSON.stringify(result, null, 2);
            document.getElementById('payment-result').style.display = 'block';
            document.getElementById('products').style.display = 'none';
            document.getElementById('order-form').style.display = 'none';
        }
    </script>
</body>
</html>