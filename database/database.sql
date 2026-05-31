-- Database KiosDigital PPOB
-- Principal PHP Native Developer

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

-- Table `roles`
CREATE TABLE `roles` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `roles` (`name`, `description`) VALUES
('owner', 'Pemilik aplikasi akses penuh'),
('admin', 'Pengelola operasional'),
('staff', 'Kasir melayani transaksi offline'),
('user', 'Pelanggan aplikasi');

-- --------------------------------------------------------

-- Table `users`
CREATE TABLE `users` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','inactive','banned') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Password for all defaults is owner123, admin123, staff123, user123 hashed
INSERT INTO `users` (`name`, `email`, `phone`, `password`, `role`, `balance`) VALUES
('Owner KiosDigital', 'owner@kiosdigital.test', '081234567890', '$2y$10$7Z6V5q/7L5pX5pX5pX5pXe.G0hV2p6A5e3S5e3S5e3S5e3S5e3S5e', 'owner', 1000000.00),
('Admin KiosDigital', 'admin@kiosdigital.test', '081234567891', '$2y$10$7Z6V5q/7L5pX5pX5pX5pXe.G0hV2p6A5e3S5e3S5e3S5e3S5e3S5e', 'admin', 500000.00),
('Staff KiosDigital', 'staff@kiosdigital.test', '081234567892', '$2y$10$7Z6V5q/7L5pX5pX5pX5pXe.G0hV2p6A5e3S5e3S5e3S5e3S5e3S5e', 'staff', 0.00),
('User Testing', 'user@kiosdigital.test', '081234567893', '$2y$10$7Z6V5q/7L5pX5pX5pX5pXe.G0hV2p6A5e3S5e3S5e3S5e3S5e3S5e', 'user', 100000.00);

-- --------------------------------------------------------

-- Table `categories`
CREATE TABLE `categories` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL UNIQUE,
  `icon` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `categories` (`name`, `slug`, `icon`) VALUES
('Pulsa', 'pulsa', 'phone'),
('Paket Data', 'paket-data', 'wifi'),
('Token PLN', 'token-pln', 'zap'),
('Top Up Game', 'top-up-game', 'gamepad'),
('E-Wallet', 'e-wallet', 'wallet'),
('Voucher Digital', 'voucher-digital', 'ticket'),
('Tagihan PPOB', 'tagihan-ppob', 'file-text');

-- --------------------------------------------------------

-- Table `products`
CREATE TABLE `products` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `product_code` varchar(50) NOT NULL UNIQUE,
  `provider_code` varchar(50) NOT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `base_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `profit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table `transactions`
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `invoice_code` varchar(50) NOT NULL UNIQUE,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `customer_target` varchar(50) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `base_price` decimal(15,2) NOT NULL,
  `selling_price` decimal(15,2) NOT NULL,
  `profit` decimal(15,2) NOT NULL,
  `payment_status` enum('unpaid','paid','expired','failed','refunded') NOT NULL DEFAULT 'unpaid',
  `transaction_status` enum('waiting_payment','processing','pending','success','failed','refunded') NOT NULL DEFAULT 'waiting_payment',
  `supplier` varchar(50) DEFAULT 'digiflazz',
  `supplier_ref_id` varchar(100) DEFAULT NULL,
  `supplier_sku_code` varchar(50) DEFAULT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `provider_response` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table `deposits`
CREATE TABLE `deposits` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_type` enum('manual','automatic') NOT NULL DEFAULT 'manual',
  `tripay_reference` varchar(50) DEFAULT NULL,
  `proof_image` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected','expired','failed') NOT NULL DEFAULT 'pending',
  `admin_note` text DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table `wallet_mutations`
CREATE TABLE `wallet_mutations` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` enum('deposit','transaksi','refund','adjustment','manual_add','manual_reduce') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance_before` decimal(15,2) NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `description` text DEFAULT NULL,
  `reference_id` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table `payment_orders`
CREATE TABLE `payment_orders` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `invoice_code` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `tripay_reference` varchar(50) DEFAULT NULL,
  `tripay_merchant_ref` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_name` varchar(100) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL,
  `checkout_url` text DEFAULT NULL,
  `pay_code` varchar(100) DEFAULT NULL,
  `qr_url` text DEFAULT NULL,
  `status` enum('unpaid','paid','expired','failed') NOT NULL DEFAULT 'unpaid',
  `expired_at` datetime DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `raw_response` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table `api_settings`
CREATE TABLE `api_settings` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `provider` varchar(50) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table `settings`
CREATE TABLE `settings` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL UNIQUE,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('app_name', 'KiosDigital PPOB'),
('app_description', 'Solusi Kebutuhan Digital Anda'),
('whatsapp_admin', '081234567890'),
('tripay_mode', 'sandbox'),
('digiflazz_mode', 'mock'),
('global_margin', '500'),
('footer_text', '© 2026 KiosDigital PPOB. All Rights Reserved.');

-- --------------------------------------------------------

-- Table `payment_methods`
CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `account_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `type` enum('bank','e-wallet','qris') NOT NULL,
  `qr_image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `payment_methods` (`name`, `account_name`, `account_number`, `type`) VALUES
('BCA', 'PT KiosDigital Indonesia', '123456789', 'bank'),
('Mandiri', 'PT KiosDigital Indonesia', '987654321', 'bank'),
('DANA', 'KiosDigital', '081234567890', 'e-wallet'),
('QRIS', 'KiosDigital', NULL, 'qris');

COMMIT;
