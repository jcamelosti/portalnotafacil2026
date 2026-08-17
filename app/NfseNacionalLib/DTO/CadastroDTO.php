<?php
namespace JCamelo\NfseNacionalLib\DTO;

class CadastroDTO
{
    public function __construct(
        public string $cnpj,
        public string $im,
        public string $status,
        public string $razaoSocial,
        public string $fantasia,
        public array $endereco,
        public string $fone,
        public string $email,
        public int $opcaoMei,
        public int $optanteSimplesNacional,
        public array $simplesNacional,
        public array $atividades,
        public int $permiteOutrasDeducoes,
        public int $permiteDescontoCondicionado,
        public int $permiteDescontoIncondicionado,
        public int $permiteExigibilidadeSuspensaDecisaoJudicial,
        public int $permiteExigibilidadeSuspensaProcAdm,
        public int $permiteTributarFora,
        public array $tributacoesPermitidas,
    ) {}
}