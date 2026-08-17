<label class="block text-sm">
    <span class="text-gray-700 ">NBS:</span>
    {!! Form::select('nbs_id', $nbs_list
    ,null, [empty($nbs_list) ? '' : 'required','class'=>'block w-full mt-1 text-sm  
    form-select
    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray', 'id' => 'nbs_id']) !!}
    @if ($errors->has('nbs_id'))
        <span class="text-xs text-red-600 ">
        <strong>{{ $errors->first('nbs_id') }}</strong>
    </span>
    @endif
</label>