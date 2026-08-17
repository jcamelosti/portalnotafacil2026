<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Protocolo;
use Illuminate\Http\Request;

class ProtocoloController extends Controller
{
    private $protocoloModel;

    public function __construct(Protocolo $protocoloModel){
        $this->protocoloModel = $protocoloModel;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $protocolos = $this->protocoloModel
            ->orderBy('id', 'desc')
            ->paginate();

        return view('admin.protocolos.index', ['protocolos' => $protocolos]);
    }
}
