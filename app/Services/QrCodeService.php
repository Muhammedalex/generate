<?php

namespace App\Services;

use App\Models\Company;
use App\Repositories\QrCodeRepositoryInterface;
use Illuminate\Http\Response;

class QrCodeService
{
    protected QrCodeRepositoryInterface $repository;

    public function __construct(QrCodeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Generate QR code for a company.
     *
     * @param Company $company
     * @param int $size
     * @param string $format
     * @return string
     */
    public function generate(Company $company, int $size = 300, string $format = 'png'): string
    {
        return $this->repository->generate($company, $size, $format);
    }

    /**
     * Get QR code as base64 for display.
     *
     * @param Company $company
     * @param int $size
     * @return string
     */
    public function getBase64(Company $company, int $size = 300): string
    {
        return $this->repository->getBase64($company, $size);
    }

    /**
     * Download QR code.
     *
     * @param Company $company
     * @param int $size
     * @param string $format
     * @return Response
     */
    public function download(Company $company, int $size = 300, string $format = 'png'): Response
    {
        $qrCode = $this->repository->generate($company, $size, $format);
        $filename = "{$company->slug}-qrcode.{$format}";

        return response($qrCode, 200)
            ->header('Content-Type', $format === 'svg' ? 'image/svg+xml' : 'image/png')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Display QR code inline.
     *
     * @param Company $company
     * @param int $size
     * @param string $format
     * @return Response
     */
    public function display(Company $company, int $size = 300, string $format = 'png'): Response
    {
        $qrCode = $this->repository->generate($company, $size, $format);

        return response($qrCode, 200)
            ->header('Content-Type', $format === 'svg' ? 'image/svg+xml' : 'image/png');
    }
}

