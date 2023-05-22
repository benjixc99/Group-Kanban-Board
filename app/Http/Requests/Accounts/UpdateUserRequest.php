<?php

namespace App\Http\Requests\Accounts;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('change_user_type');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {

        $user = $this->route('user');

        return [
                'name' => ['required', 'string', 'max:255'],
                'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
                'password'   => ['nullable', 'string', 'min:8', 'confirmed'],
                'role_id'   => ['required', 'string', 'exists:roles,id'],
        ];
    }
}
