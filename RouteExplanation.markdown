# Penjelasan Rute pada File web.php

Berikut adalah penjelasan sederhana untuk setiap rute yang didefinisikan dalam file `web.php`, dikelompokkan berdasarkan jenisnya.

## Public Routes
Rute-rute ini dapat diakses oleh semua pengguna, baik yang terautentikasi maupun tidak.

| URL | Metode HTTP | Nama Route | Tujuan | Controller & Metode | Middleware |
| --- | --- | --- | --- | --- | --- |
| `/` | GET | `home` | Menampilkan halaman beranda. | `HomeController::index` | - |
| `/about` | GET | `about` | Menampilkan halaman tentang kami. | `HomeController::about` | - |
| `/contact` | GET | `contact` | Menampilkan halaman kontak. | `HomeController::contact` | - |
| `/contact` | POST | `contact.store` | Menyimpan data dari formulir kontak. | `HomeController::contactStore` | - |
| `/privacy-policy` | GET | `privacy` | Menampilkan halaman kebijakan privasi. | `HomeController::privacy` | - |
| `/terms-of-service` | GET | `terms` | Menampilkan halaman syarat dan ketentuan. | `HomeController::terms` | - |
| `/faq` | GET | `faq` | Menampilkan halaman FAQ. | `HomeController::faq` | - |
| `/sitemap` | GET | `sitemap` | Menampilkan halaman peta situs. | `HomeController::sitemap` | - |
| `/search` | GET | `search` | Mengarahkan ke hasil pencarian. | `HomeController::search` | - |
| `/products` | GET | `products.index` | Menampilkan daftar semua produk. | `ProductController::index` | - |
| `/products/{product}` | GET | `products.show` | Menampilkan detail produk tertentu. | `ProductController::show` | - |
| `/products/{product}/reviews` | GET | `reviews.show` | Menampilkan ulasan untuk produk tertentu. | `ReviewController::show` | - |
| `/categories` | GET | `categories.index` | Menampilkan daftar semua kategori. | `CategoryController::index` | - |
| `/categories/{category}` | GET | `categories.show` | Menampilkan produk dalam kategori tertentu. | `CategoryController::show` | - |
| `/brand/{brand}` | GET | `products.brand` | Menampilkan produk berdasarkan merek. | `ProductController::brand` | - |
| `/products/featured` | GET | `products.featured` | Menampilkan produk unggulan. | `ProductController::featured` | - |
| `/cart` | GET | `cart.index` | Menampilkan isi keranjang belanja. | `CartController::index` | - |
| `/cart/add` | POST | `cart.add` | Menambahkan produk ke keranjang. | `CartController::add` | - |
| `/cart/item/{itemId}` | PUT | `cart.update` | Memperbarui jumlah item di keranjang. | `CartController::update` | - |
| `/cart/item/{itemId}` | DELETE | `cart.remove` | Menghapus item dari keranjang. | `CartController::remove` | - |
| `/cart/clear` | POST | `cart.clear` | Menghapus semua item di keranjang. | `CartController::clear` | - |
| `/payment/callback/{gateway}` | POST | `payment.callback` | Menangani callback dari gateway pembayaran. | `PaymentController::callback` | - |
| `/wishlist/public/{token}` | GET | `wishlist.public` | Menampilkan wishlist yang dibagikan melalui token. | `WishlistController::public` | - |

## AJAX Routes
Rute-rute ini digunakan untuk permintaan AJAX, biasanya menghasilkan respons JSON.

