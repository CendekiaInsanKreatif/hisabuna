<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CoaRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $rules = [
            'nomor_akun' => [
                'required',
                'regex:/^[0-9\-]+$/',
                Rule::unique('coas', 'nomor_akun')
                    ->where('created_by', auth()->id())
                    ->ignore($this->route('coa'))
            ],
            'nama_akun' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9 ]+$/'
            ],
        ];

        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'nomor_akun.required' => 'Nomor akun wajib diisi.',
            'nomor_akun.regex' => 'Nomor akun hanya boleh berisi angka dan tanda hubung.',
            'nomor_akun.unique' => 'Nomor akun sudah ada.',
            'nama_akun.required' => 'Nama akun wajib diisi.',
            'nama_akun.regex' => 'Nama akun hanya boleh berisi huruf dan spasi.',
            'nama_akun.max' => 'Nama akun maksimal 255 karakter.',
        ];
    }
}
