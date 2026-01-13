<form action="{{ route('blog.update', $blog->id) }}" method="POST" class="ajax-form">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="">
            <x-adminlte-input name="title" type="text" placeholder="title" label="Title" value="{{ $blog->title }}"/>
            <small class="field-notice text-danger" rel="title"></small>
        </div>
        <div class="">
            <x-adminlte-text-editor name="content" class="summernote" label="{{ __('Content') }}">
                {{ $blog->content }}
            </x-adminlte-text-editor>
        </div>
        <div class="">
            @php
                $pondInstanceName = 'BlogThumb' . $blog->id;
                $pondId = 'blog-thumb' . $blog->id;
            @endphp
            <x-file-uploader
                pondName="blog_thumbnail"
                pondID="{{ $pondId }}"
                pondCollection="blog_thumbnail"
                pondInstanceName="{{ $pondInstanceName }}"
                pondLable="Upload Blog Thumbnail"
                :pondMedia="$blog->blog_thumb"
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
            <x-adminlte-input name="meta_title" type="text" placeholder="Meta Title" label="Meta Title" value="{{ $blog->meta_title }}"/>
            <small class="field-notice text-danger" rel="meta_title"></small>
        </div>
        <div class="">
            <x-adminlte-textarea name="meta_description" placeholder="Meta Description" label="Meta Description" value="{{ $blog->meta_description }}"/>
            <small class="field-notice text-danger" rel="meta_description"></small>
        </div>
        <div class="">
            <x-adminlte-button type="submit" label="Submit" theme="dark" class="btn-block"/>
        </div>
    </div>
</form>