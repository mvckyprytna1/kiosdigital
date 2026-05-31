<?php
/**
 * Sync Products from Digiflazz
 * KiosDigital PPOB
 */
include '../includes/header.php';
check_role(['owner']);

require_once '../includes/digiflazz_api.php';

$message = "";
$status = "info";

if (isset($_POST['sync'])) {
    $digi = new DigiflazzAPI();
    $result = $digi->getPriceList();

    if ($result && isset($result['data'])) {
        $products = $result['data'];
        $count_new = 0;
        $count_up = 0;
        $margin = intval(get_setting('global_margin') ?? 500);

        foreach ($products as $p) {
            $sku = $p['buyer_sku_code'];
            $name = $p['product_name'];
            $category_name = $p['category'];
            $brand = $p['brand'];
            $price = $p['price'];
            $status_p = ($p['buyer_product_status'] && $p['seller_product_status']) ? 'active' : 'inactive';
            $type = $p['type'];
            
            // Map Category ID
            $cat_sql = "SELECT id FROM categories WHERE name = ? LIMIT 1";
            $c_stmt = $conn->prepare($cat_sql);
            $c_stmt->bind_param("s", $category_name);
            $c_stmt->execute();
            $cat_data = $c_stmt->get_result()->fetch_assoc();
            $cat_id = $cat_data ? $cat_id = $cat_data['id'] : 1; // Default to Pulsa if not found

            // Calculate Selling Price
            $selling_price = $price + $margin;
            $profit = $selling_price - $price;

            // Check if exists
            $check_sql = "SELECT id FROM products WHERE provider_code = ?";
            $ch_stmt = $conn->prepare($check_sql);
            $ch_stmt->bind_param("s", $sku);
            $ch_stmt->execute();
            $exist = $ch_stmt->get_result()->fetch_assoc();

            if ($exist) {
                $up_sql = "UPDATE products SET base_price = ?, selling_price = ?, profit = ?, status = ?, brand = ?, type = ?, product_name = ? WHERE id = ?";
                $u_stmt = $conn->prepare($up_sql);
                $u_stmt->bind_param("dddssssi", $price, $selling_price, $profit, $status_p, $brand, $type, $name, $exist['id']);
                $u_stmt->execute();
                $count_up++;
            } else {
                $product_code = "KD-" . $sku;
                $ins_sql = "INSERT INTO products (category_id, product_name, product_code, provider_code, brand, type, base_price, selling_price, profit, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $i_stmt = $conn->prepare($ins_sql);
                $i_stmt->bind_param("isssssddds", $cat_id, $name, $product_code, $sku, $brand, $type, $price, $selling_price, $profit, $status_p);
                $i_stmt->execute();
                $count_new++;
            }
        }
        $message = "Sync Selesai! Berhasil menambah $count_new produk baru dan memperbarui $count_up produk lama.";
        $status = "success";
    } else {
        $message = "Terjadi kesalahan saat mengambil data dari provider.";
        $status = "error";
    }
}
?>

<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-extrabold text-gray-900">Sinkronisasi Produk Digiflazz</h2>
        <p class="text-gray-500 mt-2">Ambil daftar harga terbaru dan produk aktif dari Supplier Digiflazz.</p>
    </div>

    <?php if($message): ?>
        <div class="mb-8 p-6 rounded-2xl border <?php echo $status == 'success' ? 'bg-green-50 border-green-100 text-green-700' : 'bg-red-50 border-red-100 text-red-700'; ?> flex items-center gap-4">
             <i data-lucide="<?php echo $status == 'success' ? 'check-circle' : 'alert-circle'; ?>" class="w-8 h-8"></i>
             <p class="font-bold"><?php echo $message; ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-100 p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <h4 class="text-lg font-bold text-gray-800 mb-2">Informasi Penting</h4>
                <ul class="text-sm text-gray-500 space-y-3">
                    <li class="flex gap-2">
                        <i data-lucide="check" class="w-4 h-4 text-green-500 shrink-0"></i>
                         Harga jual otomatis dihitung dari <strong>Harga Modal + Margin Global (<?php echo format_idr(get_setting('global_margin')); ?>)</strong>.
                    </li>
                    <li class="flex gap-2">
                        <i data-lucide="check" class="w-4 h-4 text-green-500 shrink-0"></i>
                        Produk lama yang sudah tidak ada di supplier tidak akan dihapus, hanya ditandai sebagai non-aktif jika status supplier non-aktif.
                    </li>
                    <li class="flex gap-2">
                        <i data-lucide="check" class="w-4 h-4 text-green-500 shrink-0"></i>
                        Proses ini mungkin memakan waktu 1-2 menit tergantung jumlah produk.
                    </li>
                </ul>
            </div>
            <div class="bg-gray-50 p-8 rounded-3xl border border-dashed border-gray-200 text-center">
                <form action="" method="POST">
                    <button type="submit" name="sync" onclick="this.disabled=true; this.innerText='Sedang Memproses...'; this.form.submit();" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 active:scale-95 transition-all text-lg flex items-center justify-center gap-3">
                        <i data-lucide="refresh-cw" class="w-6 h-6"></i>
                        Mulai Sinkronisasi
                    </button>
                </form>
                <p class="text-xs text-gray-400 mt-4 italic font-medium">Terakhir Sinkron: <?php echo date('d M Y H:i'); ?></p>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
