<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $route = $this->route()->getName();

        $rule = [
            'caption' => 'required|string|max:255',
            'info' => 'max:255'
        ];

        $route = $this->route()->getName();
        if($route === 'articles.store'){
            $rule['file'] = 'required|file|attachment';
        }
        return $rule;
    }
}
