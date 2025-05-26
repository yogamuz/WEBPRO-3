<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChatbotController extends Controller
{
    /**
     * Process chat message and return response
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processMessage(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'message' => 'required|string|max:500',
            'session_id' => 'nullable|string',
            'waiting_for_queue_choice' => 'boolean'
        ]);

        $userMessage = $validated['message'];
        $sessionId = $validated['session_id'] ?? null;
        $waitingForQueueChoice = $validated['waiting_for_queue_choice'] ?? false;
        
        // Generate response based on message content and state
        $responseData = $this->generateResponse($userMessage, $waitingForQueueChoice);
        
        return response()->json([
            'success' => true,
            'message' => $responseData['message'],
            'session_id' => $sessionId,
            'waiting_for_queue_choice' => $responseData['waiting_for_queue_choice'] ?? false
        ]);
    }

    /**
     * Generate response based on user message
     *
     * @param  string  $message
     * @param  bool  $waitingForQueueChoice
     * @return array
     */
    private function generateResponse($message, $waitingForQueueChoice = false)
    {
        $message = strtolower(trim($message));
        
        // Handle queue choice responses first
        if ($waitingForQueueChoice) {
            if ($message === "ya" || $message === "yes" || $message === "y") {
                $queueNumber = 'A' . rand(100, 999);
                $estimatedTime = rand(15, 45);
                
                return [
                    'message' =>
                                "📱 **Berikut Panduan Untuk Mengambil Antrian Dukcapil Online**\n" .
                                "1. Kunjungi website antrian online Disdukcapil Kota Depok\n" .
                                "2. Siapkan dokumen yang diperlukan\n" .
                                "3. Pilih jenis layanan yang tersedia\n" .
                                "4. Masukkan data diri anda\n" .
                                "5. Simpan dan Cetak nomor urut antrian\n\n" .
                                "Ketik 'menu' untuk melihat pilihan layanan.",
                    'waiting_for_queue_choice' => false
                ];
            }
            elseif ($message === "tidak" || $message === "no" || $message === "n") {
                return [
                    'message' => "Terima kasih telah menggunakan layanan kami!😊\n\n" .
                                "Ketik 'menu' untuk melihat pilihan layanan.",
                    'waiting_for_queue_choice' => false
                ];
            }
            else {
                return [
                    'message' => "Mohon pilih salah satu:\n\n" .
                                "✅ **Ya** - Ambil nomor antrian\n" .
                                "❌ **Tidak** - Kembali ke menu utama\n\n" .
                                "Ketik \"Ya\" atau \"Tidak\"",
                    'waiting_for_queue_choice' => true
                ];
            }
        }
        
        // Handle shortcut numbers first
        if ($message === "1") {
            return ['message' => $this->getEKTPInfo()];
        }
        elseif ($message === "2") {
            return ['message' => $this->getKKInfo()];
        }
        elseif ($message === "3") {
            return ['message' => $this->getAktaInfo()];
        }
        elseif ($message === "4") {
            return ['message' => $this->getSuratPindahInfo()];
        }
                elseif ($message === "5") {
            return [
                'message' => $this->checkQueueStatus(),
                'waiting_for_queue_choice' => true
            ];
        }
        elseif ($message === "6") {
           return ['message' => $this->getLocation()];

        }
        
        // Handle keyword-based responses
        if (str_contains($message, 'ktp') || str_contains($message, 'e-ktp')) {
            return ['message' => $this->getEKTPInfo()];
        } 
        elseif (str_contains($message, 'kartu keluarga') || str_contains($message, 'kk')) {
            return ['message' => $this->getKKInfo()];
        }
        elseif (str_contains($message, 'akta') || str_contains($message, 'kelahiran')) {
            return ['message' => $this->getAktaInfo()];
        }
        elseif (str_contains($message, 'pindah') || str_contains($message, 'domisili')) {
            return ['message' => $this->getSuratPindahInfo()];
        }
        elseif (str_contains($message, 'antrian') || str_contains($message, 'antre') || str_contains($message, 'queue')) {
            return [
                'message' => $this->checkQueueStatus(),
                'waiting_for_queue_choice' => true
            ];
        }
        elseif (str_contains($message, 'jam') || str_contains($message, 'buka') || str_contains($message, 'operasional')) {
            return ['message' => "🕐 **JAM OPERASIONAL:**\n\nSenin - Jumat: 08.00 - 15.00 WIB\nSabtu - Minggu: TUTUP\n\n*Pelayanan tutup pada hari libur nasional"];
        }
        elseif (str_contains($message, 'kontak') || str_contains($message, 'telepon') || str_contains($message, 'hubungi')) {
            return ['message' => "📞 **KONTAK KAMI:**\n\n☎️ Telepon: (021) 1234567\n📧 Email: dukcapil@example.com\n📍 Alamat: Jl. Raya Dukcapil No. 123\n🌐 Website: www.dukcapil.go.id"];
        }
        elseif (str_contains($message, 'menu') || str_contains($message, 'pilihan') || str_contains($message, 'layanan')) {
            return ['message' => $this->getMenuOptions()];
        }
        elseif (str_contains($message, 'hai') || str_contains($message, 'halo') || str_contains($message, 'hi')) {
            return ['message' => "Halo! Ada yang bisa saya bantu terkait layanan Dukcapil?\n\n" . $this->getMenuOptions()];
        }
        elseif (str_contains($message, 'terima kasih') || str_contains($message, 'makasih') || str_contains($message, 'thank')) {
            return ['message' => "Sama-sama, senang bisa membantu Anda! 😊\n\nAda hal lain yang ingin ditanyakan?\n\nKetik 'menu' untuk melihat pilihan layanan."];
        }
        else {
            return ['message' => "Maaf, saya tidak mengerti pertanyaan Anda. 😅\n\nSilakan ketik angka 1-5 untuk pilihan cepat atau hubungi petugas kami untuk informasi lebih lanjut.\n\nKetik 'menu' untuk melihat pilihan layanan."];
        }
    }

    /**
     * Get E-KTP information with current queue
     *
     * @return string
     */
    private function getEKTPInfo()
    {
        $queueCount = $this->getQueueCount('KTP');
        $estimatedWait = $queueCount * 4; // 4 minutes per person
        
        return "📋 **PERSYARATAN E-KTP:**\n\n• KTP-el lama (jika ada)\n• Kartu Keluarga asli + fotokopi\n• Akta Kelahiran asli + fotokopi\n• Surat keterangan hilang dari kepolisian (jika hilang)\n\n💰 **Biaya:** GRATIS\n⏰ **Estimasi:** 1-2 hari kerja\n📊 **Antrian saat ini:** {$queueCount} orang (± {$estimatedWait} menit)\n\nBagaimana jika belum hadir saat dipanggil?\n **Antrian anda akan dilewatkan dan harus konfirmasi ulang ke administrasi.**";
    }

    /**
     * Get Kartu Keluarga information with current queue
     *
     * @return string
     */
    private function getKKInfo()
    {
        $queueCount = $this->getQueueCount('KK');
        $estimatedWait = $queueCount * 4;
        
        return "📋 **PERSYARATAN KARTU KELUARGA:**\n\n• KTP asli semua anggota keluarga + fotokopi\n• Akta kelahiran semua anggota keluarga + fotokopi\n• Akta nikah/cerai (jika ada) + fotokopi\n• Surat pengantar dari RT/RW\n\n💰 **Biaya:** GRATIS\n⏰ **Estimasi:** 1-2 hari kerja\n📊 **Antrian saat ini:** {$queueCount} orang (± {$estimatedWait} menit)\n\nBagaimana jika belum hadir saat dipanggil?\n **Antrian anda akan dilewatkan dan harus konfirmasi ulang ke administrasi.**";
    }

    /**
     * Get Akta Kelahiran information with current queue
     *
     * @return string
     */
    private function getAktaInfo()
    {
        $queueCount = $this->getQueueCount('AKKEL');
        $estimatedWait = $queueCount * 4;
        
        return "📋 **PERSYARATAN AKTA KELAHIRAN:**\n\n• Surat keterangan lahir dari bidan/dokter/RS\n• KTP kedua orang tua asli + fotokopi\n• Kartu Keluarga asli + fotokopi\n• Akta nikah orang tua asli + fotokopi\n• 2 orang saksi dengan KTP\n\n💰 **Biaya:** GRATIS\n⏰ **Estimasi:** 3-5 hari kerja\n📊 **Antrian saat ini:** {$queueCount} orang (± {$estimatedWait} menit)\n\nBagaimana jika belum hadir saat dipanggil?\n **Antrian anda akan dilewatkan dan harus konfirmasi ulang ke administrasi.**";
    }

    /**
     * Get Surat Pindah information with current queue
     *
     * @return string
     */
    private function getSuratPindahInfo()
    {
        $queueCount = $this->getQueueCount('SP');
        $estimatedWait = $queueCount * 4;
        
        return "📋 **PERSYARATAN SURAT PINDAH:**\n\n• KTP asli + fotokopi\n• Kartu Keluarga asli + fotokopi\n• Surat pengantar dari desa/kelurahan asal\n• Surat keterangan tidak mampu (jika diperlukan)\n\n💰 **Biaya:** GRATIS\n⏰ **Estimasi:** 1 hari kerja\n📊 **Antrian saat ini:** {$queueCount} orang (± {$estimatedWait} menit)\n\nBagaimana jika belum hadir saat dipanggil?\n **Antrian anda akan dilewatkan dan harus konfirmasi ulang ke administrasi.**";
    }

    /**
     * Get menu options
     *
     * @return string
     */
    private function getMenuOptions()
    {
        return "Pilih layanan yang Anda butuhkan:\n\n1️⃣ E-KTP\n2️⃣ KARTU KELUARGA\n3️⃣ AKTA KELAHIRAN\n4️⃣ SURAT PINDAH\n5️⃣ CEK JUMLAH ANTRIAN 6️⃣ LOKASI\n\n Ketik angka 1-6 untuk memilih layanan.";
    }
    
    private function getLocation() 
    {
        return "📞 **KONTAK KAMI:**\n\n☎️: (021) 865 371\n📧: info@dukcapildepok@go.id
        📍: Jl. Margonda Raya No.54, Kec. Pancoran Mas, Kota Depok.
        🌐: disdukcapilkotadepok.go.id\n
        🕐JAM OPERASIONAL\nSenin - Jumat: 08.00 - 15.00 WIB\nSabtu - Minggu: TUTUP\n\n**Pelayanan tutup pada hari libur nasional**";
    }
    /**
     * Get current queue status from database and ask for queue choice
     * 
     * @return string
     */
    private function checkQueueStatus()
    {
        try {
            $today = Carbon::now()->format('Y-m-d');
            
            // Query dari database ambilantrians untuk mendapatkan jumlah antrian hari ini
            $ektp = $this->getQueueCount('KTP');
            $kk = $this->getQueueCount('KK');
            $akta = $this->getQueueCount('AKKEL');
            $pindah = $this->getQueueCount('SP');
            
            // Hitung estimasi waktu tunggu (asumsi 4 menit per orang)
            $ektpWait = $ektp * 4;
            $kkWait = $kk * 4;
            $aktaWait = $akta * 4;
            $pindahWait = $pindah * 4;
            
            $queueInfo = "📊 **INFORMASI ANTRIAN HARI INI:**\n\n" .
                        "🆔 **E-KTP:** {$ektp} orang (± {$ektpWait} menit)\n" .
                        "👨‍👩‍👧‍👦 **Kartu Keluarga:** {$kk} orang (± {$kkWait} menit)\n" .
                        "👶 **Akta Kelahiran:** {$akta} orang (± {$aktaWait} menit)\n" .
                        "📦 **Surat Pindah:** {$pindah} orang (± {$pindahWait} menit)\n\n" .
                        "*Data diperbarui real-time*\n" .
                        "**Jam pelayanan: 08.00-15.00 WIB**\n\n" .
                        "Ingin mengambil nomor antrian?\n\n" .
                        "✅ **Ya** - Ambil nomor antrian\n" .
                        "❌ **Tidak** - Kembali ke menu utama\n\n" .
                        "Ketik \"Ya\" atau \"Tidak\"";
                        
            return $queueInfo;
        } catch (\Exception $e) {
            // Jika terjadi kesalahan koneksi database
            return "Maaf, terjadi kesalahan saat memeriksa status antrian. Silakan coba lagi nanti atau hubungi petugas kami.";
        }
    }

    /**
     * Get queue count for specific service type
     *
     * @param string $serviceType
     * @return int
     */
    private function getQueueCount($serviceType)
    {
        try {
            $today = Carbon::now()->format('Y-m-d');
            
            // Count antrian berdasarkan tanggal hari ini dan jenis layanan
            // Asumsi: kode berisi jenis layanan (E-KTP, KARTU KELUARGA, dll)
            $count = DB::table('ambilantrians')
                ->whereDate('tanggal', $today)
                ->where('kode', 'LIKE', '%' . $serviceType . '%')
                ->count();
                
            return $count;
        } catch (\Exception $e) {
            // Return 0 jika terjadi error
            return 0;
        }
    }

    /**
     * Get real-time queue data for frontend
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQueueData()
    {
        try {
            $today = Carbon::now()->format('Y-m-d');
            
            $queueData = [
                'ktp' => [
                    'current' => $this->getQueueCount('E-KTP'),
                    'estimated_wait' => $this->getQueueCount('E-KTP') * 4
                ],
                'kk' => [
                    'current' => $this->getQueueCount('KARTU KELUARGA'),
                    'estimated_wait' => $this->getQueueCount('KARTU KELUARGA') * 4
                ],
                'akta' => [
                    'current' => $this->getQueueCount('AKTA KELAHIRAN'),
                    'estimated_wait' => $this->getQueueCount('AKTA KELAHIRAN') * 4
                ],
                'pindah' => [
                    'current' => $this->getQueueCount('SURAT PINDAH'),
                    'estimated_wait' => $this->getQueueCount('SURAT PINDAH') * 4
                ]
            ];
            
            return response()->json([
                'success' => true,
                'data' => $queueData,
                'last_updated' => Carbon::now()->format('H:i:s')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching queue data'
            ], 500);
        }
    }
}