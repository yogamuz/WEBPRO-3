@extends('dashboard.layouts.main')
@section('container')
@include('dashboard.layanan.create')

<!-- Flash Message -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-xl">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Data Kontak Layanan Disdukcapil</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Subjek</th>
                                <th>Pesan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contacts as $index => $contact)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $contact->name }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->subject }}</td>
                                <td>
                                    <div class="message-preview">
                                        @if(strlen($contact->message) > 100)
                                            {{ Str::limit($contact->message, 100) }}
                                            <br>
                                            <button class="btn btn-sm btn-outline-primary mt-1" 
                                                    onclick="showFullMessage('{{ addslashes($contact->message) }}', '{{ $contact->name }}')">
                                                Lihat Selengkapnya
                                            </button>
                                        @else
                                            {{ $contact->message }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- Button View Detail -->
                                        <button type="button" class="btn btn-info btn-sm" 
                                                onclick="viewDetail({{ json_encode($contact->name) }}, {{ json_encode($contact->email) }}, {{ json_encode($contact->subject ?? 'Tidak Ada Subjek') }}, {{ json_encode($contact->message) }}, '{{ date('d-m-Y H:i', strtotime($contact->created_at)) }}')"
                                                title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        
                                        <!-- Button Reply Email - SOLUSI GMAIL SUBJECT ISSUE -->
                                        <button type="button" class="btn btn-success btn-sm" 
                                                onclick="replyEmailGmail('{{ $contact->email }}', {{ json_encode($contact->subject) }}, {{ json_encode($contact->message) }}, {{ json_encode($contact->name) }})"
                                                title="Balas Email">
                                            <i class="bi bi-reply"></i>
                                        </button>
                                        
                                        <!-- Button Delete -->
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                onclick="deleteContact({{ $contact->id }})" 
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk menampilkan pesan lengkap -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Pesan Lengkap</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Dari:</strong> <span id="senderName"></span>
                </div>
                <div class="mb-3">
                    <strong>Pesan:</strong>
                </div>
                <div class="border p-3 rounded bg-light">
                    <p id="fullMessage" class="mb-0"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk detail kontak -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Kontak</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Nama:</strong>
                            <p id="detailName" class="mb-0"></p>
                        </div>
                        <div class="mb-3">
                            <strong>Email:</strong>
                            <p id="detailEmail" class="mb-0"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Subjek:</strong>
                            <p id="detailSubject" class="mb-0"></p>
                        </div>
                        <div class="mb-3">
                            <strong>Tanggal:</strong>
                            <p id="detailDate" class="mb-0"></p>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <strong>Pesan:</strong>
                    <div class="border p-3 rounded bg-light mt-2">
                        <p id="detailMessage" class="mb-0"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a id="replyButton" href="#" class="btn btn-success" onclick="replyEmailGmailFromModal()">
                    <i class="bi bi-reply"></i> Balas Email
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Form tersembunyi untuk delete -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function showFullMessage(message, senderName) {
        document.getElementById('senderName').textContent = senderName;
        document.getElementById('fullMessage').textContent = message;
        $('#messageModal').modal('show');
    }

    function viewDetail(name, email, subject, message, date) {
        document.getElementById('detailName').textContent = name;
        document.getElementById('detailEmail').textContent = email;
        document.getElementById('detailSubject').textContent = subject;
        document.getElementById('detailMessage').textContent = message;
        document.getElementById('detailDate').textContent = date;
        $('#detailModal').modal('show');
    }

    // Function untuk menutup modal secara manual
    function closeModal(modalId) {
        $('#' + modalId).modal('hide');
    }

    $(document).ready(function() {
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json",
                "emptyTable": "Belum ada data kontak",
                "zeroRecords": "Tidak ada data yang sesuai"
            },
            "order": [[ 0, "desc" ]], // Urutkan berdasarkan nomor terbaru
            "pageLength": 10,
            "responsive": true,
            "columnDefs": [
                { "orderable": false, "targets": 5 } // Disable ordering pada kolom Action
            ]
        });
    });

    // Function untuk menampilkan pesan lengkap
    function showFullMessage(message, senderName) {
        document.getElementById('senderName').textContent = senderName;
        document.getElementById('fullMessage').textContent = message;
        new bootstrap.Modal(document.getElementById('messageModal')).show();
    }

    // Function untuk menampilkan detail kontak
    function viewDetail(name, email, subject, message, date) {
        document.getElementById('detailName').textContent = name;
        document.getElementById('detailEmail').textContent = email;
        document.getElementById('detailSubject').textContent = subject;
        document.getElementById('detailMessage').textContent = message;
        document.getElementById('detailDate').textContent = date;
        new bootstrap.Modal(document.getElementById('detailModal')).show();
    }

    // Function untuk menghapus kontak - DIPERBAIKI
    function deleteContact(id) {
        if (confirm('Apakah Anda yakin ingin menghapus kontak ini?')) {
            const form = document.getElementById('deleteForm');
            form.action = `{{ route('dashboard.pesan.index') }}/${id}`;
            form.submit();
        }
    }

    // Function untuk balas email ke Gmail - DENGAN ALTERNATIF SOLUSI
    function replyEmailGmail(email, subject, message, name) {
        // Pastikan parameter tidak undefined atau null
        message = message || '';
        name = name || '';
        subject = subject || 'Tidak Ada Subjek';
        
        // Bersihkan dan format subject untuk reply
        var cleanSubject = subject.toString().trim();
        var replySubject = cleanSubject.startsWith('Re: ') ? cleanSubject : 'Re: ' + cleanSubject;
        
        // Template body email dengan subject di dalam body karena Gmail tidak selalu menerima parameter subject
        var emailBody = `SUBJEK: ${replySubject}

Terima kasih atas pesan Anda.

---
Pesan berasal dari Bapak/Ibu ${name}:
Subjek: ${cleanSubject}
Pesan: ${message}
`;
        
        // Encode parameters dengan benar
        var encodedEmail = encodeURIComponent(email.trim());
        var encodedSubject = encodeURIComponent(replySubject);
        var encodedBody = encodeURIComponent(emailBody);
        
        // Coba beberapa format URL Gmail
        var gmailUrls = [
            // Format 1: Standard compose
            `https://mail.google.com/mail/?view=cm&fs=1&to=${encodedEmail}&subject=${encodedSubject}&body=${encodedBody}`,
            // Format 2: Alternative compose 
            `https://mail.google.com/mail/u/0/?view=cm&fs=1&to=${encodedEmail}&subject=${encodedSubject}&body=${encodedBody}`,
            // Format 3: Mobile format
            `https://mail.google.com/mail/?view=cm&to=${encodedEmail}&subject=${encodedSubject}&body=${encodedBody}`
        ];
        
        // Gunakan format pertama sebagai default
        var primaryUrl = gmailUrls[0];
        
        console.log('=== GMAIL COMPOSE DEBUG ===');
        console.log('Email:', email);
        console.log('Original Subject:', subject);
        console.log('Reply Subject:', replySubject);
        console.log('Primary URL:', primaryUrl);
        console.log('==========================');
        
        // Buka Gmail dengan primary URL
        var newWindow = window.open(primaryUrl, '_blank');
        
        // Fallback: Jika Gmail tidak menerima subject, copy ke clipboard
        if (navigator.clipboard) {
            navigator.clipboard.writeText(`Subject: ${replySubject}`).then(function() {
                console.log('Subject copied to clipboard as fallback');
            }).catch(function(err) {
                console.log('Could not copy subject to clipboard', err);
            });
        }
        
        // Tampilkan alert dengan informasi subject untuk user
        setTimeout(function() {
            if (confirm(`Gmail dibuka dengan:\nTo: ${email}\nSubject: ${replySubject}\n\nJika subject tidak muncul otomatis, silakan copy subject dari pesan ini.\n\nKlik OK untuk copy subject ke clipboard.`)) {
                // Copy subject ke clipboard jika user setuju
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(replySubject);
                }
            }
        }, 1000);
    }

    // Function untuk balas email dari modal detail - TETAP SAMA
    function replyEmailGmailFromModal() {
        var email = document.getElementById('detailEmail').textContent.trim();
        var subject = document.getElementById('detailSubject').textContent.trim();
        var message = document.getElementById('detailMessage').textContent.trim();
        var name = document.getElementById('detailName').textContent.trim();
        
        // Pastikan data tidak kosong
        if (!email || !subject) {
            alert('Data email atau subject tidak tersedia');
            return;
        }
        
        // Gunakan function yang sama
        replyEmailGmail(email, subject, message, name);
    }

    // Function untuk debug - tampilkan data yang akan dikirim
    function debugContactData(name, email, subject, message) {
        console.log('Debug Contact Data:');
        console.log('Name:', name);
        console.log('Email:', email);  
        console.log('Subject:', subject);
        console.log('Message:', message);
    }
</script>
  
@endsection