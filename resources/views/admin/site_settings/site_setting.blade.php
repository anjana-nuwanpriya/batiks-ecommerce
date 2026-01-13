@extends('adminlte::page')
@include('adminlte::filepond.file-pond', ['preview' => true])
@section('title', 'Site Settings - ' . env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)
@section('plugins.FilePond', true)


@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ __('Site Settings') }}</h5>
        </div>
        <div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i
                            class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item active">{{ __('Site Settings') }}</li>
            </ol>
        </div>
    </div>
@endsection



@section('content')
    <section class="card">
        <div class="card-body">
            <h5>{{ __('Site Settings') }}</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="font-weight-bold">{{ __('General Information') }}</h6>
                            <hr>
                            <form action="{{ route('save.general.settings') }}" class="ajax-form" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="email">{{ __('Email') }}</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="{{ __('Email') }}" value="{{ $general_settings['email'] }}">
                                </div>
                                <div class="form-group">
                                    <label for="phone">{{ __('Primary Phone') }}</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        placeholder="{{ __('Phone') }}" value="{{ $general_settings['phone'] }}">
                                </div>
                                <div class="form-group">
                                    <label for="secondary_phone">{{ __('Secondary Phone') }}</label>
                                    <input type="text" class="form-control" id="secondary_phone" name="secondary_phone"
                                        placeholder="{{ __('Secondary Phone') }}"
                                        value="{{ $general_settings['secondary_phone'] }}">
                                </div>
                                <div class="form-group">
                                    <label for="whatsapp">{{ __('WhatsApp') }}</label>
                                    <input type="text" class="form-control" id="whatsapp" name="whatsapp"
                                        placeholder="{{ __('WhatsApp Number') }}"
                                        value="{{ $general_settings['whatsapp'] }}">
                                </div>
                                <div class="form-group">
                                    <label for="admin_notification_phones">{{ __('Admin Notification Phones') }}</label>
                                    <textarea class="form-control" id="admin_notification_phones" name="admin_notification_phones" rows="3"
                                        placeholder="{{ __('Enter phone numbers separated by commas (e.g., 0771234567, 0777654321)') }}">{{ $general_settings['admin_notification_phones'] ?? '' }}</textarea>
                                    <small class="form-text text-muted">{{ __('These phones will receive SMS notifications for important admin alerts') }}</small>
                                </div>
                                <div class="form-group">
                                    <label for="address">{{ __('Address') }}</label>
                                    <textarea class="form-control" id="address" name="address" rows="5" placeholder="{{ __('Address') }}">{{ $general_settings['address'] }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="other_address_one">{{ __('Other Address') }}</label>
                                    <textarea class="form-control" id="other_address_one" name="other_address_one" rows="5"
                                        placeholder="{{ __('Address') }}">{{ $general_settings['other_address_one'] ?? '' }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="other_address_two">{{ __('Other Address') }}</label>
                                    <textarea class="form-control" id="other_address_two" name="other_address_two" rows="5"
                                        placeholder="{{ __('Address') }}">{{ $general_settings['other_address_two'] ?? '' }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="map_link">{{ __('Google Map Link') }}</label>
                                    <input type="text" class="form-control" id="map_link" name="map_link"
                                        placeholder="{{ __('Google Map Embed Link') }}"
                                        value="{{ $general_settings['map_link'] }}">
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Map Preview') }}</label>
                                    <textarea class="form-control" id="map_preview" name="map_preview" placeholder="{{ __('Map Preview') }}">{{ $general_settings['map_preview'] }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-dark btn-block">{{ __('Save') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    {{-- Email Test --}}
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('test.email') }}" method="post" class="ajax-form">
                                @csrf
                                <h6 class="font-weight-bold">{{ __('Email Test') }}</h6>
                                <hr>
                                <div class="form-group">
                                    <label for="test_email">{{ __('Test Email') }}</label>
                                    <input type="email" class="form-control" id="test_email" name="test_email"
                                        placeholder="{{ __('Test Email') }}" value="">
                                </div>
                                <button type="submit"
                                    class="btn btn-dark btn-block">{{ __('Send Test Email') }}</button>
                            </form>
                        </div>
                    </div>

                    {{-- SMS Test --}}
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('test.sms') }}" method="post" class="ajax-form">
                                @csrf
                                <h6 class="font-weight-bold">{{ __('SMS Test') }}</h6>
                                <hr>
                                <div class="form-group">
                                    <label for="test_phone">{{ __('Test Phone Number') }}</label>
                                    <input type="text" class="form-control" id="test_phone" name="test_phone"
                                        placeholder="{{ __('e.g., +94771234567 or 0771234567') }}" value="">
                                    <small class="form-text text-muted">{{ __('Enter Sri Lankan phone number') }}</small>
                                </div>
                                <div class="form-group">
                                    <label for="test_message">{{ __('Test Message') }}</label>
                                    <textarea class="form-control" id="test_message" name="test_message" rows="3"
                                        placeholder="{{ __('Enter test message (optional)') }}">Test SMS from {{ config('app.name') }} - {{ now()->format('Y-m-d H:i:s') }}</textarea>
                                    <small class="form-text text-muted">{{ __('Leave empty for default test message') }}</small>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-sms"></i> {{ __('Send Test SMS') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- SMS Service Status --}}
                    <div class="card">
                        <div class="card-body">
                            <h6 class="font-weight-bold">{{ __('SMS Service Status') }}</h6>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-cog"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{ __('Service Status') }}</span>
                                            <span class="info-box-number">
                                                @if(env('SMS_SERVICE'))
                                                    <span class="badge badge-success">{{ __('Enabled') }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ __('Disabled') }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-warning"><i class="fas fa-key"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{ __('Configuration') }}</span>
                                            <span class="info-box-number">
                                                @if(env('HUTCH_SMS_USERNAME') && env('HUTCH_SMS_PASSWORD'))
                                                    <span class="badge badge-success">{{ __('Configured') }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ __('Not Configured') }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('sms.status.page') }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-info-circle"></i> {{ __('View Detailed Status') }}
                                </a>
                                <a href="{{ route('sms.clear.tokens') }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-trash"></i> {{ __('Clear SMS Tokens') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Site Social Media --}}
                    <div class="card">
                        <div class="card-body">
                            <h6 class="font-weight-bold">{{ __('Social Media') }}</h6>
                            <hr>
                            <form action="{{ route('save.social.settings') }}" method="post" class="ajax-form">
                                @csrf
                                <div class="form-group">
                                    <label for="facebook">{{ __('Facebook') }}</label>
                                    <input type="text" class="form-control" id="facebook" name="facebook"
                                        placeholder="{{ __('Facebook') }}" value="{{ $general_settings['facebook'] }}">
                                </div>
                                <div class="form-group">
                                    <label for="linkedin">{{ __('LinkedIn') }}</label>
                                    <input type="text" class="form-control" id="linkedin" name="linkedin"
                                        placeholder="{{ __('LinkedIn') }}" value="{{ $general_settings['linkedin'] }}">
                                </div>
                                <div class="form-group">
                                    <label for="instagram">{{ __('Instagram') }}</label>
                                    <input type="text" class="form-control" id="instagram" name="instagram"
                                        placeholder="{{ __('Instagram') }}"
                                        value="{{ $general_settings['instagram'] }}">
                                </div>
                                <div class="form-group">
                                    <label for="tiktok">{{ __('TikTok') }}</label>
                                    <input type="text" class="form-control" id="tiktok" name="tiktok"
                                        placeholder="{{ __('TikTok') }}" value="{{ $general_settings['tiktok'] }}">
                                </div>
                                <button type="submit" class="btn btn-dark btn-block">{{ __('Save') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    @include('common.scripts')
@endpush
