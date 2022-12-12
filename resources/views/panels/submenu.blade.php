{{-- For submenu --}}
<ul class="menu-content">
  @if(isset($menu))
  @foreach($menu as $submenu)
  <li @if($submenu->slug === Route::currentRouteName()) class="active" @endif>
    <a href="{{isset($submenu->url) ? url($submenu->url):'javascript:void(0)'}}" class="d-flex align-items-center" target="{{isset($submenu->newTab) && $submenu->newTab === true  ? '_blank':'_self'}}">
      @if(isset($submenu->icon))
      <i data-feather="{{$submenu->icon}}"></i>
      @endif
      <span class="menu-item text-truncate">{{ __('locale.'.$submenu->name) }}</span>
      @if ((!empty($submenu->classlist) && $submenu->classlist == 'Pending'))
        <span class="badge rounded-pill badge-light-danger ms-auto me-1 @if(Helper::getPendingComic() == 0) d-none @endif">{!! Helper::getPendingComic() !!}</span>
      @endif
      @if ((!empty($submenu->classlist) && $submenu->classlist == 'pendingpublisher'))
        <span class="badge rounded-pill badge-light-danger ms-auto me-1 @if(Helper::getPendingPublisher() == 0) d-none @endif">{!! Helper::getPendingPublisher() !!}</span>
      @endif
      @if ((!empty($submenu->classlist) && $submenu->classlist == 'pending-episode'))
        <span class="badge rounded-pill badge-light-danger ms-auto me-1 @if(Helper::getPendingEpisode() == 0) d-none @endif">{!! Helper::getPendingEpisode() !!}</span>
      @endif
    </a>
    @if (isset($submenu->submenu))
    @include('panels/submenu', ['menu' => $submenu->submenu])
    @endif
  </li>
  @endforeach
  @endif
</ul>
