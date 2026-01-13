<form method="post" action="{{ $payment['url'] }}">
    @foreach($payment['params'] as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('form').submit();
    });
</script>
