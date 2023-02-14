@extends('layouts.app')

@section('content')

    <form method="post" action="{{ ('create' === $type) ? route('task.store') : route('task.update', $task->id)  }}" role="form">
        @csrf
        
        @if ('edit' === $type)
            <input type="hidden" name="task_id" value="{{ $task->id }}">
        @endif

        <div class="mb-3">
            <label for="title" class="form-label">{{ __('Title') }}</label>
            <input
                type="text"
                class="form-control @if($errors->has('title') || $errors->has('slug')) is-invalid @endif"
                id="title"
                name="title"
                placeholder="Name of task"
                value="{{ old('title') !== NULL ? old('title') : (isset($task->title) ? $task->title : '') }}"
                autofocus
            >
            @if ($errors->has('title') || $errors->has('slug'))
                <div class="invalid-feedback">
                    {{ $errors->first('title') }}
                    {{ $errors->first('slug') }}
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label w-100">{{ __('Category') }}</label>
            <select name="category_id" id="category_id">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">{{ __('Description') }}</label>
            <textarea
                type="text"
                class="form-control"
                id="description"
                name="description"
                placeholder="Lorem ispum..."
            >{{ old('description') !== NULL ? old('description') : (isset($task->description) ? $task->description : '') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>

        <a class="text-muted ms-3" href="{{ route('task.index') }}">{{ __('Back') }}</a>

    </form>

@endsection