@if (session()->has('message'))
    <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert">
        <div class="flex">
            <div>
                <p class="text-sm">{!! session('message') !!}</p>
            </div>
        </div>
    </div>
@endif

@if (session()->has('danger'))
    <div class="bg-red-200 border-t-4 border-red-500 rounded-b text-red-800 px-4 py-3 shadow-md my-3" role="alert">
        <div class="flex">
            <div>
                <p class="text-sm">{!! session('danger') !!}</p>
            </div>
        </div>
    </div>
@endif

@if (session()->has('info'))
    <div class="bg-blue-200 border-t-4 border-blue-500 rounded-b text-blue-800 px-4 py-3 shadow-md my-3" role="alert">
        <div class="flex">
            <div>
                <p class="text-sm">{!! session('info') !!}</p>
            </div>
        </div>
    </div>
@endif

@if (session()->has('success'))
    <div class="bg-green-200 border-t-4 border-green-500 rounded-b text-green-800 px-4 py-3 shadow-md my-3" role="alert">
        <div class="flex">
            <div>
                <p class="text-sm">{!! session('success') !!}</p>
            </div>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="bg-red-200 border-t-4 border-red-500 rounded-b text-red-800 px-4 py-3 shadow-md my-3" role="alert">
       <div class="flex">
            @foreach ($errors->all() as $erro)
                 <div>
                    <p class="text-sm">{{ $erro }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endif