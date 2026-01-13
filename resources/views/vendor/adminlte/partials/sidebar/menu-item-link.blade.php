<li @isset($item['id']) id="{{ $item['id'] }}" @endisset class="nav-item">

    <a class="nav-link {{ $item['class'] }} @isset($item['shift']) {{ $item['shift'] }} @endisset"
       href="{{ $item['href'] }}" @isset($item['target']) target="{{ $item['target'] }}" @endisset
       {!! $item['data-compiled'] ?? '' !!}>

        <i class="{{ $item['icon'] ?? 'far fa-fw fa-circle' }} {{
            isset($item['icon_color']) ? 'text-'.$item['icon_color'] : ''
        }}"></i>

        <p>
            {{ $item['text'] }}

            @isset($item['label'])
                    @if ($item['text'] == 'Inquiries')
                        @php
                            $inquiries = \App\Models\Inqiry::where('is_read', 0)->count();
                        @endphp
                        @if ($inquiries > 0)
                        <span class="badge badge-{{ $item['label_color'] ?? 'primary' }} right">
                            {{ Str::padLeft($inquiries, 2, '0') }}
                        </span>
                        @endif

                    @elseif ($item['text'] == 'Customer Orders')
                    @else
                    <span class="badge badge-{{ $item['label_color'] ?? 'primary' }} right">
                        {{ $item['label'] }}
                    </span>
                    @endif
            @endisset
        </p>

    </a>

</li>
