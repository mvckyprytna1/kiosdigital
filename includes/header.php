<?php
/**
 * Header Component
 * KiosDigital PPOB
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

$app_name = get_setting('app_name') ?? 'KiosDigital PPOB';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $app_name; ?></title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.0/umd/lucide.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans">
    <?php 
    $is_dashboard = (strpos($_SERVER['REQUEST_URI'], '/user/') !== false || strpos($_SERVER['REQUEST_URI'], '/owner/') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || strpos($_SERVER['REQUEST_URI'], '/staff/') !== false);
    ?>

    <?php if ($is_dashboard && isset($_SESSION['user_id'])): ?>
    <div class="flex h-screen w-full overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col hidden lg:flex">
            <div class="p-6 flex items-center gap-3 border-b border-slate-800">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold">KD</div>
                <span class="text-xl font-bold text-white tracking-tight"><?php echo $app_name; ?></span>
            </div>
            <nav class="flex-1 p-4 space-y-1">
                <a href="/dashboard.php" class="flex items-center gap-3 px-4 py-3 <?php echo ($_SERVER['PHP_SELF'] == '/dashboard.php' || strpos($_SERVER['PHP_SELF'], 'index.php') !== false) ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 transition-colors'; ?> rounded-lg">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    Dashboard
                </a>
                <a href="/user/transactions.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 rounded-lg transition-colors">
                    <i data-lucide="history" class="w-5 h-5"></i>
                    Transaksi
                </a>
                <a href="/user/deposit.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 rounded-lg transition-colors">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    Isi Saldo
                </a>
                <?php if($_SESSION['role'] == 'owner'): ?>
                <div class="pt-4 pb-2 px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-t border-slate-800 mt-4">Management</div>
                <a href="/owner/products.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 rounded-lg transition-colors">
                    <i data-lucide="package" class="w-5 h-5"></i>
                    Produk PPOB
                </a>
                <a href="/owner/users.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 rounded-lg transition-colors">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    Pengguna
                </a>
                <a href="/owner/settings.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 rounded-lg transition-colors">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    Pengaturan
                </a>
                <?php endif; ?>
            </nav>
            <div class="p-4 mt-auto border-t border-slate-800">
                <div class="bg-slate-800 p-4 rounded-xl">
                    <p class="text-[10px] text-slate-500 uppercase font-bold mb-2">Akun Saya</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center text-white font-bold text-sm">
                            <?php echo substr($_SESSION['name'], 0, 1); ?>
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-sm font-bold text-white truncate"><?php echo $_SESSION['name']; ?></p>
                            <p class="text-[10px] text-slate-400 truncate"><?php echo $_SESSION['email']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Dashboard Header -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 shadow-sm z-10">
                <div class="flex items-center gap-2 text-slate-500 text-sm">
                    <span class="text-slate-400"><?php echo ucfirst($_SESSION['role']); ?></span>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    <span class="font-medium text-slate-800">Dashboard Area</span>
                </div>
                <div class="flex items-center gap-6">
                    <div class="flex flex-col text-right">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Saldo Utama</span>
                        <span class="text-sm font-bold text-blue-600"><?php echo format_idr($_SESSION['balance'] ?? 0); ?></span>
                    </div>
                    <div class="h-8 w-[1px] bg-slate-200"></div>
                    <a href="/logout.php" class="text-slate-400 hover:text-red-600 transition-colors">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                    </a>
                </div>
            </header>
            
            <!-- Dashboard Main Area -->
            <main class="flex-1 overflow-auto p-4 md:p-8 bg-slate-50">
    <?php else: ?>
    <!-- Standard Navbar for Landing & Auth -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex-shrink-0 flex items-center gap-2">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-sm shadow-blue-200">
                            <i data-lucide="zap" class="w-6 h-6"></i>
                        </div>
                        <span class="font-bold text-xl tracking-tight text-slate-900"><?php echo $app_name; ?></span>
                    </a>
                </div>
                <div class="hidden sm:flex sm:items-center sm:gap-4">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="flex items-center gap-3 bg-slate-50 px-4 py-1.5 rounded-full border border-slate-100">
                            <i data-lucide="wallet" class="w-4 h-4 text-blue-600"></i>
                            <span class="font-bold text-sm text-slate-800"><?php echo format_idr($_SESSION['balance'] ?? 0); ?></span>
                        </div>
                        <a href="/dashboard.php" class="text-slate-600 hover:text-blue-600 font-bold text-sm">Dashboard</a>
                        <a href="/logout.php" class="bg-slate-100 text-slate-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-slate-200 transition-colors">Keluar</a>
                    <?php else: ?>
                        <a href="/login.php" class="text-slate-600 hover:text-blue-600 font-bold text-sm">Masuk</a>
                        <a href="/register.php" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition-all shadow-md shadow-blue-100">Daftar Sekarang</a>
                    <?php endif; ?>
                </div>
                <!-- Mobile menu button -->
                <div class="flex items-center sm:hidden">
                    <button type="button" class="text-slate-400 hover:text-slate-500 focus:outline-none">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>
    <main>
    <?php endif; ?>
