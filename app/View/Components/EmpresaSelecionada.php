<?php

namespace App\View\Components;

use App\Models\Empresa;
use Illuminate\View\Component;

class EmpresaSelecionada extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $empresaSessao = request()->session()->get('empresa_selecionada');
        $empresa = Empresa::where('id', $empresaSessao)->first();        
        return view('components.empresa-selecionada', compact('empresa'));
    }
}
