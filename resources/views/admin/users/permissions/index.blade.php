@extends('adminlte::page')
@include('adminlte::filepond.file-pond', ['preview' => true])
@section('title', 'Permissions - '.env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)
@section('plugins.FilePond', true)



@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ __('Permissions') }}</h1>
        </div>
        <div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item active">{{ __('Permissions') }}</li>
            </ol>
        </div>
    </div>

    <!-- Button trigger modal -->
    <div class="d-flex justify-content-end mt-4">
        <button type="button" class="btn btn-dark btn-sm text-right" data-toggle="modal" data-target="#createModal">
            Create Permission
        </button>
    </div>

@endsection


@section('content')

    <section class="card">
        <div class="card-body">
            @php
                $heads = [
                    ['label' => '<input type="checkbox" id="selectAll">', 'width' => 5, 'no-export' => true, 'escape' => false],
                    'Role',
                    ['label' => 'Actions', 'no-export' => true, 'width' => 5],
                ];

                $config = [
                    'order' => [[1, 'asc']],
                    'columns' => [null, null, null],
                    'lengthMenu' => [ 10, 30, 50, 100],
                ];

                $data = array();

                foreach ($roles as $role) {

                    $select = "<input type='checkbox' class='select-record' data-id='".$role->id."'>";

                    //Delete the first permission
                    $btnDelete = "<button class='btn btn-xs btn-default text-danger mx-1 shadow delete-record' title='Delete' onclick='action(this)' data-id='".$role->id."' data-action='delete' data-url='".route('role.destroy', $role)."' data-title='Delete - ".$role->name."'>
                                <i class='fa fa-lg fa-fw fa-trash'></i>
                            </button>";

                    $btnEdit = "<button class='btn btn-xs btn-default text-primary mx-1 shadow' title='Edit' onclick='action(this)' data-id='".$role->id."' data-action='edit' data-url='".route('role.edit', $role)."' data-title='Edit - ".$role->name."'>
                                <i class='fa fa-lg fa-fw fa-edit'></i>
                            </button>";

                    $actions =  '<nobr>'.$btnEdit . $btnDelete.'</nobr>';

                    $data[] = [$select, $role->name, $actions];
                }

                $config["data"] = $data;
            @endphp

            <x-adminlte-datatable id="admin-table" :heads="$heads" head-theme="light" :config="$config" hoverable compressed/>
        </div>
    </section>

@endsection

@section('modal')

   {{-- Create ROLE Form --}}
   <x-adminlte-modal id="createModal" title="New Role" size="lg" v-centered static-backdrop scrollable>
        <form id="createRole" method="POST" class="ajax-form" action="{{route('role.store')}}">
            @csrf
            <div class="form-group">
                <label for="name">Role Name *</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Ex: admin">
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
                                        <input type="checkbox" name="permissions[]" class="custom-control-input" id="switch{{ $permission->id }}" value="{{ $permission->name }}">
                                        <label class="custom-control-label" for="switch{{ $permission->id }}">{{ Str::headline($permission->name) }}</label>
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
        <x-slot name="footerSlot"></x-slot>
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