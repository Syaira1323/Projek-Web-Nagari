<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AiSuratService;
use App\Services\FastApiSuratService;
use App\Services\HuggingFaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAiController extends Controller
{
    protected HuggingFaceService $hfService;
    protected AiSuratService $aiService;
    protected FastApiSuratService $fastApiService;

    public function __construct(
        HuggingFaceService $hfService,
        AiSuratService $aiService,
        FastApiSuratService $fastApiService
    ) {
        $this->hfService = $hfService;
        $this->aiService = $aiService;
        $this->fastApiService = $fastApiService;
    }

    /**
     * POST /api/ai/rekomendasi-surat
     * Klasifikasi teks kebutuhan warga menggunakan microservice FastAPI DistilBERT.
     */
    public function rekomendasiSurat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'text' => 'required|string|max:1000',
        ], [
            'text.required' => 'Teks kebutuhan surat wajib diisi.',
            'text.string' => 'Teks kebutuhan surat harus berupa string.',
            'text.max' => 'Panjang teks kebutuhan surat maksimal 1000 karakter.',
        ]);

        $cleanedText = trim($validated['text']);
        if (empty($cleanedText)) {
            return response()->json([
                'success' => false,
                'message' => 'Teks kebutuhan surat tidak boleh kosong atau hanya berupa spasi.',
                'error' => 'Teks kebutuhan surat tidak boleh kosong atau hanya berupa spasi.',
            ], 422);
        }

        $result = $this->fastApiService->predict($cleanedText);

        if (!$result['success']) {
            $statusCode = $result['code'] ?? 500;
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Gagal mendapatkan rekomendasi surat dari AI.',
                'error' => $result['error'] ?? 'Gagal mendapatkan rekomendasi surat dari AI.',
            ], $statusCode >= 400 && $statusCode <= 599 ? $statusCode : 500);
        }

        return response()->json([
            'success' => true,
            'input' => $result['input'],
            'recommendations' => $result['recommendations'],
        ]);
    }

    /**
     * POST /api/ai/surat
     * Endpoint utama Asisten AI Layanan Surat Nagari
     */
    public function suratAi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'jenis_surat' => 'nullable|string|max:100',
            'jenis_surat_id' => 'nullable|string|max:100',
            'surat_id' => 'nullable|integer',
        ]);

        $message = trim($validated['message']);
        $jenisSurat = $validated['jenis_surat'] ?? $validated['jenis_surat_id'] ?? null;
        $suratId = isset($validated['surat_id']) ? (int)$validated['surat_id'] : null;

        $result = $this->hfService->generateResponse($message, $jenisSurat, $suratId);

        return response()->json([
            'success' => $result['success'] ?? true,
            'data' => [
                'response' => $result['response'] ?? 'Maaf, belum ada tanggapan.',
                'source' => $result['source'] ?? 'huggingface',
            ],
            'message' => $result['response'] ?? '',
        ]);
    }

    /**
     * POST /api/ai/chat
     * Endpoint chatbot umum warga
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $userName = $user ? $user->name : 'Warga';

        $answer = $this->aiService->answerCitizenQuestion($request->input('message'), $userName);

        return response()->json([
            'success' => true,
            'reply' => $answer,
            'response' => $answer,
        ]);
    }
}
