@extends('adminlte::page')
@include('adminlte::filepond.file-pond', ['preview' => true])
@section('title', 'Home Page - ' . env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Summernote', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ __('Home Page') }}</h1>
        </div>
        <div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i
                            class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item active">{{ __('Home Page') }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Hero Section') }}</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('site.settings.update') }}" method="POST" class="ajax-form">
                        @csrf
                        <div class="form-group">
                            <label for="title">{{ __('Title') }}</label>
                            <input type="text" name="home_hero_title" id="title" class="form-control"
                                value="{!! get_setting('home_hero_title') !!}">
                        </div>
                        <div class="form-group">
                            <label for="subtitle">{{ __('Subtitle') }}</label>
                            <input type="text" name="home_hero_subtitle" id="subtitle" class="form-control"
                                value="{!! get_setting('home_hero_subtitle') !!}">
                        </div>
                        <div class="form-group">
                            <label for="description">{{ __('Description') }}</label>
                            <x-adminlte-text-editor name="home_hero_description" id="description" class="form-control">
                                {!! get_setting('home_hero_description') !!}
                            </x-adminlte-text-editor>
                        </div>

                        <div class="">
                            <x-file-uploader pondName="setting_image" pondID="setting-image" pondCollection="setting_image"
                                pondInstanceName="SettingImage" pondLable="Upload Image"
                                inputLabel="Image (Size: 250x250px)" />
                            <small class="field-notice text-danger" rel="setting_image"></small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-dark w-100">{{ __('Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Meta Information') }}</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('site.settings.update') }}" method="POST" class="ajax-form">
                        @csrf
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
                            <x-adminlte-input name="home_meta_title" type="text" placeholder="Meta Title"
                                label="Meta Title" value="{!! get_setting('home_meta_title') !!}" />
                            <small class="field-notice text-danger" rel="home_meta_title"></small>
                        </div>
                        <div class="">
                            <x-adminlte-textarea name="home_meta_description" placeholder="Meta Description"
                                label="Meta Description">
                                {!! get_setting('home_meta_description') !!}
                            </x-adminlte-textarea>
                            <small class="field-notice text-danger" rel="home_meta_description"></small>
                        </div>
                        <div class="">
                            <x-adminlte-button type="submit" label="Submit" theme="dark" class="btn-block" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('common.scripts')
@endpush
