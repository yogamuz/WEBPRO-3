<!-- Modal Pengambilan Nomor Antrian -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Ambil Nomor Antrian - {{ $antrian->nama_antrian ?? 'Antrian' }}</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Alert placeholder for messages -->
          <div id="modalAlertPlaceholder"></div>
          
          <form id="formAmbilAntrian" action="{{ route('store.antrian') }}" method="POST">
            @csrf
  
            <!-- Input hidden untuk antrian_id -->
            <input type="hidden" name="antrian_id" id="antrian_id" value="{{ $antrian->id }}">
            
            <!-- Debug info (remove in production) -->
 
  
            <div class="mb-3">
              <label for="tanggal" class="form-label">Tanggal Kedatangan <span class="text-danger">*</span></label>
              <input type="date" 
                     class="form-control @error('tanggal') is-invalid @enderror" 
                     id="tanggal" 
                     name="tanggal" 
                     min="{{ date('Y-m-d') }}"
                     value="{{ old('tanggal') }}"
                     required>
              @error('tanggal')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div id="tanggal-error" class="invalid-feedback"></div>
            </div>
  
            <div class="mb-3">
              <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
              <input type="text" 
                     class="form-control @error('nama_lengkap') is-invalid @enderror" 
                     id="nama_lengkap" 
                     name="nama_lengkap" 
                     placeholder="Masukkan nama lengkap"
                     value="{{ old('nama_lengkap') }}"
                     maxlength="255"
                     required>
              @error('nama_lengkap')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div id="nama_lengkap-error" class="invalid-feedback"></div>
            </div>
  
            <div class="mb-3">
              <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
              <textarea class="form-control @error('alamat') is-invalid @enderror" 
                        id="alamat" 
                        name="alamat" 
                        rows="3" 
                        placeholder="Masukkan alamat lengkap"
                        maxlength="1000"
                        required>{{ old('alamat') }}</textarea>
              @error('alamat')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div id="alamat-error" class="invalid-feedback"></div>
            </div>
  
            <div class="mb-3">
              <label for="nomorhp" class="form-label">Nomor HP <span class="text-danger">*</span></label>
              <input type="tel" 
                     class="form-control @error('nomorhp') is-invalid @enderror" 
                     id="nomorhp" 
                     name="nomorhp" 
                     placeholder="Contoh: 08123456789"
                     value="{{ old('nomorhp') }}"
                     maxlength="20"
                     pattern="[0-9+\-\s]+"
                     required>
              @error('nomorhp')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div id="nomorhp-error" class="invalid-feedback"></div>
            </div>
  
          </form>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button id="btnSimpan" type="button" class="btn btn-primary">
            <span id="btnText">Ambil Antrian</span>
            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
          </button>
        </div>
  
      </div>
    </div>
  </div>
  
<!-- Script untuk validasi dan AJAX submission -->
<script>
$(document).ready(function() {
    console.log('Form script loaded');
    
    // Event handler untuk tombol submit
    $('#btnSimpan').click(function(e) {
        e.preventDefault();
        console.log('Submit button clicked');
        submitForm();
    });
    
    // Event handler untuk form submit (jika user menekan Enter)
    $('#formAmbilAntrian').submit(function(e) {
        e.preventDefault();
        console.log('Form submitted');
        submitForm();
    });
    
    function submitForm() {
        var form = $('#formAmbilAntrian');
        var submitButton = $('#btnSimpan');
        var btnText = $('#btnText');
        var btnSpinner = $('#btnSpinner');
        
        console.log('Starting form submission...');
        
        // Reset previous errors
        clearErrors();
        
        // Collect form data
        var formData = {
            _token: $('input[name="_token"]').val(),
            antrian_id: $('#antrian_id').val(),
            tanggal: $('#tanggal').val(),
            nama_lengkap: $('#nama_lengkap').val(),
            alamat: $('#alamat').val(),
            nomorhp: $('#nomorhp').val()
        };
        
        console.log('Form data:', formData);
        
        // Client-side validation
        var errors = [];
        if (!formData.tanggal) errors.push('Tanggal harus diisi');
        if (!formData.nama_lengkap) errors.push('Nama lengkap harus diisi');
        if (!formData.alamat) errors.push('Alamat harus diisi');
        if (!formData.nomorhp) errors.push('Nomor HP harus diisi');
        if (!formData.antrian_id) errors.push('ID Antrian tidak ditemukan');
        
        if (errors.length > 0) {
            console.log('Client-side validation errors:', errors);
            showAlert('danger', 'Validasi Error:<br>• ' + errors.join('<br>• '));
            return;
        }
        
        // Disable submit button
        submitButton.prop('disabled', true);
        btnText.addClass('d-none');
        btnSpinner.removeClass('d-none');
        
        console.log('Sending AJAX request to:', form.attr('action'));
        
        // Send AJAX request
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            timeout: 5000, // 30 second timeout
            success: function(response) {
                console.log('Success response:', response);
                
                // Hide modal
                $('#exampleModal').modal('hide');
                
                // Show success message
                showMainAlert('success', response.success || 'Antrian berhasil diambil!');
                
                // Reset form
                form[0].reset();
                
                // Refresh page after 2 seconds
                // setTimeout(function() {
                //     window.location.reload();
                // }, 2000);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                var errorMessage = 'Terjadi kesalahan saat memproses permintaan';
                
                if (xhr.status === 422) {
                    // Validation errors
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.errors) {
                            handleValidationErrors(response.errors);
                            return;
                        } else if (response.error) {
                            errorMessage = response.error;
                        }
                    } catch(e) {
                        console.error('Error parsing JSON response:', e);
                    }
                } else if (xhr.status === 500) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            errorMessage = response.error;
                        }
                    } catch(e) {
                        errorMessage = 'Terjadi kesalahan server internal';
                    }
                } else if (xhr.status === 0) {
                    errorMessage = 'Koneksi terputus. Periksa koneksi internet Anda.';
                } else if (status === 'timeout') {
                    errorMessage = 'Request timeout. Silakan coba lagi.';
                }
                
                showAlert('danger', errorMessage);
            },
            complete: function() {
                // Re-enable submit button
                submitButton.prop('disabled', false);
                btnText.removeClass('d-none');
                btnSpinner.addClass('d-none');
            }
        });
    }
    
    function clearErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').hide();
        $('#modalAlertPlaceholder').empty();
    }
    
    function handleValidationErrors(errors) {
        console.log('Handling validation errors:', errors);
        
        var errorMessages = [];
        
        for (var field in errors) {
            var input = $('#' + field);
            var errorDiv = $('#' + field + '-error');
            
            if (input.length && errorDiv.length) {
                input.addClass('is-invalid');
                errorDiv.text(errors[field][0]).show();
            }
            
            errorMessages.push(field + ': ' + errors[field][0]);
        }
        
        showAlert('danger', 'Validasi Error:<br>• ' + errorMessages.join('<br>• '));
    }
    
    function showAlert(type, message) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('#modalAlertPlaceholder').html(alertHtml);
    }
    
    function showMainAlert(type, message) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Try to find main alert placeholder
        var mainAlert = $('#liveAlertPlaceholder');
        if (mainAlert.length) {
            mainAlert.html(alertHtml);
        } else {
            // Create alert at top of page if placeholder doesn't exist
            $('body').prepend('<div id="tempAlertPlaceholder">' + alertHtml + '</div>');
        }
    }
    
    // Reset form when modal is closed
    $('#exampleModal').on('hidden.bs.modal', function () {
        $('#formAmbilAntrian')[0].reset();
        clearErrors();
    });
});
</script>