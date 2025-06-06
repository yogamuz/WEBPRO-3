  <!-- ======= Footer ======= -->
  <footer id="footer">
    <div class="footer-top">
      <div class="container">
        <div class="row">

          <div class="col-lg-3 col-md-6">
            <div class="footer-info">
              <h3><img src="/assets/img/logo.png" alt="" width="150px" height="150px"></h3>
              <p>
              Gedung Balaikota DiBaleka II, Jl. Margonda Raya No.54, Depok, Kec. Pancoran Mas, Depok, Indonesia 16431.
              </p>
              <div class="social-links mt-3">
                <a href="#" class="twitter"><i class="bx bxl-twitter"></i></a>
                <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
                <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>
                <a href="#" class="google-plus"><i class="bx bxl-skype"></i></a>
                <a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-6 footer-links">
            <h4>Menu</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="/">Home</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="/antrian">Antrian</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="/contact">Contact</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="/login">Login</a></li>
            </ul>
          </div>

          <div class="col-lg-3 col-md-6 footer-links">
            <h4>Jenis Layanan</h4>
            <ul>
              @foreach ($antrians as $antrian)
                <li><i class="bx bx-chevron-right"></i> <a href="/antrian/">{{ $antrian->nama_layanan }}</a></li>
              @endforeach
            </ul>
          </div>

          <div class="col-lg-4 col-md-6 footer-newsletter">
            <h4>Dapatkan Update Informasi Terbaru</h4>
            <p>Dengan hormat, kami menyarankan segenap warga Kota Depok untuk mengikuti dan berinteraksi melalui saluran resmi media sosial Dinas Kependudukan dan Pencatatan Sipil 
              (Disdukcapil) Kota Depok, demi mendapatkan informasi layanan dan sosialisasi kebijakan secara akurat dan terkini.</p>


          </div>

        </div>
      </div>
    </div>

    <div class="container">
      <div class="copyright">
        &copy; Copyright <strong><span>Sistem Antrian Online</span></strong>. All Rights Reserved
      </div>
    </div>
  </footer><!-- End Footer -->