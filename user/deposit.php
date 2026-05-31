<?php
/**
 * Deposit Page
 * KiosDigital PPOB
 */
include '../includes/header.php';
check_role(['user']);
?>

<div class="max-w-4xl mx-auto px-4 py-8 md:py-12">
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Isi Saldo Akun</h2>
        <p class="text-gray-500 mt-2 font-medium">Top up saldo untuk kemudahan transaksi otomatis 24 jam.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Metode Tripay (Otomatis) -->
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-xl shadow-gray-100 flex flex-col group overflow-hidden relative">
            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full flex items-center justify-center -mr-4 -mt-4 opacity-30 group-hover:bg-blue-600 group-hover:opacity-10 transition-all">
                <i data-lucide="zap" class="w-10 h-10 text-blue-600"></i>
            </div>
            
            <div class="relative z-10">
                <h3 class="text-xl font-bold text-gray-900 mb-2">Deposit Otomatis</h3>
                <p class="text-sm text-gray-500 mb-8">Pembayaran via QRIS, Virtual Account, Retail (Alfamart/Indomaret). Saldo masuk otomatis setelah bayar Lunas.</p>
                
                <div class="space-y-4 mb-8">
                     <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nominal Top Up</label>
                        <input type="number" id="depo_amount_auto" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-600 focus:bg-white rounded-2xl outline-none transition-all font-bold text-xl" placeholder="Min Rp 10.000">
                    </div>
                </div>

                <button onclick="createDepositTripay()" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 active:scale-95 transition-all">
                    Pilih Metode Bayar
                </button>
            </div>
        </div>

        <!-- Metode Manual -->
        <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 flex flex-col group relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-white rounded-bl-full flex items-center justify-center -mr-4 -mt-4 opacity-50">
                <i data-lucide="upload" class="w-10 h-10 text-gray-400"></i>
            </div>

            <div class="relative z-10">
                <h3 class="text-xl font-bold text-gray-900 mb-2">Transfer Manual</h3>
                <p class="text-sm text-gray-500 mb-8">Kirim via Bank atau E-Wallet lalu upload bukti transfer. Verifikasi admin maksimal 15 Menit.</p>
                
                <div class="space-y-4 mb-8">
                     <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nominal Transfer</label>
                        <input type="number" id="depo_amount_manual" class="w-full px-6 py-4 bg-white border-2 border-transparent focus:border-gray-300 rounded-2xl outline-none transition-all font-bold text-xl" placeholder="Min Rp 5.000">
                    </div>
                </div>

                <a href="#manual" class="w-full bg-white border-2 border-gray-200 text-gray-700 py-4 rounded-2xl font-bold flex items-center justify-center hover:bg-gray-100 transition-all">
                    Lihat Rekening
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function createDepositTripay() {
        const amount = document.getElementById('depo_amount_auto').value;
        if (amount < 10000) {
            Swal.fire('Error', 'Minimal deposit otomatis Rp 10.000', 'error');
            return;
        }
        Swal.fire('Info', 'Fitur ini membutuhkan integrasi Tripay yang aktif dibackend.', 'info');
    }
</script>

<?php include '../includes/footer.php'; ?>
