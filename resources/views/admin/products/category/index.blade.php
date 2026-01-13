@extends('adminlte::page')
@include('adminlte::filepond.file-pond', ['preview' => true])
@section('title', 'Categories - '.env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)
@section('plugins.FilePond', true)
@section('plugins.Summernote', true)



@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ __('Categories') }}</h1>
        </div>
        <div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item active">{{ __('Categories') }}</li>
            </ol>
        </div>
    </div>

    <!-- Button trigger modal -->
    <div class="d-flex justify-content-end mt-4">
        <button type="button" class="btn btn-dark btn-sm text-right" data-toggle="modal" data-target="#createModal">
            Create Category
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
                    'Parent Category',
                    ['label' => 'Featured', 'width' => 5],
                    ['label' => 'Status', 'width' => 5],
                    ['label' => 'Actions', 'no-export' => true, 'width' => 5],
                ];

                $config = [
                    // 'order' => [[1, 'asc']],
                    'columns' => [null, null, null, null, null,null],
                    'lengthMenu' => [ 10, 30, 50, 100],
                ];

                $data = array();
            @endphp

            @foreach ($categories as $category)
                @php
                    $select = '<input type="checkbox" class="row-checkbox" value="'.$category->id.'">';
                    $thumbnail = $category->thumbnail ? '<img src="'.$category->thumbnail.'" alt="Thumbnail" class="img-fluid img-thumbnail" style="width: 50px; height: 50px;">' : '';
                    $name = '<button class="btn mx-1" title="Edit" onclick="action(this)" data-id="'.$category->id.'" data-action="edit" data-url="'.route('category.edit', $category->id).'" data-title="Edit - '.$category->name.'">'. $thumbnail .' '. $category->name.'</button>';

                    // Status
                    $status = generateStatusSwitch($category, 'category.status', 'status');

                    $featured = generateStatusSwitch($category, 'category.featured', 'featured');

                    $parentCategory = $category->parent ? $category->parent->name : '-';

                    // Edit
                    $btnEdit = '<button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit" onclick="action(this)" data-id="'.$category->id.'" data-action="edit" data-url="'.route('category.edit', $category->id).'" data-title="Edit - '.$category->name.'">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </button>';

                    // Delete
                    $btnDelete = '<button class="btn btn-xs btn-default text-danger mx-1 shadow delete-record" title="Delete" data-id="'.$category->id.'" data-action="delete" data-url="'.route('category.destroy', $category).'" data-title="Delete - '.$category->name.'">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>';

                    // Actions
                    $actions = '<nobr>'.$btnEdit.' '.$btnDelete.'</nobr>';

                    $data[] = [$select, $name, $parentCategory, $featured,$status, $actions];
                    $config["data"] = $data;

                @endphp
            @endforeach

            <button id="delete-selected" class="btn btn-danger mb-2 d-none"> <i class="fa fa-lg fa-fw fa-trash"></i> </button>
            <x-adminlte-datatable id="admin-table" :heads="$heads" head-theme="light" :config="$config" hoverable compressed/>
        </div>
    </section>

@endsection