| URL | Metode HTTP | Nama Route | Tujuan | Controller & Metode | Middleware |
| --- | --- | --- | --- | --- | --- |
| `/api/search-suggestions` | GET | `products.search.suggestions` | Mendapatkan saran pencarian produk. | `ProductController::searchSuggestions` | - |
| `/api/track-visitor` | POST | `track.visitor` | Melacak data pengunjung. | `HomeController::trackVisitor` | - |
| `/api/quick-stats` | GET | `quick.stats` | Mendapatkan statistik cepat situs. | `HomeController::quickStats` | - |
| `/api/categories/tree` | GET | `categories.tree` | Mendapatkan struktur pohon kategori untuk dropdown. | `CategoryController::getTree` | - |
| `/api/cart/count` | GET | `cart.count` | Mendapatkan jumlah item di keranjang. | `CartController::count` | - |
| `/api/cart/total` | GET | `cart.total` | Mendapatkan total harga keranjang. | `CartController::total` | - |
| `/api/cart/coupon` | POST | `cart.coupon.apply` | Menerapkan kupon ke keranjang. | `CartController::applyCoupon` | - |
| `/api/cart/coupon` | DELETE | `cart.coupon.remove` | Menghapus kupon dari keranjang. | `CartController::removeCoupon` | - |
| `/api/checkout/shipping/calculate` | GET | `checkout.shipping.calculate` | Menghitung tarif pengiriman. | `CheckoutController::calculateShipping` | `cart.not_empty` |
| `/api/checkout/coupon/validate` | GET | `checkout.coupon.validate` | Memvalidasi kode kupon. | `CheckoutController::validateCoupon` | `cart.not_empty` |
| `/api/checkout/coupon` | POST | `checkout.coupon.apply` | Menerapkan kupon ke checkout. | `CheckoutController::applyCoupon` | `cart.not_empty` |
| `/api/checkout/coupon` | DELETE | `checkout.coupon.remove` | Menghapus kupon dari checkout. | `CheckoutController::removeCoupon` | `cart.not_empty` |
| `/api/payment/verify` | GET | `payment.verify` | Memverifikasi status pembayaran. | `PaymentController::verify` | `auth` |
| `/api/orders/{order}/note` | POST | `admin.orders.add_note` | Menambahkan catatan ke pesanan (admin). | `OrderController::addNote` | `auth:admin` |
| `/api/orders/bulk-update` | POST | `admin.orders.bulk_update` | Memperbarui beberapa pesanan sekaligus (admin). | `OrderController::bulkUpdate` | `auth:admin` |
| `/api/wishlist/add` | POST | `wishlist.add` | Menambahkan produk ke wishlist. | `WishlistController::add` | `auth`, `user.status` |
| `/api/wishlist/remove/{product}` | POST | `wishlist.remove` | Menghapus produk dari wishlist. | `WishlistController::remove` | `auth`, `user.status` |
| `/api/wishlist/clear` | POST | `wishlist.clear` | Menghapus semua item dari wishlist. | `WishlistController::clear` | `auth`, `user.status` |
| `/api/wishlist/toggle/{product}` | POST | `wishlist.toggle` | Mengalihkan status produk di wishlist. | `WishlistController::toggle` | `auth`, `user.status` |
| `/api/wishlist/move-to-cart/{product}` | POST | `wishlist.move_to_cart` | Memindahkan produk dari wishlist ke keranjang. | `WishlistController::moveToCart` | `auth`, `user.status` |
| `/api/wishlist/check/{product}` | GET | `wishlist.check` | Memeriksa apakah produk ada di wishlist. | `WishlistController::check` | `auth`, `user.status` |
| `/api/wishlist/count` | GET | `wishlist.count` | Mendapatkan jumlah item di wishlist. | `WishlistController::count` | `auth`, `user.status` |
| `/api/wishlist/suggestions` | GET | `wishlist.suggestions` | Mendapatkan saran produk berdasarkan wishlist. | `WishlistController::suggestions` | `auth`, `user.status` |
| `/api/wishlist/share` | POST | `wishlist.share` | Menghasilkan URL untuk berbagi wishlist. | `WishlistController::share` | `auth`, `user.status` |
| `/api/wishlist/bulk-action` | POST | `wishlist.bulk_action` | Melakukan operasi massal pada wishlist (hapus/pindah ke keranjang). | `WishlistController::bulkAction` | `auth`, `user.status` |
| `/api/reviews/{review}/helpful` | POST | `reviews.helpful` | Menandai ulasan sebagai membantu. | `ReviewController::helpful` | `auth`, `user.status` |
| `/api/reviews/{review}/unhelpful` | POST | `reviews.unhelpful` | Menghapus tanda membantu dari ulasan. | `ReviewController::unhelpful` | `auth`, `user.status` |
| `/api/reviews/bulk-action` | POST | `admin.reviews.bulk_action` | Melakukan operasi massal pada ulasan (setujui/tolak/hapus) oleh admin. | `ReviewController::bulkAction` | `auth:admin` |

