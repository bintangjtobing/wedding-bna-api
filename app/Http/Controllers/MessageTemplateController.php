<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageTemplateController extends Controller
{
    public function index()
    {
        $templates = Message::where('is_template', true)->latest()->get();
        return view('messages.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('messages.templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'template_name' => 'required|string|max:100',
            'content' => 'required|string',
        ]);

        Message::create([
            'template_name' => $validated['template_name'],
            'content' => $validated['content'],
            'is_template' => true,
        ]);

        return redirect()->route('templates.index')
            ->with('success', 'Template pesan berhasil disimpan.');
    }

    public function edit(Message $template)
    {
        return view('messages.templates.edit', compact('template'));
    }

    public function update(Request $request, Message $template)
    {
        $validated = $request->validate([
            'template_name' => 'required|string|max:100',
            'content' => 'required|string',
        ]);

        $template->update([
            'template_name' => $validated['template_name'],
            'content' => $validated['content'],
        ]);

        return redirect()->route('templates.index')
            ->with('success', 'Template pesan berhasil diperbarui.');
    }

    public function destroy(Message $template)
    {
        $template->delete();

        return redirect()->route('templates.index')
            ->with('success', 'Template pesan berhasil dihapus.');
    }

    public function use(Message $template)
    {
        // Get admin info
        $currentAdmin = Auth::guard('admin')->user();
        $groomAdmin = \App\Models\Admin::where('role', 'groom')->first();
        $brideAdmin = \App\Models\Admin::where('role', 'bride')->first();

        // Ambil statistik status undangan untuk ditampilkan
        $groomSentCount = $groomAdmin ? $groomAdmin->contacts()->where('invitation_status', 'terkirim')->count() : 0;
        $groomPendingCount = $groomAdmin ? $groomAdmin->contacts()->where('invitation_status', 'belum_dikirim')->count() : 0;
        $brideSentCount = $brideAdmin ? $brideAdmin->contacts()->where('invitation_status', 'terkirim')->count() : 0;
        $bridePendingCount = $brideAdmin ? $brideAdmin->contacts()->where('invitation_status', 'belum_dikirim')->count() : 0;

        return view('messages.create', compact(
            'currentAdmin',
            'groomAdmin',
            'brideAdmin',
            'groomSentCount',
            'groomPendingCount',
            'brideSentCount',
            'bridePendingCount',
            'template'
        ));
    }

    // API endpoint untuk template pesan
    public function apiGetTemplates()
    {
        $templates = Message::where('is_template', true)->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $templates
        ]);
    }

    public function apiStoreTemplate(Request $request)
    {
        $validated = $request->validate([
            'template_name' => 'required|string|max:100',
            'content' => 'required|string',
        ]);

        $template = Message::create([
            'template_name' => $validated['template_name'],
            'content' => $validated['content'],
            'is_template' => true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Template pesan berhasil disimpan.',
            'data' => $template
        ]);
    }

    public function apiUpdateTemplate(Request $request, Message $template)
    {
        $validated = $request->validate([
            'template_name' => 'required|string|max:100',
            'content' => 'required|string',
        ]);

        $template->update([
            'template_name' => $validated['template_name'],
            'content' => $validated['content'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Template pesan berhasil diperbarui.',
            'data' => $template
        ]);
    }

    public function apiDeleteTemplate(Message $template)
    {
        $template->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Template pesan berhasil dihapus.',
            'data' => [
                'id' => $template->id
            ]
        ]);
    }
}
