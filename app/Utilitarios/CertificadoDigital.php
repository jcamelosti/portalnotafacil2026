<?php
namespace App\Utilitarios;

class CertificadoDigital
{
    public static function lerDadosCertificado($path, $password)
    {
        $pfxCertPrivado = $path;
        $cert_password = $password;
        $erros = [];

        if (!file_exists($pfxCertPrivado)) {
            $erros[] = "Certificado não encontrado!! " . $pfxCertPrivado;
        } else {
            $pfxContent = file_get_contents($pfxCertPrivado);

            if (!openssl_pkcs12_read($pfxContent, $x509certdata, $cert_password)) {
                $erros[] = "O certificado não pode ser lido!!";
            } else {
                $CertPriv = openssl_x509_parse(openssl_x509_read($x509certdata['cert']));
                $validadeCertificado = date('Y-m-d', $CertPriv['validTo_time_t']);
                $razao_social = $CertPriv['subject']['CN'];

                return ['data_validade' => $validadeCertificado, 'razao_social' => $razao_social];
            }
        }
        return $erros;
    }
}

