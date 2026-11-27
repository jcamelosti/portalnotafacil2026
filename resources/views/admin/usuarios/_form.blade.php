@php
    $editando = $user->exists;
    $inputClass = 'block w-full mt-1 text-sm form-input rounded border-gray-300 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple';
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <label class="block text-sm">
        <span class="text-gray-700">Nome: <span class="text-red-600">*</span></span>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required maxlength="255"
               class="{{ $inputClass }}">
        @error('name')
            <span class="text-xs text-red-600"><strong>{{ $message }}</strong></span>
        @enderror
    </label>

    <label class="block text-sm">
        <span class="text-gray-700">E-mail: <span class="text-red-600">*</span></span>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="255"
               class="{{ $inputClass }}">
        @error('email')
            <span class="text-xs text-red-600"><strong>{{ $message }}</strong></span>
        @enderror
    </label>

    <label class="block text-sm">
        <span class="text-gray-700">
            Senha: @unless($editando)<span class="text-red-600">*</span>@endunless
        </span>
        <input type="password" name="password" autocomplete="new-password" @unless($editando) required @endunless
               class="{{ $inputClass }}">
        @if($editando)
            <span class="text-xs text-gray-500">Deixe em branco para manter a senha atual.</span>
        @endif
        @error('password')
            <span class="block text-xs text-red-600"><strong>{{ $message }}</strong></span>
        @enderror
    </label>

    <label class="block text-sm">
        <span class="text-gray-700">
            Confirmar senha: @unless($editando)<span class="text-red-600">*</span>@endunless
        </span>
        <input type="password" name="password_confirmation" autocomplete="new-password" @unless($editando) required @endunless
               class="{{ $inputClass }}">
    </label>
</div>

<div class="flex flex-col md:flex-row gap-6 mt-6">
    <label class="inline-flex items-center text-sm">
        <input type="hidden" name="is_admin" value="0">
        <input type="checkbox" name="is_admin" value="1" class="form-checkbox text-purple-600"
               {{ old('is_admin', $user->is_admin) == '1' ? 'checked' : '' }}>
        <span class="ml-2 text-gray-700">Administrador (acesso à área administrativa)</span>
    </label>
    @error('is_admin')
        <span class="text-xs text-red-600"><strong>{{ $message }}</strong></span>
    @enderror

    <label class="inline-flex items-center text-sm">
        <input type="hidden" name="email_verificado" value="0">
        <input type="checkbox" name="email_verificado" value="1" class="form-checkbox text-purple-600"
               {{ old('email_verificado', $editando ? !is_null($user->email_verified_at) : true) ? 'checked' : '' }}>
        <span class="ml-2 text-gray-700">
            E-mail verificado
            @if($editando && $user->email_verified_at)
                <span class="text-xs text-gray-500">(em {{ $user->email_verified_at->format('d/m/Y H:i') }})</span>
            @endif
        </span>
    </label>
</div>

<div class="flex flex-wrap gap-2 justify-end bg-gray-50 px-4 py-3 mt-6 -mx-5 -mb-5 rounded-b-lg">
    <a href="{{ route('admin.usuarios.index') }}"
       class="px-4 py-2 border border-gray-300 bg-white text-sm rounded hover:text-gray-500">
        Cancelar
    </a>
    <button type="submit"
            class="px-4 py-2 rounded-md border border-transparent bg-green-600 text-sm font-medium text-white shadow-sm hover:bg-green-500">
        {{ $editando ? 'Salvar alterações' : 'Cadastrar usuário' }}
    </button>
</div>
