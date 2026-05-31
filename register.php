<?php
/**
 * Register Page
 * KiosDigital PPOB
 */
require_once 'config/database.php';
require_once 'includes/functions.php';

if (isset($_SESSION['user_id'])) {
    redirect('/dashboard.php');
}

$error = "";
$success = "";

if (isset($_POST['register'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];

    // Check if email exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $error = "Email sudah terdaftar.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user';
        $status = 'active';

        $ins = $conn->prepare("INSERT INTO users (name, email, phone, password, role, status) VALUES (?, ?, ?, ?, ?, ?)");
        $ins->bind_param("ssssss", $name, $email, $phone, $hash, $role, $status);

        if ($ins->execute()) {
            $success = "Pendaftaran berhasil! Silakan masuk.";
        } else {
            $error = "Gagal mendaftar. Silakan coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - KiosDigital PPOB</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.0/umd/lucide.min.js"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
    <div class="max-w-md w-full py-10">
        <div class="text-center mb-8">
             <a href="/" class="inline-flex items-center gap-2 mb-4">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <i data-lucide="zap" class="w-7 h-7"></i>
                </div>
                <span class="text-2xl font-bold tracking-tight text-gray-900">KiosDigital</span>
            </a>
            <h2 class="text-xl font-semibold text-gray-700">Buat Akun Baru</h2>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
            <?php if($error): ?>
                <div class="bg-red-50 text-red-600 px-4 py-3 rounded-xl mb-6 text-sm font-medium border border-red-100 italic">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="bg-green-50 text-green-600 px-4 py-3 rounded-xl mb-6 text-sm font-bold border border-green-100">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nama Lengkap</label>
                        <input type="text" name="name" required class="w-full px-5 py-3 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-600 outline-none transition-all" placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Email</label>
                        <input type="email" name="email" required class="w-full px-5 py-3 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-600 outline-none transition-all" placeholder="name@domain.com">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">No. WhatsApp</label>
                        <input type="text" name="phone" required class="w-full px-5 py-3 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-600 outline-none transition-all" placeholder="08123xxx">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Password</label>
                        <input type="password" name="password" required class="w-full px-5 py-3 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-600 outline-none transition-all" placeholder="••••••••">
                    </div>
                    <button type="submit" name="register" class="w-full bg-gray-900 text-white py-4 rounded-2xl font-bold shadow-lg hover:bg-gray-800 active:scale-95 transition-all mt-4">
                        Daftar Akun
                    </button>
                </div>
            </form>
            
            <div class="mt-8 pt-8 border-t border-gray-50 text-center">
                <p class="text-sm text-gray-500">Sudah punya akun? <a href="/login.php" class="text-blue-600 font-bold hover:underline">Masuk Sini</a></p>
            </div>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
