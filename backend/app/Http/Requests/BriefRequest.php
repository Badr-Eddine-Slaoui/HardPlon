<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BriefRequest extends FormRequest
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
            "sprint_id" => "required|exists:sprints,id",
            "class_group_id" => "required|exists:class_groups,id",
            "stack_id" => "required|exists:stacks,id",
            "title" => "required|string|min:2|max:50",
            "description" => "required|string|min:2|max:255",
            "content" => "required|string|min:2",
            "is_collective" => "required|boolean",
            "start_date" => "required|date|after_or_equal:today",
            "end_date" => "required|date|after:start_date",
            "skill_levels" => "required|array|min:1",
            "skill_levels.*.level_id" => "required|exists:levels,id",
            "skill_levels.*.skill_id" => "required|exists:skills,id",
        ];
    }

    public function messages(): array
    {
        return [
            "sprint_id.required" => "Sprint is required.",
            "sprint_id.exists" => "Sprint does not exist.",
            "class_group_id.required" => "Class group is required.",
            "class_group_id.exists" => "Class group does not exist.",
            "stack_id.required" => "Stack is required.",
            "stack_id.exists" => "Stack does not exist.",
            "title.required" => "Title is required.",
            "title.string" => "Title must be a string.",
            "title.min" => "Title must be at least 2 characters.",
            "title.max" => "Title must not exceed 50 characters.",
            "description.required" => "Description is required.",
            "description.string" => "Description must be a string.",
            "description.min" => "Description must be at least 2 characters.",
            "description.max" => "Description must not exceed 255 characters.",
            "content.required" => "Content is required.",
            "content.string" => "Content must be a string.",
            "content.min" => "Content must be at least 2 characters.",
            "is_collective.required" => "Is collective is required.",
            "is_collective.boolean" => "Is collective must be a boolean.",
            "start_date.required" => "Start date is required.",
            "start_date.date" => "Start date must be a date.",
            "start_date.after_or_equal" => "Start date must be after or equal to today.",
            "end_date.required" => "End date is required.",
            "end_date.date" => "End date must be a date.",
            "end_date.after" => "End date must be after start date.",
            "skill_levels.required" => "Skill levels are required.",
            "skill_levels.array" => "Skill levels must be an array.",
            "skill_levels.*.level_id.required" => "Level is required.",
            "skill_levels.*.level_id.exists" => "Level does not exist.",
            "skill_levels.*.skill_id.required" => "Skill is required.",
            "skill_levels.*.skill_id.exists" => "Skill does not exist.",
        ];
    }
}
