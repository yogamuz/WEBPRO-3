<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    // Menampilkan form contact

    // Menyimpan pesan contact ke database
    public function send(Request $request)
    {
        try {
            // Validasi data
            $validator = Validator::make($request->all(), [
                'name'    => 'required|string|max:255',
                'email'   => 'required|email|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:2000',
            ]);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Data tidak valid: ' . $validator->errors()->first(),
                        'errors'  => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }

            $data = $validator->validated();

            // Cek apakah ini adalah duplikat pengiriman (dengan token sesi atau timestamp)
            $sessionKey = 'contact_form_submitted_' . md5($data['email'] . $data['message']);
            
            // Jika form sudah dikirim dalam 30 detik terakhir, anggap sebagai duplikat
            if (session()->has($sessionKey)) {
                $submittedAt = session()->get($sessionKey);
                if (now()->diffInSeconds($submittedAt) < 30) {
                    if ($request->ajax()) {
                        return response()->json([
                            'status'  => 'success',
                            'message' => 'Pesan Anda berhasil dikirim.',
                        ]);
                    }
                    return back()->with('success', 'Pesan Anda berhasil dikirim.');
                }
            }
            
            // Simpan data ke database
            $contact = Contact::create($data);
            
            // Log untuk debugging
            Log::info('Contact saved successfully', ['contact_id' => $contact->id, 'name' => $contact->name]);
            
            // Tandai form ini sebagai sudah dikirim
            session()->put($sessionKey, now());
            
            // Kirim respons sesuai jenis request
            if ($request->ajax()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Pesan Anda berhasil dikirim dan tersimpan.',
                ]);
            }

            return back()->with('success', 'Pesan Anda berhasil dikirim.');
            
        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Error in ContactController@send: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Terjadi kesalahan saat menyimpan pesan. Silakan coba lagi.',
                ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan saat menyimpan pesan. Silakan coba lagi.')->withInput();
        }
    }

    // Menampilkan daftar pesan (untuk admin)
    public function showMessages()
    {
        $contacts = Contact::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.contacts.index', compact('contacts'));
    }

    // Menampilkan detail pesan
    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        return view('admin.contacts.show', compact('contact'));
    }

    // Menghapus pesan
    public function destroy($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact->delete();

            return redirect()->route('admin.contacts.index')
                        ->with('success', 'Pesan berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error in ContactController@destroy: ' . $e->getMessage());
            
            return redirect()->route('admin.contacts.index')
                        ->with('error', 'Terjadi kesalahan saat menghapus pesan');
        }
    }
}