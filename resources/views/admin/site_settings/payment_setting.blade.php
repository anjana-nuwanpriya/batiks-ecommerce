@extends('adminlte::page')
@include('adminlte::filepond.file-pond', ['preview' => true])
@section('title', 'Payment Settings - '.env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)
@section('plugins.FilePond', true)


@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ __('Payment Settings') }}</h5>
        </div>
        <div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item active">{{ __('Payment Settings') }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <section class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <h5 class="font-weight-bold">{{ __('Payhere Credentials') }}</h5>
                            </div>

                            <form action="{{ route('save.settings') }}" class="d-block mt-3" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="payhere_merchant_id">{{ __('Merchant ID') }}</label>
                                    <input type="text" class="form-control" id="payhere_merchant_id" name="PAYHERE_MERCHANT_ID" value="{{ env('PAYHERE_MERCHANT_ID') }}">
                                </div>
                                <div class="form-group">
                                    <label for="payhere_secret_key">{{ __('Secret Key') }}</label>
                                    <input type="text" class="form-control" id="payhere_secret_key" name="PAYHERE_MERCHANT_SECRET" value="{{ env('PAYHERE_MERCHANT_SECRET') }}">
                                </div>
                                <div class="form-group">
                                    <label for="payhere_currency">{{ __('Currency') }}</label>
                                    <input type="text" class="form-control" id="payhere_currency" name="PAYHERE_CURRENCY" value="{{ env('PAYHERE_CURRENCY') }}">
                                </div>
                                <div class="form-group">
                                    <label for="payhere_sandbox_mode">{{ __('Sandbox Mode') }}</label>
                                    <select class="form-control" id="payhere_sandbox_mode" name="PAYHERE_SANDBOX_MODE">
                                        <option value="0" {{ env('PAYHERE_SANDBOX_MODE') == 0 ? 'selected' : '' }}>{{ __('Live') }}</option>
                                        <option value="1" {{ env('PAYHERE_SANDBOX_MODE') == 1 ? 'selected' : '' }}>{{ __('Sandbox') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="payhere_convenience_fee">{{ __('Convenience Fee (%)') }}</label>
                                    <input type="number" class="form-control" id="payhere_convenience_fee" name="PAYHERE_CONVENIENCE_FEE" value="{{ env('PAYHERE_CONVENIENCE_FEE') }}" min="0" max="100" step="0.01">
                                    <small class="text-muted">Enter percentage value (e.g. 2.5 for 2.5%)</small>
                                </div>
                                <button type="submit" class="btn btn-dark btn-sm w-100">{{ __('Save') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <h5 class="font-weight-bold">{{ __('Bank Information') }}</h5>
                            </div>

                            <form action="{{ route('save.settings') }}" class="d-block mt-3" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="bank_name">{{ __('Bank Name') }}</label>
                                    <input type="text" class="form-control" id="bank_name" name="BANK_NAME" value="{{ env('BANK_NAME') }}">
                                </div>
                                <div class="form-group">
                                    <label for="account_name">{{ __('Account Name') }}</label>
                                    <input type="text" class="form-control" id="account_name" name="BANK_ACCOUNT_NAME" value="{{ env('BANK_ACCOUNT_NAME') }}">
                                </div>
                                <div class="form-group">
                                    <label for="account_number">{{ __('Account Number') }}</label>
                                    <input type="text" class="form-control" id="account_number" name="BANK_ACCOUNT_NUMBER" value="{{ env('BANK_ACCOUNT_NUMBER') }}">
                                </div>
                                <div class="form-group">
                                    <label for="branch_name">{{ __('Branch Name') }}</label>
                                    <input type="text" class="form-control" id="branch_name" name="BANK_BRANCH_NAME" value="{{ env('BANK_BRANCH_NAME') }}">
                                </div>
                                <div class="form-group">
                                    <label for="swift_code">{{ __('Swift Code') }}</label>
                                    <input type="text" class="form-control" id="swift_code" name="BANK_SWIFT_CODE" value="{{ env('BANK_SWIFT_CODE') }}">
                                </div>
                                <div class="form-group">
                                    <label for="note">{{ __('Note') }}</label>
                                    <textarea class="form-control" id="note" name="BANK_NOTE" rows="3">{{ env('BANK_NOTE') }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-dark btn-sm w-100">{{ __('Save') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

