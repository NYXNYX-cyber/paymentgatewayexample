<?php
//  functions.php (Jika Anda memisahkan fungsi-fungsi)
//include 'functions.php';

// Sertakan library Midtrans (pastikan pathnya benar)
require_once __DIR__ . '/midtrans-php-master/Midtrans.php'; // Jika pakai Composer
// atau: require_once __DIR__ . '/path/ke/Midtrans.php';  // Jika manual

// Konfigurasi Midtrans (GANTI DENGAN KUNCI API ANDA)
\Midtrans\Config::$serverKey = 'SB-Mid-server-y2u7UTU4OEfaXRDt-9kseaO1'; // Ganti dengan Server Key Anda
\Midtrans\Config::$isProduction = false; // Ganti ke true untuk mode produksi
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... (Kode sebelumnya untuk mengambil data produk dan menyiapkan $transactionDetails, $itemDetails, $customerDetails) ...
    $productId = $_POST['product_id'];
    $customerName = $_POST['customer_name'];
    $customerEmail = $_POST['customer_email'];
    $domain = isset($_POST['domain_name']) ? $_POST['domain_name'] : null; // Domain opsional.
    $paymentMethod = $_POST['payment_method'];

    // 1. Ambil data produk dari database (atau dari array statis jika belum ada DB)
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

    $product = null;
    foreach($products as $p){
        if($p['id'] == $productId){
            $product = $p;
            break;
        }
    }

    if (!$product) {
        die("Produk tidak ditemukan!"); // Error handling
    }

    // 2. Siapkan data untuk Midtrans
    $transactionDetails = [
        'order_id' => 'ORDER-' . time(), // Order ID unik, bisa pakai timestamp, dll.
        'gross_amount' => $product['price'], // Total harga
    ];

     $itemDetails = [
        [
            'id'       => $product['id'],
            'price'    => $product['price'],
            'quantity' => 1,
            'name'     => $product['name'] . ($domain ? " ($domain)" : ""), // Sertakan nama domain jika ada
        ]
    ];


    $customerDetails = [
        'first_name' => $customerName,
        'email' => $customerEmail,
        // Tambah detail lain jika perlu (last_name, phone, billing_address, shipping_address)
    ];

    // Buat parameter untuk Midtrans
    $params = [
        'transaction_details' => $transactionDetails,
        'item_details' => $itemDetails,
        'customer_details' => $customerDetails,
    ];

    try {
        // Dapatkan Snap Token
        $snapToken = \Midtrans\Snap::getSnapToken($params);

        // Simpan order ID dan token ke database (PENTING!)
        // Anda perlu membuat tabel untuk menyimpan ini. Contoh:
        // saveOrderToDatabase($transactionDetails['order_id'], $snapToken, $product['id'], $customerName, $customerEmail, $domain);

        // Redirect ke halaman pembayaran Midtrans
        // header('Location: ' . $paymentUrl); // Cara redirect klasik
        // exit();

        // ATAU, kirim snapToken ke frontend untuk ditampilkan dengan Snap.js (lebih modern)
        header('Content-Type: application/json');
        echo json_encode(['snapToken' => $snapToken]);
        exit();

    } catch (\Exception $e) {
        // Tangani error (misalnya, Midtrans sedang down)
        http_response_code(500); // Atau kode error lain yang sesuai
        echo json_encode(['error' => 'Gagal memproses pembayaran: ' . $e->getMessage()]);
        exit();
    }

}

// --- Contoh Handler Notifikasi (Webhook) ---
// *Ini harus di URL yang bisa diakses publik oleh Midtrans*
//  biasanya di file terpisah, misalnya: notification_handler.php
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['REQUEST_URI'], '/notification_handler.php') !== false) {


    $notif = new \Midtrans\Notification();

    $transaction = $notif->transaction_status;
    $type = $notif->payment_type;
    $orderId = $notif->order_id;
    $fraud = $notif->fraud_status;

    // Log notifikasi (penting untuk debugging)
     file_put_contents('midtrans_notification.log', print_r($notif, true), FILE_APPEND);


    // Ambil data dari database berdasarkan order_id
    // $order = getOrderFromDatabase($orderId);
    // if (!$order) {
    //     http_response_code(404); // Order tidak ditemukan
    //     exit();
    // }


    if ($transaction == 'capture') {
        // For credit card transaction, we need to check whether transaction is challenge by FDS or not
        if ($type == 'credit_card') {
            if ($fraud == 'challenge') {
                // TODO: Tandai order sebagai "perlu verifikasi manual"
                 updateOrderStatus($orderId, 'pending');
            } else {
                // TODO: Tandai order sebagai "berhasil"
                updateOrderStatus($orderId, 'success');
            }
        }
    } else if ($transaction == 'settlement') {
        // TODO: Tandai order sebagai "berhasil"
         updateOrderStatus($orderId, 'success');
    } else if ($transaction == 'pending') {
        // TODO: Tandai order sebagai "pending"
         updateOrderStatus($orderId, 'pending');
    } else if ($transaction == 'deny') {
        // TODO: Tandai order sebagai "ditolak"
         updateOrderStatus($orderId, 'denied');
    } else if ($transaction == 'expire') {
        // TODO: Tandai order sebagai "kadaluarsa"
         updateOrderStatus($orderId, 'expired');
    } else if ($transaction == 'cancel') {
        // TODO: Tandai order sebagai "dibatalkan"
        updateOrderStatus($orderId, 'cancelled');
    }

    http_response_code(200); // Beri tahu Midtrans bahwa notifikasi diterima
    exit();
}


// --- Fungsi-fungsi bantuan (contoh, perlu implementasi nyata) ---

function saveOrderToDatabase($orderId, $snapToken, $productId, $customerName, $customerEmail, $domain) {
    // Implementasi penyimpanan ke database (MySQL, PostgreSQL, dll.)
    // Contoh:
    // $db = new PDO(...);
    // $stmt = $db->prepare("INSERT INTO orders (order_id, snap_token, product_id, customer_name, customer_email, domain_name, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    // $stmt->execute([$orderId, $snapToken, $productId, $customerName, $customerEmail, $domain]);
    //  file_put_contents('log_database_save.txt', "insert into orders (orderID, snapToken, dll VALUES {$orderId}, {$snapToken}....)", FILE_APPEND ); //buat debuging kalau pakai database

}

function getOrderFromDatabase($orderId) {
    // Implementasi pengambilan data order dari database
    // return $order; // Kembalikan data order dalam bentuk array

     //  file_put_contents('log_get_order_db.txt', "select * from orders where order_id = {$orderId}", FILE_APPEND ); //buat debuging kalau pakai database
}

function updateOrderStatus($orderId, $status) {
    // Implementasi update status order di database
    // Contoh:
    // $db = new PDO(...);
    // $stmt = $db->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    // $stmt->execute([$status, $orderId]);
    //   file_put_contents('log_update_status.txt', "UPDATE orders SET status = {$status}  where order_id = {$orderId}", FILE_APPEND ); //buat debuging kalau pakai database
}

?>