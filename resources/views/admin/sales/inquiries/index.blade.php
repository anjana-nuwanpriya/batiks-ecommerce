@extends('adminlte::page')
@section('title', 'Inquiries - ' . env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ __('All Inquiries') }}</h5>
        </div>
        <div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i
                            class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item active">{{ __('Inquiries') }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    @php
        $pendingCount = $inquiries->where('status', 'pending')->count();
        $processingCount = $inquiries->where('status', 'processing')->count();
        $completedCount = $inquiries->where('status', 'completed')->count();
        $rejectedCount = $inquiries->where('status', 'rejected')->count();
    @endphp

    <!-- Status Filter Card -->
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label for="statusFilter" class="form-label">Filter by Status:</label>
                        <select id="statusFilter" class="form-control select2" style="width: 100%;">
                            <option value="">📋 All Statuses ({{ $inquiries->count() }})</option>
                            <option value="pending">🟡 Pending ({{ $pendingCount }})</option>
                            <option value="processing">🔄 Processing ({{ $processingCount }})</option>
                            <option value="completed">🟢 Completed ({{ $completedCount }})</option>
                            <option value="rejected">🔴 Rejected ({{ $rejectedCount }})</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="d-flex justify-content-end">
                        <div class="info-box-content">
                            <span class="badge badge-warning mr-2">Pending: {{ $pendingCount }}</span>
                            <span class="badge badge-info mr-2">Processing: {{ $processingCount }}</span>
                            <span class="badge badge-success mr-2">Completed: {{ $completedCount }}</span>
                            <span class="badge badge-danger">Rejected: {{ $rejectedCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @php
                $heads = [
                    [
                        'label' => '<input type="checkbox" id="selectAll">',
                        'width' => 5,
                        'no-export' => true,
                        'escape' => false,
                    ],
                    'Company',
                    'Contact Person',
                    'Email',
                    'Phone',
                    'Status',
                    'Created At',
                    ['label' => 'Actions', 'no-export' => true, 'width' => 5],
                ];

                $config = [
                    'columns' => [null, null, null, null, null, null, null, null],
                    'lengthMenu' => [10, 30, 50, 100],
                ];

                $data = [];
            @endphp

            @foreach ($inquiries as $inquiry)
                @php
                    $select = '<input type="checkbox" class="select-record" data-id="' . $inquiry->id . '">';
                    $safeCompanyName = htmlspecialchars($inquiry->company ?? 'N/A', ENT_QUOTES, 'UTF-8');
                    $btnView =
                        "<button class='btn btn-xs btn-default text-primary mx-1 shadow' title='View' onclick='action(this)' data-id='" .
                        $inquiry->id .
                        "' data-action='view' data-url='" .
                        route('product-inquiries.show', $inquiry) .
                        "' data-title='View - " .
                        $safeCompanyName .
                        "'>
                                <i class='fa fa-lg fa-fw fa-eye'></i>
                            </button>";
                    $btnDelete =
                        "<button class='btn btn-xs btn-default text-danger mx-1 shadow' title='Delete' onclick='action(this)' data-id='" .
                        $inquiry->id .
                        "' data-action='delete' data-url='" .
                        route('product-inquiries.destroy', $inquiry) .
                        "' data-title='Delete - " .
                        $safeCompanyName .
                        "'>
                                <i class='fa fa-lg fa-fw fa-trash'></i>
                            </button>";

                    // Status badge with proper styling
                    $statusClass = match ($inquiry->status) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary',
                    };
                    $statusBadge = "<span class='badge badge-{$statusClass}'>" . ucfirst($inquiry->status) . '</span>';

                    $createdAt = $inquiry->created_at ? $inquiry->created_at->format('Y-m-d h:i A') : 'N/A';
                    $actions = '<nobr>' . $btnView . ' ' . $btnDelete . '</nobr>';

                    $data[] = [
                        $select,
                        e($inquiry->company ?? 'N/A'),
                        e($inquiry->name ?? 'N/A'),
                        e($inquiry->email ?? 'N/A'),
                        e($inquiry->phone ?? 'N/A'),
                        $statusBadge,
                        $createdAt,
                        $actions,
                    ];
                @endphp
            @endforeach

            @php
                $config['data'] = $data;
            @endphp

            <x-adminlte-datatable id="admin-table" :heads="$heads" head-theme="light" :config="$config" hoverable
                compressed />

        </div>
    </div>



@endsection

@section('modal')

    <!-- Modal -->
    <x-adminlte-modal id="viewModal" title="View Inquiry" size="lg" theme="white" v-centered static-backdrop
        scrollable>
        <div id="modal-body"></div>
    </x-adminlte-modal>

@endsection

@push('js')
    <script>
        let table;

        $(document).ready(function() {
            // Initialize DataTable
            table = $('#admin-table').DataTable();

            // Initialize Select2 for status filter
            $('#statusFilter').select2({
                width: '100%',
                placeholder: 'All Statuses'
            });

            // Handle status filter change
            $('#statusFilter').on('change', function() {
                const selectedStatus = $(this).val();

                if (selectedStatus === '') {
                    // Show all rows
                    table.column(5).search('').draw();
                } else {
                    // Filter by selected status
                    table.column(5).search(selectedStatus).draw();
                }
            });
        });

        async function action(e) {
            let id = $(e).data('id');
            let action = $(e).data('action');
            let url = $(e).data('url');
            let title = $(e).data('title');

            if (action == "view") {
                let info = await getInfo(url);
                $('#viewModal #modal-body').html(info.data);

                $('#viewModal .modal-title').text(title);
                $('#viewModal .select2').select2({
                    width: '100%'
                });

                $('#viewModal').modal('show');
            } else if (action == "delete") {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteRecord(url);
                    }
                });
            }
        }

        async function deleteRecord(url) {
            try {
                const response = await $.ajax({
                    url: url,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                });

                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Reload the table
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Something went wrong!'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to delete the inquiry.'
                });
            }
        }
    </script>
    @include('common.scripts')
@endpush
