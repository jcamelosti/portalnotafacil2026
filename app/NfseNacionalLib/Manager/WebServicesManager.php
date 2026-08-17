<?php

namespace JCamelo\NfseNacionalLib\Manager;

use App\Models\Empresa;
use App\Models\EndPoint;

class WebServicesManager
{
    public function getWsUrl(int $empresaId): array
    {
        $empresa = Empresa::findOrFail($empresaId);
        $endpoint = EndPoint::where('codigo_municipio', $empresa->cidade_id)
            ->first();
        $url = '';

        if($endpoint){
            switch ($empresa->ambiente_emissao){
                case 'PRODUCAO':
                    $url = $endpoint->url_endpoint;
                    break;
                default:
                    $url = $endpoint->url_endpoint2;
                    break;
            }
        }   

        return [
            'url' => $url,
        ];
    }
}