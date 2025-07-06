@extends('layouts.app')

@section('title', 'Tentang Kami - TokoSaya')
@section('meta_description', 'Pelajari lebih lanjut tentang TokoSaya, platform e-commerce terpercaya di Indonesia dengan misi memberikan pengalaman belanja online terbaik.')

@push('styles')
<style>
    .hero-about {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 100px 0;
        position: relative;
        overflow: hidden;
    }

    .hero-about::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.1)" points="0,1000 1000,0 1000,1000"/></svg>');
        background-size: cover;
    }

    .hero-about .container {
        position: relative;
        z-index: 2;
    }

    .stats-card {
        background: white;
        border-radius: 20px;
        padding: 40px 30px;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #f8f9fa;
    }

    .stats-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }

    .stats-number {
        font-size: 3rem;
        font-weight: 700;
        color: #2563eb;
        margin-bottom: 10px;
        line-height: 1;
    }

    .stats-label {
        color: #64748b;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.9rem;
    }

    .value-card {
        background: #f8fafc;
        border-radius: 15px;
        padding: 40px 30px;
        text-align: center;
        height: 100%;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .value-card:hover {
        background: white;
        border-color: #2563eb;
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(37, 99, 235, 0.1);
    }

    .value-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        color: white;
        font-size: 2rem;
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
    }

    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 50%;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(to bottom, #2563eb, #3b82f6);
        transform: translateX(-50%);
    }

    .timeline-item {
        position: relative;
        width: 50%;
        padding: 30px 40px;
        margin-bottom: 30px;
    }

    .timeline-item:nth-child(odd) {
        left: 0;
        text-align: right;
    }

    .timeline-item:nth-child(even) {
        left: 50%;
        text-align: left;
    }

    .timeline-content {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        position: relative;
        border: 1px solid #f1f5f9;
    }

    .timeline-item:nth-child(odd) .timeline-content::after {
        content: '';
        position: absolute;
        right: -20px;
        top: 30px;
        border: 20px solid transparent;
        border-left-color: white;
    }

    .timeline-item:nth-child(even) .timeline-content::after {
        content: '';
        position: absolute;
        left: -20px;
        top: 30px;
        border: 20px solid transparent;
        border-right-color: white;
    }

    .timeline-year {
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: white;
        padding: 8px 20px;
        border-radius: 25px;
        font-weight: 700;
        font-size: 0.9rem;
        display: inline-block;
        margin-bottom: 15px;
        box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
    }

    .team-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9;
    }

    .team-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }

    .team-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        margin: 30px auto 20px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
        font-weight: 700;
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }

    .social-links {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
    }

    .social-links a {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
    }

    .social-links a:hover {
        background: #2563eb;
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
    }

    .feature-highlight {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 60px 40px;
        color: white;
        text-align: center;
        position: relative;
        overflow: hidden;
        margin: 80px 0;
    }

    .feature-highlight::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>');
        animation: float 20s infinite linear;
    }

    @keyframes float {
        0% { transform: rotate(0deg) translateX(10px) rotate(0deg); }
        100% { transform: rotate(360deg) translateX(10px) rotate(-360deg); }
    }

    .btn-cta {
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        border: none;
        padding: 15px 40px;
        border-radius: 50px;
        color: white;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.9rem;
    }

    .btn-cta:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(37, 99, 235, 0.4);
        color: white;
    }

    @media (max-width: 768px) {
        .hero-about {
            padding: 60px 0;
        }

        .timeline::before {
            left: 30px;
        }

        .timeline-item {
            width: 100%;
            left: 0 !important;
            text-align: left !important;
            padding-left: 80px;
        }

        .timeline-content::after {
            display: none;
        }

        .stats-number {
            font-size: 2.5rem;
        }

        .value-card {
            margin-bottom: 30px;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-about">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4" data-aos="fade-up">
                    Tentang TokoSaya
                </h1>
                <p class="lead mb-5" data-aos="fade-up" data-aos-delay="100">
                    Platform e-commerce terdepan di Indonesia yang menghadirkan pengalaman belanja online terbaik dengan teknologi modern dan layanan prima untuk semua kebutuhan Anda.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3" data-aos="fade-up" data-aos-delay="200">
                    <a href="#misi" class="btn-cta">
                        <i class="fas fa-rocket"></i>
                        Misi Kami
                    </a>
                    <a href="#team" class="btn-cta" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                        <i class="fas fa-users"></i>
                        Tim Kami
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-5" style="margin-top: -80px; position: relative; z-index: 3;">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stats-card">
                    <div class="stats-number" data-counter="500000">0</div>
                    <div class="stats-label">Pengguna Aktif</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stats-card">
                    <div class="stats-number" data-counter="50000">0</div>
                    <div class="stats-label">Produk Tersedia</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stats-card">
                    <div class="stats-number" data-counter="1000">0</div>
                    <div class="stats-label">Merchant Partner</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="stats-card">
                    <div class="stats-number" data-counter="99">0</div>
                    <div class="stats-label">Kepuasan %</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision Section -->
<section id="misi" class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold mb-4" data-aos="fade-up">Misi & Visi Kami</h2>
                <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100">
                    Kami berkomitmen untuk menghadirkan revolusi dalam dunia e-commerce Indonesia melalui teknologi terdepan dan layanan yang mengutamakan kepuasan pelanggan.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3 class="h4 fw-bold mb-3">Misi Kami</h3>
                    <p class="text-muted mb-4">
                        Menyediakan platform e-commerce yang mudah, aman, dan terpercaya untuk menghubungkan penjual dan pembeli di seluruh Indonesia dengan teknologi terdepan dan layanan prima.
                    </p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Platform yang user-friendly</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Keamanan transaksi terjamin</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Layanan pelanggan 24/7</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Pengiriman cepat dan aman</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-6" data-aos="fade-left">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3 class="h4 fw-bold mb-3">Visi Kami</h3>
                    <p class="text-muted mb-4">
                        Menjadi platform e-commerce terdepan di Indonesia yang memberdayakan UMKM dan memberikan pengalaman belanja online terbaik bagi seluruh masyarakat Indonesia.
                    </p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-star text-warning me-2"></i>Platform #1 di Indonesia</li>
                        <li class="mb-2"><i class="fas fa-star text-warning me-2"></i>Pemberdayaan UMKM nasional</li>
                        <li class="mb-2"><i class="fas fa-star text-warning me-2"></i>Teknologi terdepan</li>
                        <li class="mb-2"><i class="fas fa-star text-warning me-2"></i>Jangkauan seluruh Indonesia</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold mb-4" data-aos="fade-up">Nilai-Nilai Kami</h2>
                <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100">
                    Nilai-nilai yang menjadi fondasi dalam setiap keputusan dan tindakan yang kami ambil untuk memberikan yang terbaik bagi pelanggan.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="100">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Kepercayaan</h4>
                    <p class="text-muted">
                        Membangun kepercayaan melalui transparansi, keamanan transaksi, dan layanan yang konsisten untuk menciptakan pengalaman belanja yang aman.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="200">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Inovasi</h4>
                    <p class="text-muted">
                        Terus berinovasi dengan teknologi terdepan untuk memberikan solusi e-commerce yang selalu selangkah lebih maju dari kompetitor.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="300">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Kepedulian</h4>
                    <p class="text-muted">
                        Mengutamakan kepuasan pelanggan dan kesejahteraan mitra dengan memberikan dukungan penuh dalam setiap langkah perjalanan bisnis.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="400">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Kecepatan</h4>
                    <p class="text-muted">
                        Memberikan layanan yang cepat dan responsif, mulai dari proses pemesanan hingga pengiriman produk ke tangan pelanggan.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="500">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Kemitraan</h4>
                    <p class="text-muted">
                        Membangun hubungan kemitraan yang saling menguntungkan dengan merchant, supplier, dan semua stakeholder dalam ekosistem.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="600">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Keunggulan</h4>
                    <p class="text-muted">
                        Selalu berusaha memberikan yang terbaik dalam setiap aspek layanan untuk mencapai standar keunggulan yang berkelanjutan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Timeline Section -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold mb-4" data-aos="fade-up">Perjalanan Kami</h2>
                <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100">
                    Melihat kembali perjalanan TokoSaya dari awal berdiri hingga menjadi platform e-commerce terdepan di Indonesia.
                </p>
            </div>
        </div>

        <div class="timeline">
            <div class="timeline-item" data-aos="fade-right">
                <div class="timeline-content">
                    <div class="timeline-year">2020</div>
                    <h4 class="fw-bold mb-3">Awal Mula</h4>
                    <p class="text-muted">
                        TokoSaya didirikan dengan visi menciptakan platform e-commerce yang mudah digunakan untuk UMKM Indonesia. Dimulai dengan tim kecil yang passionate tentang teknologi dan bisnis digital.
                    </p>
                </div>
            </div>

            <div class="timeline-item" data-aos="fade-left">
                <div class="timeline-content">
                    <div class="timeline-year">2021</div>
                    <h4 class="fw-bold mb-3">Peluncuran Platform</h4>
                    <p class="text-muted">
                        Platform TokoSaya resmi diluncurkan dengan fitur-fitur dasar e-commerce. Dalam tahun pertama, berhasil mendapatkan 10,000+ pengguna dan 500+ merchant partner.
                    </p>
                </div>
            </div>

            <div class="timeline-item" data-aos="fade-right">
                <div class="timeline-content">
                    <div class="timeline-year">2022</div>
                    <h4 class="fw-bold mb-3">Ekspansi Fitur</h4>
                    <p class="text-muted">
                        Menambahkan fitur-fitur advanced seperti live chat, multiple payment gateway, dan sistem logistik terintegrasi. Pengguna mencapai 100,000+ dengan tingkat kepuasan 95%.
                    </p>
                </div>
            </div>

            <div class="timeline-item" data-aos="fade-left">
                <div class="timeline-content">
                    <div class="timeline-year">2023</div>
                    <h4 class="fw-bold mb-3">Era Mobile</h4>
                    <p class="text-muted">
                        Meluncurkan aplikasi mobile dan mengimplementasikan teknologi AI untuk personalisasi pengalaman pengguna. Transaksi meningkat 300% dalam setahun.
                    </p>
                </div>
            </div>

            <div class="timeline-item" data-aos="fade-right">
                <div class="timeline-content">
                    <div class="timeline-year">2024</div>
                    <h4 class="fw-bold mb-3">Kepemimpinan Pasar</h4>
                    <p class="text-muted">
                        Mencapai 500,000+ pengguna aktif dan menjadi salah satu platform e-commerce terdepan di Indonesia. Ekspansi ke kota-kota tier 2 dan tier 3.
                    </p>
                </div>
            </div>

            <div class="timeline-item" data-aos="fade-left">
                <div class="timeline-content">
                    <div class="timeline-year">2025</div>
                    <h4 class="fw-bold mb-3">Masa Depan</h4>
                    <p class="text-muted">
                        Melanjutkan inovasi dengan teknologi terdepan, ekspansi internasional, dan komitmen untuk memberdayakan lebih banyak UMKM di seluruh Indonesia.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section id="team" class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold mb-4" data-aos="fade-up">Tim Kami</h2>
                <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100">
                    Dibalik kesuksesan TokoSaya, terdapat tim profesional yang berpengalaman dan passionate dalam menciptakan solusi e-commerce terbaik.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="team-card text-center">
                    <div class="team-avatar">A</div>
                    <h4 class="fw-bold mb-2">Ahmad Santoso</h4>
                    <p class="text-primary fw-semibold mb-3">Chief Executive Officer</p>
                    <p class="text-muted small mb-4">
                        Berpengalaman 15+ tahun di bidang teknologi dan e-commerce. Memimpin visi strategis TokoSaya untuk menjadi platform terdepan di Indonesia.
                    </p>
                    <div class="social-links">
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Email"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="team-card text-center">
                    <div class="team-avatar">S</div>
                    <h4 class="fw-bold mb-2">Sari Indrawati</h4>
                    <p class="text-primary fw-semibold mb-3">Chief Technology Officer</p>
                    <p class="text-muted small mb-4">
                        Expert dalam software architecture dan cloud computing. Memastikan platform TokoSaya selalu menggunakan teknologi terdepan dan scalable.
                    </p>
                    <div class="social-links">
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="GitHub"><i class="fab fa-github"></i></a>
                        <a href="#" aria-label="Email"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="team-card text-center">
                    <div class="team-avatar">D</div>
                    <h4 class="fw-bold mb-2">Dedi Kurniawan</h4>
                    <p class="text-primary fw-semibold mb-3">Chief Marketing Officer</p>
                    <p class="text-muted small mb-4">
                        Spesialis digital marketing dengan track record membangun brand awareness untuk berbagai startup unicorn di Asia Tenggara.
                    </p>
                    <div class="social-links">
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="Email"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="team-card text-center">
                    <div class="team-avatar">L</div>
                    <h4 class="fw-bold mb-2">Lisa Permatasari</h4>
                    <p class="text-primary fw-semibold mb-3">Head of Operations</p>
                    <p class="text-muted small mb-4">
                        Mengelola operasional harian platform dan memastikan semua proses berjalan efisien untuk memberikan pengalaman terbaik bagi pengguna.
                    </p>
                    <div class="social-links">
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Email"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                <div class="team-card text-center">
                    <div class="team-avatar">R</div>
                    <h4 class="fw-bold mb-2">Rudi Hermawan</h4>
                    <p class="text-primary fw-semibold mb-3">Head of Customer Success</p>
                    <p class="text-muted small mb-4">
                        Memastikan kepuasan pelanggan melalui layanan support 24/7 dan program loyalty yang inovatif untuk meningkatkan retensi pengguna.
                    </p>
                    <div class="social-links">
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" aria-label="Email"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                <div class="team-card text-center">
                    <div class="team-avatar">M</div>
                    <h4 class="fw-bold mb-2">Maya Soleha</h4>
                    <p class="text-primary fw-semibold mb-3">Head of Product Design</p>
                    <p class="text-muted small mb-4">
                        UX/UI designer berpengalaman yang fokus menciptakan interface yang intuitif dan user-friendly untuk semua fitur TokoSaya.
                    </p>
                    <div class="social-links">
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="Dribbble"><i class="fab fa-dribbble"></i></a>
                        <a href="#" aria-label="Email"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Feature Highlight Section -->
<div class="feature-highlight">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center" style="position: relative; z-index: 2;">
                <h2 class="display-5 fw-bold mb-4" data-aos="zoom-in">
                    Bergabunglah dengan Revolusi E-commerce Indonesia
                </h2>
                <p class="lead mb-5" data-aos="zoom-in" data-aos-delay="100">
                    Jadilah bagian dari ekosistem TokoSaya dan rasakan pengalaman belanja online yang tak terlupakan dengan teknologi terdepan dan layanan terpercaya.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3" data-aos="zoom-in" data-aos-delay="200">
                    <a href="{{ route('register') }}" class="btn-cta">
                        <i class="fas fa-user-plus"></i>
                        Daftar Sekarang
                    </a>
                    <a href="{{ route('products.index') }}" class="btn-cta" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                        <i class="fas fa-shopping-bag"></i>
                        Mulai Belanja
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Information -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center">
                    <div class="value-icon mb-4">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Kantor Pusat</h4>
                    <p class="text-muted">
                        Jl. Sudirman Kav. 52-53<br>
                        Jakarta Selatan 12190<br>
                        Indonesia
                    </p>
                </div>
            </div>

            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center">
                    <div class="value-icon mb-4">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Hubungi Kami</h4>
                    <p class="text-muted">
                        Customer Service: 0804-1-500-400<br>
                        Merchant Support: 0804-1-600-400<br>
                        Email: info@tokosaya.id
                    </p>
                </div>
            </div>

            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center">
                    <div class="value-icon mb-4">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Jam Operasional</h4>
                    <p class="text-muted">
                        Senin - Jumat: 08:00 - 22:00<br>
                        Sabtu - Minggu: 09:00 - 21:00<br>
                        Customer Service 24/7
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate counters
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

            // Format number with commas
            counter.textContent = Math.floor(current).toLocaleString('id-ID');

            // Add + sign for certain counters
            if (target >= 1000 && current >= target) {
                counter.textContent += '+';
            }

            // Add % sign for percentage
            if (counter.getAttribute('data-counter') === '99') {
                counter.textContent = Math.floor(current) + '%';
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

    // Parallax effect for hero section
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const heroSection = document.querySelector('.hero-about');
        if (heroSection) {
            heroSection.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
    });

    // Add entrance animation to value cards
    const valueCards = document.querySelectorAll('.value-card');
    const cardObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = Math.random() * 0.5 + 's';
                entry.target.classList.add('animate__animated', 'animate__fadeInUp');
            }
        });
    }, { threshold: 0.3 });

    valueCards.forEach(card => {
        cardObserver.observe(card);
    });

    // Timeline animation enhancement
    const timelineItems = document.querySelectorAll('.timeline-item');
    const timelineObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.querySelector('.timeline-content').style.transform = 'scale(1)';
                entry.target.querySelector('.timeline-content').style.opacity = '1';
            }
        });
    }, { threshold: 0.5 });

    timelineItems.forEach(item => {
        item.querySelector('.timeline-content').style.transform = 'scale(0.8)';
        item.querySelector('.timeline-content').style.opacity = '0';
        item.querySelector('.timeline-content').style.transition = 'all 0.6s ease';
        timelineObserver.observe(item);
    });
});
</script>
@endpush
