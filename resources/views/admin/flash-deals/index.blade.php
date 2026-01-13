@extends('adminlte::page')
@section('title', 'Flash Deals - '.env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ __('Flash Deals') }}</h1>
        </div>
        <div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item active">{{ __('Flash Deals') }}</li>
            </ol>
        </div>
    </div>

    <!-- Button trigger modal -->
    <div class="d-flex justify-content-end mt-4">
        <a href="{{ route('flash.deal.create') }}" class="btn btn-dark btn-sm text-right">
            Create Flash Deal
        </a>
    </div>

@endsection


@section('content')

<section class="card">
    <div class="card-body">
        @php
            $heads = [
                'Title',
                'Date Range',
                'Status',
                ['label' => 'Actions', 'no-export' => true, 'width' => 5],
            ];

            $config = [
                'order' => [[1, 'asc']],
                'columns' => [null, null, null, null],
                'lengthMenu' => [ 10, 30, 50, 100],
            ];

            $data = array();

            foreach ($flashDeals as $flashDeal) {

                $title = $flashDeal->title;
                $dateRange = $flashDeal->start_date . ' to ' . $flashDeal->end_date;

                $status = generateStatusSwitch($flashDeal, 'flash.deal.status', 'status');

               // Edit
                $btnEdit = '<a href="'.route('flash.deal.edit', $flashDeal->id).'" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit" data-id="'.$flashDeal->id.'" data-action="edit" data-url="'.route('flash.deal.edit', $flashDeal->id).'" data-title="Edit - '.$flashDeal->title.'">
                            <i class="fa fa-lg fa-fw fa-pen"></i>
                        </a>';

                // Delete
                $btnDelete = '<button class="btn btn-xs btn-default text-danger mx-1 shadow delete-record" title="Delete" data-id="'.$flashDeal->id.'" data-action="delete" data-url="'.route('flash.deal.destroy', $flashDeal).'" data-title="Delete - '.$flashDeal->title.'">
                    <i class="fa fa-lg fa-fw fa-trash"></i>
                </button>';

                $actions = '<nobr>'.$btnEdit . $btnDelete.'</nobr>';

                $data[] = [
                    $title,
                    $dateRange,
                    $status,
                    $actions,
                ];

                $config['data'] = $data;

            }
        @endphp


        <x-adminlte-datatable id="admin-table" :heads="$heads" head-theme="light" :config="$config" hoverable compressed/>
    </div>
</section>

@endsection

@push('js')

    @include('common.scripts')
@endpush