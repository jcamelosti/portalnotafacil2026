<?php

declare(strict_types=1);

namespace App\Services\NotaFiscalServico;

use Barryvdh\DomPDF\Facade\Pdf;
use Milon\Barcode\Facades\DNS2DFacade;
use RuntimeException;

final class DanfsePdfService
{
    public function renderFromXml(string $xml, bool $cancelled = false, bool $substituted = false): string
    {
        $data = DanfseXmlData::fromXml($xml, $cancelled, $substituted);
        $qrCode = DNS2DFacade::getBarcodePNG((string) $data['qr_code_url'], 'QRCODE', 5, 5);

        return (string) Pdf::loadView('nota-fiscal.danfse', [
            'data' => $data,
            'qrCode' => $qrCode,
        ])
            ->setPaper('a4')
            ->output();
    }
}