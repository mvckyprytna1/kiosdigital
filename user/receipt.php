<?php
/**
 * Receipt / Invoice Page
 * KiosDigital PPOB
 */
include '../includes/header.php';
check_role(['user', 'owner', 'admin', 'staff']);

$invoice_code = sanitize($_GET['invoice'] ?? '');
$trx_sql = "SELECT t.*, c.name as cat_name, p.product_name FROM transactions t 
            LEFT JOIN categories c ON t.category_id = c.id 
            LEFT JOIN products p ON t.product_id = p.id 
            WHERE t.invoice_code = ?";
$stmt = $conn->prepare($trx_sql);
$stmt->bind_param("s", $invoice_code);
$stmt->execute();
$trx = $stmt->get_result()->fetch_assoc();

if (!$trx) {
    echo "<div class='py-20 text-center font-bold'>Data tidak ditemukan.</div>";
    include '../includes/footer.php';
    exit();
}

$status_color = 'gray';
if($trx['transaction_status'] == 'success') $status_color = 'green';
if($trx['transaction_status'] == 'pending' || $trx['transaction_status'] == 'processing') $status_color = 'yellow';
if($trx['transaction_status'] == 'failed' || $trx['transaction_status'] == 'refunded') $status_color = 'red';
?>

<div class="max-w-2xl mx-auto px-4 py-8 md:py-16">
    <div id="receipt-print" class="bg-white rounded-3xl shadow-2xl shadow-gray-200 border border-gray-100 overflow-hidden">
        <!-- Header Struk -->
        <div class="bg-blue-600 p-8 text-center text-white">
            <h1 class="text-2xl font-bold mb-1 tracking-tight">STRUK PEMBAYARAN</h1>
            <p class="text-blue-100 text-sm">Invoice: <?php echo $trx['invoice_code']; ?></p>
        </div>

        <div class="p-8">
            <!-- Status Badge -->
            <div class="flex justify-center mb-10">
                <div class="text-center">
                    <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-<?php echo $status_color; ?>-50 text-<?php echo $status_color; ?>-700 ring-2 ring-<?php echo $status_color; ?>-700/10 uppercase tracking-widest">
                        <?php echo str_replace('_', ' ', $trx['transaction_status']); ?>
                    </div>
                    <?php if($trx['transaction_status'] == 'success'): ?>
                    <p class="text-xs text-gray-400 mt-2">Diterima pada <?php echo date('d/m/Y H:i', strtotime($trx['updated_at'])); ?> WIB</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Detail Section -->
            <div class="space-y-6">
                <div class="flex justify-between border-b border-gray-50 pb-4">
                    <span class="text-gray-500 font-medium">Layanan</span>
                    <span class="text-gray-900 font-bold"><?php echo $trx['cat_name']; ?></span>
                </div>
                <div class="flex justify-between border-b border-gray-50 pb-4">
                    <span class="text-gray-500 font-medium">Nama Produk</span>
                    <span class="text-gray-900 font-bold"><?php echo $trx['product_name'] ?? 'PPOB Product'; ?></span>
                </div>
                <div class="flex justify-between border-b border-gray-50 pb-4">
                    <span class="text-gray-500 font-medium">Nomor Tujuan</span>
                    <span class="text-gray-900 font-bold tracking-wider"><?php echo $trx['customer_target']; ?></span>
                </div>
                
                <?php if(!empty($trx['serial_number'])): ?>
                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 text-center">
                    <p class="text-gray-500 text-xs font-bold mb-2 uppercase tracking-widest">SERIAL NUMBER / TOKEN</p>
                    <p class="text-2xl font-mono font-bold text-blue-600 tracking-widest break-all"><?php echo $trx['serial_number']; ?></p>
                </div>
                <?php endif; ?>

                <div class="py-6 space-y-4">
                    <div class="flex justify-between items-center bg-blue-50 p-6 rounded-2xl border border-blue-100">
                        <span class="text-blue-900 font-bold text-lg">Total Bayar</span>
                        <span class="text-blue-600 font-black text-2xl"><?php echo format_idr($trx['selling_price']); ?></span>
                    </div>
                </div>
            </div>

            <!-- QR Code Placeholder -->
            <div class="mt-8 flex flex-col items-center">
                <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center text-gray-300">
                    <i data-lucide="qr-code" class="w-16 h-16"></i>
                </div>
                <p class="text-gray-400 text-[10px] mt-2 uppercase tracking-tighter">Verified by KiosDigital PPOB</p>
            </div>
        </div>

        <div class="bg-gray-50 px-8 py-6 text-center border-t border-gray-100">
            <p class="text-gray-500 text-sm font-medium">Terima kasih telah bertransaksi di <?php echo get_setting('app_name'); ?>!</p>
            <p class="text-gray-400 text-xs mt-1 italic">Simpan struk ini sebagai bukti pembayaran yang sah.</p>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-8 grid grid-cols-2 gap-4 no-print">
        <button onclick="window.print()" class="bg-white border-2 border-gray-200 text-gray-700 py-4 rounded-2xl font-bold flex items-center justify-center gap-2 hover:bg-gray-50 transition-all">
            <i data-lucide="printer" class="w-5 h-5"></i>
            Cetak Struk
        </button>
        <button class="bg-blue-600 text-white py-4 rounded-2xl font-bold flex items-center justify-center gap-2 shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all">
            <i data-lucide="share-2" class="w-5 h-5"></i>
            Bagikan
        </button>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        #receipt-print { box-shadow: none !important; border: 1px solid #eee !important; }
    }
</style>

<?php include '../includes/footer.php'; ?>
