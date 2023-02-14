<div class="modal fade" id="task-id-{{ $task->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('task.share') }}" method="post">
                @csrf


                {{-- @dump($user->shared()->where(['task_id' => 5])->get()) --}}
                {{-- @dump($task->shared()->where(['task_id' => 5])->get()) --}}
                {{-- @foreach($task->shared()->get() as $item)
                    @dump($item->id)
                @endforeach --}}

                
        
                <input type="hidden" name="task_id" value="{{ $task->id }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Share tasks') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <dl>
                        @foreach ($users as $user)
                            <dd class="user-select-none">
                                <label role="button">
                                    <input value="{{ $user->id }}" name="users[]" type="checkbox" @if ($task->shared()->where(['task_id' => $task->id])->get()->contains($user->id)) @checked(true) @endif>
                                    <span>{{ $user->name }}</span>
                                </label>
                            </dd>
                        @endforeach
                    </dl>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>  