<div class="py-4 text-gray-500 ">
    <a class="ml-6 text-lg font-bold text-gray-800 " href="#">
        {{ config('app.name') }}
    </a>
    <ul class="mt-6">
        <li class="relative px-6 py-3">
            {!! request()->routeIs('dashboard') ? '<span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>' : '' !!}
            <a data-turbolinks-action="replace" class="inline-flex items-center w-full text-sm font-semibold text-gray-800 transition-colors duration-150 hover:text-gray-800 :text-gray-200 " href="{{route('dashboard')}}">
                <svg class="w-5 h-5" ari a-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                <span class="ml-4">Início</span>
            </a>
        </li>

        <li class="relative px-6 py-3">
            {!! request()->routeIs('empresas-certificados.index') ? '<span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>' : '' !!}
            <a data-turbolinks-action="replace" class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 :text-gray-200" href="{{route('empresas-certificados.index')}}">
                <svg class="w-5 h-5"
                    aria-hidden="true"
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path d="M7 3h7l5 5v6"></path>
                    <path d="M14 3v5h5"></path>
                    <path d="M7 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h5"></path>
                    <path d="M9 12h6"></path>
                    <path d="M9 16h3"></path>
                    <circle cx="17" cy="17" r="4"></circle>
                    <path d="m15.5 17 1 1 2-2"></path>

                </svg>
                <span class="ml-4"><b>C</b>ertificados Digitais</span>
            </a>
        </li>
        <li class="relative px-6 py-3">
            {!! request()->routeIs('empresas.index') ? '<span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>' : '' !!}
            <a data-turbolinks-action="replace" class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 :text-gray-200" href="{{route('empresas.index')}}">
                <svg class="w-5 h-5"
                    aria-hidden="true"
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path d="M3 21h18"></path>
                    <path d="M5 21V5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v16"></path>
                    <path d="M15 9h2a2 2 0 0 1 2 2v10"></path>
                    <path d="M8 7h2"></path>
                    <path d="M8 11h2"></path>
                    <path d="M8 15h2"></path>
                    <path d="M16 13h1"></path>
                    <path d="M16 17h1"></path>
                </svg>
                <span class="ml-4"><b>E</b>mpresas</span>
            </a>
        </li>
        
        <li class="relative px-6 py-3">
            {!! request()->routeIs('empresas.list-solicitar-acesso-empresa') ? '<span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>' : '' !!}
            <a data-turbolinks-action="replace" class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 :text-gray-200" href="{{route('empresas.list-solicitar-acesso-empresa')}}">
                <svg class="w-5 h-5"
                    aria-hidden="true"
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path d="M3 21h18"></path>
                    <path d="M5 21V7l7-4v18"></path>
                    <path d="M12 10h7v11"></path>
                    <path d="M8 9v.01"></path>
                    <path d="M8 13v.01"></path>
                    <path d="M8 17v.01"></path>
                    <path d="M16 14v.01"></path>
                    <path d="M16 17v.01"></path>
                </svg>
                <span class="ml-4"><b>E</b>mpresas Compartilhadas</span>
            </a>
        </li>

        <!--li class="relative px-6 py-3">
            {!! request()->routeIs('faturas.index') ? '<span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>' : '' !!}
            <a data-turbolinks-action="replace" class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 :text-gray-200" href="{{route('faturas.index')}}">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                    </path>
                </svg>
                <span class="ml-4"><b>F</b>aturas/Notas Fiscais</span>
            </a>
        </li-->
        
        <li class="relative px-6 py-3">
            {!! request()->routeIs('empresas.selecionar-empresa') ? '<span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>' : '' !!}
            <a title="Notas Fiscais de Serviço Emitidas pelo Portal Nota Fácil para seu CNPJ" data-turbolinks-action="replace" class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 :text-gray-200" href="{{route('empresas.selecionar-empresa')}}">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                    </path>
                </svg>
                <span class="ml-4"><b>E</b>missor</span>
            </a>
        </li>

        @if(Auth::user()->can_insert_credit == 1)
        <li class="relative px-6 py-3">
            {!! request()->routeIs('creditos.emitir') ? '<span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>' : '' !!}
            <a data-turbolinks-action="replace" class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 :text-gray-200" href="{{route('creditos.emitir')}}">
                <svg class="w-5 h-5"
                    aria-hidden="true"
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <rect x="3" y="6" width="18" height="15" rx="2"></rect>
                    <path d="M3 10h18"></path>
                    <path d="M7 6V4a2 2 0 0 1 2-2h9"></path>
                    <path d="M16 15h.01"></path>
                </svg>
                <span class="ml-4">Inserir Créditos</span>
            </a>
        </li>
        @endif

        <li class="relative px-6 py-3">
            {!! request()->routeIs('licenca.index') ? '<span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>' : '' !!}
            <a title="Consulta Licenças Vencidas/A Vencer" data-turbolinks-action="replace" class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 :text-gray-200" href="{{route('licenca.index')}}">
               <svg class="w-5 h-5"
                    aria-hidden="true"
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path d="M7 3h7l5 5v5"></path>
                    <path d="M14 3v5h5"></path>
                    <path d="M19 13v-5"></path>
                    <path d="M5 3h9l5 5v3"></path>
                    <path d="M5 3v18h7"></path>
                    <circle cx="17" cy="17" r="4"></circle>
                    <path d="m15.5 17 1 1 2-2"></path>
                </svg>
                <span class="ml-4">Licenças de Uso</span>
            </a>
        </li>    

        <li class="relative px-6 py-3">
            {!! request()->routeIs('servicos-tomados.index') ? '<span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>' : '' !!}
            <a title="Consulta de Notas Fiscais de Serviços Tomados ou Prestados no Ambiente de Dados(ADN)" data-turbolinks-action="replace" class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 :text-gray-200" href="{{route('servicos-tomados.index')}}">
               <svg class="w-5 h-5"
                    aria-hidden="true"
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <path d="M14 2v6h6"></path>
                    <path d="m10 13-2 2 2 2"></path>
                    <path d="m14 13 2 2-2 2"></path>
                </svg>
                <span class="ml-4">Serviços Tomados/Prestados</span>
            </a>
        </li>
        
        @if(Auth::user()->is_admin == 1)
        <li class="relative px-6 py-3">
            {!! request()->routeIs('admin.empresas.index') ? '<span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>' : '' !!}
            <a data-turbolinks-action="replace" class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 :text-gray-200" href="{{route('admin.empresas.index')}}">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                    </path>
                </svg>
                <span class="ml-4">Painel Administrativo</span>
            </a>
        </li>
        @endif
    </ul>
</div>
