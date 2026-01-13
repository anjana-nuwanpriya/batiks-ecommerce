@extends('adminlte::page')
@include('adminlte::filepond.file-pond', ['preview' => true])
@section('title', 'Customers - '.env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)
@section('plugins.FilePond', true)



@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ __('Customers') }}</h1>
        </div>
        <div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item active">{{ __('Customers') }}</li>
            </ol>
        </div>
    </div>
@endsection


@section('content')

    <section class="card">
        <div class="card-body">
            @php
                $heads = [
                    'Name',
                    'Phone',
                    'Email',
                    'Created By',
                    'Account Status',
                    ['label' => 'Actions', 'no-export' => true, 'width' => 5],
                ];

                $config = [
                    'order' => [[1, 'asc']],
                    'columns' => [null, null, null, null, null, null],
                    'lengthMenu' => [ 10, 30, 50, 100],
                ];

                $data = array();

                foreach ($customers as $customer) {
                    $banned = generateStatusSwitch($customer, 'admin.customer.activate', 'is_active');
                    $phone = $customer->phone ? $customer->phone : 'N/A';
                    $email = $customer->email ? $customer->email : 'N/A';
                    $btnDelete = "<button class='btn btn-xs btn-default text-danger mx-1 shadow delete-record' title='Delete' onclick='action(this)' data-id='".$customer->id."' data-action='delete' data-url='".route('admin.customer.destroy', $customer)."' data-title='Delete - ".$customer->name."'>
                                <i class='fa fa-lg fa-fw fa-trash'></i>
                            </button>";
                    // $btnLogAsUser = "<a href='' class='btn btn-xs btn-default text-primary mx-1 shadow' title='Log as User' data-id='".$customer->id."' data-action='log-as-user' data-url='".route('admin.user.show', $customer)."' data-title='Log as User - ".$customer->name."'>
                    //             <i class='fa fa-lg fa-fw fa-user'></i>
                    //         </a>";
                    $actions = '<nobr>'.$btnDelete.'</nobr>';
                    $data[] = [$customer->name, $phone, $email, $customer->created_by, $banned, $actions];
                }

                $config["data"] = $data;
            @endphp

            <x-adminlte-datatable id="admin-table" :heads="$heads" head-theme="light" :config="$config" hoverable compressed/>
        </div>
    </section>

@endsection


@push('js')
    <script>

        async function action(e) {
            let id = $(e).data('id');
            let action = $(e).data('action');
            let url = $(e).data('url');
            let title = $(e).data('title');

            console.log(id, action, url, title);

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