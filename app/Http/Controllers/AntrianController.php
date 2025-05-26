<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Layanan;
use App\Models\Ambilantrian;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\PDF;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\DetailAntrian;

class AntrianController extends Controller
{
    public function index(Antrian $antrian)
    {
        $kode = $antrian->kode;

        return view('antrian.index', [
            'antrianList'   => Antrian::all(),
            'antrian'       => $antrian,
            'kode'          => $kode,
        ]);
    }

    public function create(Antrian $antrian)
    {
        $kode = $antrian->kode;

        return view('antrian.create', [
            'antrian'   => $antrian,
            'kode'      => $kode
        ]); 
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tanggal' => 'required|date|after_or_equal:today',
                'nama_lengkap' => 'required|string|max:255',
                'alamat' => 'required|string|max:1000',
                'nomorhp' => 'required|string|max:20|regex:/^[0-9+\-\s]+$/',
                'antrian_id' => 'required|exists:antrians,id',
            ], [
                'tanggal.after_or_equal' => 'Tanggal tidak boleh sebelum hari ini',
                'nomorhp.regex' => 'Format nomor HP tidak valid'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'Validasi gagal'
                ], 422);
            }

            $validated = $validator->validated();

            if (!auth()->check()) {
                return response()->json([
                    'message' => 'Anda harus login terlebih dahulu'
                ], 401);
            }

            $antrian = Antrian::findOrFail($validated['antrian_id']);

            $antrianCount = Ambilantrian::where('antrian_id', $antrian->id)
                ->where('tanggal', $validated['tanggal'])
                ->count();

            if ($antrianCount >= $antrian->batas_antrian) {
                return response()->json([
                    'message' => 'Maaf, antrian untuk tanggal ini sudah penuh'
                ], 422);
            }

            $kodeAntrian = $this->generateKodeAntrian($antrian->id, $validated['tanggal'], $antrian->kode);

            $ambilAntrian = Ambilantrian::create([
                'tanggal' => $validated['tanggal'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'alamat' => $validated['alamat'],
                'nomorhp' => $validated['nomorhp'],
                'kode' => $kodeAntrian,
                'antrian_id' => $antrian->id,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => 'Berhasil mengambil antrian! Nomor antrian Anda: ' . $kodeAntrian,
                'kode_antrian' => $kodeAntrian
            ]);

        } catch (\Exception $e) {
            Log::error('Error in store method: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateKodeAntrian($antrianId, $tanggal, $kodeLayanan)
    {
        $lastRecord = Ambilantrian::where('antrian_id', $antrianId)
            ->where('tanggal', $tanggal)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->kode, strrpos($lastRecord->kode, '-') + 1);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        
        return $kodeLayanan . '-' . $formattedNumber;
    }

    public function detail($id)
    {
        $antrian = Antrian::find($id);
        
        if (!$antrian) {
            abort(404, 'Antrian tidak ditemukan');
        }

        $detailAntrian = Ambilantrian::where('user_id', auth()->id())
                                    ->where('antrian_id', $antrian->id)
                                    ->get();

        return view('antrian.detail', [
            'antrian' => $antrian,
            'detailAntrian' => $detailAntrian
        ]);
    }

    public function detailUser()
    {
        $detailAntrian = DetailAntrian::with('antrian')
                          ->where('user_id', Auth::id())
                          ->get();

        return view('antrian.detail', compact('detailAntrian'));
    }

    public function show(Layanan $listPendaftar)
    {
        return view('antrian.show', [
            'listPendaftar' => $listPendaftar
        ]);
    }

    public function edit($id)
    {
        $antrian = Ambilantrian::findOrFail($id);
        
        if ($antrian->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json($antrian);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'alamat' => 'required|string'
        ]);

        $antrian = Ambilantrian::findOrFail($id);
        
        if ($antrian->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit antrian ini');
        }

        $antrian->update([
            'nama_lengkap' => $request->nama_lengkap,
            'tanggal' => $request->tanggal,
            'alamat' => $request->alamat
        ]);

        return redirect()->back()->with('success', 'Data antrian berhasil diperbarui');
    }

    public function destroy($id)
    {
        $ambilAntrian = Ambilantrian::findOrFail($id);
        
        if ($ambilAntrian->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus antrian ini');
        }
        
        Ambilantrian::destroy($ambilAntrian->id);

        return redirect()->back()->with('success', 'Berhasil Menghapus Antrian');
    }

    public function generatePDF($id)
    {
        $layanan = Layanan::findOrFail($id);
        
        $antrians = Ambilantrian::where('antrian_id', $id)
                               ->where('user_id', auth()->id())
                               ->get();
        
        $pdf = PDF::loadView('antrian.pdf', [
            'layanan' => $layanan,
            'antrians' => $antrians,
            'user' => auth()->user()
        ]);
        
        return $pdf->stream('daftar-antrian-' . $layanan->nama_layanan . '-' . auth()->user()->name . '.pdf');
    }
}