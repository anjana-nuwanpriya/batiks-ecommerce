@extends('adminlte::page')
@include('adminlte::filepond.file-pond', ['preview' => true])
@section('title', 'Product Reviews - ' . env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)
@section('plugins.FilePond', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ __('Product Reviews') }}</h1>
        </div>
        <div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i
                            class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item active">{{ __('Product Reviews') }}</li>
            </ol>
        </div>
    </div>
@endsection


@section('content')
    <section class="card">
        <div class="card-body">
            @php
                $heads = [
                    'Product',
                    'User',
                    'Rating',
                    'Comment',
                    'Publish',
                    'Show Home Page',
                    'Created At',
                    ['label' => 'Actions', 'no-export' => true, 'width' => 5],
                ];

                $config = [
                    'columns' => [null, null, null, null, null, null, null, null],
                    'lengthMenu' => [10, 30, 50, 100],
                ];

                $data = [];
            @endphp

            @foreach ($reviews as $review)
                @php
                    $product = $review->product ? $review->product->name : 'N/A';
                    $user = $review->user ? $review->user->name : 'N/A';
                    $rating = $review->rating ? $review->rating : 'N/A';

                    switch ($rating) {
                        case '5':
                            $reviewBatch = '<span class="badge badge-success">Excellent</span>';
                            break;
                        case '4':
                            $reviewBatch = '<span class="badge badge-primary">Very Good</span>';
                            break;
                        case '3':
                            $reviewBatch = '<span class="badge badge-warning">Good</span>';
                            break;
                        case '2':
                            $reviewBatch = '<span class="badge badge-info">Fair</span>';
                            break;
                        case '1':
                            $reviewBatch = '<span class="badge badge-danger">Poor</span>';
                            break;
                        case '0':
                            $reviewBatch = '<span class="badge badge-secondary">No Rating</span>';
                            break;
                        default:
                            $reviewBatch = '<span class="badge badge-secondary">No Rating</span>';
                            break;
                    }

                    $comment = $review->comment ? Str::limit($review->comment, 40) : 'N/A';

                    $publish = generateStatusSwitch($review, 'review.status', 'is_approved');
                    $showInHomePage = generateStatusSwitch($review, 'review.show.home', 'show_in_home');

                    $createdAt = $review->created_at ? $review->created_at->diffForHumans() : 'N/A';

                    $view =
                        '<button class="btn btn-xs btn-default text-primary mx-1 shadow" title="View" onclick="action(this)" data-id="' .
                        $review->id .
                        '" data-action="view" data-url="' .
                        route('review.show', $review->id) .
                        '" data-title="View Review">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </button>';
                    $deleteButton =
                        '<button class="btn btn-sm btn-danger delete-review" data-id="' .
                        $review->id .
                        '" title="Delete Review"><i class="fas fa-trash"></i></button>';
                    $actions = '<nobr>' . $view . $deleteButton . '</nobr>';

                    $data[] = [
                        $product,
                        $user,
                        $reviewBatch,
                        $comment,
                        $publish,
                        $showInHomePage,
                        $createdAt,
                        $actions,
                    ];
                    $config['data'] = $data;

                @endphp
            @endforeach
            <x-adminlte-datatable id="admin-table" :heads="$heads" head-theme="light" :config="$config" hoverable
                compressed with-buttons />
        </div>
    </section>

@endsection

@section('modal')

    <!-- Modal -->
    <x-adminlte-modal id="viewModal" title="View Category" size="lg" theme="white" v-centered static-backdrop
        scrollable>
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

            if (action == "view") {
                let info = await getInfo(url);

                $('#viewModal #modal-body').html(info);
                $('#viewModal .modal-title').text(title);

                $('#viewModal').modal('show');
            }
        }
    </script>
    @include('common.scripts')
@endpush