@section('modal')

    <!-- Create -->
    <x-adminlte-modal id="createModal" title="Create Category" size="lg" theme="white" v-centered static-backdrop scrollable>
        <form action="{{ route('category.store') }}" method="POST" class="ajax-form">
            @csrf
            <div class="modal-body">
                <div class="">
                    <x-adminlte-input name="name" type="text" placeholder="name" label="Name"/>
                    <small class="field-notice text-danger" rel="name"></small>
                </div>
                <div class="">
                    <x-adminlte-select2 name="parent_id" label="Parent Category" class="select2">
                        <option value="">Select Parent Category</option>
                        @foreach ($categoryTree as $category)
                            <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                        @endforeach
                    </x-adminlte-select2>
                    <small class="field-notice text-danger" rel="parent_id"></small>
                </div>
                <div class="">
                    <x-adminlte-text-editor name="description" label="Description" placeholder="Enter category description..."/>
                    <small class="field-notice text-danger" rel="description"></small>
                </div>
                <div class="">
                    <x-file-uploader
                        pondName="category_thumbnail"
                        pondID="category-thumbnail"
                        pondCollection="category_thumbnail"
                        pondInstanceName="categoryImagePond"
                        pondLable="Upload Category Thumbnail"
                        inputLabel="Thumbnail (Size: 400x600px)"
                    />
                    <small class="field-notice text-danger" rel="category_thumbnail"></small>
                </div>
                <hr>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-info" data-toggle="popover" data-html="true" data-placement="right"
                        title="SEO Meta Information Tips"
                        data-content="<div>
                            <p><strong>Meta Title:</strong></p>
                            <ul>
                                <li>Keep it between 50-60 characters</li>
                                <li>Include your main keyword</li>
                                <li>Make it unique and descriptive</li>
                                <li>Format: Primary Keyword - Secondary Keyword | Brand Name</li>
                            </ul>
                            <p><strong>Meta Description:</strong></p>
                            <ul>
                                <li>Ideal length is 150-160 characters</li>
                                <li>Include your target keywords naturally</li>
                                <li>Write compelling, actionable content</li>
                                <li>Highlight unique selling points</li>
                                <li>Include a call-to-action when appropriate</li>
                            </ul>
                        </div>">
                        <i class="fas fa-info-circle"></i>
                    </button>
                    <span class="ml-2">SEO Meta Information</span>
                </div>
                <div class="">
                    <x-adminlte-input name="meta_title" type="text" placeholder="Meta Title" label="Meta Title"/>
                    <small class="field-notice text-danger" rel="meta_title"></small>
                </div>
                <div class="">
                    <x-adminlte-textarea name="meta_description" placeholder="Meta Description" label="Meta Description"/>
                    <small class="field-notice text-danger" rel="meta_description"></small>
                </div>
                <div class="">
                    <x-adminlte-button type="submit" label="Submit" theme="dark" class="btn-block"/>
                </div>
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
                $('#viewModal #description').summernote({
                    height: 200,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ]
                });



                $('#viewModal').modal('show');
            }
        }

    </script>
    <script>
        $(document).ready(function() {

            let table = $('#admin-table').DataTable();

            $('#selectAll').on('change', function () {
                $('.row-checkbox').prop('checked', this.checked);
                toggleDeleteButton();
            });

            // Toggle delete button on individual checkbox change
            $(document).on('change', '.row-checkbox', function () {
                // If all checked, check the "select-all" checkbox
                $('#selectAll').prop('checked', $('.row-checkbox:checked').length === $('.row-checkbox').length);
                toggleDeleteButton();
            });

            function toggleDeleteButton() {
                if ($('.row-checkbox:checked').length > 0) {
                    $('#delete-selected').removeClass('d-none');
                } else {
                    $('#delete-selected').addClass('d-none');
                }
            }

            $('#delete-selected').on('click', function () {
                let selectedIds = $('.row-checkbox:checked').map(function () {
                    return $(this).val();
                }).get();

                if (selectedIds.length === 0) return;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to delete selected category(s).",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('category.destroy.all') }}",
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                ids: selectedIds
                            },
                            onBeforeSend: function(xhr) {
                                $('##delete-selected').addClass('btn-loading').prop('disabled', true);
                            },
                            success: function (response) {
                                if (response.status) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    selectedIds.forEach(function(id) {
                                        table.row($(`.row-checkbox[value="${id}"]`).closest('tr')).remove().draw();
                                    });
                                    $('#select-all').prop('checked', false);
                                    toggleDeleteButton();
                                } else {
                                    Swal.fire('Warning', response.message, 'warning');
                                }
                            },
                            error: function (xhr) {
                                let message = 'Something went wrong!';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                                Swal.fire('Error', message, 'error');
                            },
                            complete: function () {
                                $('#delete-selected').removeClass('btn-loading').prop('disabled', false);
                            }
                        });
                    }
                });
            });
        });
    </script>
    @include('common.scripts')
@endpush
