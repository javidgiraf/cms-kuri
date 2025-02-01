<?php

namespace App\Http\Requests;

use App\Models\SchemeSetting;
use App\Models\SchemeType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserCreatePostRequest extends FormRequest
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
        $rules = [
            'name' => ['required'],
            'email' => ['nullable', 'email', Rule::unique('users', 'email')],
            'referrel_code' => ['required', Rule::unique('customers', 'referrel_code')],
            'mobile' => ['required', 'min:10', 'max:15', 'regex:/^[6-9]\d{9}$/', Rule::unique('customers', 'mobile')],
            'scheme_id' => ['required', Rule::exists('schemes','id')],
            'start_date' => ['required'],
            'subscribe_amount' => ['nullable'],
            'password' => ['required', 'string', 'min:6', 'confirmed']
        ];

        $schemeSetting = SchemeSetting::where('scheme_id', SchemeType::FIXED_PLAN)->first();

        if (request('schemeTypeId') == SchemeType::FIXED_PLAN) {

            $minAmount = (int) $schemeSetting->min_payable_amount;
            $maxAmount = (int) $schemeSetting->max_payable_amount;


            $rules['subscribe_amount'] = [
                'required',
                'numeric',
                "min:{$minAmount}",
                "max:{$maxAmount}"
            ];
        }

        return $rules;
    }
}
