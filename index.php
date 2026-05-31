<?php
/**
 * Landing Page
 * KiosDigital PPOB
 */
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="relative bg-white pt-24 pb-32 overflow-hidden">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full opacity-5 pointer-events-none">
        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-600 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-600 rounded-full blur-3xl"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.2em] bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10 mb-8 animate-fade-in">
            Digital Commerce Solution
        </span>
        <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tight leading-[1.1] mb-8">
            Fastest Way to Buy <br>
            <span class="text-blue-600 italic">Digital Goods.</span>
        </h1>
        <p class="mt-6 text-lg md:text-xl text-slate-500 max-w-3xl mx-auto leading-relaxed font-medium">
            Join thousands of users who trust KiosDigital for their daily PPOB needs. Cheap, reliable, and processed in seconds 24/7.
        </p>
        <div class="mt-12 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="/register.php" class="w-full sm:w-auto px-10 py-5 bg-blue-600 text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-2xl shadow-blue-200 hover:bg-blue-700 hover:-translate-y-1 transition-all active:scale-95">
                Join KiosDigital
            </a>
            <a href="#services" class="w-full sm:w-auto px-10 py-5 bg-slate-50 text-slate-900 rounded-2xl font-black text-sm uppercase tracking-widest border border-slate-200 hover:bg-slate-100 transition-all">
                Explore Services
            </a>
        </div>
    </div>
</section>

<!-- Stats Bar -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-20">
    <div class="bg-white rounded-[2rem] p-8 md:p-12 shadow-2xl shadow-slate-200 border border-slate-100 grid grid-cols-2 md:grid-cols-4 gap-8">
        <div class="text-center md:border-r border-slate-50">
            <h4 class="text-3xl font-black text-slate-900 mb-1">50k+</h4>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Active Users</p>
        </div>
        <div class="text-center md:border-r border-slate-50">
            <h4 class="text-3xl font-black text-slate-900 mb-1">1M+</h4>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Transactions</p>
        </div>
        <div class="text-center md:border-r border-slate-50">
            <h4 class="text-3xl font-black text-slate-900 mb-1">200+</h4>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Products</p>
        </div>
        <div class="text-center">
            <h4 class="text-3xl font-black text-slate-900 mb-1">99.9%</h4>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Uptime</p>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="py-32 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-20">
            <span class="text-blue-600 font-black text-[10px] uppercase tracking-widest block mb-4">Our Ecosystem</span>
            <h2 class="text-4xl font-black text-slate-900 tracking-tight">Everything you need <br> in one platform.</h2>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-6 md:gap-8">
            <?php
            $categories = [
                ['name' => 'Pulsa', 'slug' => 'pulsa', 'icon' => 'smartphone', 'color' => 'bg-blue-600'],
                ['name' => 'Data', 'slug' => 'data', 'icon' => 'globe', 'color' => 'bg-indigo-600'],
                ['name' => 'Token PLN', 'slug' => 'pln', 'icon' => 'zap', 'color' => 'bg-amber-500'],
                ['name' => 'Game', 'slug' => 'game', 'icon' => 'gamepad-2', 'color' => 'bg-rose-600'],
                ['name' => 'E-Money', 'slug' => 'ewallet', 'icon' => 'credit-card', 'color' => 'bg-emerald-600'],
                ['name' => 'Voucher', 'slug' => 'voucher', 'icon' => 'ticket', 'color' => 'bg-orange-500'],
                ['name' => 'Billing', 'slug' => 'billing', 'icon' => 'clipboard-list', 'color' => 'bg-slate-800']
            ];
            foreach ($categories as $cat):
            ?>
            <a href="/user/<?php echo $cat['slug']; ?>.php" class="group bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-100 hover:shadow-2xl hover:border-blue-500 transition-all flex flex-col items-center gap-6">
                <div class="w-14 h-14 <?php echo $cat['color']; ?> rounded-2xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 group-hover:-rotate-6 transition-all duration-300">
                    <i data-lucide="<?php echo $cat['icon']; ?>" class="w-7 h-7"></i>
                </div>
                <span class="text-xs font-black text-slate-900 uppercase tracking-widest"><?php echo $cat['name']; ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Why Us -->
<section class="py-32 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-16 md:gap-24">
            <div class="space-y-6">
                <div class="w-16 h-16 bg-blue-50 rounded-3xl flex items-center justify-center text-blue-600">
                    <i data-lucide="zap" class="w-8 h-8"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight">Instant Delivery</h3>
                <p class="text-slate-500 font-medium leading-relaxed">No more waiting. Our automated system handles your orders instantly, 24 hours a day.</p>
            </div>
            <div class="space-y-6">
                <div class="w-16 h-16 bg-indigo-50 rounded-3xl flex items-center justify-center text-indigo-600">
                    <i data-lucide="shield-check" class="w-8 h-8"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight">Enterprise Security</h3>
                <p class="text-slate-500 font-medium leading-relaxed">Your data and transactions are secured with military-grade encryption and secure gateways.</p>
            </div>
            <div class="space-y-6">
                <div class="w-16 h-16 bg-emerald-50 rounded-3xl flex items-center justify-center text-emerald-600">
                    <i data-lucide="headset" class="w-8 h-8"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight">Expert Support</h3>
                <p class="text-slate-500 font-medium leading-relaxed">Facing issues? Our dedicated support team is ready to help you anytime, anywhere.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
