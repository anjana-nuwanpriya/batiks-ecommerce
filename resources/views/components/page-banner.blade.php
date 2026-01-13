<section class="page-banner" style="background-image: url({{ $backgroundImage }})">
    <div class="page-banner__container container">
        <nav class="page-banner__breadcrumb-nav" style="--bs-breadcrumb-divider: '|';"  aria-label="breadcrumb">
            <ol class="page-banner__breadcrumb breadcrumb mb-0">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="page-banner__breadcrumb-item breadcrumb-item {{ $loop->last ? 'active' : '' }}"
                        {{ $loop->last ? 'aria-current=page' : '' }}>
                        @if (!$loop->last)
                            <a class="page-banner__breadcrumb-link" href="{{ $breadcrumb['url'] }}">{!! $breadcrumb['label'] !!}</a>
                        @else
                            {!! $breadcrumb['label'] !!}
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    </div>
</section>
