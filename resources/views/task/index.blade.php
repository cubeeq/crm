@extends('layouts.app')

@section('content')
    <div class="task-list">

        <div class="task-filter">
            <form action="{{ route('task.index') }}" method="post">
                @csrf
                <div class="d-flex justify-content-end mb-3">
                    <select name="task-category" id="task-category" class="form-select me-3" onchange="this.form.submit()">
                        <option value="0" disabled="disabled">{{ __('Choose category') }}</option>
                        <option value="0" {{0 === $categoryId  ? 'selected' : ''}}>{{ __('All') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{$category->id == $categoryId  ? 'selected' : ''}}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <select name="task-attributes" id="task-attributes" class="form-select" onchange="this.form.submit()">
                        <option value="0" disabled="disabled">{{ __('Choose attribute') }}</option>
                        <option value="0" {{0 === $attributes  ? 'selected' : ''}}>{{ __('All') }}</option>
                        <option value="finished" {{'finished' === $attributes  ? 'selected' : ''}}>{{ __('Finished') }}</option>
                        <option value="unfinished" {{'unfinished' === $attributes  ? 'selected' : ''}}>{{ __('Unfinished') }}</option>
                        <option value="my-tasks" {{'my-tasks' === $attributes  ? 'selected' : ''}}>{{ __('My tasks') }}</option>
                        <option value="my-tasks&finished" {{'my-tasks&finished' === $attributes  ? 'selected' : ''}}>{{ __('My tasks - Finished') }}</option>
                        <option value="my-tasks&unfinished" {{'my-tasks&unfinished' === $attributes  ? 'selected' : ''}}>{{ __('My tasks - Unfinished') }}</option>
                        <option value="shared" {{'shared' === $attributes  ? 'selected' : ''}}>{{ __('Shared') }}</option>
                        <option value="shared&finished" {{'shared&finished' === $attributes  ? 'selected' : ''}}>{{ __('Shared - Finished') }}</option>
                        <option value="shared&unfinished" {{'shared&unfinished' === $attributes  ? 'selected' : ''}}>{{ __('Shared - Unfinished') }}</option>
                    </select>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div class="alert d-flex justify-content-between align-items-center fade show js-autoHideAlert @if(session('id')) important alert-warning @else alert-success @endif" role="alert">
                {{ session('success') }}
                @if(session('id'))
                    <a class="text-reset" href="{{ url('/task/restore/'.session('id')) }}">{{ __('Restore') }}</a>
                @endif
            </div>
        @endif

        <ul class="list-group mb-3">
            @foreach ($tasks as $task)
                <li class="list-group-item d-flex {{ 'category-'.Str::lower($task->category->name) }}" id="task-{{ $task->id }}">
                    <div>
                        <input type="checkbox" class="me-2 js-taskChangeStatus" @if($task->done) checked="checked" @endif>
                    </div>
                    <div>
                        <div class="d-flex align-items-center">
                            <div class="point me-1 rounded-circle"></div>
                            <b>{{ $task->title }}</b>
                            <small class="text-muted ms-1">
                                ({{ __('Autor') }}:
                                {{ isset($task->user) ? $task->user->name : '' }})
                            </small>
                        </div>
                        <div><i class="text-muted">{{ $task->description }}</i></div>
                    </div>
                    <div class="controls text-end ps-2 ms-auto">
                        <span role="button" data-bs-toggle="modal" data-bs-target="#task-id-{{ $task->id }}">{{__('Share')}}</span>
                        &middot;
                        <a class="text-decoration-none text-reset" href="{{ route('task.edit', $task->id) }}">{{ __('Edit') }}</a>
                        &middot;
                        <a
                            class="text-decoration-none text-danger"
                            href="{{ route('task.delete', $task->id) }}"
                            onclick="return confirm('{{ __ ('Really?') }}')"
                        >{{ __('Delete') }}</a>
                    </div>
                </li>
            @endforeach
        </ul>

        <a href="{{ route('task.create') }}" class="btn btn-success">{{ __('Create task') }}</a>

        @foreach ($tasks as $task)            
            @include('inc.task.modal', [
                'task' => $task,
                'users' => $users,
            ])
        @endforeach

    </div>
@endsection