<form action="{{ route('category.update', $category) }}" method="POST" class="ajax-form">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="">
            <x-adminlte-input name="name" type="text" placeholder="name" label="Name" value="{{ $category->name }}"/>
            <small class="field-notice text-danger" rel="name"></small>
        </div>
        <div class="">
            <x-adminlte-select2 name="parent_id" id="parent_id{{ $category->id }}" label="Parent Category" class="select2">
                <option value="">Select Parent Category</option>
                @foreach ($categoryTree as $pcategory)
                    <option value="{{ $pcategory['id'] }}" {{ $category->parent_id == $pcategory['id'] ? 'selected' : '' }}>{{ $pcategory['name'] }}</option>
                @endforeach
            </x-adminlte-select2>
            <small class="field-notice text-danger" rel="parent_id"></small>
        </div>
        <div class="">
            <x-adminlte-text-editor name="description" label="Description" placeholder="Enter category description...">
                {{ $category->description }}
            </x-adminlte-text-editor>
            <small class="field-notice text-danger" rel="description"></small>
        </div>
        <div class="">
            @php
                $pondInstanceName = 'CategoryThumb' . $category->id;
                $pondId = 'category-thumb' . $category->id;
            @endphp
            <x-file-uploader
                pondName="category_thumbnail"
                pondID="{{ $pondId }}"
                pondCollection="category_thumbnail"
                pondInstanceName="{{ $pondInstanceName }}"
                pondLable="Upload Category Thumbnail"
                :pondMedia="$category->category_thumb"
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
            <x-adminlte-input name="meta_title" type="text" placeholder="Meta Title" label="Meta Title" value="{{ $category->meta_title }}"/>
            <small class="field-notice text-danger" rel="meta_title"></small>
        </div>
        <div class="">
            <x-adminlte-textarea name="meta_description" placeholder="Meta Description" label="Meta Description">
                {{ $category->meta_description }}
            </x-adminlte-textarea>
            <small class="field-notice text-danger" rel="meta_description"></small>
        </div>
        <div class="">
            <x-adminlte-button type="submit" label="Submit" theme="dark" class="btn-block"/>
        </div>
    </div>
</form>
