<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Ambilantrian;
use App\Models\User;
use App\Models\Antrian;
use App\Models\Layanan;
use App\Models\Contact;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Antrian::create([
            'nama_layanan' => 'Layanan E-KTP',
            'kode'         => 'KTP',
            'slug'         => 'layanan-e-ktp',
            'batas_antrian'=> 20,
            'deskripsi'    => 'Ambil antrian untuk mengurus perekaman E-KTP',
            'persyaratan'  => 'Berkas yang dibawa Fotocopy KK, KTP-el asli yang lama, Surat keterangan hilang kepolisian', 
            'user_id'      => 1,
        ]);

        Antrian::create([
            'nama_layanan' => 'Layanan Kartu Keluarga',
            'kode'         => 'KK',
            'slug'         => 'layanan-kartu-keluarga',
            'batas_antrian'=> 20,
            'deskripsi'    => 'Ambil antrian untuk mengurus Kartu Keluarga',
            'persyaratan'  => 'Berkas yang dibawa Fotocopy KK', 
            'user_id'      => 1,
        ]);

        Antrian::create([
            'nama_layanan' => 'Layanan Akta Kelahiran',
            'kode'         => 'AKKEL',
            'slug'         => 'layanan-akta-kelahiran',
            'batas_antrian'=> 20,
            'deskripsi'    => 'Ambil antrian untuk mengurus Akta Kelahiran',
            'persyaratan'  => 'Berkas yang dibawa Fotocopy KK', 
            'user_id'      => 1,
        ]);

        Antrian::create([
            'nama_layanan' => 'Layanan Surat Pindah',
            'kode'         => 'SP',
            'slug'         => 'layanan-surat-pindah',
            'batas_antrian'=> 20,
            'deskripsi'    => 'Ambil antrian untuk mengurus Surat Pindah',
            'persyaratan'  => 'Berkas yang dibawa Fotocopy KK', 
            'user_id'      => 1,
        ]);

        Layanan::create([
            'nama_layanan' => 'Layanan E-KTP',
            'kode'         => 'KTP',
            'deskripsi'    => 'Pelayanan dan pengurusan E-KTP',
            'user_id'      => 1
        ]);

        Layanan::create([
            'nama_layanan' => 'Layanan Kartu Keluarga',
            'kode'         => 'KK',
            'deskripsi'    => 'Pelayanan dan pengurusan Kart Keluarga (KK)',
            'user_id'      => 1
        ]);

        Layanan::create([
            'nama_layanan' => 'Layanan Akta Kelahiran',
            'kode'         => 'AKKEL',
            'deskripsi'    => 'Pelayanan dan pengurusan Akta Kelahiran',
            'user_id'      => 1
        ]);

        Layanan::create([
            'nama_layanan' => 'Layanan Surat Pindah',
            'kode'         => 'KK',
            'deskripsi'    => 'Pelayanan dan pengurusan Surat Pindah',
            'user_id'      => 1
        ]);

        User::create([
            'name'      => 'Admin',
            'email'     => 'admin@gmail.com',
            'password'  => bcrypt('1234'),
            'roles'     => 'admin'
        ]);

        // 2. User Pertama
        User::create([
            'name'      => 'Andi Wijaya',
            'email'     => 'andi@gmail.com',
            'password'  => bcrypt('password123'),
            'roles'     => 'masyarakat'
        ]);

        // 3. User Kedua
        User::create([
            'name'      => 'Budi Santoso',
            'email'     => 'budi@gmail.com',
            'password'  => bcrypt('password123'),
            'roles'     => 'masyarakat'
        ]);

        // 4. User Ketiga
        User::create([
            'name'      => 'Citra Lestari',
            'email'     => 'citra@gmail.com',
            'password'  => bcrypt('password123'),
            'roles'     => 'masyarakat'
        ]);

        // 5. User Keempat
        User::create([
            'name'      => 'Dewi Anggraeni',
            'email'     => 'dewi@gmail.com',
            'password'  => bcrypt('password123'),
            'roles'     => 'masyarakat'
        ]);

        // 6. User Kelima
        User::create([
            'name'      => 'Eko Prasetyo',
            'email'     => 'eko@gmail.com',
            'password'  => bcrypt('password123'),
            'roles'     => 'masyarakat'
        ]);

        // 7. User Keenam
        User::create([
            'name'      => 'Fitriani Sari',
            'email'     => 'fitri@gmail.com',
            'password'  => bcrypt('password123'),
            'roles'     => 'masyarakat'
        ]);

        // ===== DATA DUMMY UNTUK TABEL CONTACT =====
        Contact::create([
            'name'    => 'Ahmad Rizki',
            'email'   => 'ahmad.rizki@gmail.com',
            'subject' => 'Pertanyaan tentang Syarat E-KTP',
            'message' => 'Selamat pagi, saya ingin menanyakan tentang syarat-syarat yang diperlukan untuk mengurus E-KTP baru. Apakah ada persyaratan khusus untuk warga yang baru pindah domisili?'
        ]);

        Contact::create([
            'name'    => 'Siti Nurhaliza',
            'email'   => 'siti.nurhaliza@yahoo.com',
            'subject' => 'Jam Operasional Pelayanan',
            'message' => 'Mohon informasi mengenai jam operasional pelayanan di kantor kelurahan. Apakah buka pada hari Sabtu? Terima kasih.'
        ]);

        Contact::create([
            'name'    => 'Bambang Sutrisno',
            'email'   => 'bambang.sutrisno@gmail.com',
            'subject' => 'Keluhan Antrian Online',
            'message' => 'Saya mengalami kesulitan dalam mengambil antrian online untuk layanan Kartu Keluarga. Sistem sepertinya error. Mohon bantuan untuk memperbaiki sistem tersebut.'
        ]);

        Contact::create([
            'name'    => 'Maya Sari',
            'email'   => 'maya.sari@hotmail.com',
            'subject' => 'Prosedur Akta Kelahiran',
            'message' => 'Bagaimana prosedur untuk mengurus akta kelahiran anak yang baru lahir? Dokumen apa saja yang harus disiapkan? Mohon penjelasannya.'
        ]);

        Contact::create([
            'name'    => 'Rudi Hartono',
            'email'   => 'rudi.hartono@gmail.com',
            'subject' => 'Saran Perbaikan Layanan',
            'message' => 'Saya ingin memberikan saran untuk meningkatkan kualitas pelayanan, terutama dalam hal waktu tunggu yang terlalu lama. Semoga bisa menjadi bahan evaluasi.'
        ]);

        // ===== DATA DUMMY UNTUK TABEL AMBILANTRIANS =====
        Ambilantrian::create([
            'tanggal'       => now()->format('Y-m-d'),
            'kode'          => 'KTP-001',
            'nama_lengkap'  => 'Andi Wijaya',
            'alamat'        => 'Jl. Margonda Raya No. 123, Depok',
            'nomorhp'       => '081234567890',
            'batas_antrian' => null,
            'antrian_id'    => 1, // ID dari tabel antrian untuk layanan E-KTP
            'user_id'       => 2  // ID user Andi Wijaya
        ]);

        Ambilantrian::create([
            'tanggal'       => now()->format('Y-m-d'),
            'kode'          => 'KTP-002',
            'nama_lengkap'  => 'Budi Santoso',
            'alamat'        => 'Jl. Pochinki Raya No. 456, Beji, Depok',
            'nomorhp'       => '081234567891',
            'batas_antrian' => null,
            'antrian_id'    => 1, // ID dari tabel antrian untuk layanan E-KTP
            'user_id'       => 3  // ID user Budi Santoso
        ]);

        Ambilantrian::create([
            'tanggal'       => now()->format('Y-m-d'),
            'kode'          => 'KK-001',
            'nama_lengkap'  => 'Citra Lestari',
            'alamat'        => 'Jl. Juanda No. 789, Pancoran Mas, Depok',
            'nomorhp'       => '081234567892',
            'batas_antrian' => null,
            'antrian_id'    => 2, // ID dari tabel antrian untuk layanan KK
            'user_id'       => 4  // ID user Citra Lestari
        ]);


        Ambilantrian::create([
            'tanggal'       => now()->format('Y-m-d'),
            'kode'          => 'KK-002',
            'nama_lengkap'  => 'Fitriani Sari',
            'alamat'        => 'Jl. Limo Raya No. 987, Limo, Depok',
            'nomorhp'       => '081234567895',
            'batas_antrian' => null,
            'antrian_id'    => 2, // ID dari tabel antrian untuk layanan KK
            'user_id'       => 7  // ID user Fitriani Sari
        ]);
    }
}