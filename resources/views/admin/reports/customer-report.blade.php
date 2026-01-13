@extends('adminlte::page')
@include('adminlte::filepond.file-pond', ['preview' => true])
@section('title', 'Customer Report - '.env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Customer Report</h1>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            @php
                $config = [
                    'columns' => [null, null, null, null, null, null],
                    'lengthMenu' => [ 20, 30, 50, 100],
                ];

                $heads = [
                    'Customer Name',
                    'Email',
                    'Phone',
                    'Total Orders',
                    'Total Amount',
                    'Created At',
                ];

                $data = [];

                foreach ($customers as $customer) {
                    $data[] = [$customer->name, $customer->email, $customer->phone, $customer->orders->count(), formatCurrency($customer->orders->sum('grand_total')), $customer->created_at];
                    $config['data'] = $data;
                }
            @endphp

            <x-adminlte-datatable id="admin-table" :heads="$heads" head-theme="light" :config="$config" hoverable with-buttons compressed/>
        </div>
    </div>
@endsection
