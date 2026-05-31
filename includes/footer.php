<?php
/**
 * Footer Component
 * KiosDigital PPOB
 */
$footer_text = get_setting('footer_text') ?? '© 2026 KiosDigital PPOB. All Rights Reserved.';
?>
    <?php if ($is_dashboard && isset($_SESSION['user_id'])): ?>
            </main>
            <!-- Bottom Bar -->
            <footer class="bg-white border-t border-slate-200 px-8 py-3 flex items-center justify-between text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                <div><?php echo $app_name; ?> v2.4.0 (PHP Native)</div>
                <div class="flex gap-4">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> API Status: Online</span>
                    <span>Server: Cloud Run</span>
                </div>
            </footer>
        </div>
    </div>
    <?php else: ?>
    </main>

    <footer class="bg-white border-t border-slate-200 py-16 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-sm shadow-blue-100">
                            <i data-lucide="zap" class="w-6 h-6"></i>
                        </div>
                        <span class="font-bold text-xl tracking-tight text-slate-900"><?php echo get_setting('app_name'); ?></span>
                    </div>
                    <p class="text-slate-500 max-w-sm leading-relaxed">
                        Solusi PPOB modern untuk kebutuhan digital harian Anda. Transaksi instan, aman, dan terpercaya dengan sistem otomatis 24 jam.
                    </p>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 mb-6 uppercase text-xs tracking-widest">Layanan Utama</h3>
                    <ul class="space-y-3 text-sm text-slate-500 font-medium">
                        <li><a href="#" class="hover:text-blue-600 transition-colors">Pulsa & Data</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition-colors">PLN Prabayar</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition-colors">Voucher Games</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition-colors">E-Money Top Up</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 mb-6 uppercase text-xs tracking-widest">Support</h3>
                    <ul class="space-y-3 text-sm text-slate-500 font-medium">
                        <li><a href="#" class="hover:text-blue-600 transition-colors">Pusat Bantuan</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition-colors">Syarat Ketentuan</a></li>
                        <li><a href="https://wa.me/<?php echo get_setting('whatsapp_admin'); ?>" class="flex items-center gap-2 text-green-600 font-bold">
                            <i class="fab fa-whatsapp"></i> Customer Service
                        </a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-100 mt-16 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-[11px] text-slate-400 font-bold uppercase tracking-widest">
                <p><?php echo $footer_text; ?></p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-blue-600 transition-colors">Terms</a>
                    <a href="#" class="hover:text-blue-600 transition-colors">Privacy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bottom Nav Mobile -->
    <div class="fixed bottom-0 left-0 right-0 bg-white/80 backdrop-blur-md border-t border-slate-100 sm:hidden flex justify-around py-3 px-2 z-50">
        <a href="/" class="flex flex-col items-center gap-1 text-blue-600">
            <i data-lucide="home" class="w-5 h-5"></i>
            <span class="text-[10px] font-bold">Home</span>
        </a>
        <a href="/user/transactions.php" class="flex flex-col items-center gap-1 text-slate-400">
            <i data-lucide="shopping-bag" class="w-5 h-5"></i>
            <span class="text-[10px] font-bold">Orders</span>
        </a>
        <a href="/user/deposit.php" class="flex flex-col items-center gap-1 text-slate-400">
            <i data-lucide="plus-square" class="w-5 h-5"></i>
            <span class="text-[10px] font-bold">Deposit</span>
        </a>
        <a href="/dashboard.php" class="flex flex-col items-center gap-1 text-slate-400">
            <i data-lucide="user-circle" class="w-5 h-5"></i>
            <span class="text-[10px] font-bold">Account</span>
        </a>
    </div>
    <?php endif; ?>

    <script>
        // Init Lucide Icons
        lucide.createIcons();
    </script>
</body>
</html>
