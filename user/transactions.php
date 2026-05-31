<?php
/**
 * User Transactions History
 * KiosDigital PPOB
 */
include '../includes/header.php';
check_role(['user']);

$user_id = $_SESSION['user_id'];
?>

<div class="max-w-7xl mx-auto px-4 py-8 md:py-16">
    <div class="mb-10">
        <h2 class="text-3xl font-black text-gray-900 tracking-tight">Riwayat Transaksi</h2>
        <p class="text-gray-500 font-medium">Lacak seluruh pesanan produk digital Anda.</p>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-100 border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
             <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                        <th class="px-8 py-6">Invoice</th>
                        <th class="px-8 py-6">Produk / Target</th>
                        <th class="px-8 py-6">Harga</th>
                        <th class="px-8 py-6">Status Trx</th>
                        <th class="px-8 py-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php
                    $sql = "SELECT t.*, p.product_name FROM transactions t 
                            LEFT JOIN products p ON t.product_id = p.id 
                            WHERE t.user_id = ? ORDER BY t.id DESC";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $transactions = $stmt->get_result();

                    if ($transactions->num_rows > 0):
                        while($row = $transactions->fetch_assoc()):
                            $status_color = 'gray';
                            if($row['transaction_status'] == 'success') $status_color = 'green';
                            if($row['transaction_status'] == 'processing' || $row['transaction_status'] == 'pending') $status_color = 'yellow';
                            if($row['transaction_status'] == 'failed') $status_color = 'red';
                    ?>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <span class="block font-black text-gray-900 text-sm">#<?php echo $row['invoice_code']; ?></span>
                            <span class="text-[10px] text-gray-400 font-bold"><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></span>
                        </td>
                        <td class="px-8 py-6">
                            <p class="font-bold text-gray-800 text-sm"><?php echo $row['product_name'] ?? 'PPOB Item'; ?></p>
                            <p class="text-[11px] text-blue-600 font-bold tracking-widest"><?php echo $row['customer_target']; ?></p>
                        </td>
                        <td class="px-8 py-6 font-black text-gray-900"><?php echo format_idr($row['selling_price']); ?></td>
                        <td class="px-8 py-6">
                            <span class="inline-flex items-center px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest bg-<?php echo $status_color; ?>-50 text-<?php echo $status_color; ?>-700 ring-1 ring-inset ring-<?php echo $status_color; ?>-700/10">
                                <?php echo str_replace('_', ' ', $row['transaction_status']); ?>
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <a href="/user/receipt.php?invoice=<?php echo $row['invoice_code']; ?>" class="inline-flex items-center gap-1 text-blue-600 font-black text-xs hover:gap-2 transition-all">
                                STRUK <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center text-gray-400">
                             <div class="max-w-xs mx-auto">
                                <i data-lucide="inbox" class="w-16 h-16 mx-auto mb-4 opacity-10"></i>
                                <p class="font-bold text-gray-300">Belum ada transaksi</p>
                                <a href="/dashboard.php" class="inline-block mt-4 text-blue-600 font-bold text-xs underline">Mulai Belanja</a>
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
