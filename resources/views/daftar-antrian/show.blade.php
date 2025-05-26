@extends('layouts.main')
@section('container')
<section id="services" class="services">
    <div class="container" data-aos="fade-up">
      <div class="section-title">
        <h2>Antrian</h2>
        <p>Daftar Antrian {{ $listPendaftar->nama_layanan }}</p>
      </div>
      <div class="row">
        <div class="col">
            <div class="row mb-3">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="tanggal">Pilih Tanggal Antrian (Format : bulan - tanggal - tahun)</label>
                        <input type="date" class="form-control mt-2" id="tanggal">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="tanggal">Reset Filter</label>
                        <button id="reset-filter" class="btn btn-primary mt-2"><i class="bi bi-arrow-clockwise"></i> Reset Filter</button>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="tanggal">Cetak PDF</label>
                        <a href="{{ route('antrian.pdf', $listPendaftar->id) }}" class="btn btn-danger mt-2" target="_blank"><i class="bi bi-file-earmark-pdf"></i> Cetak PDF</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="display" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tgl. Antrian</th>
                            <th>Nama</th>
                            <th>Nomor Antrian</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Menampilkan List Pengambil Antrian Berdasarkan Layanan Di Halaman Depan Agar User Lain Bisa Melihat -->
                        @foreach ($listPendaftar->ambilantrians as $antrian)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $antrian->tanggal->format('d-m-Y') }}</td>
                                <td>{{ strtoupper($antrian->nama_lengkap) }}</td>
                                <td>{{ $antrian->kode }}</td>
                                <td>{{ $antrian->alamat }}</td>
                                <td>
                    @if(auth()->id() == $antrian->user_id)
                        <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="{{ $antrian->id }}" data-bs-toggle="modal" data-bs-target="#editModal">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                    
                    @else
                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
      </div>
    </div>
  </section><!-- End Services Section --> 

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Antrian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editForm" method="POST" action="">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label for="edit_nama_lengkap" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="edit_nama_lengkap" name="nama_lengkap" required>
          </div>
          <div class="mb-3">
            <label for="edit_tanggal" class="form-label">Tanggal</label>
            <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
          </div>
          <div class="mb-3">
            <label for="edit_alamat" class="form-label">Alamat</label>
            <textarea class="form-control" id="edit_alamat" name="alamat" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Apakah Anda yakin ingin menghapus antrian ini?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <form id="deleteForm" method="POST" action="">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Hapus</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTable
        var table = $('#dataTable').DataTable();
        
        // Ketika tanggal berubah, atur filter pada DataTable
        $('#tanggal').on('change', function() {
            var tanggal = $('#tanggal').val();
            table.columns(1).search(tanggal).draw();
        });
        
        // Ketika tombol reset di klik, hapus filter pada DataTable
        $('#reset-filter').on('click', function() {
            $('#tanggal').val('');
            $('#antrian_id').val('').trigger('change');
            table.columns().search('').draw();
        });

        // Edit Modal
        $('.edit-btn').on('click', function() {
            var id = $(this).data('id');
            // Set action URL untuk form
            $('#editForm').attr('action', `/antrian/${id}/update`);
            
            // Ambil data untuk antrian ini
            $.ajax({
                url: `/antrian/${id}/edit`,
                type: 'GET',
                success: function(data) {
                    if (data.error) {
                        alert(data.error);
                        $('#editModal').modal('hide');
                        return;
                    }
                    $('#edit_nama_lengkap').val(data.nama_lengkap);
                    $('#edit_tanggal').val(data.tanggal);
                    $('#edit_alamat').val(data.alamat);
                },
                error: function(err) {
                    console.error('Error:', err);
                    if (err.status === 403) {
                        alert('Anda tidak memiliki akses untuk mengedit antrian ini');
                    } else {
                        alert('Terjadi kesalahan saat mengambil data');
                    }
                    $('#editModal').modal('hide');
                }
            });
        });

        // Delete Modal
// Delete Modal
// Delete Modal - PERBAIKAN URL
$('.delete-btn').on('click', function() {
    var id = $(this).data('id');
    // PERBAIKAN: Sesuaikan dengan route yang benar
    $('#deleteForm').attr('action', `/antrian/detail/${id}`);
});
    });
</script>
@endsection