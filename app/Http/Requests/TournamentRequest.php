<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TournamentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "titre" => "required",
            "operateur" => "required|exists:App\Models\Operator,id",
            "typetournoi" => "required|in:Freeroll,Freezeout,Rebuy",
            "buyin" => "required|numeric",
            "password" => "required",
            "added_op" => "required|numeric",
            "article_id" => "required|exists:App\Models\Article,id",
            "date_debut" => "required",
            "date_fin" => "required"
        ];
    }
}
