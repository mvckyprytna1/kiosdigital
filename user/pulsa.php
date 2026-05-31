<?php
/**
 * Pulsa Purchase Page
 * KiosDigital PPOB
 */
include '../includes/header.php';
check_role(['user']);
?>

<div class="max-w-4xl mx-auto px-4 py-8 md:py-12">
    <div class="mb-8">
        <a href="/dashboard.php" class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors mb-4">
            <i data-lucide="chevron-left" class="w-4 h-4 mr-1"></i>
            Kembali ke Dashboard
        </a>
        <h2 class="text-3xl font-bold text-gray-900">Isi Pulsa</h2>
        <p class="text-gray-500 mt-2">Beli pulsa all operator harga termurah.</p>
    </div>

    <div class="bg-white rounded-3xl shadow-xl shadow-gray-100 border border-gray-100 p-8">
        <div class="space-y-8">
            <!-- Input Nomor -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <i data-lucide="phone" class="w-4 h-4 text-blue-600"></i>
                    Nomor Handphone Target
                </label>
                <div class="relative">
                    <input type="text" id="phone_number" class="w-full px-6 py-5 bg-gray-50 rounded-2xl border-2 border-transparent focus:border-blue-600 focus:bg-white outline-none transition-all text-xl font-bold tracking-widest placeholder:tracking-normal placeholder:font-medium" placeholder="Contoh: 08123456789">
                    <div id="operator_logo" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center grayscale opacity-50 transition-all">
                        <i data-lucide="smartphone" class="w-8 h-8"></i>
                    </div>
                </div>
                <div id="operator_name" class="mt-2 text-sm font-semibold text-gray-400">Masukkan nomor untuk mendeteksi operator</div>
            </div>

            <!-- List Produk -->
            <div id="product_section" class="hidden">
                <label class="block text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <i data-lucide="list" class="w-4 h-4 text-blue-600"></i>
                    Pilih Nominal Pulsa
                </label>
                <div id="product_list" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Products will be loaded via JS -->
                    <div class="animate-pulse bg-gray-100 h-20 rounded-2xl"></div>
                    <div class="animate-pulse bg-gray-100 h-20 rounded-2xl"></div>
                </div>
            </div>
            
            <div id="empty_products" class="hidden text-center py-10">
                <i data-lucide="search-x" class="w-16 h-16 text-gray-200 mx-auto mb-4"></i>
                <p class="text-gray-500 font-medium">Maaf, operator tidak ditemukan atau produk sedang kosong.</p>
            </div>
        </div>
    </div>
</div>

