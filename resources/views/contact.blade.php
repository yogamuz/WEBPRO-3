@extends('layouts.main')
@section('container')
    <!-- ======= Contact Section ======= -->
    <section id="contact" class="contact">
        <div class="container" data-aos="fade-up">
  
          <div class="section-title">
            <h2>Kontak</h2>
            <p>Kontak Dan Lokasi</p>
          </div>
  
          <div>
            <iframe width="100%" height="400" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" id="gmap_canvas" 
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3964.9942813572943!2d106.81853517587007!3d-6.394737562547175!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69ebe3cfcdb1d9%3A0xa737694ddd894861!2sGedung%20Dibaleka%202%20Kota%20Depok!5e0!3m2!1sid!2sid!4v1748017721367!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe><a href='https://maps-generator.com/'></a>
          </div>
  
          <div class="row mt-5">
  
            <div class="col-lg-4">
              <div class="info">
                <div class="address">
                  <i class="bi bi-geo-alt"></i>
                  <h4>Lokasi :</h4>
                  <p>Gedung Balaikota DiBaleka II, Jl. Margonda Raya No.54, Depok, Kec. Pancoran Mas, Depok, Indonesia 16431.</p>
                </div>
  
                <div class="email">
                  <i class="bi bi-envelope"></i>
                  <h4>Email:</h4>
                  <p>info@dukcapildepok@go.id</p>
                </div>
  
                <div class="phone">
                  <i class="bi bi-phone"></i>
                  <h4>Nomor Telepon:</h4>
                  <p>(021) 865 371</p>
                </div>
  
              </div>
  
            </div>
<div class="col-lg-8 mt-5 mt-lg-0">
    <!-- Container for notifications -->
    <div id="notification-container"></div>
    
    <!-- Display Laravel session messages if not AJAX -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <form id="contactForm" action="{{ route('contact.send') }}" method="POST" class="contact-form">
        @csrf
        <div class="row">
            <div class="col-md-6 form-group">
                <input type="text" 
                       name="name" 
                       class="form-control @error('name') is-invalid @enderror" 
                       id="name" 
                       placeholder="Nama Anda" 
                       value="{{ old('name') }}"
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 form-group mt-3 mt-md-0">
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       name="email" 
                       id="email" 
                       placeholder="Email Anda" 
                       value="{{ old('email') }}"
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="form-group mt-3">
            <input type="text" 
                   class="form-control @error('subject') is-invalid @enderror" 
                   name="subject" 
                   id="subject" 
                   placeholder="Subjek" 
                   value="{{ old('subject') }}"
                   required>
            @error('subject')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group mt-3">
            <textarea class="form-control @error('message') is-invalid @enderror" 
                      name="message" 
                      id="message" 
                      rows="5" 
                      placeholder="Tulis Pesan" 
                      required>{{ old('message') }}</textarea>
            @error('message')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="text-center">
            <button type="submit" id="submitBtn" class="submit-btn">Kirim Pesan</button>
        </div>
    </form>
</div>

<!-- Make sure CSRF token is available in meta tag -->
<script>
    // Ensure CSRF token is available
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = '{{ csrf_token() }}';
        document.getElementsByTagName('head')[0].appendChild(meta);
    }
</script>
          </div>
        </div>
    </section><!-- End Contact Section -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/contact.js') }}"></script>
@endpush