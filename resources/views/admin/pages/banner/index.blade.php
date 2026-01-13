@extends('adminlte::page')
@include('adminlte::filepond.file-pond', ['preview' => true])
@section('title', 'Banner - ' . env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)
@section('plugins.FilePond', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ __('Banners') }}</h1>
        </div>
        <div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i
                            class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item active">{{ __('Banners') }}</li>
            </ol>
        </div>
    </div>

    <!-- Button trigger modal -->
    <div class="d-flex justify-content-end mt-4">
        <button type="button" class="btn btn-dark btn-sm text-right" data-toggle="modal" data-target="#createModal">
            Create Banner
        </button>
    </div>

@endsection


@section('content')

    <section class="card">
        <div class="card-body">
            @php
                $heads = [
                    ['label' => '', 'width' => 2, 'no-export' => true], // Drag handle column
                    'Title',
                    'Sub Title',
                    'Published',
                    ['label' => 'Actions', 'no-export' => true, 'width' => 5],
                ];

                $config = [
                    'order' => [], // Disable default ordering for drag-and-drop
                    'columns' => [
                        ['orderable' => false], // Drag handle column
                        null,
                        null,
                        null,
                        ['orderable' => false], // Actions column
                    ],
                    'lengthMenu' => [10, 30, 50, 100],
                    'rowReorder' => false, // We'll use custom sortable
];

$data = [];

foreach ($banners as $banner) {
    $thumbnail = $banner->thumbnail
        ? '<img src="' .
            $banner->thumbnail .
            '" alt="Thumbnail" class="img-fluid img-thumbnail" style="width: 50px; height: 50px;">'
        : '';

    $title = $thumbnail . ' ' . $banner->title;

    // Drag handle
    $dragHandle =
        '<i class="fas fa-grip-vertical text-muted drag-handle" style="cursor: move;" title="Drag to reorder"></i>';

    // Status
    $status = generateStatusSwitch($banner, 'main-banner.status', 'is_active');

    // Edit
    $btnEdit =
        '<button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit" onclick="action(this)" data-id="' .
        $banner->id .
        '" data-action="edit" data-url="' .
        route('main-banner.edit', $banner) .
        '" data-title="Edit - ' .
        $banner->title .
        '">
                                                                                                                                                                <i class="fa fa-lg fa-fw fa-pen"></i>
                                                                                                                                                            </button>';

    // Delete
    $btnDelete =
        '<button class="btn btn-xs btn-default text-danger mx-1 shadow delete-record" title="Delete" data-id="' .
        $banner->id .
        '" data-action="delete" data-url="' .
        route('main-banner.destroy', $banner) .
        '" data-title="Delete - ' .
        $banner->title .
        '">
                                                                                                                                                                <i class="fa fa-lg fa-fw fa-trash"></i>
                                                                                                                                                            </button>';

    // Actions
    $actions = '<nobr>' . $btnEdit . ' ' . $btnDelete . '</nobr>';
    $data[] = [$dragHandle, $title, $banner->subtitle, $status, $actions];

    $config['data'] = $data;
                }

            @endphp

            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-info"></i> Info!</h5>
                You can drag and drop rows to reorder banners. Changes will be saved automatically.
            </div>

            <x-adminlte-datatable id="admin-table" :heads="$heads" head-theme="light" :config="$config" hoverable
                compressed />
        </div>
    </section>

@endsection