## Newsletter Routes
Rute-rute ini menangani langganan dan pembatalan langganan newsletter.

| URL | Metode HTTP | Nama Route | Tujuan | Controller & Metode | Middleware |
| --- | --- | --- | --- | --- | --- |
| `/newsletter/subscribe` | POST | `newsletter.subscribe` | Berlangganan newsletter. | `HomeController::newsletterSubscribe` | - |
| `/newsletter/unsubscribe/{token}` | GET | `newsletter.unsubscribe` | Membatalkan langganan newsletter. | `HomeController::newsletterUnsubscribe` | - |

## Guest Routes
Rute-rute ini hanya dapat diakses oleh pengguna yang belum terautentikasi.

| URL | Metode HTTP | Nama Route | Tujuan | Controller & Metode | Middleware |
| --- | --- | --- | --- | --- | --- |
| `/login` | GET | `login` | Menampilkan formulir login. | `AuthController::showLogin` | `guest` |
| `/login` | POST | `login.submit` | Menangani proses login. | `AuthController::login` | `guest` |
| `/register` | GET | `register` | Menampilkan formulir registrasi. | `AuthController::showRegister` | `guest` |
| `/register` | POST | `register.submit` | Menangani proses registrasi. | `AuthController::register` | `guest` |

## Authenticated Routes
Rute-rute ini hanya dapat diakses oleh pengguna yang terautentikasi, dengan status pengguna yang valid.

| URL | Metode HTTP | Nama Route | Tujuan | Controller & Metode | Middleware |
| --- | --- | --- | --- | --- | --- |
| `/logout` | POST | `logout` | Menangani proses logout. | `AuthController::logout` | `auth`, `user.status` |
| `/dashboard` | GET | `dashboard` | Menampilkan dashboard pengguna. | `AuthController::dashboard` | `auth`, `user.status` |
| `/reviews/{product}` | POST | `reviews.store` | Menyimpan ulasan baru untuk produk. | `ReviewController::store` | `auth`, `user.status` |
| `/reviews/{review}/update` | POST | `reviews.update` | Memperbarui ulasan yang ada. | `ReviewController::update` | `auth`, `user.status` |
| `/reviews` | GET | `reviews.user` | Menampilkan ulasan pengguna. | `ReviewController::userReviews` | `auth`, `user.status` |
| `/wishlist` | GET | `wishlist.index` | Menampilkan daftar wishlist pengguna. | `WishlistController::index` | `auth`, `user.status` |
| `/orders` | GET | `orders.index` | Menampilkan daftar pesanan pengguna. | `OrderController::index` | `auth`, `user.status` |
| `/orders/{order}` | GET | `orders.show` | Menampilkan detail pesanan tertentu. | `OrderController::show` | `auth`, `user.status` |
| `/orders/store` | POST | `orders.store` | Membuat pesanan dari keranjang. | `OrderController::store` | `auth`, `user.status` |
| `/orders/{order}/cancel` | POST | `orders.cancel` | Membatalkan pesanan. | `OrderController::cancel` | `auth`, `user.status` |
| `/orders/{order}/reorder` | POST | `orders.reorder` | Menambahkan item pesanan kembali ke keranjang. | `OrderController::reorder` | `auth`, `user.status` |
| `/orders/{order}/track` | GET | `orders.track` | Melacak status pesanan. | `OrderController::track` | `auth`, `user.status` |
| `/orders/{order}/review` | GET | `orders.review` | Menampilkan formulir ulasan pesanan. | `OrderController::review` | `auth`, `user.status` |
| `/orders/{order}/review` | POST | `orders.store_review` | Menyimpan ulasan untuk pesanan. | `OrderController::storeReview` | `auth`, `user.status` |
| `/checkout` | GET | `checkout.index` | Menampilkan halaman checkout. | `CheckoutController::index` | `auth`, `user.status`, `cart.not_empty` |
| `/checkout/shipping` | POST | `checkout.shipping` | Menangani langkah pengiriman di checkout. | `CheckoutController::shipping` | `auth`, `user.status`, `cart.not_empty` |
| `/checkout/payment` | POST | `checkout.payment` | Menangani langkah pembayaran di checkout. | `CheckoutController::payment` | `auth`, `user.status`, `cart.not_empty` |
| `/checkout/review` | GET | `checkout.review` | Menampilkan halaman tinjauan pesanan. | `CheckoutController::review` | `auth`, `user.status`, `cart.not_empty` |
| `/checkout/process` | POST | `checkout.process` | Memproses pesanan di checkout. | `CheckoutController::process` | `auth`, `user.status`, `cart.not_empty` |
| `/checkout/success/{order}` | GET | `checkout.success` | Menampilkan halaman sukses checkout. | `CheckoutController::success` | `auth`, `user.status` |
| `/checkout/failed` | GET | `checkout.failed` | Menampilkan halaman gagal checkout. | `CheckoutController::failed` | `auth`, `user.status` |
| `/payment/process` | POST | `payment.process` | Memproses pembayaran untuk pesanan. | `PaymentController::process` | `auth`, `user.status` |
| `/payment/success` | GET | `payment.success` | Menampilkan halaman sukses pembayaran. | `PaymentController::success` | `auth`, `user.status` |
| `/payment/failed` | GET | `payment.failed` | Menampilkan halaman gagal pembayaran. | `PaymentController::failed` | `auth`, `user.status` |

