<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    private $task;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(Task $task)
    {
        $id = request()->input('task_id');
        $task = $this->task = $task->findOrFail($id);
        return $this->user()->can('update-task', $task);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(Task $task)
    {
        return [
            'title' => [
                'min:3',
                'required',
                Rule::unique('tasks')->ignore($this->task['title'], 'title'),
            ],
        ];
    }
}
