<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $except = $this->method() == 'PUT';
        return [
            "first_name" => "required|string|min:2|max:50",
            "last_name" => "required|string|min:2|max:50",
            "age" => "required|integer|min:18|max:35",
            "email" => "required|email|unique:users,email" . ($except ? ',' . $this->route('user')->id : ''),
            "cin" => "required|string|min:8|max:8|unique:users,cin" . ($except ? ',' . $this->route('user')->id : ''),
            "phone" => "required|string|min:10|max:12|unique:users,phone" . ($except ? ',' . $this->route('user')->id : ''),
            "password" => $except ? "nullable" : "required|string|min:8|max:50",
            "role" => "required|string|in:STUDENT,TEACHER,ADMIN"
        ];
    }

    public function messages(): array
    {
        return [
            "first_name.required" => "First name is required.",
            "first_name.string" => "First name must be a string.",
            "first_name.min" => "First name must be at least 2 characters.",
            "first_name.max" => "First name must not exceed 50 characters.",
            "last_name.required" => "Last name is required.",
            "last_name.string" => "Last name must be a string.",
            "last_name.min" => "Last name must be at least 2 characters.",
            "last_name.max" => "Last name must not exceed 50 characters.",
            "age.required" => "Age is required.",
            "age.integer" => "Age must be an integer.",
            "age.min" => "Age must be at least 18 years old.",
            "age.max" => "Age must not exceed 35 years old.",
            "email.required" => "Email is required.",
            "email.email" => "Email must be a valid email address.",
            "email.unique" => "Email already exists.",
            "cin.required" => "CIN is required.",
            "cin.string" => "CIN must be a string.",
            "cin.min" => "CIN must be at least 8 characters.",
            "cin.max" => "CIN must not exceed 8 characters.",
            "cin.unique" => "CIN already exists.",
            "phone.required" => "Phone number is required.",
            "phone.string" => "Phone number must be a string.",
            "phone.min" => "Phone number must be at least 8 characters.",
            "phone.max" => "Phone number must not exceed 8 characters.",
            "phone.unique" => "Phone number already exists.",
            "password.required" => "Password is required.",
            "password.string" => "Password must be a string.",
            "password.min" => "Password must be at least 8 characters.",
            "password.max" => "Password must not exceed 50 characters.",
            "role.required" => "Role is required.",
            "role.string" => "Role must be a string.",
            "role.in" => "Role must be either 'STUDENT' or 'TEACHER'.",
        ];
    }
}
