@extends('layouts.app')

@section('title', 'Hubungi Kami - TokoSaya')
@section('meta_description', 'Hubungi tim TokoSaya melalui berbagai channel yang tersedia. Kami siap membantu Anda 24/7 dengan layanan customer service terbaik.')

@push('styles')
<style>
    .hero-contact {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }

    .hero-contact::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.05)" points="0,1000 1000,0 1000,1000"/></svg>');
        background-size: cover;
    }

    .contact-card {
        background: white;
        border-radius: 20px;
        padding: 40px 30px;
        text-align: center;
        box-shadow: 0 15px 50px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .contact-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
    }

    .contact-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 70px rgba(0,0,0,0.15);
    }

    .contact-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        color: white;
        font-size: 1.8rem;
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);
        transition: all 0.3s ease;
    }

    .contact-card:hover .contact-icon {
        transform: scale(1.1);
        box-shadow: 0 15px 35px rgba(79, 70, 229, 0.4);
    }

    .form-section {
        background: white;
        border-radius: 25px;
        padding: 50px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        border: 1px solid #f1f5f9;
        position: relative;
        overflow: hidden;
    }

    .form-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(135deg, #4f46e5, #7c3aed, #ec4899);
    }

    .form-control, .form-select {
        border: 2px solid #e2e8f0;
        border-radius: 15px;
        padding: 15px 20px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f8fafc;
    }

    .form-control:focus, .form-select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.15);
        background: white;
        transform: translateY(-2px);
    }

    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 10px;
        font-size: 0.95rem;
    }

    .btn-submit {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        border: none;
        padding: 18px 50px;
        border-radius: 50px;
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);
        text-transform: uppercase;
        letter-spacing: 1px;
        width: 100%;
    }

    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(79, 70, 229, 0.4);
        color: white;
    }

    .btn-submit:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .map-container {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 50px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        height: 400px;
        position: relative;
    }

    .map-placeholder {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        color: #64748b;
    }

    .map-placeholder i {
        font-size: 4rem;
        margin-bottom: 20px;
        color: #cbd5e1;
    }

    .faq-section {
        background: #f8fafc;
        border-radius: 20px;
        padding: 40px;
        margin-top: 60px;
    }

    .faq-item {
        background: white;
        border-radius: 15px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .faq-item:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .faq-question {
        padding: 25px 30px;
        cursor: pointer;
        background: white;
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
    }

    .faq-question:hover {
        background: #f8fafc;
        color: #4f46e5;
    }

    .faq-question i {
        transition: transform 0.3s ease;
        color: #4f46e5;
    }

    .faq-question.active i {
        transform: rotate(180deg);
    }

    .faq-answer {
        padding: 0 30px 25px;
        color: #64748b;
        line-height: 1.7;
        display: none;
        animation: fadeInDown 0.3s ease;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .social-contact {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        border-radius: 20px;
        padding: 40px;
        color: white;
        text-align: center;
        margin-top: 40px;
        position: relative;
        overflow: hidden;
    }

    .social-contact::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
        background-size: 30px 30px;
        animation: moveBackground 20s linear infinite;
    }

    @keyframes moveBackground {
        0% { transform: translate(0, 0); }
        100% { transform: translate(30px, 30px); }
    }

    .social-links {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 30px;
        position: relative;
        z-index: 2;
    }

    .social-links a {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 2px solid rgba(255,255,255,0.3);
        font-size: 1.3rem;
    }

    .social-links a:hover {
        background: white;
        color: #4f46e5;
        transform: translateY(-5px) scale(1.1);
        box-shadow: 0 10px 25px rgba(255,255,255,0.3);
    }

    .alert-custom {
        border: none;
        border-radius: 15px;
        padding: 20px 25px;
        margin-bottom: 30px;
        border-left: 5px solid;
        font-weight: 500;
    }

    .alert-success-custom {
        background: rgba(16, 185, 129, 0.1);
        color: #065f46;
        border-left-color: #10b981;
    }

    .alert-danger-custom {
        background: rgba(239, 68, 68, 0.1);
        color: #991b1b;
        border-left-color: #ef4444;
    }

    .loading-spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s ease-in-out infinite;
        margin-right: 10px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .office-info {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 15px 50px rgba(0,0,0,0.1);
        border: 1px solid #f1f5f9;
        margin-bottom: 40px;
    }

    .office-info h4 {
        color: #4f46e5;
        margin-bottom: 20px;
        font-weight: 700;
    }

    .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding: 15px;
        background: #f8fafc;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .info-item:hover {
        background: #4f46e5;
        color: white;
        transform: translateX(10px);
    }

    .info-item i {
        width: 40px;
        height: 40px;
        background: #4f46e5;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        transition: all 0.3s ease;
    }

    .info-item:hover i {
        background: white;
        color: #4f46e5;
        transform: scale(1.1);
    }

    @media (max-width: 768px) {
        .hero-contact {
            padding: 50px 0;
        }

        .form-section {
            padding: 30px 20px;
            margin-top: 30px;
        }

        .contact-card {
            margin-bottom: 30px;
        }

        .social-links {
            flex-wrap: wrap;
            gap: 15px;
        }

        .office-info {
            padding: 25px;
        }

        .map-container {
            height: 300px;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-contact">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center" style="position: relative; z-index: 2;">
                <h1 class="display-4 fw-bold mb-4" data-aos="fade-up">
                    Hubungi Kami
                </h1>
                <p class="lead mb-5" data-aos="fade-up" data-aos-delay="100">
                    Tim TokoSaya siap membantu Anda 24/7. Hubungi kami melalui berbagai channel yang tersedia atau kunjungi kantor pusat kami untuk konsultasi langsung.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3" data-aos="fade-up" data-aos-delay="200">
                    <a href="#contact-form" class="btn btn-light btn-lg rounded-pill px-4">
                        <i class="fas fa-envelope me-2"></i>
                        Kirim Pesan
                    </a>
                    <a href="tel:0804-1-500-400" class="btn btn-outline-light btn-lg rounded-pill px-4">
                        <i class="fas fa-phone me-2"></i>
                        Telepon Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Methods -->
<section class="py-5" style="margin-top: -60px; position: relative; z-index: 3;">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Customer Service</h4>
                    <p class="text-muted mb-4">
                        Tim customer service kami siap membantu Anda 24/7 untuk semua pertanyaan dan kendala yang Anda hadapi.
                    </p>
                    <div class="d-flex flex-column gap-2">
                        <a href="tel:0804-1-500-400" class="btn btn-outline-primary rounded-pill">
                            <i class="fas fa-phone me-2"></i>0804-1-500-400
                        </a>
                        <a href="https://wa.me/6281234567890" class="btn btn-success rounded-pill" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Merchant Support</h4>
                    <p class="text-muted mb-4">
                        Dukungan khusus untuk merchant dan seller yang ingin bergabung atau mengembangkan bisnis di TokoSaya.
                    </p>
                    <div class="d-flex flex-column gap-2">
                        <a href="tel:0804-1-600-400" class="btn btn-outline-primary rounded-pill">
                            <i class="fas fa-phone me-2"></i>0804-1-600-400
                        </a>
                        <a href="mailto:merchant@tokosaya.id" class="btn btn-warning rounded-pill">
                            <i class="fas fa-envelope me-2"></i>merchant@tokosaya.id
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Live Chat</h4>
                    <p class="text-muted mb-4">
                        Chat langsung dengan tim support kami melalui website atau aplikasi mobile untuk respon yang lebih cepat.
                    </p>
                    <div class="d-flex flex-column gap-2">
                        <button class="btn btn-primary rounded-pill" onclick="openLiveChat()">
                            <i class="fas fa-comment-dots me-2"></i>Mulai Chat
                        </button>
                        <small class="text-muted">Rata-rata respon: < 2 menit</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form & Office Info -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8" data-aos="fade-right">
                <div class="form-section" id="contact-form">
                    <div class="text-center mb-5">
                        <h2 class="display-6 fw-bold mb-3">Kirim Pesan</h2>
                        <p class="text-muted">
                            Isi form di bawah ini dan tim kami akan merespon dalam 24 jam
                        </p>
                    </div>

                    <div id="alert-container"></div>

                    <form id="contactForm" novalidate>
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Lengkap *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <div class="invalid-feedback">
                                    Nama lengkap wajib diisi.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback">
                                    Email valid wajib diisi.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Nomor Telepon</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="08xxxxxxxxxx">
                            </div>

                            <div class="col-md-6">
                                <label for="subject" class="form-label">Kategori Pesan *</label>
                                <select class="form-select" id="subject" name="subject" required>
                                    <option value="">Pilih kategori...</option>
                                    <option value="general">Pertanyaan Umum</option>
                                    <option value="order">Pesanan & Pengiriman</option>
                                    <option value="payment">Pembayaran & Refund</option>
                                    <option value="merchant">Kemitraan Merchant</option>
                                    <option value="technical">Masalah Teknis</option>
                                    <option value="complaint">Keluhan & Saran</option>
                                    <option value="other">Lainnya</option>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih kategori pesan.
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="message" class="form-label">Pesan *</label>
                                <textarea class="form-control" id="message" name="message" rows="6" required placeholder="Tuliskan pesan Anda di sini..."></textarea>
                                <div class="invalid-feedback">
                                    Pesan wajib diisi.
                                </div>
                                <div class="form-text">Minimal 10 karakter</div>
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="privacy" name="privacy" required>
                                    <label class="form-check-label" for="privacy">
                                        Saya setuju dengan <a href="#" class="text-primary">Kebijakan Privasi</a> dan <a href="#" class="text-primary">Syarat & Ketentuan</a> TokoSaya *
                                    </label>
                                    <div class="invalid-feedback">
                                        Anda harus menyetujui kebijakan privasi.
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn-submit">
                                    <span class="loading-spinner"></span>
                                    <span class="btn-text">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Kirim Pesan
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4" data-aos="fade-left">
                <!-- Office Information -->
                <div class="office-info">
                    <h4><i class="fas fa-building me-2"></i>Kantor Pusat</h4>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Alamat</strong><br>
                            Jl. Sudirman Kav. 52-53<br>
                            Jakarta Selatan 12190
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Jam Operasional</strong><br>
                            Senin - Jumat: 08:00 - 22:00<br>
                            Sabtu - Minggu: 09:00 - 21:00
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email</strong><br>
                            info@tokosaya.id<br>
                            support@tokosaya.id
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <strong>Telepon</strong><br>
                            Customer: 0804-1-500-400<br>
                            Merchant: 0804-1-600-400
                        </div>
                    </div>
                </div>

                <!-- Map -->
                <div class="map-container" data-aos="zoom-in">
                    <div class="map-placeholder">
                        <i class="fas fa-map-marked-alt"></i>
                        <h5 class="fw-bold mb-2">Lokasi Kantor Pusat</h5>
                        <p class="mb-3">Jl. Sudirman Kav. 52-53, Jakarta Selatan</p>
                        <button class="btn btn-primary rounded-pill" onclick="openMaps()">
                            <i class="fas fa-directions me-2"></i>Lihat Rute
                        </button>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="social-contact">
                    <h4 class="fw-bold mb-3">Ikuti Kami</h4>
                    <p class="mb-4">
                        Dapatkan update terbaru dan promo menarik melalui media sosial kami
                    </p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" aria-label="Instagram" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" aria-label="Twitter" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" aria-label="LinkedIn" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" aria-label="YouTube" title="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" aria-label="TikTok" title="TikTok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold mb-3" data-aos="fade-up">Pertanyaan yang Sering Diajukan</h2>
            <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100">
                Temukan jawaban untuk pertanyaan umum seputar TokoSaya
            </p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="faq-section" data-aos="fade-up" data-aos-delay="200">
                    <div class="faq-item">
                        <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            Bagaimana cara membuat akun di TokoSaya?
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer collapse" id="faq1">
                            <p>Untuk membuat akun di TokoSaya, klik tombol "Daftar" di pojok kanan atas halaman utama. Isi formulir pendaftaran dengan informasi yang diminta seperti nama, email, dan nomor telepon. Setelah mendaftar, Anda akan menerima email verifikasi. Klik link verifikasi dalam email untuk mengaktifkan akun Anda.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            Metode pembayaran apa saja yang tersedia?
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer collapse" id="faq2">
                            <p>TokoSaya menerima berbagai metode pembayaran termasuk: Transfer Bank (BCA, Mandiri, BRI, BNI), E-Wallet (GoPay, OVO, DANA, ShopeePay), Kartu Kredit/Debit (Visa, Mastercard), dan COD (Cash on Delivery) untuk area tertentu. Semua transaksi dijamin aman dengan enkripsi SSL.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            Berapa lama waktu pengiriman pesanan?
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer collapse" id="faq3">
                            <p>Waktu pengiriman bervariasi tergantung lokasi dan jenis pengiriman yang dipilih: Same Day (Jakarta, 4-8 jam), Next Day (Jabodetabek, 1-2 hari), Regular (Java, 2-4 hari), Luar Java (3-7 hari). Anda dapat memantau status pengiriman melalui halaman "Pesanan Saya" di akun Anda.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            Bagaimana kebijakan pengembalian barang?
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer collapse" id="faq4">
                            <p>TokoSaya memberikan garansi 30 hari untuk pengembalian barang. Syaratnya: barang masih dalam kondisi asli, belum digunakan, dalam kemasan asli, dan disertai bukti pembelian. Untuk mengajukan pengembalian, masuk ke halaman "Pesanan Saya", pilih pesanan yang ingin dikembalikan, dan klik "Ajukan Pengembalian".</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                            Apakah ada program loyalitas untuk pelanggan?
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer collapse" id="faq5">
                            <p>Ya! TokoSaya memiliki program TokoSaya Points dimana Anda mendapatkan poin setiap berbelanja. Poin dapat ditukar dengan voucher diskon atau produk gratis. Selain itu, member VIP mendapat benefit khusus seperti free shipping, early access sale, dan customer service prioritas.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                            Bagaimana cara menjadi merchant di TokoSaya?
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer collapse" id="faq6">
                            <p>Untuk menjadi merchant TokoSaya: 1) Daftar akun merchant melalui halaman "Jual di TokoSaya", 2) Lengkapi data bisnis dan dokumen yang diperlukan, 3) Tunggu proses verifikasi (1-3 hari kerja), 4) Setelah disetujui, Anda dapat mulai upload produk dan berjualan. Tim merchant support kami siap membantu selama proses onboarding.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Links -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold mb-3" data-aos="fade-up">Bantuan Cepat</h2>
            <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100">
                Akses langsung ke halaman bantuan yang paling sering dicari
            </p>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="100">
                <div class="contact-card h-100">
                    <div class="contact-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Panduan Berbelanja</h5>
                    <p class="text-muted mb-4 small">
                        Pelajari cara berbelanja di TokoSaya dari A sampai Z
                    </p>
                    <a href="#" class="btn btn-outline-success rounded-pill btn-sm">
                        Lihat Panduan
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="200">
                <div class="contact-card h-100">
                    <div class="contact-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Lacak Pesanan</h5>
                    <p class="text-muted mb-4 small">
                        Pantau status pengiriman pesanan Anda secara real-time
                    </p>
                    <a href="{{ route('orders.track') }}" class="btn btn-outline-warning rounded-pill btn-sm">
                        Lacak Sekarang
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="300">
                <div class="contact-card h-100">
                    <div class="contact-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                        <i class="fas fa-undo-alt"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Pengembalian</h5>
                    <p class="text-muted mb-4 small">
                        Ajukan pengembalian barang dengan mudah dan cepat
                    </p>
                    <a href="#" class="btn btn-outline-danger rounded-pill btn-sm">
                        Ajukan Return
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="400">
                <div class="contact-card h-100">
                    <div class="contact-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                        <i class="fas fa-store"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Jadi Merchant</h5>
                    <p class="text-muted mb-4 small">
                        Bergabung sebagai seller dan mulai jualan di TokoSaya
                    </p>
                    <a href="#" class="btn btn-outline-primary rounded-pill btn-sm">
                        Daftar Merchant
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
    // Contact form handling
    const contactForm = document.getElementById('contactForm');
    const alertContainer = document.getElementById('alert-container');
    const submitBtn = contactForm.querySelector('.btn-submit');
    const loadingSpinner = contactForm.querySelector('.loading-spinner');
    const btnText = contactForm.querySelector('.btn-text');

    // Form validation
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!contactForm.checkValidity()) {
            e.stopPropagation();
            contactForm.classList.add('was-validated');
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        loadingSpinner.style.display = 'inline-block';
        btnText.innerHTML = '<i class="fas fa-clock me-2"></i>Mengirim...';

        // Simulate form submission
        setTimeout(() => {
            showAlert('success', 'Pesan Anda berhasil dikirim! Tim kami akan merespon dalam 24 jam.');
            contactForm.reset();
            contactForm.classList.remove('was-validated');

            // Reset button state
            submitBtn.disabled = false;
            loadingSpinner.style.display = 'none';
            btnText.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Kirim Pesan';

            // Scroll to alert
            alertContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 2000);
    });

    // Show alert function
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success-custom' : 'alert-danger-custom';
        const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';

        alertContainer.innerHTML = `
            <div class="alert-custom ${alertClass} alert-dismissible" role="alert">
                <i class="${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    }

    // FAQ accordion
    document.querySelectorAll('.faq-question').forEach(button => {
        button.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            const targetId = this.getAttribute('data-bs-target');
            const target = document.querySelector(targetId);

            // Toggle active class for icon rotation
            this.classList.toggle('active');

            // Show/hide answer
            if (target) {
                if (isExpanded) {
                    target.style.display = 'none';
                    this.setAttribute('aria-expanded', 'false');
                } else {
                    target.style.display = 'block';
                    this.setAttribute('aria-expanded', 'true');
                }
            }
        });
    });

    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.startsWith('8')) {
            value = '0' + value;
        }
        if (value.length > 13) {
            value = value.slice(0, 13);
        }
        e.target.value = value;
    });

    // Message character counter
    const messageTextarea = document.getElementById('message');
    const charCounter = document.createElement('div');
    charCounter.className = 'form-text text-end';
    charCounter.style.marginTop = '5px';
    messageTextarea.parentNode.appendChild(charCounter);

    function updateCharCounter() {
        const current = messageTextarea.value.length;
        const min = 10;
        const max = 1000;

        charCounter.textContent = `${current}/${max} karakter`;

        if (current < min) {
            charCounter.className = 'form-text text-end text-warning';
            charCounter.textContent += ` (minimal ${min} karakter)`;
        } else if (current > max) {
            charCounter.className = 'form-text text-end text-danger';
            messageTextarea.value = messageTextarea.value.slice(0, max);
        } else {
            charCounter.className = 'form-text text-end text-success';
        }
    }

    messageTextarea.addEventListener('input', updateCharCounter);
    updateCharCounter();

    // Enhanced form validation
    contactForm.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('blur', function() {
            if (this.checkValidity()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });

        field.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.checkValidity()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
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

    // Auto-hide alerts after 5 seconds
    function autoHideAlert() {
        const alerts = document.querySelectorAll('.alert-custom');
        alerts.forEach(alert => {
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }
            }, 5000);
        });
    }

    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe all cards
    document.querySelectorAll('.contact-card, .office-info, .faq-section').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s ease';
        observer.observe(el);
    });
});

// Global functions
function openLiveChat() {
    // In a real implementation, this would open your live chat widget
    alert('Live chat akan segera dibuka. Untuk sementara, silakan hubungi WhatsApp kami di 081234567890');
    window.open('https://wa.me/6281234567890', '_blank');
}

function openMaps() {
    // Open Google Maps with office location
    const address = "Jl. Sudirman Kav. 52-53, Jakarta Selatan 12190";
    const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`;
    window.open(url, '_blank');
}

// Real-time form field hints
document.addEventListener('DOMContentLoaded', function() {
    const emailField = document.getElementById('email');
    const phoneField = document.getElementById('phone');

    // Email domain suggestions
    emailField.addEventListener('input', function() {
        const value = this.value;
        if (value.includes('@') && !value.includes('.')) {
            const commonDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com'];
            // You could implement autocomplete suggestions here
        }
    });

    // Phone number validation for Indonesian numbers
    phoneField.addEventListener('input', function() {
        const value = this.value;
        if (value.length > 0) {
            if (!value.startsWith('08') && !value.startsWith('+62')) {
                this.setCustomValidity('Nomor telepon harus dimulai dengan 08 atau +62');
            } else {
                this.setCustomValidity('');
            }
        }
    });
});
</script>
@endpush
