<form id="editRole" method="POST" class="ajax-form" action="{{route('role.update', $role)}}">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="name">Role Name *</label>
        <input type="text" name="name" id="name" class="form-control" placeholder="Ex: admin" value="{{ $role->name }}" readonly>
        <div class="field-notice text-danger font-weight-bold" rel="name"></div>
    </div>
    @foreach ($permission_groups as $key => $permission_group)
    <div class="bd-example mb-2">
        <ul class="list-group">
            <li class="list-group-item bg-light" aria-current="true">{{ Str::headline($permission_group[0]['section']) }}</li>
            <li class="list-group-item">
                <div class="row">
                    @foreach ($permission_group as $key => $permission)
                    <div class="col-12">
                        <div class="mb-2">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="permissions[]" class="custom-control-input" id="switch{{ $permission->id }}-{{ Str::slug($role->name) }}" value="{{ $permission->name }}" {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="switch{{ $permission->id }}-{{ Str::slug($role->name) }}">{{ Str::headline($permission->name) }}</label>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </li>
        </ul>
    </div>
    @endforeach
    <div class="form-group text-right">
        <x-adminlte-button type="submit" label="Submit" theme="dark" class="ml-auto mr-0 normal" id="create-item-btn"/>
    </div>
</form>