<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\SuratPermohonan;
use App\Services\AiSuratService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WargaAiController extends Controller
{
    protected AiSuratService $aiService;

    public function __construct(AiSuratService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Halaman Bantuan / AI Assistant
     */
    public function index(): View
    {
        $user = Auth::user();
        $daftarSurat = SuratPermohonan::daftarJenisSurat();
        return view('warga.bantuan', compact('user', 'daftarSurat'));
    }

    /**
     * AJAX Chat dengan AI
     */
    public function chat(Request $request): JsonResponse
    {
        $message = trim($request->input('message', ''));
        if (empty($message)) {
            return response()->json([
                'success' => false,
                'reply' => 'Silakan ketikkan pertanyaan atau keperluan surat Anda.',
            ]);
        }

        $userName = Auth::check() ? Auth::user()->name : 'Warga Nagari';
        $reply = $this->aiService->answerCitizenQuestion($message, $userName);
        $classification = $this->aiService->classifyWithDistilBERT($message);

        return response()->json([
            'success' => true,
            'reply' => $reply,
            'classification' => $classification,
        ]);
    }
}
