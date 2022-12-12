@php
$configData = Helper::applClasses();
@endphp
<div class="main-menu menu-fixed {{(($configData['theme'] === 'dark') || ($configData['theme'] === 'semi-dark')) ? 'menu-dark' : 'menu-light'}} menu-accordion menu-shadow" data-scroll-to-active="true">
  <div class="navbar-header">
    <ul class="nav navbar-nav flex-row">
      <li class="nav-item me-auto">
        <a class="navbar-brand" href="{{url('/admin/home')}}">
         
          <h2 class="brand-text">Vivid Panel</h2>
        </a>
      </li>
      <!-- <li class="nav-item nav-toggle">
        <a class="nav-link modern-nav-toggle pe-0" data-toggle="collapse">
          <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
          <i class="d-none d-xl-block collapse-toggle-icon font-medium-4 text-primary" data-feather="disc" data-ticon="disc"></i>
        </a>
      </li> -->
    </ul>
  </div>
  <div class="shadow-bottom"></div>
  <div class="main-menu-content">
    <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
      {{-- Foreach menu item starts --}}
      @if(isset($menuData[0]))
      @foreach($menuData[0]->menu as $menu)
       
      @if(isset($menu->navheader))
      <li class="navigation-header">
        <span>{{ __('locale.'.$menu->navheader) }}</span>
        <i data-feather="more-horizontal"></i>
      </li>
      @else
      {{-- Add Custom Class with nav-item --}}
      @php
      $custom_classes = "";
      if(isset($menu->classlist)) {
      $custom_classes = $menu->classlist;
      }
  
      @endphp
      <li class="nav-item {{ $custom_classes }} {{Route::currentRouteName() === $menu->slug ? 'active' : ''}}">
        <a href="{{isset($menu->url)? url($menu->url):'javascript:void(0)'}}" class="d-flex align-items-center" target="{{isset($menu->newTab) ? '_blank':'_self'}}">
          <i data-feather="{{ $menu->icon }}"></i>
          <span class="menu-title text-truncate">{{ __('locale.'.$menu->name) }}</span>
          <!-- @if (isset($menu->badge))
          <?php $badgeClasses = "badge rounded-pill badge-light-primary ms-auto me-1" ?>
          <span class="{{ isset($menu->badgeClass) ? $menu->badgeClass : $badgeClasses }}">{{$menu->badge}}</span>
          @endif -->
          @if ((!empty($menu->classlist) && $menu->classlist == 'Comics'))
            <span class="badge rounded-pill badge-light-danger ms-auto me-1 @if(Helper::getPendingComicAndEpisode() == 0) d-none @endif">{!! Helper::getPendingComicAndEpisode() !!}</span>
          @endif
          @if ((!empty($menu->classlist) && $menu->classlist == 'Publishers'))
            <span class="badge rounded-pill badge-light-danger ms-auto me-1 @if(Helper::getPendingPublisher() == 0) d-none @endif">{!! Helper::getPendingPublisher() !!}</span>
          @endif
        </a>
        @if(isset($menu->submenu))
        @include('panels/submenu', ['menu' => $menu->submenu])
        @endif
        
      </li>
      @endif
      @endforeach
      @endif
      {{-- Foreach menu item ends --}}
    </ul>
  </div>
</div>
<!-- END: Main Menu-->
