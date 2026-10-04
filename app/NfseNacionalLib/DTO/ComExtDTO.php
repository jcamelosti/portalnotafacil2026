<?php
namespace JCamelo\NfseNacionalLib\DTO;

class ComExtDTO
{
    public function __construct(

        /**
         * Modo de Prestação
         *
         * 0 - Desconhecido
         * 1 - Transfronteiriço
         * 2 - Consumo no Brasil
         * 3 - Movimento Temporário de Pessoas Físicas
         * 4 - Consumo no Exterior
         */
        public int $mdPrestacao,

        /**
         * Vínculo entre as partes no negócio
         *
         * 0 - Sem vínculo
         * 1 - Controlada
         * 2 - Controladora
         * 3 - Coligada
         * 4 - Matriz
         * 5 - Filial ou sucursal
         * 6 - Outro vínculo
         * 9 - Desconhecido
         */
        public int $vincPrest,

        /**
         * Código da moeda da transação
         */
        public string $tpMoeda,

        /**
         * Valor do serviço na moeda estrangeira
         */
        public string $vServMoeda,//era float coloquei string pelo formato necessário

        /**
         * Mecanismo de apoio/fomento ao Comércio Exterior
         * utilizado pelo prestador
         */
        public string $mecAFComexP,

        /**
         * Mecanismo de apoio/fomento ao Comércio Exterior
         * utilizado pelo tomador
         */
        public string $mecAFComexT,

        /**
         * Vínculo da operação à movimentação temporária de bens
         *
         * 0 - Desconhecido
         * 1 - Não
         * 2 - Vinculada à Declaração de Importação
         * 3 - Vinculada à Declaração de Exportação
         */
        public int $movTempBens,

        /**
         * Número da Declaração de Importação
         * DI/DSI/DA/DRI-E
         */
        public ?string $nDI = null,

        /**
         * Número do Registro de Exportação
         */
        public ?string $nRE = null,

        /**
         * Indicador de compartilhamento com o MDIC
         *
         * 0 - Não enviar
         * 1 - Enviar
         */
        public int $mdic = 0,
    ) {
    }
}