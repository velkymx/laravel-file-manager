@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Group for {{ $user->name }}</h1>

    <form action="{{ route('users.updateGroup', $user->id) }}" method="POST">
        @csrf
        @method('POST')
        <div class="mb-3">
            <label for="group_id" class="form-label">Group</label>
            <select name="group_id" id="group_id" class="form-control">
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" {{ $user->group_id == $group->id ? 'selected' : '' }}>
                        {{ $group->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
