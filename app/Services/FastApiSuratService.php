<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FastApiSuratService
{
    protected string $baseUrl;
    protected int $timeout;

    public function getBaseUrl(): string
    {
        return rtrim((string) config('services.fastapi.url', 'http://127.0.0.1:8000'), '/');
    }

    public function getTimeout(): int
    {
        return (int) config('services.fastapi.timeout', 10);
    }

    /**
     * Mengirim teks kebutuhan warga ke FastAPI untuk klasifikasi DistilBERT.
     *
     * @param string $text
     * @return array
     */
    public function predict(string $text): array
    {
        $url = "{$this->getBaseUrl()}/predict";
        $timeout = $this->getTimeout();

        try {
            $response = Http::timeout($timeout)
                ->acceptJson()
                ->asJson()
                ->post($url, [
                    'text' => $text,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!is_array($data) || !isset($data['recommendations']) || !is_array($data['recommendations'])) {
                    Log::warning('FastApiSuratService: Format respons tidak sesuai', ['response' => $data]);
                    return [
                        'success' => false,
                        'error' => 'Format respons dari microservice AI tidak valid.',
                        'code' => 502,
                    ];
                }

                return [
                    'success' => true,
                    'input' => $data['input'] ?? $text,
                    'recommendations' => $data['recommendations'],
                ];
            }

            if ($response->status() === 422) {
                $errorDetail = $response->json('detail') ?? 'Validasi input ditolak oleh layanan AI.';
                return [
                    'success' => false,
                    'error' => is_string($errorDetail) ? $errorDetail : json_encode($errorDetail),
                    'code' => 422,
                ];
            }

            Log::error('FastApiSuratService: Error dari server FastAPI', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Layanan AI mengembalikan respons kesalahan (Status ' . $response->status() . ').',
                'code' => $response->status(),
            ];

        } catch (ConnectionException $e) {
            Log::error('FastApiSuratService: Gagal terhubung ke FastAPI', [
                'url' => $url,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Gagal terhubung ke layanan AI Microservice (FastAPI tidak aktif atau port tidak dapat dijangkau).',
                'code' => 503,
            ];
        } catch (\Throwable $e) {
            Log::error('FastApiSuratService: Exception tidak terduga', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Terjadi kesalahan sistem saat memproses permintaan AI.',
                'code' => 500,
            ];
        }
    }

    /**
     * Memeriksa kesehatan microservice FastAPI (Health Check).
     *
     * @return array
     */
    public function checkHealth(): array
    {
        $url = "{$this->getBaseUrl()}/health";

        try {
            $response = Http::timeout(3)
                ->acceptJson()
                ->get($url);

            if ($response->successful()) {
                return [
                    'online' => true,
                    'status' => $response->json('status', 'ok'),
                    'model' => $response->json('model', 'unknown'),
                    'classes' => $response->json('classes', 7),
                ];
            }

            return [
                'online' => false,
                'status' => 'error',
                'code' => $response->status(),
            ];
        } catch (\Throwable $e) {
            return [
                'online' => false,
                'status' => 'offline',
                'error' => $e->getMessage(),
            ];
        }
    }
}