@section('modal')

    <!-- Create -->
    <x-adminlte-modal id="createModal" title="Create Banner" size="lg" theme="white" v-centered static-backdrop
        scrollable>
        <form action="{{ route('main-banner.store') }}" method="POST" class="ajax-form">
            @csrf
            <div class="modal-body">
                <div class="">
                    <x-adminlte-input name="Title" type="text" placeholder="title" label="Title" />
                    <small class="field-notice text-danger" rel="title"></small>
                </div>
                <div class="">
                    <x-adminlte-input name="sub_title" type="text" placeholder="Sub Title" label="Sub Title" />
                    <small class="field-notice text-danger" rel="sub_title"></small>
                </div>
                <div class="">
                    <x-adminlte-textarea name="description" placeholder="Description" label="Description" />
                    <small class="field-notice text-danger" rel="description"></small>
                </div>
                <div class="">
                    <x-adminlte-input name="link_text" type="text" placeholder="Link Text" label="Link Text" />
                    <small class="field-notice text-danger" rel="link_text"></small>
                </div>
                <div class="">
                    <x-adminlte-input name="link" type="text" placeholder="Link" label="Link" />
                    <small class="field-notice text-danger" rel="link"></small>
                </div>
                <div class="">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="applyShade" name="apply_shade">
                        <label class="custom-control-label" for="applyShade">Apply Shade</label>
                    </div>
                    <small class="field-notice text-danger" rel="apply_shade"></small>
                </div>
                <div class="">
                    <x-file-uploader pondName="banner_image" pondID="banner-image" pondCollection="banner_image"
                        pondInstanceName="bannerImagePond" pondLable="Upload Banner Image"
                        inputLabel="Image (Size: 1920x800px)" />
                    <small class="field-notice text-danger" rel="banner_image"></small>
                </div>
                <div class="">
                    <x-file-uploader pondName="mobile_banner_image" pondID="mobile_banner-image"
                        pondCollection="mobile_banner_image" pondInstanceName="mobileBannerImagePond"
                        pondLable="Upload Mobile Banner Image" inputLabel="Image (Size: 768x800px)" />
                    <small class="field-notice text-danger" rel="mobile_banner_image"></small>
                </div>
                <hr>
                <div class="">
                    <x-adminlte-button type="submit" label="Submit" theme="dark" class="btn-block" />
                </div>
            </div>
        </form>
    </x-adminlte-modal>

    <!-- Modal -->
    <x-adminlte-modal id="viewModal" title="View Category" size="lg" theme="white" v-centered static-backdrop
        scrollable>
        <div id="modal-body"></div>
    </x-adminlte-modal>
@endsection

@push('js')
    <!-- SortableJS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        let table;
        let sortable;

        $(document).ready(function() {
            // Initialize DataTable
            table = $('#admin-table').DataTable();

            // Initialize Sortable after DataTable is ready
            initializeSortable();
        });

        function initializeSortable() {
            const tableBody = document.querySelector('#admin-table tbody');

            if (tableBody) {
                sortable = new Sortable(tableBody, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    onEnd: function(evt) {
                        updateBannerOrder();
                    }
                });
            }
        }

        function updateBannerOrder() {
            const rows = $('#admin-table tbody tr');
            const orderData = [];

            rows.each(function(index) {
                const editBtn = $(this).find('button[data-action="edit"]');
                if (editBtn.length > 0) {
                    const bannerId = editBtn.data('id');
                    orderData.push({
                        id: bannerId,
                        order: index + 1
                    });
                }
            });

            // Send AJAX request to update order
            $.ajax({
                url: '{{ route('main-banner.update-order') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_data: orderData
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Banner order updated successfully!');
                    } else {
                        toastr.error('Failed to update banner order');
                        location.reload(); // Reload to restore original order
                    }
                },
                error: function(xhr) {
                    toastr.error('Error updating banner order');
                    location.reload(); // Reload to restore original order
                }
            });
        }

        async function action(e) {
            let id = $(e).data('id');
            let action = $(e).data('action');
            let url = $(e).data('url');
            let title = $(e).data('title');

            if (action == "edit") {
                let info = await getInfo(url);
                $('#viewModal #modal-body').html(info.data);

                $('#viewModal .modal-title').text(title);
                $('#viewModal').modal('show');
            }
        }
    </script>

    <style>
        .sortable-ghost {
            opacity: 0.4;
            background: #f8f9fa;
        }

        .sortable-chosen {
            background: #e3f2fd;
        }

        .sortable-drag {
            background: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .drag-handle:hover {
            color: #007bff !important;
        }

        tbody tr {
            transition: background-color 0.2s ease;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>

    @include('common.scripts')
@endpush
