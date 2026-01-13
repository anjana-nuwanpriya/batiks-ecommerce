<div class="sidebar py-3 bg-white">

    <div class="d-flex align-items-center px-2 pt-2 pb-3 border-bottom mb-5">
        <div class="flex-shrink-0">
          <img src="https://preview-dashboard-ux-enhancements-kzmk6u2fnjdhbivgj0iu.vusercontent.net/placeholder.svg?height=48&width=48" alt="{{ Auth::user()->name }}" class="rounded-circle" width="50" height="50">
        </div>
        <div class="flex-grow-1 ms-3">
          <h6 class="mb-0 fw-medium">{{ Auth::user()->name }}</h6>
          @if (!empty(Auth::user()->email))
          <p class="mb-0">{{ Auth::user()->email }}</p>
          @else
          <p class="mb-0 fs-12">{{ Auth::user()->phone }}</p>
          @endif
        </div>
      </div>

    <nav class="nav flex-column">
        <a class="nav-link {{ Route::is('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">
            <i class="las la-tachometer-alt"></i> {{ __('Dashboard') }}
        </a>
        <a class="nav-link {{ Route::is('user.order-list') ? 'active' : '' }}" href="{{ route('user.order-list') }}">
            <i class="las la-history"></i>  {{ __('Purchase History') }}
        </a>
        <a class="nav-link {{ Route::is('user.wishlist') ? 'active' : '' }}" href="{{ route('user.wishlist') }}">
            <i class="las la-heart"></i> {{ __('Wishlist') }}
        </a>
        <a class="nav-link {{ Route::is('user.manage.account') ? 'active' : '' }}" href="{{ route('user.manage.account') }}">
            <i class="las la-cog"></i> {{ __('Manage Account') }}
        </a>
        <a class="nav-link" href="{{ route('logout') }}">
            <i class="las la-sign-out-alt"></i> {{ __('Log-out') }}
        </a>
    </nav>
</div>
