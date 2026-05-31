<?php
/**
 * Owner Dashboard
 * KiosDigital PPOB
 */
include '../includes/header.php';
check_role(['owner']);

// Summary Data
$stats = [
    'omzet' => $conn->query("SELECT SUM(selling_price) as total FROM transactions WHERE transaction_status = 'success'")->fetch_assoc()['total'] ?? 0,
    'profit' => $conn->query("SELECT SUM(profit) as total FROM transactions WHERE transaction_status = 'success'")->fetch_assoc()['total'] ?? 0,
    'trx_count' => $conn->query("SELECT COUNT(*) as total FROM transactions")->fetch_assoc()['total'] ?? 0,
    'trx_success' => $conn->query("SELECT COUNT(*) as total FROM transactions WHERE transaction_status = 'success'")->fetch_assoc()['total'] ?? 0,
    'users' => $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'")->fetch_assoc()['total'] ?? 0,
];

require_once '../includes/digiflazz_api.php';
$digi = new DigiflazzAPI();
$digi_balance = $digi->getBalance()['data']['deposit'] ?? 0;
?>

<div class="space-y-8">
    <!-- Owner Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Omzet Akumulatif</p>
            <h3 class="text-2xl font-black text-slate-900"><?php echo format_idr($stats['omzet']); ?></h3>
            <div class="mt-4 flex items-center text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded w-fit">
                <i data-lucide="trending-up" class="w-3 h-3 mr-1"></i>
                LIFETIME
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Total Net Profit</p>
            <h3 class="text-2xl font-black text-emerald-600"><?php echo format_idr($stats['profit']); ?></h3>
            <p class="text-[10px] text-slate-400 font-bold mt-2 uppercase tracking-tight italic">Net after supplier cost</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Transaksi Berhasil</p>
            <h3 class="text-2xl font-black text-slate-900"><?php echo number_format($stats['trx_success']); ?></h3>
            <div class="mt-4 w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                <?php 
                $rate = $stats['trx_count'] > 0 ? ($stats['trx_success'] / $stats['trx_count']) * 100 : 0;
                ?>
                <div class="bg-blue-600 h-1.5 rounded-full" style="width: <?php echo $rate; ?>%"></div>
            </div>
            <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase tracking-tighter">Success Rate <?php echo round($rate); ?>%</p>
        </div>
        <div class="bg-slate-900 p-6 rounded-2xl shadow-xl shadow-slate-200 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Saldo Digiflazz</p>
                <h3 class="text-2xl font-black text-blue-400"><?php echo format_idr($digi_balance); ?></h3>
                <button class="mt-4 text-[10px] font-bold bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg uppercase tracking-widest transition-all">Top Up Supplier</button>
            </div>
            <i data-lucide="server" class="absolute right-[-10px] bottom-[-10px] w-24 h-24 text-white opacity-5 group-hover:scale-110 transition-transform"></i>
        </div>
    </div>

    <!-- Admin Quick View Split -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Orders -->
        <div class="lg:col-span-2 bg-white rounded-[2rem] border border-slate-200 shadow-sm flex flex-col overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h4 class="text-xl font-black text-slate-900 tracking-tight">Transaksi Terkini</h4>
                <a href="/owner/transactions.php" class="text-[10px] font-black bg-slate-50 px-4 py-2 rounded-xl text-slate-500 hover:bg-slate-100 uppercase tracking-widest transition-colors">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">
                        <tr>
                            <th class="px-8 py-4">Invoice</th>
                            <th class="px-8 py-4">Status</th>
                            <th class="px-8 py-4 text-right">Profit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 italic font-medium">
                        <?php
                        $trx_query = "SELECT t.*, p.product_name FROM transactions t 
                                      LEFT JOIN products p ON t.product_id = p.id 
                                      ORDER BY t.id DESC LIMIT 6";
                        $result = $conn->query($trx_query);
                        while($row = $result->fetch_assoc()):
                            $status_color = 'slate';
                            if($row['transaction_status'] == 'success') $status_color = 'emerald';
                            if($row['transaction_status'] == 'processing' || $row['transaction_status'] == 'pending') $status_color = 'amber';
                            if($row['transaction_status'] == 'failed') $status_color = 'rose';
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors not-italic">
                            <td class="px-8 py-5">
                                <span class="block text-sm font-bold text-slate-900">#<?php echo $row['invoice_code']; ?></span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"><?php echo $row['product_name'] ?? 'PPOB Transaksi'; ?></span>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-2 py-1 text-[10px] font-black rounded-lg bg-<?php echo $status_color; ?>-50 text-<?php echo $status_color; ?>-700 uppercase tracking-widest border border-<?php echo $status_color; ?>-100">
                                    <?php echo str_replace('_', ' ', $row['transaction_status']); ?>
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right font-black text-emerald-600 text-sm">
                                +<?php echo format_idr($row['profit']); ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sidebar Actions & System Stats -->
        <div class="space-y-8">
            <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm">
                <h4 class="text-xl font-black text-slate-900 tracking-tight mb-8">System Health</h4>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                         <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-widest">Database</span>
                         </div>
                         <span class="text-[10px] font-black text-emerald-600">HEALTHY</span>
                    </div>
                    <div class="flex items-center justify-between">
                         <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-widest">Digiflazz API</span>
                         </div>
                         <span class="text-[10px] font-black text-emerald-600">CONNECTED</span>
                    </div>
                    <div class="flex items-center justify-between">
                         <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-widest">Tripay API</span>
                         </div>
                         <span class="text-[10px] font-black text-emerald-600">ACTIVE</span>
                    </div>
                </div>
            </div>

            <div class="bg-blue-600 p-8 rounded-[2rem] shadow-2xl shadow-blue-200 text-white">
                <h4 class="text-xl font-bold mb-6 tracking-tight">Quick Actions</h4>
                <div class="grid grid-cols-2 gap-4">
                    <a href="/owner/products.php" class="bg-white/10 hover:bg-white/20 p-4 rounded-2xl flex flex-col items-center gap-2 transition-all group">
                        <i data-lucide="package" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Products</span>
                    </a>
                    <a href="/owner/users.php" class="bg-white/10 hover:bg-white/20 p-4 rounded-2xl flex flex-col items-center gap-2 transition-all group">
                        <i data-lucide="users" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Users</span>
                    </a>
                    <a href="/owner/settings.php" class="bg-white/10 hover:bg-white/20 p-4 rounded-2xl flex flex-col items-center gap-2 transition-all group">
                        <i data-lucide="settings" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Config</span>
                    </a>
                    <a href="/owner/sync-products.php" class="bg-white/10 hover:bg-white/20 p-4 rounded-2xl flex flex-col items-center gap-2 transition-all group">
                        <i data-lucide="refresh-cw" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Sync</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
