<form id="createUser" method="POST" class="ajax-form" action="{{route('staff.update', $staff)}}">
    @csrf
    @method('PUT')
    <div class="form-group">
        <x-adminlte-select2 name="roles[]" id="roles" label="Select Role " class="select2" multiple>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" {{ $staff->user->roles->contains($role->id) ? 'selected' : '' }}>{{ $role->name }}</option>
            @endforeach
        </x-adminlte-select2>
        <div class="field-notice text-danger" rel="role"></div>
    </div>
    <div class="form-group">
        <label for="name">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control" value="{{ $staff->user->name }}">
        <div class="field-notice text-danger" rel="name"></div>
    </div>
    <div class="form-group">
        <label for="email">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" id="email" class="form-control" value="{{ $staff->user->email }}">
        <div class="field-notice text-danger" rel="email"></div>
    </div>
    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" name="phone" id="phone" class="form-control" value="{{ $staff->user->phone }}">
        <div class="field-notice text-danger" rel="phone"></div>
    </div>
    <div class="form-group">
        <label for="gender">Gender <span class="text-danger">*</span></label>
        <select name="gender" id="gender" class="form-control">
            <option value="male" {{ $staff->user->gender == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ $staff->user->gender == 'female' ? 'selected' : '' }}>Female</option>
        </select>
        <div class="field-notice text-danger" rel="gender"></div>
    </div>
    <div class="form-group">
        <label for="password">Password <span class="text-danger">*</span></label>
        <input type="password" name="password" id="password" class="form-control">
        <div class="field-notice text-danger" rel="password"></div>
    </div>
    <div class="form-group">
        <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
        <div class="field-notice text-danger" rel="password_confirmation"></div>
    </div>
    <div class="form-group">
        <label for="status">Status <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-control">
            <option value="1" {{ $staff->user->is_active == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ $staff->user->is_active == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    <div class="form-group text-right">
        <x-adminlte-button type="submit" label="Submit" theme="dark" class="ml-auto mr-0 normal" id="create-item-btn"/>
    </div>
</form>