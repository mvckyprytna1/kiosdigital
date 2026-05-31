<?php
/**
 * Game Top Up Page
 * KiosDigital PPOB
 */
include '../includes/header.php';
check_role(['user']);

$games = [
    ['name' => 'Mobile Legends', 'slug' => 'mobile-legends', 'image' => 'https://api.dicebear.com/7.x/initials/svg?seed=ML'],
    ['name' => 'Free Fire', 'slug' => 'free-fire', 'image' => 'https://api.dicebear.com/7.x/initials/svg?seed=FF'],
    ['name' => 'PUBG Mobile', 'slug' => 'pubg-mobile', 'image' => 'https://api.dicebear.com/7.x/initials/svg?seed=PUBG'],
    ['name' => 'Valorant', 'slug' => 'valorant', 'image' => 'https://api.dicebear.com/7.x/initials/svg?seed=VAL'],
    ['name' => 'Genshin Impact', 'slug' => 'genshin-impact', 'image' => 'https://api.dicebear.com/7.x/initials/svg?seed=GI']
];
?>

<div class="max-w-7xl mx-auto px-4 py-8 md:py-16">
    <div class="mb-12 text-center">
        <h2 class="text-4xl font-black text-gray-900 tracking-tight mb-3">Top Up Game Favorit</h2>
        <p class="text-gray-500 font-medium max-w-2xl mx-auto text-lg">Pilih game favoritmu dan kirimkan Diamond/UC sekarang juga dengan proses hitungan detik.</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">
        <?php foreach($games as $g): ?>
        <a href="/user/game-order.php?slug=<?php echo $g['slug']; ?>" class="group bg-white rounded-[2rem] p-6 border border-gray-100 shadow-xl shadow-gray-50 hover:shadow-2xl hover:border-blue-500 hover:-translate-y-2 transition-all flex flex-col items-center gap-4 text-center">
            <div class="w-24 h-24 rounded-3xl overflow-hidden shadow-lg group-hover:scale-105 transition-transform">
                <img src="<?php echo $g['image']; ?>" alt="<?php echo $g['name']; ?>" class="w-full h-full object-cover">
            </div>
            <span class="font-black text-gray-800 text-lg group-hover:text-blue-600"><?php echo $g['name']; ?></span>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-green-50 text-green-600 ring-1 ring-inset ring-green-600/10">OTOMATIS</span>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
