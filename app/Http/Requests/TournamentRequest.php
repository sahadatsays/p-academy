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
            "date_debut" => "required|date",
            "heure_debut_submit" => "required",
            "date_fin" => "required|date",
            "heure_fin_submit" => "required"
        ];
    }

    public function messages()
    {
        return [
            "titre.required" => "Champ requis",
            "operateur.required" => "Champ requis",
            "typetournoi.required" => "Champ requis",
            "buyin.required" => "Champ requis",
            "password.required" => "Champ requis",
            "added_op.required" => "Champ requis",
            "article_id.required" => "Champ requis",
            "date_debut.required" => "Champ requis",
            "heure_debut_submit.required" => "Champ requis",
            "date_fin.required" => "Champ requis",
            "heure_fin_submit.required" => "Champ requis"
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'date_debut' => $this->date_debut_submit . ' ' . $this->heure_debut_submit,
            'date_fin' => $this->date_fin_submit . ' ' . $this->heure_fin_submit
        ]);
    }
}
