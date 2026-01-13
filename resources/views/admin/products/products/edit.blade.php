@extends('adminlte::page')
@include('adminlte::filepond.file-pond', ['preview' => true])
@section('title', 'Edit Product - ' . env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)
@section('plugins.FilePond', true)
@section('plugins.Summernote', true)

@push('css')
<link rel="stylesheet" href="{{ asset('css/variant-image-upload.css') }}">
@endpush

@push('js')
<script src="{{ asset('js/variant-image-upload.js') }}"></script>
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h5>{{ __('Edit Product') }} - {{ $product->name }}</h5>
    </div>
@endsection


@section('content')
    <form action="{{ route('product.update', $product->id) }}" method="POST" class="ajax-form position-relative">
        @csrf
        @method('PUT')
        <div class="row pb-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('General') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" id="name"
                                placeholder="{{ __('Product Name') }}" value="{{ $product->name }}">
                            <small class="field-notice text-danger" rel="name"></small>
                        </div>
                        <div class="form-group">
                            <label for="sinhala_name">{{ __('Sinhala Name') }}</label>
                            <input type="text" name="sinhala_name" class="form-control" id="sinhala_name"
                                placeholder="{{ __('Product Name in Sinhala') }}" value="{{ $product->sinhala_name }}">
                            <small class="field-notice text-danger" rel="sinhala_name"></small>
                        </div>

                        <div class="form-group">
                            <label for="category_id">{{ __('Category') }} <span class="text-danger">*</span></label>
                            <x-adminlte-select2 name="category_ids[]" id="category_ids" multiple>
                                @foreach ($categories as $category)
                                    <option value="{{ $category['id'] }}"
                                        {{ $category['id'] == $product->categories->contains($category['id']) ? 'selected' : '' }}>
                                        {{ $category['name'] }}</option>
                                @endforeach
                            </x-adminlte-select2>
                            <small class="field-notice text-danger" rel="category_ids"
                                style="position: relative; top: -15px;"></small>
                        </div>

                        <div class="form-group">
                            <label for="short_description">{{ __('Short Description') }}</label>
                            <textarea name="short_description" class="form-control" id="short_description" rows="2" placeholder="">{!! $product->short_description !!}</textarea>
                            <small class="field-notice text-danger" rel="short_description"></small>
                        </div>

                        <div class="form-group">
                            <x-adminlte-text-editor name="descr" label="{{ __('Description') }}">
                                {!! $product->description !!}
                            </x-adminlte-text-editor>
                            <small class="field-notice text-danger" rel="description"></small>
                        </div>

                        <div class="form-group">
                            <x-adminlte-text-editor name="how_to_use" label="{{ __('How to use') }}">
                                {!! $product->how_to_use !!}
                            </x-adminlte-text-editor>
                            <small class="field-notice text-danger" rel="description"></small>
                        </div>
                    </div>
                </div>

                @php
                    $stock = $product->stocks()->where('is_standard', true)->first();
                    $stock = $stock ?? null;
                    $variants = $product->stocks()->where('is_standard', false)->get();
                @endphp

                <div class="card stock-section">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Pricing & Stock') }}</h5>
                        <input type="hidden" name="standard" value="{{ $stock->id ?? 0 }}">
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="variant_name">{{ __('Variant Name') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="variant_name" class="form-control" id="variant_name"
                                        placeholder="{{ __('e.g., 100g, 1Kg, 500ml') }}"
                                        value="{{ $stock->variant ?? '' }}">
                                    <small
                                        class="text-muted d-block">{{ __('Product variant size/weight (e.g., 100g, 1Kg, 500ml)') }}</small>
                                    <small class="field-notice text-danger" rel="variant_name"></small>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="sku">{{ __('SKU') }}</label>
                                    <input type="text" name="sku" class="form-control" id="sku"
                                        placeholder="{{ __('SKU') }}" value="{{ $stock->sku ?? '' }}">
                                    <small class="field-notice text-danger" rel="sku"></small>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="stock">{{ __('Quantity') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="stock" class="form-control" id="stock"
                                        placeholder="{{ __('Quantity') }}" min="0" value="{{ $stock->qty ?? 0 }}">
                                    <small class="field-notice text-danger" rel="stock"></small>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="weight">{{ __('Weight') }} (g) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="weight" class="form-control" id="weight"
                                        placeholder="{{ __('Weight in grams') }}" min="0" step="0.01"
                                        value="{{ $stock->weight ?? 0 }}">
                                    <small class="text-muted d-block">{{ __('Product weight in grams') }}</small>
                                    <small class="field-notice text-danger" rel="weight"></small>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="price">{{ __('Cost') }}(Rs.) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="price" step="0.01" class="form-control"
                                        id="price" placeholder="{{ __('Cost') }}"
                                        value="{{ $stock->purchase_price ?? 0 }}">
                                    <small
                                        class="text-muted d-block">{{ __('Purchase/manufacturing cost of the product') }}</small>
                                    <small class="field-notice text-danger" rel="price"></small>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="selling_price">{{ __('Selling Price') }}(Rs.) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="selling_price" step="0.01" class="form-control"
                                        id="selling_price" placeholder="{{ __('Selling Price') }}"
                                        value="{{ $stock->selling_price ?? 0 }}">
                                    <small
                                        class="text-muted d-block">{{ __('The final price that customers will pay for the product') }}</small>
                                    <small class="field-notice text-danger" rel="selling_price"></small>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group varient-uploader">
                                    <label for="variantImage" class="form-label">
                                        {{ __('Image (600x600px)') }}
                                    </label>

                                    <!-- Image preview container -->
                                    <div class="image-preview-container mb-2" id="imageContainer_standard">
                                        @if ($stock && $stock->thumbnail)
                                            <div class="image-preview-wrapper position-relative d-inline-block">
                                                <img id="previewImage_standard" src="{{ $stock->thumbnail }}"
                                                    alt="Preview" class="img-thumbnail"
                                                    style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                                <button type="button"
                                                    class="btn btn-danger btn-sm position-absolute delete-image-btn"
                                                    style="top: -8px; right: -8px; border-radius: 50%; width: 24px; height: 24px; padding: 0; font-size: 12px;"
                                                    onclick="deleteStandardVariantImage({{ $stock->id ?? 0 }})"
                                                    title="{{ __('Delete Image') }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @else
                                            <div class="image-preview-wrapper position-relative d-inline-block"
                                                style="display: none;">
                                                <img id="previewImage_standard" src="" alt="Preview"
                                                    class="img-thumbnail"
                                                    style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                                <button type="button"
                                                    class="btn btn-danger btn-sm position-absolute delete-image-btn"
                                                    style="top: -8px; right: -8px; border-radius: 50%; width: 24px; height: 24px; padding: 0; font-size: 12px;"
                                                    onclick="clearVariantImagePreview('standard')"
                                                    title="{{ __('Remove Image') }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- File input with improved styling -->
                                    <div class="custom-file">
                                        <input type="file" id="variantImage_standard" name="variant_image"
                                            class="custom-file-input" accept="image/*"
                                            onchange="previewVariantImage(this, 'standard')">
                                        <label class="custom-file-label" for="variantImage_standard">
                                            {{ __('Choose image...') }}
                                        </label>
                                    </div>

                                    <!-- Hidden input to track image deletion -->
                                    <input type="hidden" id="deleteImage_standard" name="delete_standard_image"
                                        value="0">

                                    <small class="field-notice text-danger" rel="variant_image"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('Additional Variants') }}</h5>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="enable_variants"
                                    name="enable_variants" {{ $variants->count() > 0 ? 'checked' : '' }}>
                                <label class="custom-control-label"
                                    for="enable_variants">{{ __('Enable Variants') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="variants-container" class="{{ $variants->count() > 0 ? '' : 'd-none' }}">
                            <div class="variants-list mb-3">
                                @if ($variants->count() > 0)
                                    @foreach ($variants as $key => $variant)
                                        @php
                                            $key = $key + 1;
                                        @endphp
                                        <div class="variant-item card mb-3">
                                            <input type="hidden" name="variants[{{ $key }}][id]"
                                                value="{{ $variant->id }}">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <h6 class="mb-0">{{ __('Variant') }} #<span class="variant-number">
                                                            {{ $key + 1 }}</span></h6>
                                                    <button type="button" class="btn btn-danger btn-sm remove-variant">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12 col-md-3">
                                                        <div class="form-group">
                                                            <label>{{ __('Variant Name') }} <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text"
                                                                name="variants[{{ $key }}][name]"
                                                                class="form-control" value="{{ $variant->variant }}">
                                                            <small
                                                                class="text-muted d-block">{{ __('Product variant size/weight (e.g., 100g, 1Kg, 500ml)') }}</small>
                                                            <small class="field-notice text-danger"
                                                                rel="variants.{{ $key }}.name"></small>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3">
                                                        <div class="form-group">
                                                            <label>{{ __('SKU') }}</label>
                                                            <input type="text"
                                                                name="variants[{{ $key }}][sku]"
                                                                class="form-control" value="{{ $variant->sku }}">
                                                            <small class="field-notice text-danger"
                                                                rel="variants.{{ $key }}.sku"></small>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3">
                                                        <div class="form-group">
                                                            <label>{{ __('Stock') }}</label>
                                                            <input type="number"
                                                                name="variants[{{ $key }}][stock]"
                                                                class="form-control" value="{{ $variant->qty }}">
                                                            <small class="field-notice text-danger"
                                                                rel="variants.{{ $key }}.stock"></small>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3">
                                                        <div class="form-group">
                                                            <label>{{ __('Weight') }} (g)</label>
                                                            <input type="number"
                                                                name="variants[{{ $key }}][weight]"
                                                                class="form-control" value="{{ $variant->weight }}"
                                                                step="0.01" min="0">
                                                            <small
                                                                class="text-muted d-block">{{ __('Product weight in grams') }}</small>
                                                            <small class="field-notice text-danger"
                                                                rel="variants.{{ $key }}.weight"></small>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <div class="form-group">
                                                            <label>{{ __('Cost') }}(Rs.)</label>
                                                            <input type="number"
                                                                name="variants[{{ $key }}][cost]"
                                                                class="form-control"
                                                                value="{{ $variant->purchase_price }}">
                                                            <small
                                                                class="text-muted d-block">{{ __('Purchase/manufacturing cost of the product') }}</small>
                                                            <small class="field-notice text-danger"
                                                                rel="variants.{{ $key }}.cost"></small>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <div class="form-group">
                                                            <label>{{ __('Selling Price') }}(Rs.)</label>
                                                            <input type="number"
                                                                name="variants[{{ $key }}][price]"
                                                                class="form-control"
                                                                value="{{ $variant->selling_price }}">
                                                            <small
                                                                class="text-muted d-block">{{ __('The final price that customers will pay for the product') }}</small>
                                                            <small class="field-notice text-danger"
                                                                rel="variants.{{ $key }}.price"></small>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <div class="form-group varient-uploader">
                                                            <label for="variantImage_{{ $key }}"
                                                                class="form-label">
                                                                {{ __('Image (600x600px)') }}
                                                            </label>

                                                            <!-- Image preview container -->
                                                            <div class="image-preview-container mb-2"
                                                                id="imageContainer_{{ $key }}">
                                                                @if ($variant->thumbnail)
                                                                    <div
                                                                        class="image-preview-wrapper position-relative d-inline-block">
                                                                        <img id="previewImage_{{ $key }}"
                                                                            src="{{ $variant->thumbnail }}"
                                                                            alt="Preview" class="img-thumbnail"
                                                                            style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                                                        <button type="button"
                                                                            class="btn btn-danger btn-sm position-absolute delete-image-btn"
                                                                            style="top: -8px; right: -8px; border-radius: 50%; width: 24px; height: 24px; padding: 0; font-size: 12px;"
                                                                            onclick="deleteVariantImage({{ $key }}, {{ $variant->id }})"
                                                                            title="{{ __('Delete Image') }}">
                                                                            <i class="fas fa-times"></i>
                                                                        </button>
                                                                    </div>
                                                                @else
                                                                    <div class="image-preview-wrapper position-relative d-inline-block"
                                                                        style="display: none;">
                                                                        <img id="previewImage_{{ $key }}"
                                                                            src="" alt="Preview"
                                                                            class="img-thumbnail"
                                                                            style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                                                        <button type="button"
                                                                            class="btn btn-danger btn-sm position-absolute delete-image-btn"
                                                                            style="top: -8px; right: -8px; border-radius: 50%; width: 24px; height: 24px; padding: 0; font-size: 12px;"
                                                                            onclick="clearVariantImagePreview({{ $key }})"
                                                                            title="{{ __('Remove Image') }}">
                                                                            <i class="fas fa-times"></i>
                                                                        </button>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <!-- File input with improved styling -->
                                                            <div class="custom-file">
                                                                <input type="file"
                                                                    id="variantImage_{{ $key }}"
                                                                    name="variants[{{ $key }}][image]"
                                                                    class="custom-file-input" accept="image/*"
                                                                    onchange="previewVariantImage(this, {{ $key }})">
                                                                <label class="custom-file-label"
                                                                    for="variantImage_{{ $key }}">
                                                                    {{ __('Choose image...') }}
                                                                </label>
                                                            </div>

                                                            <!-- Hidden input to track image deletion -->
                                                            <input type="hidden"
                                                                   id="deleteImage_{{ $key }}"
                                                                   name="variants[{{ $key }}][delete_image]"
                                                                   value="0">

                                                            <small class="field-notice text-danger"
                                                                rel="variants[{{ $key }}][image]"></small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add-variant">
                                <i class="fas fa-plus"></i> {{ __('Add Variant') }}
                            </button>
                        </div>
                        @if ($variants->count() == 0)
                            <div id="no-variants-message" class="text-center text-muted">
                                {{ __('Enable variants to add multiple product variations') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pricing Section -->
            <div class="col-md-4">
                <div class="card pricing-section">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Special Pricing') }}</h5>
                        <small class="text-muted">{{ __('This pricing will be applied to the all variants') }}</small>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label for="special_price_type">{{ __('Special Price Type') }}</label>
                            <x-adminlte-select2 name="special_price_type" id="special_price_type">
                                <option value="fixed" {{ $product->special_price_type == 'fixed' ? 'selected' : '' }}>
                                    {{ __('Fixed') }}</option>
                                <option value="percentage"
                                    {{ $product->special_price_type == 'percentage' ? 'selected' : '' }}>
                                    {{ __('Percentage') }}</option>
                            </x-adminlte-select2>
                            <small class="field-notice text-danger" rel="special_price_type"></small>
                        </div>

                        <div class="form-group">
                            <label for="special_price">{{ __('Special Price') }}</label>
                            <input type="number" name="special_price" step="0.01" class="form-control"
                                id="special_price" placeholder="{{ __('Special Price') }}"
                                value="{{ $product->special_price }}">
                            <small class="field-notice text-danger" rel="special_price"></small>
                        </div>

                        <div class="form-group">
                            <label for="sp_start_date">{{ __('Special Price Start Date') }}</label>
                            <input type="date" name="sp_start_date" class="form-control" id="sp_start_date"
                                value="{{ $product->special_price_start ? $product->special_price_start : '' }}">
                            <small class="field-notice text-danger" rel="sp_start_date"></small>
                        </div>

                        <div class="form-group">
                            <label for="sp_end_date">{{ __('Special Price End Date') }}</label>
                            <input type="date" name="sp_end_date" class="form-control" id="sp_end_date"
                                value="{{ $product->special_price_end ? $product->special_price_end : '' }}">
                            <small class="field-notice text-danger" rel="sp_end_date"></small>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Media') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <x-file-uploader pondName="product_thumbnail" pondID="product-thumbnail"
                                pondCollection="product_thumbnail" pondInstanceName="productImagePond"
                                pondLable="Upload Product Thumbnail" inputLabel="Thumbnail (Size: 400x400px)"
                                :pondMedia="$product->product_thumb" />
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Shipping Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="shipping_type">{{ __('Shipping Type') }}</label>
                            <x-adminlte-select2 name="shipping_type" id="shipping_type" class="form-control">
                                <option value="free" {{ $product->is_free_shipping ? 'selected' : '' }}>
                                    {{ __('Free Shipping') }}</option>
                                <option value="weight" {{ !$product->is_free_shipping ? 'selected' : '' }}>
                                    {{ __('Weight Based Shipping') }}</option>
                            </x-adminlte-select2>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('SEO') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-sm btn-info" data-toggle="popover" data-html="true"
                                data-placement="right" title="SEO Meta Information Tips"
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
                            <x-adminlte-input name="meta_title" type="text" placeholder="Meta Title"
                                label="Meta Title" value="{{ $product->meta_title }}" />
                            <small class="field-notice text-danger" rel="meta_title"></small>
                        </div>
                        <div class="">
                            <x-adminlte-textarea name="meta_description" placeholder="Meta Description"
                                label="Meta Description">
                                {!! $product->meta_description !!}
                            </x-adminlte-textarea>
                            <small class="field-notice text-danger" rel="meta_description"></small>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Actions') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="published" name="published"
                                    {{ $product->is_active ? 'checked' : '' }}>
                                <label class="custom-control-label" for="published">{{ __('Publish Product') }}</label>
                            </div>
                            <small
                                class="text-muted">{{ __('Toggle to make this product visible on the store') }}</small>
                        </div>

                        <div class="form-group mt-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="allow_inquiries"
                                    name="allow_inquiries" {{ $product->allow_inquiries ? 'checked' : '' }}>
                                <label class="custom-control-label"
                                    for="allow_inquiries">{{ __('Enable Product Inquiries') }}</label>
                            </div>
                            <small
                                class="text-muted">{{ __('Allow customers to make inquiries about this product') }}</small>
                        </div>

                        <div class="form-group mt-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="featured" name="featured"
                                    {{ $product->is_featured ? 'checked' : '' }}>
                                <label class="custom-control-label" for="featured">{{ __('Feature Product') }}</label>
                            </div>
                            <small
                                class="text-muted">{{ __('Feature this product to highlight it in special sections') }}</small>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Linked Products') }}</h5>
                    </div>


                    <div class="card-body">
                        <div class="form-group">
                            <label for="related_products">{{ __('Related Products') }}</label>
                            <x-adminlte-select2 name="related_products[]" id="related_products" multiple>
                                @foreach ($relatedProducts as $relatedProduct)
                                    <option value="{{ $relatedProduct['id'] }}"
                                        {{ $product->relatedProducts()->pluck('related_product_id')->contains($relatedProduct['id']) ? 'selected' : '' }}>
                                        {{ $relatedProduct['name'] }}
                                    </option>
                                @endforeach
                            </x-adminlte-select2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="btns-container">
            <button type="submit" class="btn btn-dark">{{ __('Update') }}</button>
            <a href="{{ route('product.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
        </div>
    </form>
@endsection


@push('js')
    <script>
        // Variants functionality
        const enableVariantsToggle = document.getElementById('enable_variants');
        const variantsContainer = document.getElementById('variants-container');
        const noVariantsMessage = document.getElementById('no-variants-message');
        const addVariantBtn = document.getElementById('add-variant');
        const variantsList = document.querySelector('.variants-list');

        function createVariantTemplate() {
            const variantIndex = variantsList.children.length + 1;
            console.log(variantIndex);

            return `
                <div class="variant-item card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="mb-0">{{ __('Variant') }} #<span class="variant-number"></span></h6>
                            <button type="button" class="btn btn-danger btn-sm remove-variant">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label>{{ __('Variant Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="variants[${variantIndex}][name]" class="form-control">
                                    <small class="text-muted d-block">{{ __('Product variant size/weight (e.g., 100g, 1Kg, 500ml)') }}</small>
                                    <small class="field-notice text-danger" rel="variants.${variantIndex}.name"></small>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label>{{ __('SKU') }}</label>
                                    <input type="text" name="variants[${variantIndex}][sku]" class="form-control">
                                    <small class="field-notice text-danger" rel="variants.${variantIndex}.sku"></small>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label>{{ __('Quantity') }}</label>
                                    <input type="number" name="variants[${variantIndex}][stock]" class="form-control">
                                    <small class="text-muted d-block">{{ __('Product variant quantity') }}</small>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label>{{ __('Weight') }} (g)</label>
                                    <input type="number" name="variants[${variantIndex}][weight]" class="form-control" step="0.01" min="0">
                                    <small class="text-muted d-block">{{ __('Product weight in grams') }}</small>
                                    <small class="field-notice text-danger" rel="variants.${variantIndex}.weight"></small>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Cost') }}(Rs.) </label>
                                    <input type="number" name="variants[${variantIndex}][cost]" class="form-control" >
                                    <small class="text-muted d-block">{{ __('Purchase/manufacturing cost of the product') }}</small>
                                    <small class="field-notice text-danger" rel="variants.${variantIndex}.cost"></small>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Selling Price') }}(Rs.)</label>
                                    <input type="number" name="variants[${variantIndex}][price]" class="form-control" >
                                    <small class="text-muted d-block">{{ __('The final price that customers will pay for the product') }}</small>
                                    <small class="field-notice text-danger" rel="variants.${variantIndex}.price"></small>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group varient-uploader">
                                    <label for="variantImage_${variantIndex}" class="form-label">
                                        {{ __('Image (600x600px)') }}
                                    </label>

                                    <!-- Image preview container -->
                                    <div class="image-preview-container mb-2" id="imageContainer_${variantIndex}">
                                        <div class="image-preview-wrapper position-relative d-inline-block" style="display: none;">
                                            <img id="previewImage_${variantIndex}"
                                                 src=""
                                                 alt="Preview"
                                                 class="img-thumbnail"
                                                 style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                            <button type="button"
                                                    class="btn btn-danger btn-sm position-absolute delete-image-btn"
                                                    style="top: -8px; right: -8px; border-radius: 50%; width: 24px; height: 24px; padding: 0; font-size: 12px;"
                                                    onclick="clearVariantImagePreview(${variantIndex})"
                                                    title="{{ __('Remove Image') }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- File input with improved styling -->
                                    <div class="custom-file">
                                        <input type="file"
                                            id="variantImage_${variantIndex}"
                                            name="variants[${variantIndex}][image]"
                                            class="custom-file-input"
                                            accept="image/*"
                                            onchange="previewVariantImage(this, ${variantIndex})">
                                        <label class="custom-file-label" for="variantImage_${variantIndex}">
                                            {{ __('Choose image...') }}
                                        </label>
                                    </div>

                                    <!-- Hidden input to track image deletion -->
                                    <input type="hidden"
                                           id="deleteImage_${variantIndex}"
                                           name="variants[${variantIndex}][delete_image]"
                                           value="0">

                                    <small class="field-notice text-danger" rel="variants[${variantIndex}][image]"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        enableVariantsToggle.addEventListener('change', function() {
            if (this.checked) {
                variantsContainer.classList.remove('d-none');
                noVariantsMessage.classList.add('d-none');
                if (variantsList.children.length === 0) {
                    addVariant();
                }
            } else {
                variantsContainer.classList.add('d-none');
                noVariantsMessage.classList.remove('d-none');
                variantsList.innerHTML = '';
            }
        });

        function addVariant() {
            const variantTemplate = createVariantTemplate();
            const variantElement = document.createElement('div');
            variantElement.innerHTML = variantTemplate;
            variantsList.appendChild(variantElement.firstElementChild);

            // Update variant numbers
            updateVariantNumbers();
        }

        // Use event delegation for remove variant buttons
        variantsList.addEventListener('click', function(e) {
            if (e.target.closest('.remove-variant')) {
                e.target.closest('.variant-item').remove();
                updateVariantNumbers();
            }
        });

        function updateVariantNumbers() {
            const variantItems = variantsList.querySelectorAll('.variant-item');
            variantItems.forEach((item, index) => {
                const variantIndex = index + 1;
                item.querySelector('.variant-number').textContent = variantIndex;

                // Update all input names with the new index
                item.querySelectorAll('input').forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        input.setAttribute('name', name.replace(/variants\[\d+\]/,
                            `variants[${variantIndex}]`));
                    }
                });
            });
        }

        addVariantBtn.addEventListener('click', addVariant);

        function previewVariantImage(input, index) {
            const preview = document.getElementById(`previewImage_${index}`);
            const wrapper = preview.closest('.image-preview-wrapper');
            const fileLabel = input.nextElementSibling;

            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Update file label
                fileLabel.textContent = file.name;

                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    wrapper.style.display = 'inline-block';
                };
                reader.readAsDataURL(file);

                // Reset delete flag
                const deleteInput = document.getElementById(`deleteImage_${index}`);
                if (deleteInput) {
                    deleteInput.value = '0';
                }
            } else {
                clearVariantImagePreview(index);
            }
        }

        function clearVariantImagePreview(index) {
            const preview = document.getElementById(`previewImage_${index}`);
            const wrapper = preview.closest('.image-preview-wrapper');
            const fileInput = document.getElementById(`variantImage_${index}`);
            const fileLabel = fileInput.nextElementSibling;

            // Clear preview
            preview.src = '';
            wrapper.style.display = 'none';

            // Reset file input
            fileInput.value = '';
            fileLabel.textContent = '{{ __('Choose image...') }}';
        }

        function deleteVariantImage(variantIndex, variantId) {
            if (confirm('{{ __('Are you sure you want to delete this image?') }}')) {
                // Set delete flag
                const deleteInput = document.getElementById(`deleteImage_${variantIndex}`);
                if (deleteInput) {
                    deleteInput.value = '1';
                }

                // Get elements
                const preview = document.getElementById(`previewImage_${variantIndex}`);
                const wrapper = preview.closest('.image-preview-wrapper');
                const fileInput = document.getElementById(`variantImage_${variantIndex}`);
                const fileLabel = fileInput.nextElementSibling;
                const container = document.getElementById(`imageContainer_${variantIndex}`);

                // Add fade out animation
                wrapper.style.transition = 'opacity 0.3s ease';
                wrapper.style.opacity = '0';

                setTimeout(() => {
                    // Clear preview image
                    preview.src = '';
                    wrapper.style.display = 'none';
                    wrapper.style.opacity = '1';

                    // Reset file input
                    fileInput.value = '';
                    fileLabel.textContent = '{{ __('Choose image...') }}';

                    // Add visual indicator that image is marked for deletion
                    if (container) {
                        container.innerHTML = `
                            <div class="alert alert-warning alert-sm mb-2" role="alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ __('Image marked for deletion') }}
                            </div>
                        `;
                    }
                }, 300);

                // Show success message
                if (typeof toastr !== 'undefined') {
                    toastr.warning('{{ __('Image will be deleted when you save the product') }}');
                } else {
                    alert('{{ __('Image will be deleted when you save the product') }}');
                }
            }
        }

        function deleteStandardVariantImage(stockId) {
            if (confirm('{{ __('Are you sure you want to delete this image?') }}')) {
                // Set delete flag
                const deleteInput = document.getElementById('deleteImage_standard');
                if (deleteInput) {
                    deleteInput.value = '1';
                }

                // Get elements
                const preview = document.getElementById('previewImage_standard');
                const wrapper = preview.closest('.image-preview-wrapper');
                const fileInput = document.getElementById('variantImage_standard');
                const fileLabel = fileInput.nextElementSibling;
                const container = document.getElementById('imageContainer_standard');

                // Add fade out animation
                wrapper.style.transition = 'opacity 0.3s ease';
                wrapper.style.opacity = '0';

                setTimeout(() => {
                    // Clear preview image
                    preview.src = '';
                    wrapper.style.display = 'none';
                    wrapper.style.opacity = '1';

                    // Reset file input
                    fileInput.value = '';
                    fileLabel.textContent = '{{ __('Choose image...') }}';

                    // Add visual indicator that image is marked for deletion
                    if (container) {
                        container.innerHTML = `
                            <div class="alert alert-warning alert-sm mb-2" role="alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ __('Image marked for deletion') }}
                            </div>
                        `;
                    }
                }, 300);

                // Show success message
                if (typeof toastr !== 'undefined') {
                    toastr.warning('{{ __('Image will be deleted when you save the product') }}');
                } else {
                    alert('{{ __('Image will be deleted when you save the product') }}');
                }
            }
        }
    </script>

    @include('common.scripts')
@endpush
