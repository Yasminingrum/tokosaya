@extends('layouts.app')

@section('title', 'FAQ - Frequently Asked Questions - TokoSaya')
@section('meta_description', 'Temukan jawaban untuk pertanyaan yang sering diajukan seputar TokoSaya. Panduan lengkap berbelanja, pembayaran, pengiriman, dan layanan pelanggan.')

@push('styles')
<style>
    .hero-faq {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
        color: white;
        padding: 100px 0;
        position: relative;
        overflow: hidden;
    }

    .hero-faq::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="90" r="1.5" fill="rgba(255,255,255,0.1)"/></svg>');
        animation: float 15s infinite ease-in-out;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    .search-box {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(20px);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 25px;
        padding: 8px;
        margin-top: 40px;
        transition: all 0.3s ease;
    }

    .search-box:focus-within {
        background: rgba(255, 255, 255, 0.25);
        border-color: rgba(255, 255, 255, 0.4);
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }

    .search-input {
        background: transparent;
        border: none;
        color: white;
        font-size: 1.1rem;
        padding: 15px 25px;
        width: 100%;
        outline: none;
    }

    .search-input::placeholder {
        color: rgba(255, 255, 255, 0.8);
    }

    .search-btn {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        padding: 15px 25px;
        border-radius: 20px;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .search-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
        transform: scale(1.05);
    }

    .category-nav {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
        border: 1px solid #f1f5f9;
        margin-top: -50px;
        position: relative;
        z-index: 10;
    }

    .category-btn {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        color: #64748b;
        padding: 15px 25px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-block;
        margin: 5px;
        position: relative;
        overflow: hidden;
    }

    .category-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        transition: left 0.3s ease;
        z-index: -1;
    }

    .category-btn:hover, .category-btn.active {
        color: white;
        border-color: #6366f1;
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
    }

    .category-btn:hover::before, .category-btn.active::before {
        left: 0;
    }

    .faq-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .faq-section {
        margin-bottom: 50px;
    }

    .section-title {
        color: #1e293b;
        font-weight: 700;
        margin-bottom: 30px;
        position: relative;
        padding-left: 25px;
    }

    .section-title::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border-radius: 3px;
    }

    .faq-item {
        background: white;
        border-radius: 20px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .faq-item:hover {
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        transform: translateY(-5px);
        border-color: #6366f1;
    }

    .faq-question {
        padding: 25px 30px;
        cursor: pointer;
        background: transparent;
        border: none;
        width: 100%;
        text-align: left;
        font-weight: 600;
        color: #374151;
        font-size: 1.1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
        position: relative;
    }

    .faq-question:hover {
        color: #6366f1;
        background: rgba(99, 102, 241, 0.05);
    }

    .faq-question.active {
        color: #6366f1;
        background: rgba(99, 102, 241, 0.1);
    }

    .faq-question .icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .faq-question.active .icon {
        transform: rotate(180deg);
        background: linear-gradient(135deg, #8b5cf6, #ec4899);
    }

    .faq-answer {
        padding: 0 30px 25px;
        color: #64748b;
        line-height: 1.8;
        display: none;
        animation: fadeInUp 0.4s ease;
        font-size: 1rem;
    }

    .faq-answer.show {
        display: block;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .popular-badge {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stats-section {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-radius: 25px;
        padding: 50px;
        margin: 60px 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .stats-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6, #ec4899);
    }

    .stat-item {
        text-align: center;
        margin-bottom: 30px;
    }

    .stat-number {
        font-size: 3rem;
        font-weight: 700;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 10px;
        line-height: 1;
    }

    .stat-label {
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.9rem;
    }

    .contact-cta {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        border-radius: 25px;
        padding: 50px;
        text-align: center;
        margin: 60px 0;
        position: relative;
        overflow: hidden;
    }

    .contact-cta::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
        background-size: 40px 40px;
        animation: movePattern 20s linear infinite;
    }

    @keyframes movePattern {
        0% { transform: translate(0, 0) rotate(0deg); }
        100% { transform: translate(40px, 40px) rotate(360deg); }
    }

    .btn-cta {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 15px 40px;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        margin: 0 10px;
        position: relative;
        z-index: 2;
    }

    .btn-cta:hover {
        background: white;
        color: #6366f1;
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(255, 255, 255, 0.3);
    }

    .no-results {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
    }

    .no-results i {
        font-size: 4rem;
        color: #cbd5e1;
        margin-bottom: 20px;
    }

    .highlight {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.2));
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 600;
    }

    .quick-links {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid #f1f5f9;
        margin-bottom: 40px;
    }

    .quick-link {
        display: block;
        padding: 15px 20px;
        color: #64748b;
        text-decoration: none;
        border-radius: 12px;
        transition: all 0.3s ease;
        margin-bottom: 10px;
        border: 1px solid #f1f5f9;
    }

    .quick-link:hover {
        background: #6366f1;
        color: white;
        transform: translateX(10px);
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
    }

    .quick-link i {
        margin-right: 12px;
        width: 20px;
    }

    @media (max-width: 768px) {
        .hero-faq {
            padding: 60px 0;
        }

        .search-box {
            margin-top: 20px;
        }

        .search-input {
            font-size: 1rem;
            padding: 12px 20px;
        }

        .search-btn {
            padding: 12px 20px;
        }

        .category-nav {
            padding: 15px;
            margin-top: -30px;
        }

        .category-btn {
            padding: 12px 20px;
            margin: 3px;
            font-size: 0.9rem;
        }

        .faq-question {
            padding: 20px;
            font-size: 1rem;
        }

        .faq-answer {
            padding: 0 20px 20px;
        }

        .stats-section {
            padding: 30px 20px;
        }

        .stat-number {
            font-size: 2.5rem;
        }

        .contact-cta {
            padding: 40px 20px;
        }

        .quick-links {
            padding: 20px;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-faq">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center" style="position: relative; z-index: 2;">
                <h1 class="display-3 fw-bold mb-4" data-aos="fade-up">
                    Frequently Asked Questions
                </h1>
                <p class="lead mb-4" data-aos="fade-up" data-aos-delay="100">
                    Temukan jawaban untuk pertanyaan yang sering diajukan seputar TokoSaya. Kami telah menyiapkan panduan lengkap untuk membantu pengalaman berbelanja Anda.
                </p>

                <!-- Search Box -->
                <div class="search-box" data-aos="fade-up" data-aos-delay="200">
                    <div class="d-flex align-items-center">
                        <input type="text" class="search-input" id="faqSearch" placeholder="Cari pertanyaan atau topik...">
                        <button class="search-btn" type="button" onclick="searchFAQ()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Category Navigation -->
<section class="py-4">
    <div class="container">
        <div class="category-nav" data-aos="fade-up">
            <div class="text-center">
                <a href="#" class="category-btn active" data-category="all">
                    <i class="fas fa-th-large me-2"></i>Semua
                </a>
                <a href="#" class="category-btn" data-category="account">
                    <i class="fas fa-user me-2"></i>Akun & Profil
                </a>
                <a href="#" class="category-btn" data-category="shopping">
                    <i class="fas fa-shopping-cart me-2"></i>Berbelanja
                </a>
                <a href="#" class="category-btn" data-category="payment">
                    <i class="fas fa-credit-card me-2"></i>Pembayaran
                </a>
                <a href="#" class="category-btn" data-category="shipping">
                    <i class="fas fa-shipping-fast me-2"></i>Pengiriman
                </a>
                <a href="#" class="category-btn" data-category="return">
                    <i class="fas fa-undo-alt me-2"></i>Pengembalian
                </a>
                <a href="#" class="category-btn" data-category="merchant">
                    <i class="fas fa-store me-2"></i>Merchant
                </a>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 d-none d-lg-block" data-aos="fade-right">
                <!-- Quick Links -->
                <div class="quick-links sticky-top" style="top: 100px;">
                    <h5 class="fw-bold mb-4">Panduan Cepat</h5>
                    <a href="{{ route('register') }}" class="quick-link">
                        <i class="fas fa-user-plus"></i>Cara Daftar Akun
                    </a>
                    <a href="#" class="quick-link">
                        <i class="fas fa-shopping-bag"></i>Cara Berbelanja
                    </a>
                    <a href="#" class="quick-link">
                        <i class="fas fa-credit-card"></i>Metode Pembayaran
                    </a>
                    <a href="{{ route('orders.track', $order) }}" class="quick-link">
                        <i class="fas fa-truck"></i>Lacak Pesanan
                    </a>
                    <a href="{{ route('contact') }}" class="quick-link">
                        <i class="fas fa-headset"></i>Hubungi Support
                    </a>
                    <a href="#" class="quick-link">
                        <i class="fas fa-store"></i>Jadi Merchant
                    </a>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="faq-container">
                    <!-- Account & Profile Section -->
                    <div class="faq-section" data-category="account" data-aos="fade-up">
                        <h2 class="section-title">
                            <i class="fas fa-user me-3"></i>Akun & Profil
                        </h2>

                        <!-- FAQ Items for Account Section -->
                        <div class="faq-item">
                            <button class="faq-question" type="button">
                                <span>Bagaimana cara membuat akun di TokoSaya?</span>
                                <span class="popular-badge">Popular</span>
                                <div class="icon">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </button>
                            <div class="faq-answer">
                                <!-- FAQ answer content -->
                            </div>
                        </div>
                        <!-- More FAQ items for Account section -->
                    </div>

                    <!-- Shopping Section -->
                    <div class="faq-section" data-category="shopping" data-aos="fade-up">
                        <h2 class="section-title">
                            <i class="fas fa-shopping-cart me-3"></i>Berbelanja
                        </h2>

                        <!-- FAQ Items for Shopping Section -->
                        <div class="faq-item">
                            <button class="faq-question" type="button">
                                <span>Bagaimana cara mencari produk yang saya inginkan?</span>
                                <span class="popular-badge">Popular</span>
                                <div class="icon">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </button>
                            <div class="faq-answer">
                                <!-- FAQ answer content -->
                            </div>
                        </div>
                        <!-- More FAQ items for Shopping section -->
                    </div>

                    <!-- Payment Section -->
                    <div class="faq-section" data-category="payment" data-aos="fade-up">
                        <h2 class="section-title">
                            <i class="fas fa-credit-card me-3"></i>Pembayaran
                        </h2>

                        <!-- FAQ Items for Payment Section -->
                        <div class="faq-item">
                            <button class="faq-question" type="button">
                                <span>Metode pembayaran apa saja yang tersedia?</span>
                                <span class="popular-badge">Popular</span>
                                <div class="icon">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </button>
                            <div class="faq-answer">
                                <!-- FAQ answer content -->
                            </div>
                        </div>
                        <!-- More FAQ items for Payment section -->
                    </div>

                    <!-- Shipping Section -->
                    <div class="faq-section" data-category="shipping" data-aos="fade-up">
                        <h2 class="section-title">
                            <i class="fas fa-shipping-fast me-3"></i>Pengiriman
                        </h2>

                        <div class="faq-item">
                            <button class="faq-question" type="button">
                                <span>Berapa lama waktu pengiriman pesanan?</span>
                                <span class="popular-badge">Popular</span>
                                <div class="icon">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </button>
                            <div class="faq-answer">
                                <p>Waktu pengiriman bervariasi tergantung lokasi dan jenis layanan yang dipilih:</p>

                                <h6><strong>⚡ Same Day (Jakarta):</strong></h6>
                                <ul>
                                    <li><strong>4-8 jam</strong> untuk pemesanan sebelum jam 14:00</li>
                                    <li>Tersedia untuk area Jakarta dan sekitarnya</li>
                                    <li>Biaya tambahan Rp 15.000</li>
                                </ul>

                                <h6><strong>🚀 Next Day (Jabodetabek):</strong></h6>
                                <ul>
                                    <li><strong>1-2 hari kerja</strong></li>
                                    <li>Gratis untuk pembelian di atas Rp 250.000</li>
                                </ul>

                                <h6><strong>📦 Regular (Pulau Jawa):</strong></h6>
                                <ul>
                                    <li><strong>2-4 hari kerja</strong></li>
                                    <li>Pilihan ekonomis dan terpercaya</li>
                                </ul>

                                <h6><strong>🏝️ Luar Pulau Jawa:</strong></h6>
                                <ul>
                                    <li><strong>3-7 hari kerja</strong></li>
                                    <li>Tergantung aksesibilitas wilayah</li>
                                </ul>

                                <p><strong>Catatan:</strong> Waktu pengiriman dihitung dari barang dikirim oleh merchant, bukan dari waktu pemesanan.</p>
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-question" type="button">
                                <span>Bagaimana cara melacak status pengiriman?</span>
                                <div class="icon">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </button>
                            <div class="faq-answer">
                                <p>Anda dapat melacak pesanan dengan mudah melalui beberapa cara:</p>

                                <h6><strong>🖥️ Melalui Website/Aplikasi:</strong></h6>
                                <ol>
                                    <li>Login ke akun TokoSaya Anda</li>
                                    <li>Masuk ke menu <strong>"Pesanan Saya"</strong></li>
                                    <li>Pilih pesanan yang ingin dilacak</li>
                                    <li>Klik <strong>"Lacak Pesanan"</strong></li>
                                    <li>Lihat status real-time pengiriman</li>
                                </ol>

                                <h6><strong>📱 Melalui SMS/Email:</strong></h6>
                                <ul>
                                    <li>Kami akan mengirim <strong>nomor resi</strong> otomatis</li>
                                    <li>Update status pengiriman via notifikasi</li>
                                    <li>Notifikasi saat barang dalam perjalanan dan tiba</li>
                                </ul>

                                <h6><strong>🔍 Langsung ke Kurir:</strong></h6>
                                <ul>
                                    <li>Gunakan nomor resi di website kurir (JNE, JNT, SiCepat, dll)</li>
                                    <li>Tracking lebih detail dengan estimasi waktu tiba</li>
                                </ul>

                                <p><em>Tips: Simpan nomor resi untuk memudahkan tracking dan komunikasi dengan kurir.</em></p>
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-question" type="button">
                                <span>Apakah ada asuransi untuk barang yang dikirim?</span>
                                <div class="icon">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </button>
                            <div class="faq-answer">
                                <p><strong>Ya!</strong> TokoSaya menyediakan perlindungan untuk semua pesanan:</p>

                                <h6><strong>🛡️ Asuransi Otomatis:</strong></h6>
                                <ul>
                                    <li><strong>Gratis</strong> untuk pembelian di atas Rp 100.000</li>
                                    <li>Mengganti kerugian hingga <strong>100% nilai barang</strong></li>
                                    <li>Berlaku untuk kerusakan dan kehilangan</li>
                                </ul>

                                <h6><strong>📋 Syarat Klaim:</strong></h6>
                                <ul>
                                    <li>Lapor dalam <strong>24 jam</strong> setelah barang diterima</li>
                                    <li>Sertakan foto bukti kerusakan/kehilangan</li>
                                    <li>Kemasan masih utuh (untuk kasus kerusakan)</li>
                                </ul>

                                <h6><strong>⚡ Proses Klaim Cepat:</strong></h6>
                                <ol>
                                    <li>Hubungi customer service: 0804-1-500-400</li>
                                    <li>Isi formulir klaim online</li>
                                    <li>Upload bukti foto/video</li>
                                    <li>Proses verifikasi 1-3 hari kerja</li>
                                    <li>Penggantian atau refund sesuai kesepakatan</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Return Section -->
                    <div class="faq-section" data-category="return" data-aos="fade-up">
                        <h2 class="section-title">
                            <i class="fas fa-undo-alt me-3"></i>Pengembalian & Refund
                        </h2>

                        <!-- FAQ Items for Return Section -->
                        <div class="faq-item">
                            <button class="faq-question" type="button">
                                <span>Bagaimana kebijakan pengembalian barang?</span>
                                <span class="popular-badge">Popular</span>
                                <div class="icon">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </button>
                            <div class="faq-answer">
                                <!-- FAQ answer content -->
                            </div>
                        </div>
                        <!-- More FAQ items for Return section -->
                    </div>

                    <!-- Merchant Section -->
                    <div class="faq-section" data-category="merchant" data-aos="fade-up">
                        <h2 class="section-title">
                            <i class="fas fa-store me-3"></i>Menjadi Merchant
                        </h2>

                        <!-- FAQ Items for Merchant Section -->
                        <div class="faq-item">
                            <button class="faq-question" type="button">
                                <span>Bagaimana cara menjadi merchant di TokoSaya?</span>
                                <span class="popular-badge">Popular</span>
                                <div class="icon">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </button>
                            <div class="faq-answer">
                                <!-- FAQ answer content -->
                            </div>
                        </div>
                        <!-- More FAQ items for Merchant section -->
                    </div>

                    <!-- No Results -->
                    <div id="noResults" class="no-results" style="display: none;" data-aos="fade-up">
                        <i class="fas fa-search"></i>
                        <h4 class="fw-bold mb-3">Tidak ditemukan hasil untuk pencarian Anda</h4>
                        <p class="mb-4">Coba gunakan kata kunci lain atau hubungi customer service kami untuk bantuan lebih lanjut.</p>
                        <a href="{{ route('contact') }}" class="btn btn-primary rounded-pill">
                            <i class="fas fa-headset me-2"></i>
                            Hubungi Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics -->
<section class="py-5">
    <div class="container">
        <div class="stats-section" data-aos="zoom-in">
            <h3 class="display-6 fw-bold mb-5">TokoSaya dalam Angka</h3>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number" data-counter="500000">0</div>
                        <div class="stat-label">Pengguna Aktif</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number" data-counter="50000">0</div>
                        <div class="stat-label">Produk Tersedia</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number" data-counter="1000">0</div>
                        <div class="stat-label">Merchant Partner</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number" data-counter="99">0</div>
                        <div class="stat-label">Tingkat Kepuasan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA -->
<section class="py-5">
    <div class="container">
        <div class="contact-cta" data-aos="fade-up">
            <div style="position: relative; z-index: 2;">
                <h2 class="display-6 fw-bold mb-4">Masih Ada Pertanyaan?</h2>
                <p class="lead mb-5">
                    Tim customer service kami siap membantu Anda 24/7 melalui berbagai channel yang tersedia.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ route('contact') }}" class="btn-cta">
                        <i class="fas fa-envelope"></i>
                        Kirim Pesan
                    </a>
                    <a href="tel:0804-1-500-400" class="btn-cta">
                        <i class="fas fa-phone"></i>
                        0804-1-500-400
                    </a>
                    <a href="https://wa.me/6281234567890" class="btn-cta" target="_blank">
                        <i class="fab fa-whatsapp"></i>
                        WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Accordion functionality
    const faqQuestions = document.querySelectorAll('.faq-question');

    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const answer = this.nextElementSibling;
            const isActive = this.classList.contains('active');

            // Close all other FAQ items
            faqQuestions.forEach(q => {
                q.classList.remove('active');
                q.nextElementSibling.classList.remove('show');
            });

            // Toggle current item
            if (!isActive) {
                this.classList.add('active');
                answer.classList.add('show');
            }
        });
    });

    // Category filtering
    const categoryBtns = document.querySelectorAll('.category-btn');
    const faqSections = document.querySelectorAll('.faq-section');

    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();

            // Update active button
            categoryBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const category = this.dataset.category;

            // Show/hide sections
            faqSections.forEach(section => {
                if (category === 'all' || section.dataset.category === category) {
                    section.style.display = 'block';
                    section.style.animation = 'fadeInUp 0.5s ease';
                } else {
                    section.style.display = 'none';
                }
            });

            // Hide no results
            document.getElementById('noResults').style.display = 'none';
        });
    });

    // Search functionality
    const searchInput = document.getElementById('faqSearch');
    let searchTimeout;

    function searchFAQ() {
        const searchTerm = searchInput.value.toLowerCase().trim();

        if (searchTerm.length < 2) {
            // Show all sections if search term is too short
            faqSections.forEach(section => {
                section.style.display = 'block';
                section.querySelectorAll('.faq-item').forEach(item => {
                    item.style.display = 'block';
                    item.querySelector('.faq-question span').innerHTML =
                        item.querySelector('.faq-question span').textContent;
                });
            });
            document.getElementById('noResults').style.display = 'none';
            return;
        }

        let hasResults = false;

        faqSections.forEach(section => {
            let sectionHasResults = false;

            section.querySelectorAll('.faq-item').forEach(item => {
                const questionText = item.querySelector('.faq-question span').textContent.toLowerCase();
                const answerText = item.querySelector('.faq-answer').textContent.toLowerCase();

                if (questionText.includes(searchTerm) || answerText.includes(searchTerm)) {
                    item.style.display = 'block';
                    sectionHasResults = true;
                    hasResults = true;

                    // Highlight search term in question
                    const questionSpan = item.querySelector('.faq-question span');
                    const originalText = questionSpan.textContent;
                    const regex = new RegExp(`(${searchTerm})`, 'gi');
                    questionSpan.innerHTML = originalText.replace(regex, '<span class="highlight">$1</span>');
                } else {
                    item.style.display = 'none';
                }
            });

            section.style.display = sectionHasResults ? 'block' : 'none';
        });

        // Show/hide no results message
        document.getElementById('noResults').style.display = hasResults ? 'none' : 'block';

        // Reset category filter
        categoryBtns.forEach(btn => btn.classList.remove('active'));
        categoryBtns[0].classList.add('active'); // "Semua" button
    }

    // Search on input with debounce
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(searchFAQ, 300);
    });

    // Search on button click
    window.searchFAQ = searchFAQ;

    // Counter animation
    const counters = document.querySelectorAll('[data-counter]');

    const animateCounter = (counter) => {
        const target = parseInt(counter.getAttribute('data-counter'));
        const increment = target / 100;
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }

            if (target === 99) {
                counter.textContent = Math.floor(current) + '%';
            } else {
                counter.textContent = Math.floor(current).toLocaleString('id-ID') + '+';
            }
        }, 20);
    };

    // Intersection Observer for counter animation
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => {
        counterObserver.observe(counter);
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Auto-expand first FAQ in each section
    faqSections.forEach(section => {
        const firstFaq = section.querySelector('.faq-question');
        if (firstFaq) {
            // Delay to allow page load animation
            setTimeout(() => {
                firstFaq.click();
            }, 1000);
        }
    });

    // Keyboard navigation for FAQ
    faqQuestions.forEach((question, index) => {
        question.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                const nextQuestion = faqQuestions[index + 1];
                if (nextQuestion) nextQuestion.focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                const prevQuestion = faqQuestions[index - 1];
                if (prevQuestion) prevQuestion.focus();
            }
        });

        // Make focusable
        question.setAttribute('tabindex', '0');
    });

    // Search suggestions (simple implementation)
    const searchSuggestions = [
        'cara daftar akun',
        'lupa password',
        'metode pembayaran',
        'ongkos kirim',
        'cara return barang',
        'jadi merchant',
        'lacak pesanan',
        'customer service',
        'refund',
        'voucher diskon'
    ];

    // Create suggestion dropdown
    const suggestionDropdown = document.createElement('div');
    suggestionDropdown.className = 'position-absolute w-100 bg-white border rounded mt-1 shadow-lg';
    suggestionDropdown.style.display = 'none';
    suggestionDropdown.style.zIndex = '1000';
    suggestionDropdown.style.maxHeight = '200px';
    suggestionDropdown.style.overflowY = 'auto';

    searchInput.parentNode.style.position = 'relative';
    searchInput.parentNode.appendChild(suggestionDropdown);

    searchInput.addEventListener('input', function() {
        const value = this.value.toLowerCase();

        if (value.length > 1) {
            const matches = searchSuggestions.filter(suggestion =>
                suggestion.includes(value)
            );

            if (matches.length > 0) {
                suggestionDropdown.innerHTML = matches.map(match =>
                    `<div class="p-2 border-bottom cursor-pointer suggestion-item" style="cursor: pointer;">${match}</div>`
                ).join('');
                suggestionDropdown.style.display = 'block';

                // Add click handlers to suggestions
                suggestionDropdown.querySelectorAll('.suggestion-item').forEach(item => {
                    item.addEventListener('click', function() {
                        searchInput.value = this.textContent;
                        suggestionDropdown.style.display = 'none';
                        searchFAQ();
                    });

                    item.addEventListener('mouseenter', function() {
                        this.style.backgroundColor = '#f8fafc';
                    });

                    item.addEventListener('mouseleave', function() {
                        this.style.backgroundColor = 'white';
                    });
                });
            } else {
                suggestionDropdown.style.display = 'none';
            }
        } else {
            suggestionDropdown.style.display = 'none';
        }
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.parentNode.contains(e.target)) {
            suggestionDropdown.style.display = 'none';
        }
    });

    // Enhanced search with Enter key
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            suggestionDropdown.style.display = 'none';
            searchFAQ();
        }
    });

    // Analytics tracking for FAQ usage
    function trackFAQInteraction(action, question) {
        // In a real implementation, this would send data to your analytics service
        console.log('FAQ Interaction:', action, question);

        // Example: Google Analytics event
        if (typeof gtag !== 'undefined') {
            gtag('event', 'faq_interaction', {
                'event_category': 'FAQ',
                'event_label': question,
                'value': action === 'expand' ? 1 : 0
            });
        }
    }

    // Track when FAQ items are expanded
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', function() {
            const questionText = this.querySelector('span').textContent;
            trackFAQInteraction('expand', questionText);
        });
    });
});
</script>
@endpush
