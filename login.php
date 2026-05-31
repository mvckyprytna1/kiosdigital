<?php
/**
 * Login Page
 * KiosDigital PPOB
 */
require_once 'config/database.php';
require_once 'includes/functions.php';

if (isset($_SESSION['user_id'])) {
    redirect('/dashboard.php');
}

$error = "";

if (isset($_POST['login'])) {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            if ($user['status'] == 'active') {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['balance'] = $user['balance'];

                redirect('/dashboard.php');
            } else {
                $error = "Akun Anda sedang dinonaktifkan.";
            }
        } else {
            $error = "Email atau password salah.";
        }
    } else {
        $error = "Email atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - KiosDigital PPOB</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.0/umd/lucide.min.js"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-10">
            <a href="/" class="inline-flex items-center gap-2 mb-4">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <i data-lucide="zap" class="w-7 h-7"></i>
                </div>
                <span class="text-2xl font-bold tracking-tight text-gray-900">KiosDigital</span>
            </a>
            <h2 class="text-xl font-semibold text-gray-700">Masuk ke Akun Anda</h2>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <?php if($error): ?>
                <div class="bg-red-50 text-red-600 px-4 py-3 rounded-lg mb-6 text-sm font-medium border border-red-100">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition-all" placeholder="name@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition-all" placeholder="••••••••">
                    </div>
                    <button type="submit" name="login" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 active:scale-95 transition-all">
                        Masuk Sekarang
                    </button>
                </div>
            </form>
            
            <div class="mt-8 pt-8 border-t border-gray-50 text-center">
                <p class="text-sm text-gray-500">Belum punya akun? <a href="/register.php" class="text-blue-600 font-bold hover:underline">Daftar Sekarang</a></p>
            </div>
        </div>
        
        <p class="mt-8 text-center text-gray-400 text-xs italic">
            Default Login (Dev Only): owner@kiosdigital.test / owner123
        </p>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
