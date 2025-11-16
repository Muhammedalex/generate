<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QrCodeController extends Controller
{
    protected QrCodeService $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Display QR code for a company.
     *
     * @param Company $company
     * @param Request $request
     * @return Response
     */
    public function show(Company $company, Request $request): Response
    {
        // Check authorization - user must own the company
        if ($company->user_id !== auth()->id()) {
            abort(403);
        }

        $size = (int) $request->get('size', 300);
        $format = $request->get('format', 'png');

        return $this->qrCodeService->display($company, $size, $format);
    }

    /**
     * Download QR code for a company.
     *
     * @param Company $company
     * @param Request $request
     * @return Response
     */
    public function download(Company $company, Request $request): Response
    {
        // Check authorization - user must own the company
        if ($company->user_id !== auth()->id()) {
            abort(403);
        }

        $size = (int) $request->get('size', 300);
        $format = $request->get('format', 'png');

        return $this->qrCodeService->download($company, $size, $format);
    }

    /**
     * Get QR code as base64 for embedding.
     *
     * @param Company $company
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function base64(Company $company, Request $request)
    {
        // Check authorization - user must own the company
        if ($company->user_id !== auth()->id()) {
            abort(403);
        }

        $size = (int) $request->get('size', 300);
        $base64 = $this->qrCodeService->getBase64($company, $size);

        return response()->json([
            'base64' => $base64,
            'url' => $company->public_url,
        ]);
    }
}
