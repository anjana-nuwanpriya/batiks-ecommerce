<form action="{{ route('main-banner.update', $banner) }}" method="POST" class="ajax-form">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="">
            <x-adminlte-input name="title" type="text" placeholder="Title" label="Title"
                value="{!! $banner->title !!}" />
            <small class="field-notice text-danger" rel="title"></small>
        </div>
        <div class="">
            <x-adminlte-input name="sub_title" type="text" placeholder="Sub Title" label="Sub Title"
                value="{{ $banner->subtitle }}" />
            <small class="field-notice text-danger" rel="sub_title"></small>
        </div>
        <div class="">
            <x-adminlte-textarea name="description" placeholder="Description" label="Description">
                {!! $banner->description !!}
            </x-adminlte-textarea>
            <small class="field-notice text-danger" rel="description"></small>
        </div>
        <div class="">
            <x-adminlte-input name="link_text" type="text" placeholder="Link Text" label="Link Text"
                value="{{ $banner->link_text }}" />
            <small class="field-notice text-danger" rel="link_text"></small>
        </div>
        <div class="">
            <x-adminlte-input name="link" type="text" placeholder="Link" label="Link"
                value="{{ $banner->link }}" />
            <small class="field-notice text-danger" rel="link"></small>
        </div>
        <div class="mb-3">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="applyShade{{ $banner->id }}"
                    name="apply_shade" {{ $banner->apply_shade ? 'checked' : '' }}>
                <label class="custom-control-label" for="applyShade{{ $banner->id }}">Apply Shade</label>
            </div>
            <small class="field-notice text-danger" rel="apply_shade"></small>
        </div>
        @php
            $pondID = 'banner-image' . $banner->id;
            $pondInstanceName = 'bannerImagePond' . $banner->id;
            $pondIDmobile = 'mobile_banner-image' . $banner->id;
            $pondInstanceNamemobile = 'mobileBannerImagePond' . $banner->id;
        @endphp
        <div class="">
            <x-file-uploader pondName="banner_image" pondID="{{ $pondID }}" pondCollection="banner_image"
                pondInstanceName="{{ $pondInstanceName }}" :pondMedia="$banner->image" pondLable="Upload Banner Image"
                inputLabel="Image (Size: 1920x800px)" />
            <small class="field-notice text-danger" rel="banner_image"></small>
        </div>
        <div class="">
            <x-file-uploader pondName="mobile_banner_image" pondID="{{ $pondIDmobile }}"
                pondCollection="mobile_banner_image" pondInstanceName="{{ $pondInstanceNamemobile }}" :pondMedia="$banner->mobile_banner_image"
                pondLable="Upload Mobile Banner Image" inputLabel="Image (Size: 768x800px)" />
            <small class="field-notice text-danger" rel="mobile_banner_image"></small>
        </div>
        <hr>
        <div class="">
            <x-adminlte-button type="submit" label="Submit" theme="dark" class="btn-block" />
        </div>
    </div>
</form>
