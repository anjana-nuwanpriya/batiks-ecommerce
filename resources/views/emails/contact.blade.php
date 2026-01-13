@component('mail::message')
# New Contact Form Submission

**Name:** {{ $data['name'] }}

**Email:** {{ $data['email'] }}

**Phone:** {{ $data['phone'] }}

**Subject:** {{ ucfirst($data['subject']) }}

**Message:**

{{ $data['message'] }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
