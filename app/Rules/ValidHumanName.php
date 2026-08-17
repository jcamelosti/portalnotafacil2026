<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidHumanName implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Remove espaços duplicados
        $value = trim(preg_replace('/\s+/', ' ', $value));

        // Proibir URL, spam, símbolos repetidos e números
        if (preg_match('/https?:\/\/|www\./i', $value)) return false;
        if (preg_match('/[*$#@%^&+=~`<>]/', $value)) return false;
        if (preg_match('/\d/', $value)) return false;

        // Aceitar somente letras + espaços
        return preg_match('/^[A-Za-zÀ-ÿ]+( [A-Za-zÀ-ÿ]+)*$/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O campo :attribute deve conter um nome real.';
    }
}
