<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function update($user, array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'image', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');


        // salva em storage/app/public/fotos
        //$path = $input['photo']->store('fotos', 'public');
        /*$nomeArquivo = $user->id . '.' . $input['photo']->getClientOriginalExtension();
        $path = $input['photo']->storeAs(
            'fotos',
            $nomeArquivo,
            'public'
        );

        // Exemplo: salvar no banco
        auth()->user()->update([
            'foto' => $path
        ]);*/

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
            $nomeArquivo = 'profile-photos/'.$user->id . '.' . $input['photo']->getClientOriginalExtension();
            //$hasname = $input['photo']->hashName()
            $disk = 'public';
            $antigo = 'profile-photos/'.$input['photo']->hashName();
            $novo   = $nomeArquivo;

            if (Storage::disk($disk)->exists($antigo)) {
                Storage::disk($disk)->delete($novo);
                Storage::disk($disk)->move($antigo, $novo);
            }
            
            auth()->user()->update([
                'profile_photo_path' => $novo
            ]);
        }

        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
        ])->save();
    }
}
