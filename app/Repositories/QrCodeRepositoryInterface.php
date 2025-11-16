<?php

namespace App\Repositories;

use App\Models\Company;

interface QrCodeRepositoryInterface
{
    /**
     * Generate QR code for a company.
     *
     * @param Company $company
     * @param int $size
     * @param string $format
     * @return string
     */
    public function generate(Company $company, int $size = 300, string $format = 'png'): string;

    /**
     * Get QR code as base64 string.
     *
     * @param Company $company
     * @param int $size
     * @return string
     */
    public function getBase64(Company $company, int $size = 300): string;

    /**
     * Save QR code to storage.
     *
     * @param Company $company
     * @param int $size
     * @param string $format
     * @return string
     */
    public function saveToStorage(Company $company, int $size = 300, string $format = 'png'): string;
}

