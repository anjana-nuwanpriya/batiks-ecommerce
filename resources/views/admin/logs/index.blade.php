@extends('adminlte::page')

@section('title', 'Log Viewer')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Log Viewer</h1>
        <div class="btn-group">
            @if($selectedFile)
                <button type="button" class="btn btn-warning btn-sm" onclick="clearLog('{{ $selectedFile }}')">
                    <i class="fas fa-eraser"></i> Clear Log
                </button>
                <a href="{{ route('admin.logs.download', ['file' => $selectedFile]) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-download"></i> Download
                </a>
                <button type="button" class="btn btn-danger btn-sm" onclick="deleteLog('{{ $selectedFile }}')">
                    <i class="fas fa-trash"></i> Delete
                </button>
            @endif
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Log Files</h3>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($logFiles as $file)
                            <a href="{{ route('admin.logs.index', ['file' => $file['name']]) }}"
                               class="list-group-item list-group-item-action {{ $selectedFile === $file['name'] ? 'active' : '' }}">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $file['name'] }}</h6>
                                    <small>{{ $file['size'] }}</small>
                                </div>
                                <small>{{ $file['modified'] }}</small>
                            </a>
                        @empty
                            <div class="list-group-item">
                                <p class="mb-0 text-muted">No log files found</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ $selectedFile ? $selectedFile : 'Select a log file' }}
                    </h3>
                    <div class="card-tools">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAutoRefresh()">
                                <i class="fas fa-sync-alt" id="refresh-icon"></i> Auto Refresh
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshLogs()">
                                <i class="fas fa-refresh"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($selectedFile && count($logEntries) > 0)
                        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                            <table class="table table-sm table-striped mb-0">
                                <thead class="thead-dark sticky-top">
                                    <tr>
                                        <th width="150">Timestamp</th>
                                        <th width="80">Level</th>
                                        <th>Message</th>
                                        <th width="50">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($logEntries as $index => $entry)
                                        <tr class="log-entry {{ strtolower($entry['level']) }}">
                                            <td class="text-nowrap">
                                                <small>{{ $entry['timestamp'] }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{
                                                    strtolower($entry['level']) === 'error' ||
                                                    strtolower($entry['level']) === 'critical' ||
                                                    strtolower($entry['level']) === 'alert' ||
                                                    strtolower($entry['level']) === 'emergency' ? 'danger' :
                                                    (strtolower($entry['level']) === 'warning' ? 'warning' :
                                                    (strtolower($entry['level']) === 'info' || strtolower($entry['level']) === 'notice' ? 'info' :
                                                    (strtolower($entry['level']) === 'debug' ? 'secondary' : 'primary')))
                                                }}">
                                                    {{ $entry['level'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="log-message">
                                                    {{ Str::limit($entry['message'], 100) }}
                                                    @if(strlen($entry['message']) > 100 || !empty(trim($entry['context'])))
                                                        <button class="btn btn-link btn-sm p-0 ml-2"
                                                                onclick="toggleLogDetails({{ $index }})">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                                @if(!empty(trim($entry['context'])))
                                                    <div class="log-context collapse" id="context-{{ $index }}">
                                                        <pre class="mt-2 p-2 bg-light border rounded"><code>{{ trim($entry['context']) }}</code></pre>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-secondary"
                                                        onclick="copyToClipboard('{{ addslashes($entry['full_line']) }}')"
                                                        title="Copy full log entry">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif($selectedFile)
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-file-alt fa-3x mb-3"></i>
                            <p>This log file is empty or contains no parseable entries.</p>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-file-alt fa-3x mb-3"></i>
                            <p>Select a log file from the sidebar to view its contents.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .log-entry.error { background-color: #f8d7da; }
    .log-entry.warning { background-color: #fff3cd; }
    .log-entry.info { background-color: #d1ecf1; }
    .log-entry.debug { background-color: #f8f9fa; }

    .log-message {
        font-family: 'Courier New', monospace;
        font-size: 0.9em;
    }

    .log-context pre {
        font-size: 0.8em;
        max-height: 200px;
        overflow-y: auto;
    }

    .list-group-item.active {
        background-color: #007bff;
        border-color: #007bff;
    }

    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
    }
</style>
@stop

@section('js')
<script>
let autoRefreshInterval = null;
let isAutoRefresh = false;

function toggleAutoRefresh() {
    if (isAutoRefresh) {
        clearInterval(autoRefreshInterval);
        isAutoRefresh = false;
        $('#refresh-icon').removeClass('fa-spin');
        toastr.info('Auto refresh disabled');
    } else {
        autoRefreshInterval = setInterval(refreshLogs, 5000);
        isAutoRefresh = true;
        $('#refresh-icon').addClass('fa-spin');
        toastr.info('Auto refresh enabled (5 seconds)');
    }
}

function refreshLogs() {
    window.location.reload();
}

function toggleLogDetails(index) {
    $('#context-' + index).collapse('toggle');
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        toastr.success('Log entry copied to clipboard');
    }, function(err) {
        toastr.error('Failed to copy to clipboard');
    });
}

function clearLog(fileName) {
    if (confirm('Are you sure you want to clear this log file? This action cannot be undone.')) {
        $.post('{{ route("admin.logs.clear") }}', {
            file: fileName,
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to clear log file');
        });
    }
}

function deleteLog(fileName) {
    if (confirm('Are you sure you want to delete this log file? This action cannot be undone.')) {
        $.post('{{ route("admin.logs.delete") }}', {
            file: fileName,
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => window.location.href = '{{ route("admin.logs.index") }}', 1000);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to delete log file');
        });
    }
}
</script>
@stop