<!-- Checkout Modal -->
<div id="modal_checkout" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden animate-in zoom-in duration-300">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-lg">Konfirmasi Pembelian</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <div class="p-8 space-y-6">
                <div class="flex items-center gap-4 bg-blue-50 p-4 rounded-2xl border border-blue-100">
                    <div id="modal_op_icon" class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm">
                         <i data-lucide="smartphone" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div>
                        <p id="modal_product_name" class="font-bold text-blue-900"></p>
                        <p id="modal_customer_no" class="text-blue-700 font-medium text-sm"></p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Harga Produk</span>
                        <span id="modal_price" class="font-bold text-gray-900"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Metode Pembayaran</span>
                        <span class="font-bold text-blue-600">Saldo Akun</span>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-50">
                    <button id="btn_process" onclick="processOrder()" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 active:scale-95 transition-all text-lg">
                        Bayar Sekarang
                    </button>
                    <p class="text-center text-xs text-gray-400 mt-4">Transaksi diproses otomatis secara real-time.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedProduct = null;

    document.getElementById('phone_number').addEventListener('input', function(e) {
        let val = e.target.value;
        if (val.length >= 4) {
            detectOperator(val);
        } else {
            resetUI();
        }
    });

    function resetUI() {
        document.getElementById('product_section').classList.add('hidden');
        document.getElementById('empty_products').classList.add('hidden');
        document.getElementById('operator_name').innerText = "Masukkan nomor untuk mendeteksi operator";
    }

    function detectOperator(number) {
        // Simple mock detection
        const prefix = number.substring(0, 4);
        let brand = "";
        
        const operatorMap = {
            '0811': 'Telkomsel', '0812': 'Telkomsel', '0813': 'Telkomsel', '0821': 'Telkomsel', '0822': 'Telkomsel', '0852': 'Telkomsel', '0853': 'Telkomsel',
            '0814': 'Indosat', '0815': 'Indosat', '0816': 'Indosat', '0855': 'Indosat', '0856': 'Indosat', '0857': 'Indosat', '0858': 'Indosat',
            '0817': 'XL', '0818': 'XL', '0819': 'XL', '0859': 'XL', '0877': 'XL', '0878': 'XL',
            '0831': 'Axis', '0832': 'Axis', '0833': 'Axis', '0838': 'Axis',
            '0895': 'Tri', '0896': 'Tri', '0897': 'Tri', '0898': 'Tri', '0899': 'Tri',
            '0881': 'Smartfren', '0882': 'Smartfren', '0883': 'Smartfren', '0884': 'Smartfren', '0885': 'Smartfren'
        };

        brand = operatorMap[prefix] || null;

        if (brand) {
            document.getElementById('operator_name').innerText = brand;
            document.getElementById('operator_name').className = "mt-2 text-sm font-bold text-blue-600 uppercase tracking-widest animate-in fade-in";
            loadProducts(brand);
        } else {
            resetUI();
        }
    }

    async function loadProducts(brand) {
        document.getElementById('product_section').classList.remove('hidden');
        document.getElementById('product_list').innerHTML = `
            <div class="col-span-1 md:col-span-2 py-10 flex justify-center">
                 <div class="animate-spin rounded-full h-10 w-10 border-4 border-blue-600 border-t-transparent"></div>
            </div>`;

        try {
            const response = await fetch(`/api/get-products.php?category=pulsa&brand=${brand}`);
            const result = await response.json();
            
            if (result.success && result.data.length > 0) {
                let html = '';
                result.data.forEach(p => {
                    html += `
                        <button onclick='confirmPurchase(${JSON.stringify(p)})' class="flex justify-between items-center bg-gray-50 border border-gray-100 p-6 rounded-2xl hover:border-blue-600 hover:bg-blue-50 transition-all group">
                            <div class="text-left">
                                <p class="font-bold text-gray-800 text-lg">${p.product_name}</p>
                                <p class="text-xs text-gray-500">${p.description || 'Proses Cepat'}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-blue-600 text-lg">Rp ${parseInt(p.selling_price).toLocaleString('id-ID')}</p>
                            </div>
                        </button>
                    `;
                });
                document.getElementById('product_list').innerHTML = html;
            } else {
                document.getElementById('product_section').classList.add('hidden');
                document.getElementById('empty_products').classList.remove('hidden');
            }
        } catch (error) {
            console.error(error);
        }
    }

    function confirmPurchase(p) {
        selectedProduct = p;
        const phone = document.getElementById('phone_number').value;
        
        if (!phone || phone.length < 10) {
            Swal.fire('Error', 'Silakan masukkan nomor HP yang valid', 'error');
            return;
        }

        document.getElementById('modal_product_name').innerText = p.product_name;
        document.getElementById('modal_customer_no').innerText = phone;
        document.getElementById('modal_price').innerText = "Rp " + parseInt(p.selling_price).toLocaleString('id-ID');
        
        document.getElementById('modal_checkout').classList.remove('hidden');
        lucide.createIcons();
    }

    function closeModal() {
        document.getElementById('modal_checkout').classList.add('hidden');
    }

    async function processOrder() {
        const btn = document.getElementById('btn_process');
        btn.disabled = true;
        btn.innerHTML = `<i data-lucide="loader-2" class="w-6 h-6 animate-spin mx-auto text-white"></i>`;
        lucide.createIcons();

        try {
            const response = await fetch('/api/create-transaction.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    product_id: selectedProduct.id,
                    customer_target: document.getElementById('phone_number').value,
                    payment_method: 'balance'
                })
            });

            const result = await response.json();
            
            if (result.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: result.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = `/user/receipt.php?invoice=${result.invoice_code}`;
                });
            } else {
                Swal.fire('Gagal', result.message, 'error');
                btn.disabled = false;
                btn.innerText = "Bayar Sekarang";
            }
        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            btn.disabled = false;
            btn.innerText = "Bayar Sekarang";
        }
    }
</script>

<?php include '../includes/footer.php'; ?>
