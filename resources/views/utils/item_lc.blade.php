<label class="block text-sm">
    <span class="text-gray-700 ">Item da LC 116/2003:</span>
    {!! Form::select('item_lc_id', $servicos
    ,null, [empty($servicos) ? '' : 'required','class'=>'block w-full mt-1 text-sm  
    
    form-select
    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray', 'id' => 'item_lc_id']) !!}
    @if ($errors->has('item_lc_id'))
        <span class="text-xs text-red-600 ">
        <strong>{{ $errors->first('item_lc_id') }}</strong>
    </span>
    @endif
</label>