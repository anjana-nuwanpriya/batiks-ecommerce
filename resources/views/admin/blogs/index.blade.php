@extends('adminlte::page')
@include('adminlte::filepond.file-pond', ['preview' => true])
@section('title', 'Blogs - '.env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)
@section('plugins.FilePond', true)
@section('plugins.Summernote', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ __('Blogs') }}</h1>
        </div>
        <div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item active">{{ __('Blogs') }}</li>
            </ol>
        </div>
    </div>

    <!-- Button trigger modal -->
    <div class="d-flex justify-content-end mt-4">
        <button type="button" class="btn btn-dark btn-sm text-right" data-toggle="modal" data-target="#createModal">
            Create Blog
        </button>
    </div>

@endsection

@section('content')

    <section class="card">
        <div class="card-body">
            @php
                $heads = [
                    'Title',
                    'Published',
                    ['label' => 'Actions', 'no-export' => true, 'width' => 5],
                ];

                $config = [
                    'order' => [[1, 'asc']],
                    'columns' => [null, null,null],
                    'lengthMenu' => [ 10, 30, 50, 100],
                ];

                $data = array();

                foreach ($blogs as $blog) {

                    $image = $blog->getFirstMediaUrl('blog_thumbnail') ? '<img src="'.$blog->getFirstMediaUrl('blog_thumbnail').'" alt="'.$blog->title.'" class="img-fluid img-thumbnail" style="width: 50px; height: 50px;">' : '';

                    $title = $image.' '.$blog->title;

                    $status = generateStatusSwitch($blog, 'blog.status', 'is_published');

                   // Edit
                    $btnEdit = '<button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit" onclick="action(this)" data-id="'.$blog->id.'" data-action="edit" data-url="'.route('blog.edit', $blog->id).'" data-title="Edit - '.$blog->title.'">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </button>';

                    // Delete
                    $btnDelete = '<button class="btn btn-xs btn-default text-danger mx-1 shadow delete-record" title="Delete" data-id="'.$blog->id.'" data-action="delete" data-url="'.route('blog.destroy', $blog).'" data-title="Delete - '.$blog->title.'">
                        <i class="fa fa-lg fa-fw fa-trash"></i>
                    </button>';

                    $actions = '<nobr>'.$btnEdit . $btnDelete.'</nobr>';

                    $data[] = [
                        $title,
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

@section('modal')

    <!-- Create -->
    <x-adminlte-modal id="createModal" title="Create Blog" size="lg" theme="white" v-centered static-backdrop scrollable>
        <form action="{{ route('blog.store') }}" method="POST" class="ajax-form">
            @csrf
            <div class="modal-body">
                <div class="">
                    <x-adminlte-input name="title" type="text" placeholder="title" label="Title"/>
                    <small class="field-notice text-danger" rel="title"></small>
                </div>
                <div class="">
                    <x-adminlte-text-editor name="content" label="{{ __('Content') }}"/>
                </div>
                <div class="">
                    <x-file-uploader
                        pondName="blog_thumbnail"
                        pondID="blog-thumbnail"
                        pondCollection="blog_thumbnail"
                        pondInstanceName="blogImagePond"
                        pondLable="Upload Blog Thumbnail"
                        inputLabel="Thumbnail (Size: 650x400px)"
                    />
                    <small class="field-notice text-danger" rel="blog_thumbnail"></small>
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
                $('#viewModal .summernote').summernote();
                $('#viewModal').modal('show');
            }
        }

    </script>
    @include('common.scripts')
@endpush