## Profile Routes
Rute-rute ini menangani pengelolaan profil pengguna, hanya dapat diakses oleh pengguna terautentikasi.

| URL | Metode HTTP | Nama Route | Tujuan | Controller & Metode | Middleware |
| --- | --- | --- | --- | --- | --- |
| `/profile` | GET | `profile.index` | Menampilkan dashboard profil pengguna. | `ProfileController::index` | `auth`, `user.status` |
| `/profile/edit` | GET | `profile.edit` | Menampilkan formulir edit profil. | `ProfileController::edit` | `auth`, `user.status` |
| `/profile` | PUT | `profile.update` | Memperbarui data profil pengguna. | `ProfileController::update` | `auth`, `user.status` |
| `/profile/avatar` | POST | `profile.avatar.upload` | Mengunggah avatar pengguna. | `ProfileController::uploadAvatar` | `auth`, `user.status` |
| `/profile/password` | PUT | `profile.password.update` | Mengubah kata sandi pengguna. | `ProfileController::changePassword` | `auth`, `user.status` |
| `/profile/orders` | GET | `profile.orders` | Menampilkan riwayat pesanan pengguna. | `ProfileController::orders` | `auth`, `user.status` |
| `/profile/addresses` | GET | `profile.addresses` | Menampilkan daftar alamat pengguna. | `ProfileController::addresses` | `auth`, `user.status` |
| `/profile/addresses` | POST | `profile.addresses.store` | Menyimpan alamat baru. | `ProfileController::storeAddress` | `auth`, `user.status` |
| `/profile/addresses/{addressId}` | PUT | `profile.addresses.update` | Memperbarui alamat pengguna. | `ProfileController::updateAddress` | `auth`, `user.status` |
| `/profile/addresses/{addressId}` | DELETE | `profile.addresses.delete` | Menghapus alamat pengguna. | `ProfileController::deleteAddress` | `auth`, `user.status` |
| `/profile/addresses/{addressId}/default` | PUT | `profile.addresses.set_default` | Menetapkan alamat default. | `ProfileController::setDefaultAddress` | `auth`, `user.status` |
| `/profile/reviews` | GET | `profile.reviews` | Menampilkan ulasan pengguna di profil. | `ProfileController::reviews` | `auth`, `user.status` |
| `/profile/notifications` | GET | `profile.notifications` | Menampilkan notifikasi pengguna. | `ProfileController::notifications` | `auth`, `user.status` |
| `/profile/loyalty` | GET | `profile.loyalty` | Menampilkan poin loyalitas/reward pengguna. | `ProfileController::loyalty` | `auth`, `user.status` |
| `/profile/downloads` | GET | `profile.downloads` | Menampilkan file yang dapat diunduh pengguna. | `ProfileController::downloads` | `auth`, `user.status` |
| `/profile/export-data` | GET | `profile.export_data` | Mengekspor data pengguna. | `ProfileController::exportData` | `auth`, `user.status` |
| `/profile/delete-account` | POST | `profile.delete_account` | Menghapus akun pengguna. | `ProfileController::deleteAccount` | `auth`, `user.status` |

