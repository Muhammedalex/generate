<?php

namespace App\Repositories;

use App\Models\Company;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QrCodeRepository implements QrCodeRepositoryInterface
{
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
        $url = $company->public_url;
        
        $qrCode = QrCode::format($format)
            ->size($size)
            ->errorCorrection('H')
            ->generate($url);

        return $qrCode;
    }

    /**
     * Get QR code as base64 string.
     *
     * @param Company $company
     * @param int $size
     * @return string
     */
    public function getBase64(Company $company, int $size = 300): string
    {
        $qrCode = $this->generate($company, $size, 'png');
        return 'data:image/png;base64,' . base64_encode($qrCode);
    }

    /**
     * Save QR code to storage.
     *
     * @param Company $company
     * @param int $size
     * @param string $format
     * @return string
     */
    public function saveToStorage(Company $company, int $size = 300, string $format = 'png'): string
    {
        $qrCode = $this->generate($company, $size, $format);
        $filename = "qrcodes/company-{$company->id}-{$size}.{$format}";
        
        Storage::disk('public')->put($filename, $qrCode);
        
        return $filename;
    }
}

