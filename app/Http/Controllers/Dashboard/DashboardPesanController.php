<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;   

class DashboardPesanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil data kontak dari database
        $contacts = Contact::orderBy('created_at', 'desc')->get();
        // Atau kalau mau paginate: ->paginate(10);

        // Pass variabel $contacts ke view
        return view('dashboard.pesan.index', compact('contacts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.pesan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi basic
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Untuk sementara redirect dengan success message
        return redirect()->route('dashboard.pesan.index')
            ->with('success', 'Pesan berhasil dikirim!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        return view('dashboard.pesan.show', compact('contact'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $contact = Contact::findOrFail($id);
        return view('dashboard.pesan.edit', compact('contact'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contact->update($request->only(['subject', 'message']));

        return redirect()->route('dashboard.pesan.index')
            ->with('success', 'Pesan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact->delete();
            
            return redirect()->route('dashboard.pesan.index')
                ->with('success', 'Pesan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('dashboard.pesan.index')
                ->with('error', 'Gagal menghapus pesan!');
        }
    }

    /**
     * Reply to a message
     */
    public function reply(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        
        $request->validate([
            'reply_message' => 'required|string'
        ]);

        // Di sini Anda bisa menambahkan logic untuk menyimpan reply
        // atau mengirim email balasan

        return redirect()->route('dashboard.pesan.show', $id)
            ->with('success', 'Balasan berhasil dikirim!');
    }

    /**
     * Mark message as read
     */
    public function markAsRead($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact->update(['is_read' => true]);
            
            return response()->json([
                'success' => true,
                'message' => 'Pesan telah ditandai sebagai dibaca'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai pesan sebagai dibaca'
            ], 500);
        }
    }
}