## Admin Routes (Accessible by admin, super_admin, staff roles)
Rute-rute ini hanya dapat diakses oleh pengguna dengan peran admin, super_admin, atau staff.

| URL | Metode HTTP | Nama Route | Tujuan | Controller & Metode | Middleware |
| --- | --- | --- | --- | --- | --- |
| `/admin/dashboard` | GET | `admin.dashboard` | Menampilkan dashboard admin. | `AdminDashboardController::index` | `auth`, `role:admin,super_admin,staff` |
| `/admin/analytics` | GET | `admin.analytics` | Menampilkan data analitik admin. | `AdminDashboardController::analytics` | `auth`, `role:admin,super_admin,staff` |
| `/admin/reports` | GET | `admin.reports` | Menampilkan laporan admin. | `AdminDashboardController::reports` | `auth`, `role:admin,super_admin,staff` |
| `/admin/sales-chart` | GET | `admin.sales_chart` | Menampilkan data grafik penjualan. | `AdminDashboardController::salesChart` | `auth`, `role:admin,super_admin,staff` |
| `/admin/overview` | GET | `admin.overview` | Menampilkan ringkasan data admin. | `AdminDashboardController::overview` | `auth`, `role:admin,super_admin,staff` |
| `/admin/export` | POST | `admin.export` | Mengekspor data admin. | `AdminDashboardController::export` | `auth`, `role:admin,super_admin,staff` |
| `/admin/products` | GET | `admin.products.index` | Menampilkan daftar produk untuk admin. | `ProductController::adminIndex` | `auth`, `role:admin,super_admin,staff` |
| `/admin/products/create` | GET | `admin.products.create` | Menampilkan formulir pembuatan produk. | `ProductController::create` | `auth`, `role:admin,super_admin,staff` |
| `/admin/products` | POST | `admin.products.store` | Menyimpan produk baru. | `ProductController::store` | `auth`, `role:admin,super_admin,staff` |
| `/admin/products/{product}/edit` | GET | `admin.products.edit` | Menampilkan formulir edit produk. | `ProductController::edit` | `auth`, `role:admin,super_admin,staff` |
| `/admin/products/{product}` | PUT | `admin.products.update` | Memperbarui produk. | `ProductController::update` | `auth`, `role:admin,super_admin,staff` |
| `/admin/products/{product}` | DELETE | `admin.products.destroy` | Menghapus produk. | `ProductController::destroy` | `auth`, `role:admin,super_admin,staff` |
| `/admin/products/{product}/stock` | POST | `admin.products.stock.update` | Memperbarui stok produk. | `ProductController::updateStock` | `auth`, `role:admin,super_admin,staff` |
| `/admin/products/{product}/status` | PUT | `admin.products.status.toggle` | Mengubah status produk (aktif/tidak aktif). | `ProductController::toggleStatus` | `auth`, `role:admin,super_admin,staff` |

## Admin Routes (Accessible by admin with auth:admin)
Rute-rute ini hanya dapat diakses oleh admin dengan autentikasi khusus (`auth:admin`).

