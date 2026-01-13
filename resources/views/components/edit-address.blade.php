<div class="modal-content">
    <div class="modal-header">
        <h1 class="modal-title fs-5" id="editAddressModalLabel">Edit Address</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <form action="{{ route('user.update.address', $address->id) }}" method="post" class="ajax-form" novalidate>
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="">Address <span class="text-danger">*</span></label>
                <textarea name="address" id="address" class="form-control" rows="2">{{ $address->address }}</textarea>
                <span class="text-danger field-notice" rel="address"></span>
            </div>

            <div class="mb-3">
                <label for="">Zip/Postal Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="zip_code" value="{{ $address->zip_code }}">
                <span class="text-danger field-notice" rel="zip_code"></span>
            </div>

            <div class="mb-3">
                <label for="">City <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="city" value="{{ $address->city }}">
                <span class="text-danger field-notice" rel="city"></span>
            </div>

            <div class="mb-3">
                <label for="state">Province / State <span class="text-danger">*</span></label>
                <input
                    type="text"
                    class="form-control"
                    name="state"
                    id="state"
                    value="{{ $address->state }}"
                    list="provinceList"
                >

                <datalist id="provinceList">
                    <option value="Central">
                    <option value="Eastern">
                    <option value="Northern">
                    <option value="North Central">
                    <option value="North Western">
                    <option value="Sabaragamuwa">
                    <option value="Southern">
                    <option value="Uva">
                    <option value="Western">
                </datalist>

                <span class="text-danger field-notice" rel="state"></span>
            </div>

            <div class="mb-3">
                <label for="">Country <span class="text-danger">*</span></label>
                <select name="country" class="form-select">
                    <option value="">Select Country</option>
                    <option value="Sri Lanka" {{ $address->country == 'Sri Lanka' ? 'selected' : '' }}>Sri Lanka</option>
                </select>
                <span class="text-danger field-notice" rel="country"></span>
            </div>

            <div class="mb-3">
                <label for="">Phone <span class="text-danger">*</span></label>
                <input type="tel" class="form-control" name="phone" value="{{ $address->phone }}">
                <span class="text-danger field-notice" rel="phone"></span>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-success w-100">Save</button>
            </div>
        </form>
    </div>
</div>