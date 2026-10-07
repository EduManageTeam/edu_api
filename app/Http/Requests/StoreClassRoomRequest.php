<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'grade' => 'required|string|max:50',
            'room' => 'required|string|max:50',
            'teacher_id' => ['nullable', 'integer', 'exists:teachers,id'],
        ];
    }
}