| URL | Metode HTTP | Nama Route | Tujuan | Controller & Metode | Middleware |
| --- | --- | --- | --- | --- | --- |
| `/admin/categories` | GET | `admin.categories.index` | Menampilkan daftar kategori untuk admin. | `CategoryController::adminIndex` | `auth:admin` |
| `/admin/categories/create` | GET | `admin.categories.create` | Menampilkan formulir pembuatan kategori. | `CategoryController::create` | `auth:admin` |
| `/admin/categories` | POST | `admin.categories.store` | Menyimpan kategori baru. | `CategoryController::store` | `auth:admin` |
| `/admin/categories/{category}/edit` | GET | `admin.categories.edit` | Menampilkan formulir edit kategori. | `CategoryController::edit` | `auth:admin` |
| `/admin/categories/{category}` | PUT | `admin.categories.update` | Memperbarui kategori. | `CategoryController::update` | `auth:admin` |
| `/admin/categories/{category}` | DELETE | `admin.categories.destroy` | Menghapus kategori. | `CategoryController::destroy` | `auth:admin` |
| `/admin/categories/reorder` | POST | `admin.categories.reorder` | Mengatur ulang urutan kategori. | `CategoryController::reorder` | `auth:admin` |
| `/admin/categories/{category}/status` | PUT | `admin.categories.status.toggle` | Mengubah status kategori (aktif/tidak aktif). | `CategoryController::toggleStatus` | `auth:admin` |
| `/admin/payments` | GET | `admin.payments.index` | Menampilkan daftar pembayaran untuk admin. | `PaymentController::adminIndex` | `auth:admin` |
| `/admin/payments/{payment}` | GET | `admin.payments.show` | Menampilkan detail pembayaran. | `PaymentController::adminShow` | `auth:admin` |
| `/admin/payments/{payment}/approve` | POST | `admin.payments.approve` | Menyetujui pembayaran manual. | `PaymentController::approve` | `auth:admin` |
| `/admin/payments/{payment}/reject` | POST | `admin.payments.reject` | Menolak pembayaran manual. | `PaymentController::reject` | `auth:admin` |
| `/admin/orders` | GET | `admin.orders.index` | Menampilkan daftar pesanan untuk admin. | `OrderController::adminIndex` | `auth:admin` |
| `/admin/orders/{order}` | GET | `admin.orders.show` | Menampilkan detail pesanan untuk admin. | `OrderController::adminShow` | `auth:admin` |
| `/admin/orders/{order}/status` | POST | `admin.orders.update_status` | Memperbarui status pesanan. | `OrderController::updateStatus` | `auth:admin` |
| `/admin/orders/{order}/invoice` | GET | `admin.orders.print_invoice` | Mencetak faktur pesanan. | `OrderController::printInvoice` | `auth:admin` |
| `/admin/reviews` | GET | `admin.reviews.index` | Menampilkan daftar ulasan untuk moderasi. | `ReviewController::adminIndex` | `auth:admin` |
| `/admin/reviews/{review}` | GET | `admin.reviews.show` | Menampilkan detail ulasan. | `ReviewController::adminShow` | `auth:admin` |
| `/admin/reviews/{review}/approve` | POST | `admin.reviews.approve` | Menyetujui ulasan. | `ReviewController::approve` | `auth:admin` |
| `/admin/reviews/{review}/reject` | POST | `admin.reviews.reject` | Menolak ulasan. | `ReviewController::reject` | `auth:admin` |
| `/admin/reviews/{review}/destroy` | POST | `admin.reviews.destroy` | Menghapus ulasan. | `ReviewController::destroy` | `auth:admin` |

## Custom Routes
Rute khusus untuk menangani kasus tertentu.

| URL | Metode HTTP | Nama Route | Tujuan | Controller & Metode | Middleware |
| --- | --- | --- | --- | --- | --- |
| * | * | `notfound` | Menangani halaman 404 (tidak ditemukan). | `HomeController::notFound` | - |
| `/health` | GET | `health` | Memeriksa status kesehatan aplikasi. | `HomeController::health` | - |
