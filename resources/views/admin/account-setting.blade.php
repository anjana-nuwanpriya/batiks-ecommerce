@extends('adminlte::page')
@section('title', 'Account Settings - ' . env('APP_NAME'))
@section('plugins.Chartjs', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ __('Account Settings') }}</h1>
        </div>
        <div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i
                            class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item active">{{ __('Account Settings') }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <section class="card">
        <div class="card-body">
            <form action="{{ route('admin.update.account.info') }}" method="POST" class="ajax-form">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" class="form-control"
                        value="{{ auth()->user()->name }}">
                    <span class="field-notice text-danger" rel="name"></span>
                </div>
                <div class="form-group">
                    <label for="email">{{ __('Email') }}</label>
                    <input type="email" name="email" id="email" class="form-control"
                        value="{{ auth()->user()->email }}">
                    <span class="field-notice text-danger" rel="email"></span>
                </div>
                <div class="form-group">
                    <label for="phone">{{ __('Phone') }}</label>
                    <input type="text" name="phone" id="phone" class="form-control"
                        value="{{ auth()->user()->phone }}">
                    <span class="field-notice text-danger" rel="phone"></span>
                </div>
                <div class="form-group">
                    <label for="password">{{ __('Password') }}</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password"
                        autocomplete="off">
                    <span class="field-notice text-danger" rel="password"></span>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">{{ __('Password Confirmation') }}</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                        placeholder="Password Confirmation" autocomplete="off">
                </div>
                <button type="submit" class="btn btn-dark">{{ __('Update') }}</button>
            </form>
        </div>
    </section>

@endsection

@push('js')
    @include('common.scripts')
@endpush
