@extends('layouts.app')

@section('content')
    <a class="btn btn-primary" href="{{ route('task.index') }}">{{ __('Tasks') }}</a>
@endsection
