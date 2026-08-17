<label class="block text-sm w-10/12" id="cidade_id">
    <span class="text-gray-700 ">Cidade:</span>
    {!! Form::select('cidade_id', $cidades, null, ['id' => 'cidade_id','maxlength' => '255','required','class'=>'block w-full mt-1 text-sm  
    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
    :shadow-outline-gray form-input', 'placeholder'=>'', 'id'=>'city']) !!}
    @if ($errors->has('cidade_id'))
        <span class="text-xs text-red-600 ">
        <strong>{{ $errors->first('cidade_id') }}</strong>
    </span>
    @endif
</label>