<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];

        // Add specific rules based on report type
        switch ($this->getReportType()) {
            case 'cash_flow':
                $rules = array_merge($rules, [
                    'dibuat' => 'required|string|max:255',
                    'alamat' => 'nullable|string|max:255',
                    'tanggal' => 'nullable|date',
                    'jabatan' => 'nullable|string|max:255',
                    'jumlahLaman' => 'nullable|integer|min:1',
                ]);
                break;

            case 'balance_sheet':
                $rules = array_merge($rules, [
                    'excel' => 'nullable|boolean',
                    'dibuat' => 'nullable|string|max:255',
                    'alamat' => 'nullable|string|max:255',
                    'tanggal' => 'nullable|date',
                    'jabatan' => 'nullable|string|max:255',
                    'jumlahLaman' => 'nullable|integer|min:1',
                ]);
                break;

            case 'profit_loss':
                $rules = array_merge($rules, [
                    'text_input1' => 'nullable|string|max:255',
                    'text_input2' => 'nullable|string|max:255',
                ]);
                break;

            case 'trial_balance':
                $rules = array_merge($rules, [
                    'dibuat' => 'nullable|string|max:255',
                    'alamat' => 'nullable|string|max:255',
                    'tanggal' => 'nullable|date',
                    'jabatan' => 'nullable|string|max:255',
                    'jumlahLaman' => 'nullable|integer|min:1',
                ]);
                break;

            case 'general_ledger':
                $rules = array_merge($rules, [
                    'akun' => 'nullable|string',
                ]);
                break;
        }

        return $rules;
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'start_date.date' => 'Format tanggal mulai tidak valid.',
            'end_date.date' => 'Format tanggal selesai tidak valid.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'dibuat.required' => 'Field dibuat harus diisi.',
            'dibuat.max' => 'Field dibuat maksimal 255 karakter.',
            'alamat.max' => 'Field alamat maksimal 255 karakter.',
            'jabatan.max' => 'Field jabatan maksimal 255 karakter.',
            'jumlahLaman.integer' => 'Jumlah laman harus berupa angka.',
            'jumlahLaman.min' => 'Jumlah laman minimal 1.',
        ];
    }

    /**
     * Get the report type from the request
     */
    private function getReportType(): string
    {
        $route = $this->route()->getName();

        if (str_contains($route, 'aruskas')) {
            return 'cash_flow';
        } elseif (str_contains($route, 'neraca')) {
            return 'balance_sheet';
        } elseif (str_contains($route, 'labarugi')) {
            return 'profit_loss';
        } elseif (str_contains($route, 'neracasaldo')) {
            return 'trial_balance';
        } elseif (str_contains($route, 'bukubesar')) {
            return 'general_ledger';
        }

        return 'default';
    }

    /**
     * Prepare data for validation
     */
    protected function prepareForValidation()
    {
        // Convert date formats if needed
        if ($this->has('start_date') && !empty($this->start_date)) {
            try {
                $this->merge([
                    'start_date' => $this->convertDateFormat($this->start_date)
                ]);
            } catch (\Exception $e) {
                // Keep original format if conversion fails
            }
        }

        if ($this->has('end_date') && !empty($this->end_date)) {
            try {
                $this->merge([
                    'end_date' => $this->convertDateFormat($this->end_date)
                ]);
            } catch (\Exception $e) {
                // Keep original format if conversion fails
            }
        }
    }

    /**
     * Convert date format from d-m-Y to Y-m-d
     */
    private function convertDateFormat($date)
    {
        // If already in Y-m-d format, return as is
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        // Convert from d-m-Y to Y-m-d
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
            $parts = explode('-', $date);
            return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        }

        return $date;
    }
}
