<?php
/**
 * User Dashboard
 * KiosDigital PPOB
 */
include '../includes/header.php';
check_role(['user']);

$user_id = $_SESSION['user_id'];
$user_sql = "SELECT balance FROM users WHERE id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$balance = $user_data['balance'];
?>

<div class="space-y-8">
    <!-- Top Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:scale-110 transition-transform">
                <i data-lucide="wallet" class="w-12 h-12 text-blue-600"></i>
            </div>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Saldo Akun</p>
            <h3 class="text-2xl font-black text-slate-900"><?php echo format_idr($balance); ?></h3>
            <div class="mt-4 flex gap-2">
                <a href="/user/deposit.php" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline">Top Up →</a>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Total Transaksi</p>
            <?php 
            $count_sql = "SELECT COUNT(*) as total FROM transactions WHERE user_id = ?";
            $c_stmt = $conn->prepare($count_sql);
            $c_stmt->bind_param("i", $user_id);
            $c_stmt->execute();
            $total_trx = $c_stmt->get_result()->fetch_assoc()['total'];
            ?>
            <h3 class="text-2xl font-black text-slate-900"><?php echo number_format($total_trx); ?></h3>
            <p class="text-[10px] text-slate-400 font-bold mt-2 uppercase tracking-tight">Lifetime orders</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Status Keanggotaan</p>
            <h3 class="text-2xl font-black text-emerald-600">VIP Silver</h3>
            <p class="text-[10px] text-emerald-400 font-bold mt-2 uppercase tracking-tight">3% Cashback active</p>
        </div>
        <div class="bg-blue-600 p-6 rounded-2xl shadow-xl shadow-blue-100 text-white relative overflow-hidden">
             <div class="relative z-10">
                <p class="text-[10px] text-blue-200 font-bold uppercase tracking-widest mb-1">Promo Khusus</p>
                <h3 class="text-xl font-bold leading-tight">Diskon 5% Semua Game</h3>
                <a href="/user/game.php" class="inline-block mt-4 text-[10px] font-bold bg-white text-blue-600 px-3 py-1.5 rounded-lg uppercase tracking-widest transition-transform active:scale-95">Beli Sekarang</a>
             </div>
             <i data-lucide="zap" class="absolute right-[-10px] bottom-[-10px] w-24 h-24 text-white opacity-10"></i>
        </div>
    </div>

    <!-- Quick Services Area -->
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h4 class="text-xl font-black text-slate-900 tracking-tight">Pilih Layanan</h4>
                <p class="text-slate-400 font-medium text-sm">Transaksi cepat di ujung jari Anda.</p>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-6 md:gap-8">
            <?php
            $services = [
                ['name' => 'Pulsa', 'slug' => 'pulsa', 'icon' => 'smartphone', 'color' => 'bg-blue-600'],
                ['name' => 'Data', 'slug' => 'data', 'icon' => 'globe', 'color' => 'bg-indigo-600'],
                ['name' => 'PLN', 'slug' => 'pln', 'icon' => 'zap', 'color' => 'bg-amber-500'],
                ['name' => 'Game', 'slug' => 'game', 'icon' => 'gamepad-2', 'color' => 'bg-rose-600'],
                ['name' => 'E-Money', 'slug' => 'ewallet', 'icon' => 'credit-card', 'color' => 'bg-emerald-600'],
                ['name' => 'Voucher', 'slug' => 'voucher', 'icon' => 'ticket', 'color' => 'bg-orange-500']
            ];
            foreach ($services as $s):
            ?>
            <a href="/user/<?php echo $s['slug']; ?>.php" class="group flex flex-col items-center gap-4 text-center">
                <div class="w-16 h-16 <?php echo $s['color']; ?> rounded-[1.5rem] flex items-center justify-center text-white shadow-lg group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300">
                    <i data-lucide="<?php echo $s['icon']; ?>" class="w-8 h-8"></i>
                </div>
                <span class="text-[10px] font-black text-slate-900 uppercase tracking-widest"><?php echo $s['name']; ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Latest Activity Table -->
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm flex flex-col overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h4 class="text-xl font-black text-slate-900 tracking-tight">Aktivitas Terakhir</h4>
            <a href="/user/transactions.php" class="text-[10px] font-black bg-slate-100 px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-200 uppercase tracking-widest transition-colors">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">
                    <tr>
                        <th class="px-8 py-4">Pesanan</th>
                        <th class="px-8 py-4">Tujuan</th>
                        <th class="px-8 py-4">Status</th>
                        <th class="px-8 py-4 text-right">Harga</th>
                        <th class="px-8 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 italic font-medium">
                    <?php
                    $trx_sql = "SELECT t.*, p.product_name FROM transactions t LEFT JOIN products p ON t.product_id = p.id WHERE t.user_id = ? ORDER BY t.id DESC LIMIT 5";
                    $stmt = $conn->prepare($trx_sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $transactions = $stmt->get_result();

                    if ($transactions->num_rows > 0):
                        while ($trx = $transactions->fetch_assoc()):
                            $status_color = 'slate';
                            if($trx['transaction_status'] == 'success') $status_color = 'emerald';
                            if($trx['transaction_status'] == 'pending' || $trx['transaction_status'] == 'waiting_payment') $status_color = 'amber';
                            if($trx['transaction_status'] == 'failed') $status_color = 'rose';
                    ?>
                    <tr class="hover:bg-slate-50/50 transition-colors not-italic">
                        <td class="px-8 py-5">
                            <span class="block text-sm font-bold text-slate-900"><?php echo $trx['product_name'] ?? 'PPOB Transaksi'; ?></span>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"><?php echo date('d M, H:i', strtotime($trx['created_at'])); ?></span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg"><?php echo $trx['customer_target']; ?></span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 text-[10px] font-black rounded-lg bg-<?php echo $status_color; ?>-50 text-<?php echo $status_color; ?>-700 uppercase tracking-widest border border-<?php echo $status_color; ?>-100">
                                <?php echo str_replace('_', ' ', $trx['transaction_status']); ?>
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right font-black text-slate-900 text-sm">
                            <?php echo format_idr($trx['selling_price']); ?>
                        </td>
                        <td class="px-8 py-5 text-right">
                             <a href="/user/receipt.php?invoice=<?php echo $trx['invoice_code']; ?>" class="p-2 bg-slate-50 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg inline-block transition-all">
                                <i data-lucide="external-link" class="w-4 h-4"></i>
                             </a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="max-w-xs mx-auto">
                                <i data-lucide="info" class="w-12 h-12 text-slate-200 mx-auto mb-4"></i>
                                <p class="text-slate-400 font-bold italic">Belum ada transaksi terbaru.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
