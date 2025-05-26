@extends('layouts.main')

@section('container')
<!-- ====== Hero Section ======= -->
  <section id="hero" class="d-flex align-items-center justify-content-center">
    <div class="container" data-aos="fade-up">

      <div class="row justify-content-center" data-aos="fade-up" data-aos-delay="150">
        <div class="col-xl-6 col-lg-8">
          <h1>Dinas Kependudukan & Pencatatan Sipil Depok</h1>
        </div>
      </div>

          <div class="row gy-4 mt-5 justify-content-center" data-aos="zoom-in" data-aos-delay="250">
            <div class="col-xl-2 col-md-4">
              <a href="/antrian" class="icon-box text-decoration-none text-dark d-block text-center">
                <i class="bi bi-person-bounding-box"></i>
                <h3>Pencatatan E-KTP</h3>
              </a>
            </div>
            <div class="col-xl-2 col-md-4">
              <a href="/antrian" class="icon-box text-decoration-none text-dark d-block text-center">
                <i class="bi bi-people"></i>
                <h3>Kartu Keluarga</h3>
              </a>
            </div>
            <div class="col-xl-2 col-md-4">
              <a href="/antrian" class="icon-box text-decoration-none text-dark d-block text-center">

                <i class="bi bi-person-badge"></i>
                <h3>Akta Kelahiran</h3>
              </a>
            </div>
            <div class="col-xl-2 col-md-4">
              <a href="/antrian" class="icon-box text-decoration-none text-dark d-block text-center">

                <i class="bi bi-arrow-left-right"></i>
                <h3>Surat Pindah</h3>
              </a>
            </div>
            <div class="col-xl-2 col-md-4">
              <a href="#" class="icon-box text-decoration-none text-dark d-block text-center">

                <i class="bi bi-three-dots"></i>
                <h3>Dan Sebagainya..</h3>
              </a>
            </div>
          </div>

    </div>
  </section><!-- End Hero -->

  <main id="main">
    <!-- ======= About Section ======= -->
    <section id="about" class="about">
      <div class="container" data-aos="fade-up">

        <div class="row">
          <div class="col-lg-5 order-1 order-lg-2" data-aos="fade-left" data-aos-delay="100">
            <img src="assets/img/about.png" class="img-fluid" alt="">
          </div>
          <div class="col-lg-7 pt-4 pt-lg-0 order-2 order-lg-1 content" data-aos="fade-right" data-aos-delay="100">
            <h3>Sistem Antrian Online Disdukcapil</h3>
            <p class="fst-italic">
              Masyarakat bisa melakukan pendaftaran online dan memilih waktu kunjungan ke kantor Dukcapil 
              sesuai dengan kebutuhan dan ketersediaan waktu mereka. Hal ini tentunya akan menghemat waktu dan tenaga masyarakat dalam melakukan pengurusan administrasi kependudukan. 
            </p>
            <ul>
              <li><i class="ri-number-1"></i> Kunjungi website antrian online Disdukcapil Kota Depok</li>
              <li><i class="ri-number-2"></i> Login / Register akun </li>
              <li><i class="ri-number-3"></i> Pilih jenis layanan yang tersedia</li>
              <li><i class="ri-number-4"></i> Masukkan data diri anda</li>
              <li><i class="ri-number-5"></i> Simpan dan Cetak nomor urut antrian</li>
            </ul>
            <p>
              Dengan adanya antrian dukcapil online, masyarakat tidak perlu lagi datang ke kantor Dinas Kependudukan dan Catatan Sipil (Dukcapil) 
              untuk mengantri dan melakukan pengurusan administrasi kependudukan seperti pembuatan KTP, KK, akta kelahiran, dan sebagainya.
            </p>
          </div>
        </div>

      </div>
    </section><!-- End About Section -->
  </main><!-- End #main -->

  <!-- Chatbot -->
 <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="chat-widget-button" id="chatButton">
    <i class="fas fa-headset fa-lg"></i>
</div>

<div class="chat-widget-container" id="chatContainer">
    <div class="chat-header">
        <div>
            <i class="fas fa-headset"></i>
            <span class="ms-2">Customer Service</span>
        </div>
        <button class="close-chat" id="closeChat">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="chat-body" id="chatBody">
        <div class="message bot-message">
            Halo! Ada yang bisa kami bantu?
        </div>
    </div>
    <div class="chat-footer">
        <input type="text" class="chat-input" id="userInput" placeholder="Tulis pesan..." />
        <button class="send-button" id="sendButton">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>
  
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="{{ asset('assets/js/chatbot.js') }}"></script>
@endsection