<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false" title="{{ __('general.language') }}">
        @if(app()->getLocale() === 'id')
            🇮🇩 <span class="d-none d-md-inline">ID</span>
        @else
            🇺🇸 <span class="d-none d-md-inline">EN</span>
        @endif
        <i class="fas fa-caret-down ml-1"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right" style="min-width: 160px;">
        <h6 class="dropdown-header">
            <i class="fas fa-globe mr-1"></i> {{ __('general.language') }}
        </h6>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ route('lang.switch', 'en') }}">
            🇺🇸 {{ __('general.english') }}
            @if(app()->getLocale() === 'en')
                <i class="fas fa-check float-right mt-1 text-success"></i>
            @endif
        </a>
        <a class="dropdown-item {{ app()->getLocale() === 'id' ? 'active' : '' }}" href="{{ route('lang.switch', 'id') }}">
            🇮🇩 {{ __('general.indonesian') }}
            @if(app()->getLocale() === 'id')
                <i class="fas fa-check float-right mt-1 text-success"></i>
            @endif
        </a>
    </div>
</li>
