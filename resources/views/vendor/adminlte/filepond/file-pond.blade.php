@once
    @push('file_pond')
        {{-- File-Pond CSS --}}
        <link rel="stylesheet" href="{{ asset('vendor/filepond/dist/filepond.min.css') }}">
        @if(isset($preview) && $preview)
            <link rel="stylesheet" href="{{ asset('vendor/filepond/dist/filepond-plugin-file-poster.min.css') }}">
            <link rel="stylesheet" href="{{ asset('vendor/filepond/dist/filepond-plugin-image-preview.min.css') }}">
            {{-- <link rel="stylesheet" href="{{ asset('vendor/ImageEdit/filepond-plugin-image-edit.min.css') }}"> --}}
        @endif

        {{-- File-Pond JS --}}
        <script src="{{ asset('vendor/filepond/dist/filepond.min.js') }}"></script>
        @if(isset($preview) && $preview)
            <script src="{{ asset('vendor/filepond/dist/filepond-plugin-file-poster.min.js') }}"></script>
            <script src="{{ asset('vendor/filepond/dist/filepond-plugin-image-preview.min.js') }}"></script>
            <script src="{{ asset('vendor/filepond/dist/filepond-plugin-image-edit.min.js') }}"></script>
        @endif

        {{-- File-Pond Scripts --}}
        @if(isset($preview) && $preview)
        <script>
            FilePond.registerPlugin(FilePondPluginImagePreview);
            FilePond.registerPlugin(FilePondPluginFilePoster);
            FilePond.registerPlugin(FilePondPluginImageEdit);
        </script>
        @endif
    @endpush
@endonce