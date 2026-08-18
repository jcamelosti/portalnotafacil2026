<?php

namespace JCamelo\NfseNacionalLib\Manager;

use App\Models\Empresa;
use App\Models\EndPoint;

class WebServicesManager
{
    public function getWsUrl(int $empresaId): array
    {
        $url = null;

        $empresa = Empresa::findOrFail($empresaId);
        $endpoint = EndPoint::where('codigo_municipio', $empresa->cidade_id)
            ->first();
            
        if ($empresa->ambiente_emissao === 'PRODUCAO') {
            $url = $endpoint->url_endpoint;
        } else {
            $url = $endpoint->url_endpoint2;
        }

        return [
            'url' => $url,
        ];
    }
}