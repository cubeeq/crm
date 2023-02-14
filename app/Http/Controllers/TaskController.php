<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Contracts\Database\Query\Builder;

class TaskController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Task $task, Request $request)
    {
        if ($request->isMethod('post')) {
            $category = $request->input('task-category');
            $attributes = $request->input('task-attributes') ? explode('&', $request->input('task-attributes')) : [];
            $tasks = $task
                ->where(function(Builder $query) use ($attributes) {
                    if (in_array('finished', $attributes)) {
                        $query
                            ->where('user_id', Auth::id())
                            ->where('done', true)
                            ->orWhereHas('shared', function($query) {
                                $query
                                    ->where('user_id', Auth::id())
                                    ->where('done', true);
                            });
                    }
                })
                ->where(function(Builder $query) use ($attributes) {
                    if (in_array('unfinished', $attributes)) {
                        $query
                            ->where('user_id', Auth::id())
                            ->where('done', false)
                            ->orWhereHas('shared', function($query) {
                                $query
                                    ->where('user_id', Auth::id())
                                    ->where('done', false);
                            });
                    }
                })
                ->where(function(Builder $query) use ($attributes) {
                    if (in_array('my-tasks', $attributes)) $query->where('user_id', Auth::id());
                })
                ->where(function(Builder $query) use ($attributes) {
                    if (in_array('shared', $attributes)) $query->whereHas('shared', function($q) {
                            $q->where('user_id', Auth::id());
                        });
                })
                ->where(function(Builder $query) use ($category) {
                    if (isset($category) && !empty($category)) $query->where([
                        ['category_id', $category],
                    ]);
                })
                ;
        } else {
            $tasks = $task->where('user_id', Auth::id());
        }

        $tasks = $tasks->orderBy('created_at', 'desc')->paginate(10);

        return view('task.index', [
            'title' => __('List of tasks'),
            'tasks' => $tasks,
            'categories' => Category::all(),
            'users' => User::all(),
            'categoryId' => isset($category) ? $category : null,
            'attributes' => isset($attributes) ? implode('&', $attributes) : null,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('task.form', [
            'title' => __('Create task'),
            'type' => 'create',
            'categories' => Category::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $request)
    {
        $request['slug'] = Str::slug($request->title);
        $request['user_id'] = Auth::id();

        Task::create($request->all());

        notify(__('Task').' '.$request->title.' '.__('has been created.'));

        return redirect()
            ->route('task.index')
            ->withSuccess(__('The action was successfully processed!'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task, $id)
    {
        $task = $task->findOrFail($id);
        return view('task.form', [
            'title' => __('Edit task'),
            'type' => 'edit',
            'task' => $task,
            'categories' => Category::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTaskRequest $request, Task $task, $id)
    {
        $task = $task->findOrFail($id);
        $request['slug'] = Str::slug($request->title);
        $task->update($request->all());

        notify(__('Task').' '.$request->title.' '.__('has been edited.'));

        return redirect()
            ->route('task.index')
            ->withSuccess(__('The action was successfully processed!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task, $id)
    {
        $task = $task->findOrFail($id);

        if (!Gate::allows('destroy-task', $task)) abort(403);

        $task->delete();

        notify(__('Task').' '.$task->title.' '.__('has been deleted.'));

        return redirect()
            ->route('task.index')
            ->with([
                'success' => __('The action was successfully processed!'),
                'id' => $id,
            ]);
    }

    /**
     * Restore task data
     */
    public function restore(Task $task, $id) 
    {
        $task = $task->onlyTrashed()->findOrFail($id);
        $task->restore();

        notify(__('Task').' '.$task->title.' '.__('has been restored.'));

        return redirect()
            ->route('task.index')
            ->withSuccess(__('The action was successfully processed!'));
    }

    /**
     *  Task share
     */
    public function share(Request $request, Task $task)
    {
        $task = $task->findOrFail($request->input('task_id'));

        if (!Gate::allows('share-task', $task)) abort(403);

        $users = $request->input('users');

        $task->shared()->sync($users);

        if ($users) {
            foreach ($users as $user) {
                notify(__('Task').' '.$task->title.' '.__('has been published.'), User::find($user));
            }
            notify(__('Task').' '.$task->title.' '.__('has been published.'));
        }

        return redirect()
            ->route('task.index')
            ->withSuccess(__('The action was successfully processed!'));
    }

    /**
     * Change task status
     */
    public function changeStatus(Request $request, Task $task)
    {
        $task = $task->findOrFail($request->params['id']);
        if ($task->update(['done' => !$task->done])) {
            $task->done
                ? notify(__('Task').' '.$task->title.' '.__('has been completed.'))
                : notify(__('Task').' '.$task->title.' '.__('has been marked as incomplete.'));
            return true;
        }
        return false;
    }

}
