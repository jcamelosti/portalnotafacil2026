<x-area-admin-layout title="Edição de Empresa">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Edição de Empresa') }}
        </h2>
    </x-slot>

    <div class="py-10 mx-auto w-full sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
                <label class="block text-sm">
                    <span class="text-gray-700 ">CPF/CNPJ:</span>
                    {!! Form::text('razao_social', $empresa->cpf_cnpj_fmt, ['maxlength' => '255', 'required', 'class'=>'block w-full mt-1 text-sm  
                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                    :shadow-outline-gray form-input', 'placeholder'=>'Razão Social']) !!}
                    @if ($errors->has('razao_social'))
                        <span class="text-xs text-red-600 ">
                        <strong>{{ $errors->first('razao_social') }}</strong>
                        </span>
                    @endif
                </label>
                <label class="block text-sm">
                    <span class="text-gray-700 ">Razão Social:</span>
                    {!! Form::text('razao_social', $empresa->razao_social, ['maxlength' => '255', 'required', 'class'=>'block w-full mt-1 text-sm  
                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                    :shadow-outline-gray form-input', 'placeholder'=>'Razão Social']) !!}
                    @if ($errors->has('razao_social'))
                        <span class="text-xs text-red-600 ">
                        <strong>{{ $errors->first('razao_social') }}</strong>
                        </span>
                    @endif
                </label>
            </div>
            <form id="delete-form" method="POST" action="{{ route('admin.empresas.destroy', $empresa->id) }}">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}

                        <div class="form-group">
                        <input type="submit" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-red-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150 sm:text-sm sm:leading-5" value="Remover Registro">
                        </div>
                </form>
            </span>
        </div>
    </div>
</x-area-admin-layout>
