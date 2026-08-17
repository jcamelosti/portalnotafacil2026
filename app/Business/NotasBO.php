<?php

namespace App\Business;

use App\Cliente;
use App\Utilitarios\Utilitarios;

class NotasBO extends GeralBO
{
    protected $model;

    public function __construct(){
        $this->model = $this->getNotasModel();
    }

    /*
    * Cria nova Instancia de NotaBO
    * */
    public static function newInstance(){
        return new NotasBO();
    }

    protected function getNotasModel(){
        return null;
    }

    public function tratarDados($dados){
        $dados = Utilitarios::formatarValoresDB($dados);
        return $dados;
    }

    public function tratarDadosApi($dados){
        $dados = Utilitarios::formatarValoresNota($dados);
        return $dados;
    }

    public function montarRps($dados){
        dd($dados);
    }
}
