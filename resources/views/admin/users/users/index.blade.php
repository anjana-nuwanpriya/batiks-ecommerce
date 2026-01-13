@extends('adminlte::page')
@section('title', 'Admin Users - '.env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)




@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ __('Admin Users') }}</h1>
        </div>
        <div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item active">{{ __('Admin Users') }}</li>
            </ol>
        </div>
    </div>

    <!-- Button trigger modal -->
    <div class="d-flex justify-content-end mt-4">
        <button type="button" class="btn btn-dark btn-sm text-right" data-toggle="modal" data-target="#createModal">
            Create User
        </button>
    </div>

@endsection


@section('content')

    <section class="card">
        <div class="card-body">
            @php
                $heads = [
                    ['label' => '<input type="checkbox" id="selectAll">', 'width' => 5, 'no-export' => true, 'escape' => false],
                    'Name',
                    'Role',
                    'Email',
                    ['label' => 'Status (Active/Inactive)', 'width' => 8],
                    ['label' => 'Actions', 'no-export' => true, 'width' => 5],
                ];

                $config = [
                    'order' => [[1, 'asc']],
                    'columns' => [null, null, null, null, null, null],
                    'lengthMenu' => [ 10, 30, 50, 100],
                ];

                $data = array();
            @endphp

            @foreach ($staffs as $staff)
                @php
                     $select = "<input type='checkbox' class='select-record' data-id='".$staff->id."'>";

                     $name = $staff->user->name;
                     $role = $staff->user->roles->pluck('name')->implode(', ');
                     $email = $staff->user->email;
                     $status = ($staff->user->is_active)?' <span class="badge badge-success">Active</span>' : ' <span class="badge badge-danger">Inactive</span>';

                     $btnEdit = "<button class='btn btn-xs btn-default text-primary mx-1 shadow' title='Edit' onclick='action(this)' data-id='".$staff->id."' data-action='edit' data-url='".route('staff.edit', $staff)."' data-title='Edit - ".$staff->user->name."'>
                                <i class='fa fa-lg fa-fw fa-edit'></i>
                            </button>";

                     $btnDelete = "<button class='btn btn-xs btn-default text-danger mx-1 shadow delete-record' title='Delete' onclick='action(this)' data-id='".$staff->id."' data-action='delete' data-url='".route('staff.destroy', $staff)."' data-title='Delete - ".$staff->user->name."'>
                                <i class='fa fa-lg fa-fw fa-trash'></i>
                            </button>";

                     $actions = '<nobr>'.$btnEdit . $btnDelete.'</nobr>';

                     $data[] = [$select, $name, $role, $email, $status, $actions];
                @endphp
            @endforeach
            @php
                $config["data"] = $data;
            @endphp

            <x-adminlte-datatable id="admin-table" :heads="$heads" head-theme="light" :config="$config" hoverable compressed/>
        </div>
    </section>

@endsection

@section('modal')

   {{-- Create ROLE Form --}}
   <x-adminlte-modal id="createModal" title="New User" size="lg" v-centered static-backdrop scrollable>
        <form id="createUser" method="POST" class="ajax-form" action="{{route('staff.store')}}">
            @csrf
            <div class="form-group">
                <x-adminlte-select2 name="roles[]" id="roles" label="Select Role " class="select2" multiple>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </x-adminlte-select2>
                <div class="field-notice text-danger" rel="role"></div>
            </div>
            <div class="form-group">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control">
                <div class="field-notice text-danger" rel="name"></div>
            </div>
            <div class="form-group">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" id="email" class="form-control">
                <div class="field-notice text-danger" rel="email"></div>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control">
                <div class="field-notice text-danger" rel="phone"></div>
            </div>
            <div class="form-group">
                <label for="gender">Gender <span class="text-danger">*</span></label>
                <select name="gender" id="gender" class="form-control">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
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
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div class="form-group text-right">
                <x-adminlte-button type="submit" label="Submit" theme="dark" class="ml-auto mr-0 normal" id="create-item-btn"/>
            </div>
        </form>
    </x-adminlte-modal>



    <!-- Modal -->
    <x-adminlte-modal id="viewModal" title="View Category" size="lg" theme="white" v-centered static-backdrop scrollable>
        <div id="modal-body"></div>
    </x-adminlte-modal>
@endsection

@push('js')
    <script>

        async function action(e) {
            let id = $(e).data('id');
            let action = $(e).data('action');
            let url = $(e).data('url');
            let title = $(e).data('title');

            if(action == "edit"){
                let info = await getInfo(url);
                $('#viewModal #modal-body').html(info.data);

                $('#viewModal .modal-title').text(title);
                $('#viewModal .select2').select2({width: '100%'});

                $('#viewModal').modal('show');
            }
        }

    </script>
    @include('common.scripts')
@endpush