<!DOCTYPE html>
<html>

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>@yield('title') - Vivid</title>
   <link rel="stylesheet" href="{{asset('front/css/bootstrap.min.css')}}">
   <link rel='stylesheet' href="{{asset('front/css/owl.carousel.min.css')}}">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
   <link rel='stylesheet' href='//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
   <link rel='stylesheet' href="{{asset('front/css/flatpickr.min.css')}}">
   <link rel="icon" href="{{asset('images/logo/favicon.png')}}" type="image/gif" />
   <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">
   @yield('vendor-styles')
   <link rel="stylesheet" type="text/css" href="{{asset('front/css/style.css')}}">
   <link rel="stylesheet" type="text/css" href="{{asset('front/css/main.css')}}">
   <link rel="stylesheet" href="{{ URL::asset('js/custom/library/toastr.min.css') }}">
   <link rel="stylesheet" href="{{ URL::asset('css/custom.css') }}">
   <link rel="stylesheet" href="//cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
   <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-drag-drop.css')) }}">
   <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/dragula.min.css')) }}">
   @yield('styles')
   <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
</head>

<body>
   <?php
   $todayDate = date("Y-m-d");
   $ajaxUrl = url('/');
   ?>
   <script>
      var todayDate = "<?php echo $todayDate; ?>";
      var ajaxUrl = "<?php echo $ajaxUrl; ?>";
   </script>
   <style>
      .orignl-badge {
         background-color: #f0c556 !important;
      }
   </style>

   <body class="main-layout">
      <header class="bgcolorr">
         <div class="container">
            <div class="row">
               <nav class="navbar navbar-expand-md navbar-dark {{ request()->route('id') == 'mobile' ? 'd-none' : '' }} ">
                  <div class="container-fluid">
                     <a class="navbar-brand" href="{{route('home-page')}}">
                        <img src="{{asset('front/images/logo.svg')}}" />
                     </a>
                     <div class="mobilesearchcombo">
                        <div class="mobilesearch">
                           <form class="me-auto ml-2 headersearch" action="{{url('search-comics')}}" method="get">
                              <div class="input-group filter-search ">
                                 <input type="text" class="input-search search-field" placeholder="Search..." tabindex="-1" aria-label="Search..." aria-describedby="input-search" name="serach_text" id="serach_text" aria-required="false" autocomplete="off">
                                 <button type="submit" class="hidebutton"><img id="arrrowsearchbutton" src="{{asset('front/images/searchsubmit.png')}}" height="14" width="17" style="display:none" /></button>
                                 <button class="search-submit desktopsearch" type="button">
                                    <span class="input-group-text" id="input-search">
                                       <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                          <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path>
                                       </svg>
                                    </span>
                                 </button>
                              </div>
                           </form>
                        </div>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample04" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
                           <span class="navbar-toggler-icon"></span>
                        </button>
                        @if(!empty(Auth::User()->name) && Auth::User()->role == 2)
                        <ul class="mobprofile">
                           <li class="nav-item dropdown">
                              <a class="nav-link dropdown-toggle userprodrop" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                 <i class="fa fa-user" aria-hidden="true"></i>
                              </a>
                              <ul class="dropdown-menu">
                                 <li class="dropdown_items">
                                    <a class="dropdown-item" href="javascript:void(0);"><b>Hey {{Str::limit(Auth::user()->name,6) }}</b></a>

                                 </li>
                                 <li class="dropdown_items">
                                    <a class="dropdown-item" href="{{ route('my-library') }}">
                                       <img src="{{asset('front/images/libraryuser.png')}}">
                                       My Library
                                    </a>
                                 </li>
                                 <li class="dropdown_items feather-user">
                                    <a class="dropdown-item" href="{{ route('my-account') }}">
                                       <img src="{{asset('front/images/account.png')}}">
                                       My Account
                                    </a>
                                 </li>
                                 <li class="dropdown_items">
                                    <a class="dropdown-item" href="{{route('my-order')}}">
                                       <img src="{{asset('front/images/order.png')}}">
                                       My Orders
                                    </a>
                                 </li>
                                 <li class="dropdown_items">
                                    <a class="dropdown-item logoutanc" data-bs-toggle="modal" data-bs-target="#logoutmodel">
                                       <img src="{{asset('front/images/logout1.png')}}">
                                       Logout
                                    </a>
                                 </li>
                              </ul>
                           </li>
                        </ul>
                        @elseif(!empty(Auth::User()->name) && Auth::User()->role == 3)
                        <ul class="mobprofile">
                           <li class="nav-item dropdown">
                              <a class="nav-link dropdown-toggle userprodrop" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                 <i class="fa fa-user" aria-hidden="true"></i>
                              </a>
                              <ul class="dropdown-menu">
                                 <li class="dropdown_items">
                                    <a class="dropdown-item" href="javscript:void(0)"><b> Hey {{Str::limit(Auth::user()->name,6) }}</b></a>
                                 </li>
                                 <li class="dropdown_items">
                                    <a class="dropdown-item" href="{{ route('my-dashboard') }}">
                                       <img src="{{asset('front/images/dashboard.png')}}">
                                       Publisher Portal
                                    </a>
                                 </li>
                                 <li class="dropdown_items">
                                    <a class="dropdown-item" href="{{ route('my-comic') }}">
                                       <img src="{{asset('front/images/comic.png')}}">
                                       My Comics
                                    </a>
                                 </li>
                                 <li class="dropdown_items feather-user">
                                    <a class="dropdown-item" href="{{ route('my-accounts') }}">
                                       <img src="{{asset('front/images/account.png')}}">
                                       My Account
                                    </a>
                                 </li>
                                 <li class="dropdown_items feather-user">
                                    <a class="dropdown-item" href="{{ route('my-wallet') }}">
                                       <img src="{{asset('front/images/wallet.png')}}">
                                       My Wallet
                                    </a>
                                 <li class="dropdown_items feather-user">
                                    <a class="dropdown-item" href="{{ route('my-referral') }}">
                                       <img src="{{asset('front/images/refferal.png')}}">
                                       My Earnings
                                    </a>
                                 </li>
                                 <li class="dropdown_items">
                                    <a class="dropdown-item logoutanc" data-bs-toggle="modal" data-bs-target="#logoutmodel">
                                       <img src="{{asset('front/images/logout1.png')}}">
                                       Logout
                                    </a>
                                 </li>
                              </ul>
                           </li>
                        </ul>
                        @endif
                     </div>
                     <input type="hidden" id="checkPage" value="{{ (request()->is('*home*') || request()->is('/')) ? 'home-page' : '' }}">
                     <div class="collapse navbar-collapse" id="navbarsExample04">
                        <ul class="navbar-nav  mb-2 mb-md-0">
                           <li class="nav-item">
                              <a class="nav-link {{ (request()->is('*home*') || request()->is('/')) ? 'active' : '' }}" aria-current="page" href="{{ route('home-page') }}">Home</a>
                           </li>


                           <li class="nav-item dropdown genres">
                              <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                 Genres
                              </a>
                              <ul class="dropdown-menu genress">
                                 @php
                                 $category = Helper::getCategories();

                                 //print_r($category->toArray());exit;


                                 @endphp
                                 @foreach($category as $categorys)
                                 <li><a href="{{url('genres')}}/{{$categorys['slug'] ?? 0}}">{{$categorys['category_name']}} ({{ $categorys['active_series'] }})</a></li>
                                 @endforeach

                              </ul>
                           </li>
                           @if((!empty(Auth::User()->name) && Auth::User()->role != 3) || empty(Auth::User()->name))
                           <li class="nav-item">
                              <a class="nav-link {{ request()->is('*store*') ? 'active' : '' }}" href="{{route('store')}}">Store</a>
                           </li>
                           @endif
                           <li class="nav-item">
                              <a class="nav-link {{ request()->is('*publisher*') ? 'active' : '' }}" href="{{route('publisher')}}">Publishers</a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link {{ request()->is('*about*') ? 'active' : '' }}" href="{{route('about')}}">About Us</a>
                           </li>
                        </ul>
                        <div class="me-auto ml-2">
                        </div>
                        <div class="text-end1 loginheader mobileview d-flex justify-content-around" id="normalLogin">
                           <div class="headersearc" style="margin-right:20px;">
                              <form class="me-auto ml-2 headersearch" action="{{url('search-comics')}}" method="get">
                                 <div class="input-group filter-search ">
                                    <input type="text" class="input-search search-field" placeholder="Search..." tabindex="-1" aria-label="Search..." aria-describedby="input-search" name="serach_text" id="serach_text" aria-required="false" autocomplete="off">
                                    <button type="submit" class="hidebutton"><img id="arrrowsearchbutton" src="{{asset('front/images/searchsubmit.png')}}" height="14" width="17" style="display:none" /></button>
                                    <button class="search-submit desktopsearch" type="button">
                                       <span class="input-group-text" id="input-search">
                                          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                             <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path>
                                          </svg>
                                       </span>
                                    </button>
                                 </div>
                              </form>
                           </div>
                           @if(!empty(Auth::User()->name) && Auth::User()->role == 2)
                           @if(Helper::getCustomerType(Auth::User()->id) == 1)
                           <a href="{{route('subscription-page')}}" class="btn btn-primary me-2 masterpublish">Subscribe</a>
                           @endif
                           <ul class="righthead">
                              <!-- <li class="nav-item dropdown
                                 ">
                                 <a class="nav-link" href=" @if(!empty(Helper::countCart(Auth::User()->id))) {{route('buy-now')}} @else # @endif" >
                                 @if(!empty(Helper::countCart(Auth::User()->id)))
                                 <i class="fa fa-shopping-cart" aria-hidden="true"></i> <span class="badge rounded-pill bg-primary badge-up cartvalue">{{Helper::countCart(Auth::User()->id)}}</span>
                                 @else
                                 <i class="fa fa-shopping-cart emptyCart" aria-hidden="true" ></i> <span class="badge rounded-pill bg-primary badge-up cartvalue"></span>
                                 @endif
                                 </a>
                              </li> -->
                              @if(!empty(Helper::countCart(Auth::User()->id)))
                              <li class="nav-item dropdown
                                 ">
                                 <a class="nav-link" href="{{route('buy-now')}}">
                                    <i class="fa fa-shopping-cart" aria-hidden="true"></i> <span class="badge rounded-pill bg-primary badge-up cartvalue">{{Helper::countCart(Auth::User()->id)}}</span>
                                 </a>
                              </li>
                              @else
                              <li class="nav-item dropdown">
                                 <a class="nav-link dropdown-toggle" href="" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                 </a>

                                 <ul class="dropdown-menu notificationWeb changemob">
                                    <li class="dropdown-menu-header">
                                       <div class="dropdown-header d-flex">
                                          <h4 class="notification-title mb-0 me-auto">Cart</h4>
                                       </div>
                                    </li>
                                    <li class="scrollable-container media-list ps">
                                       <img class="notificationimage mobimg emptymasterrj" src="{{asset('front/images/emptycart3.png') }}">
                                    </li>

                                 </ul>
                              </li>
                              @endif

                              <li class="nav-item dropdown
                                 ">
                                 <a class="nav-link coina" href="{{ route('my-coins') }}">
                                    <img class="coinimg" src="{{ asset('front/images/v-coins.png') }}"> <span class="coinval"> {{ Helper::getcoinsTotal(Auth::user()->id) ?? 0}}</span>
                                 </a>
                              </li>

                              <li class="nav-item dropdown removearrow" id="notificationhide">
                                 <a class="nav-link dropdown-toggle notificatinBadge" href=""  data-bs-toggle="dropdown" aria-expanded="false">
                                 <i class="fa fa-bell-o" aria-hidden="true"></i>
                                 @if(Helper::getNotificationCount(Auth::user()->id))
                                 <span class="badge rounded-pill bg-primary badge-up cartvalue">{{Helper::getNotificationCount(Auth::user()->id)}}</span>
                                 @endif
                                 </a>
                                 <ul class="dropdown-menu notificationWeb">
                                    <li class="dropdown-menu-header">
                                       <div class="dropdown-header d-flex">
                                          <h4 class="notification-title mb-0 me-auto">Notifications</h4>
                                       </div>
                                    </li>

                                    @php
                                    if(!empty(Auth::User()->name) && Auth::User()->role == 2)
                                    {
                                    $getNotification = Helper::getNotification(Auth::user()->id);
                                    }
                                    @endphp

                                    @if(!empty($getNotification[0]->title))
                                    <li class="scrollable-container media-list ps appendNotificationData">
                                       @foreach($getNotification as $getNotify)
                                       <a class="d-flex" @if(!empty($getNotify->slug)) href="{{ url('comic-detail')}}/{{$getNotify->slug}}" @else href="#" @endif>
                                          <div class="list-item d-flex align-items-start">
                                             <div class="me-2">
                                                <div class="avatar">
                                                </div>
                                                <div class="list-item-body d-flex flex-grow-1">
                                                   <div class="notified">
                                                      <h6>{{ $getNotify->title  }}</h6>
                                                      <p class="media-heading">{{ $getNotify->description}}</p>
                                                   </div>
                                                   <div class="minsago">
                                                      <p class="media-heading">{{ now()->create($getNotify->date)->diffForHumans() }}</p>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </a>
                                       @endforeach
                                    </li>
                                    @else
                                    <img class="notificationimage" src="{{asset('front/images/notificationempty.png')}}">
                                    @endif
                                 </ul>
                              </li>
                              <li class="nav-item dropdown hidemobwel">
                                 <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                    Hey {{Str::limit(Auth::user()->name,6) }} <i class="fa fa-chevron-down downicon" aria-hidden="true"></i>
                                 </a>
                                 <ul class="dropdown-menu">
                                    <li class="dropdown_items">
                                       <a class="dropdown-item" href="{{ route('my-library') }}">
                                          <img src="{{asset('front/images/libraryuser.png')}}">
                                          My Library
                                       </a>
                                    </li>
                                    <li class="dropdown_items feather-user">
                                       <a class="dropdown-item" href="{{ route('my-account') }}">
                                          <img src="{{asset('front/images/account.png')}}">
                                          My Account
                                       </a>
                                    </li>
                                    <li class="dropdown_items">
                                       <a class="dropdown-item" href="{{route('my-order')}}">
                                          <img src="{{asset('front/images/order.png')}}">
                                          My Orders
                                       </a>
                                    </li>
                                    <li class="dropdown_items">
                                       <a class="dropdown-item logoutanc" data-bs-toggle="modal" data-bs-target="#logoutmodel">
                                          <img src="{{asset('front/images/logout1.png')}}">
                                          Logout
                                       </a>
                                    </li>
                                 </ul>
                              </li>
                           </ul>
                           @elseif(!empty(Auth::User()->name) && Auth::User()->role == 3)
                           <ul class="righthead">
                              <li class="nav-item dropdown">
                                 <a class="nav-link dropdown-toggle hidemobwel" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                    Hey {{Str::limit(Auth::user()->name,6) }} <i class="fa fa-chevron-down downicon" aria-hidden="true"></i>
                                 </a>
                                 <ul class="dropdown-menu">
                                    <li class="dropdown_items">
                                       <a class="dropdown-item" href="{{ route('my-dashboard') }}">
                                          <img src="{{asset('front/images/dashboard.png')}}">
                                          Publisher Portal
                                       </a>
                                    </li>
                                    <li class="dropdown_items">
                                       <a class="dropdown-item" href="{{ route('my-comic') }}">
                                          <img src="{{asset('front/images/comic.png')}}">
                                          My Comics
                                       </a>
                                    </li>
                                    <li class="dropdown_items feather-user">
                                       <a class="dropdown-item" href="{{ route('my-accounts') }}">
                                          <img src="{{asset('front/images/account.png')}}">
                                          My Account
                                       </a>
                                    </li>
                                    <li class="dropdown_items feather-user">
                                       <a class="dropdown-item" href="{{ route('my-wallet') }}">
                                          <img src="{{asset('front/images/wallet.png')}}">
                                          My Wallet
                                       </a>
                                    <li class="dropdown_items feather-user">
                                       <a class="dropdown-item" href="{{ route('my-referral') }}">
                                          <img src="{{asset('front/images/refferal.png')}}">
                                          My Earnings
                                       </a>
                                    </li>
                                    <li class="dropdown_items">
                                       <a class="dropdown-item logoutanc" data-bs-toggle="modal" data-bs-target="#logoutmodel">
                                          <img src="{{asset('front/images/logout1.png')}}">
                                          Logout
                                       </a>
                                    </li>
                                 </ul>
                              </li>
                           </ul>
                           @else

                           <a href="{{route('front-login-view')}}" class="btn me-2 withoutlogin">Login</a>
                           @endif
                        </div>
                     </div>
                  </div>
               </nav>
            </div>
         </div>
      </header>
      @yield('content')
      <footer class="bgcolorr footermain {{ request()->route('id') == 'mobile' ? 'd-none' : '' }}">
         <div class="footer">
            <div class="container">
               <div class="row">
                  <div class="col-md-5 copyrights">
                     <p>Copyright &copy; 2010-2022 Vivid Panel All rights reserved.</p>
                  </div>
                  <div class="col-md-7 footerLink">
                     <span class="first"><a href="{{url('copyright')}}">Copyright Notice</a></span>
                     <span class="first"><a href="{{url('cancellation-policy')}}">Shipping policy</a></span>
                     <span class="first"><a href="{{url('cookie-policy')}}">Cookies policy</a></span>
                     <span class="first"><a href="{{url('term-conditions')}}/web">Term & conditions</a></span>
                     <span><a href="{{url('privacy-policy')}}/web">Privacy policy</a></span>
                  </div>
               </div>
            </div>
         </div>
      </footer>
      <div id="FullScreenOverlay" class="overlay">
         <span class="closebtn" onclick="closeSearchHero()" title="Close Overlay">×</span>
         <div class="overlay-content">
            <form class="me-auto ml-2 headersearch" action="{{url('search-comics')}}" method="get">
               <div class="input-group filter-search">
                  <input type="text" class="form-control input-search" placeholder="Search..." tabindex="-1" aria-label="Search..." aria-describedby="input-search" name="serach_text" id="serach_text">
                  <button type="submit" class="hidebutton"><img id="arrrowsearchbutton" src="{{asset('front/images/searchsubmit.png')}}" height="14" width="17" style="display:none" /></button>
               </div>
            </form>
         </div>
      </div>
      <!----subscription and PreviewModel Modal Start--->
      <div class="modal fade modelbutton" id="subscriptionAndPreviewModel">
         <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
               <!-- Modal Header -->
               <div class="modal-header text-center">
                  <h4 class="modal-title w-100">Subscribe to use</h4>
                  <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25" />
               </div>
               <!-- Modal body -->
               <div class="modal-body text-center">
                  <p class="colorwhite fw-bolder">To view the premium content, you need to purchase a subscriptions</p>
               </div>
               <!-- Modal footer -->
               <div class="modal-footer">
                  <a class="btn askLoginBtn" href="{{ route('my-account') }}">Subscribe </a>
                  <a class="btn askLoginBtn" id="previewModelLink" href="">Preview </a>
               </div>
            </div>
         </div>
      </div>
      <!----subscription and PreviewModel Modal Start-->
      <!----subscriptionModel Modal Start--->
      <div class="modal fade modelbutton" id="subscriptionModel">
         <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
               <!-- Modal Header -->
               <div class="modal-header text-center">
                  <h4 class="modal-title w-100">Subscribe to use</h4>
                  <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25" />
               </div>
               <!-- Modal body -->
               <div class="modal-body text-center">
                  <p class="colorwhite fw-bolder">To view the premium content, you need to purchase a subscriptions</p>
               </div>
               <!-- Modal footer -->
               <div class="modal-footer">
                  <a class="btn askLoginBtn" href="{{ route('my-account') }}">Subscribe </a>
               </div>
            </div>
         </div>
      </div>
      <!----subscriptionModel Modal End-->
      <!----Rating Modal Start--->
      <div class="modal fade modelbutton" id="signuppopup">
         <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
               <!-- Modal Header -->
               <div class="modal-header text-center">
                  <h4 class="modal-title w-100">LOGIN</h4>
                  <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25" />
               </div>
               <!-- Modal body -->
               <div class="modal-body text-center">
                  <p class="colorwhite fw-bolder">Create an account or log in to read.</p>
               </div>
               <!-- Modal footer -->
               <div class="modal-footer">
                  <a class="btn askLoginBtn" href="{{ route('front-login-view') }}">Login </a>
               </div>
            </div>
         </div>
      </div>
      <!----Rating Modal End-->
      <div class="modal fade modelbutton" id="signuppoppurchase">
         <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
               <!-- Modal Header -->
               <div class="modal-header text-center">
                  <h4 class="modal-title w-100">LOGIN</h4>
                  <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25" />
               </div>
               <!-- Modal body -->
               <div class="modal-body text-center">
                  <p class="colorwhite fw-bolder">Create an account or log in to Purchase.</p>
               </div>
               <!-- Modal footer -->
               <div class="modal-footer">
                  <a class="btn askLoginBtn" href="{{ route('front-login-view') }}">Login </a>
               </div>
            </div>
         </div>
      </div>
      <!---delete account model--->
      <div class="modal fade modelbutton" id="deleteAccountmodel">
         <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
               <!-- Modal Header -->
               <div class="modal-header text-center">
                  <h4 class="modal-title w-100">Delete Account</h4>
                  <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25" />
               </div>
               <!-- Modal body -->
               <div class="modal-body text-center">
                  <p class="colorwhite fw-bolder">Are you sure, you want to delete your account.</p>
               </div>
               <!-- Modal footer -->
               <div class="modal-footer">
                  <a class="btn deleteaccount">submit</a>
               </div>
            </div>
         </div>
      </div>
      <!----delete account End-->
      <!---logout model--->
      <div class="modal fade modelbutton" id="logoutmodel">
         <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
               <!-- Modal Header -->
               <div class="modal-header text-center">
                  <h4 class="modal-title w-100">Logout Account</h4>
                  <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25" />
               </div>
               <!-- Modal body -->
               <div class="modal-body text-center">
                  <p class="colorwhite fw-bolder">Are you sure, you want to logout from this account.</p>
               </div>
               <!-- Modal footer -->
               <div class="modal-footer">
                  <a href="{{route('logout')}}" class="btn">Logout</a>
               </div>
            </div>
         </div>
      </div>
      <!----logout End-->

      <!---Download Our App model--->
      <div class="modal fade modelbutton" id="DownloadOurAppModel">
         <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
               <!-- Modal Header -->
               <div class="modal-header text-center">
                  <h4 class="modal-title w-100">Download Our App</h4>
                  <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25" />
               </div>
               <!-- Modal body -->
               <div class="modal-body text-center">
                  <div class="grapic-sec text-center">
                     <img src="{{ asset('front/images/Pop_up_img.png') }}" width="350">
                  </div>
                  <!-- <p class="colorwhite ">Vivid Panel is a indie comic book publishing platform designed solely for indie creators. We exist to provide creators with the tools to launch projects, build an audience and EARN A LIVING at the same time.</br></br> --></br>
                  <span class="fw-bolder colorwhite popupfrontrj">Want the BEST experience? Download the app now</span></p>
                  <div class="btnsec d-flex justify-content-around">
                     <div class="google-sec">
                        <img src="{{ asset('front/images/google_play_button.png') }}" width="200">
                     </div>&nbsp;
                     <div class="apple-sec">
                        <img src="{{ asset('front/images/app_store_button.png') }}" width="200">
                     </div>
                  </div>
               </div>
               <!-- Modal footer -->
               <div class="modal-footer">

               </div>
            </div>
         </div>
      </div>
      <!----Download Our App End-->

      <!---Publisher Ask Create Account model--->
      <div class="modal fade modelbutton" id="PubAskAcc">
         <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
               <!-- Modal Header -->
               <div class="modal-header text-center">
                  <h4 class="modal-title w-100">Create Your Customer Account</h4>
                  <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25" />
               </div>
               <!-- Modal body -->
               <div class="modal-body text-center">
                  <p class="colorwhite fw-bolder">To read this comic, You need to create a customer account.</p>
               </div>
               <!-- Modal footer -->
               <div class="modal-footer">
                  <a href="{{url('signup')}}" class="btn">Sign Up</a>
               </div>
            </div>
         </div>
      </div>
      <!----logout End-->
      <input type="hidden" id="sessionUserId" value="{{ Auth::user()->id ?? ''}}">
      <input type="hidden" id="requestPage" value="{{ request()->is('*home*') }}">
   </body>

   <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js'></script>
   <script src="{{asset('front/js/owl.carousel.min.js')}}"></script>
   <script src="{{asset('front/js/bootstrap.bundle.min.js')}}"></script>
   <script src="{{asset('front/js/flatpickr.min.js')}}"></script>
   <script src="{{ URL::asset('js/custom/library/toastr.min.js') }}"></script>
   <script src="//cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
   <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
   <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
   @yield('vendor-scripts')
   <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
   <script src="{{ asset(mix('vendors/js/forms/cleave/cleave.min.js'))}}"></script>
   <script src="{{ asset(mix('vendors/js/forms/cleave/addons/cleave-phone.us.js'))}}"></script>
   <script src="{{ asset(mix('js/scripts/pages/app-user-view.js')) }}"></script>
   <script src="{{ asset(mix('vendors/js/extensions/dragula.min.js')) }}"></script>
   <script src="{{ asset(mix('js/scripts/extensions/ext-component-drag-drop.js')) }}"></script>
   <script src="{{ asset(mix('js/scripts/forms/form-input-mask.js')) }}"></script>
   <script type="text/javascript">
      function openSearchHero() {
         document.getElementById("FullScreenOverlay").style.display = "block";
      }

      function closeSearchHero() {
         document.getElementById("FullScreenOverlay").style.display = "none";
      }
      $.ajaxSetup({
         headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
         },
      });

      toastr.options = {
         closeButton: true,
         debug: false,
         newestOnTop: true,
         progressBar: true,
         positionClass: "toast-top-right",
         preventDuplicates: false,
         onclick: null,
         showDuration: 500,
         hideDuration: 1000,
         timeOut: 4000,
         extendedTimeOut: 1000,
         showEasing: "swing",
         hideEasing: "linear",
         showMethod: "fadeIn",
         hideMethod: "fadeOut",
      };
      $(document).on("click", ".deleteaccount", function(e) {
         //  var cat_id = $(this).attr('cat_id');
         //  getComic(12,1,cat_id,'');
         $.ajax({
            'url': "{{route('delete-account')}}",
            'type': "post",
            'dataType': "json",
            'data': {
               "_token": "{{ csrf_token() }}"
            },
            'success': function(response) {
               console.log(response)
               if (response.status) {
                  toastr["success"](response.message, "Success");
                  setTimeout(function() {
                     var u = "{{ env('APP_URL_SERVE') }}";
                     location.href = u + '/login';
                     //window.location = document.location.href;
                  }, 1000)
               } else {
                  toastr["error"](response.message, "Error");
               }

            }
         });
      });


      $(document).on('change', '.input-search', function(e) {
         var searchInput = $(this).val();
         //alert(searchInput);
         Search(10, 1, searchInput);
      });

      function Search(limit, page, serach_text) {
         $.ajax({
            'url': "{{route('get-comic')}}",
            'type': "post",
            'dataType': "json",
            'data': {
               limit: limit,
               page: page,
               serach_text: serach_text,
               "_token": "{{ csrf_token() }}"
            },
            'success': function(response) {
               console.log(response);
               $("#comicMainDiv").empty();
               var comicHtml = '';
               if (response.status) {

                  var t = 1;
                  var cls = '';
                  response.data.data.forEach(function(item, index) {

                     var imgPath = "{{ env('IMAGE_PATH_large') }}" + item.featured_image;
                     var checkimg = item.access_type;
                     if (checkimg == 1) {
                        var img = "{{asset('front/images/free.png')}}";
                     } else {
                        var img = "{{asset('front/images/paid.png')}}";
                     }

                     if (t > 6)
                        cls = 'mt-3';

                     comicHtml += `<div class="col-lg-2 col-md-4 col-sm-4 ` + cls + `">
                  <div class="imgwithtag">
                    <a href ="{{url('comic-detail')}}/` + item.id + `"><img class="f_img" src="` + imgPath + `">

                     <img class="tagimg" src="` + img + `"></a>


                  </div>
                 <div class="content-sec">
                     <div class="contenttextsec">
                           <p>` + item.name + `</p>
                     </div>
                     <div class="contentratingsec">
                        <p><i class="fa fa-star" aria-hidden="true"></i>` + item.ratings + `</p>
                     </div>
                 </div>
             </div>`;
                     t++;
                  });
               } else {
                  comicHtml += 'Data Not Foudnd';
                  $("#comicMainDiv").append(comicHtml);
               }
               $("#comicMainDiv").append(comicHtml);
            }
         });
      }


      $(document).ready(function() {
         var minim = $('.headersearch input').val();
         // alert('min')
         $('.headersearch input').keyup(function() {
            if ($(this).val() > minim) {
               //$("body").css({"background-color": "#000","color":"#fff", "opacity": "1"});
               //$("body").addClass("bodybggg");


               //$("#arrrowsearchbutton").show();
            } else {
               //$("body").css({"background-color": "#000", "opacity": "1"});
               //  $("body").removeClass("bodybggg");
               //$("#arrrowsearchbutton").hide();
            }
         });
         setTimeout(function() {
            if ($("#checkPage").val() == 'home-page') {
               var x = getCookie('downloadApp');
               if (x) {

               } else {
                  $("#DownloadOurAppModel").modal('toggle');
               }
               setTimeout(function() {
                  setCookie('downloadApp', 'show', 7);
               }, 1000)
            }
         }, 1000)
      });

      // setTimeout(function(){
      //    var searchpage = $(".searchpage").outerHeight();

      //    var orderdetailpagemin = $(".orderdetailpagemin").outerHeight();
      //    if(searchpage<="500" || orderdetailpagemin<="500")
      //       {
      //             $(".footermain").addClass("footerfixed");
      //       }
      // }, 500);

      $(document).on("click", ".askLoginBtn", function() {
         document.cookie = "askLogin=" + window.location.href + "; expires=Thu, 18 Dec 2030 12:00:00 UTC;path=/";
      });
      $(document).on("click", "#normalLogin", function() {
         var mydate = new Date();
         mydate.setTime(mydate.getTime() - 1);
         document.cookie = "askLogin=; expires=" + mydate.toGMTString();
      });

      $(document).on("click", ".eyeHide", function() {
         var thi = $(this).attr("vlu");
         $(".eyeHide").removeClass("d-none")
         $(this).addClass("d-none")

         if (thi == "show")
            $("input[name='password']").prop("type", "text");
         else
            $("input[name='password']").prop("type", "password");
      });

      $(document).on("input", ".preventCharacters", function() {
         $(this).val($(this).val().replace(/[^0-9.]/g, ''));
      });
      $(document).on("input", ".preventCharactersAndDot", function() {
         $(this).val($(this).val().replace(/[^0-9]/g, ''));
      });

      $(document).on("click", ".emptyCart", function() {
         toastr["warning"]("Cart empty", "Warning");
      });
      $(document).on("click", ".shareIcon", function() {
         $(".sociallinks").toggleClass("d-none");
      });

      $(document).on("click", ".item-delete", function() {
         $("#deleteid").val(
            jQuery(this).parents("tr").find(".dataField").attr("id")
         );
      });



      $(document).on('click', '#notificationhide', function(e) {
        // alert("searchInput");
        $.ajax({
            'url': "{{route('hide-notification')}}",
            'type': "post",
            'dataType': "json",
            'data': {
               "_token": "{{ csrf_token() }}"
            },
            'success': function (response) {
                     if(response.status)
                     {
                        $("#notificationhide span").remove();
                     }
            },
          });
       });

      function getNotificationCount()
      {
         $.ajax({
            'url': "{{route('count-notification')}}",
            'type': "post",
            'dataType': "json",
            'data': {
               "_token": "{{ csrf_token() }}"
            },
            'success': function (response) {
               console.log(response)

               var nhtml, appendNotificationData = ``;
               if(response.status)
               {
                  if(response.count > 0)
                  {
                     $(".notificatinBadge").empty();
                     nhtml = `<i class="fa fa-bell-o" aria-hidden="true"></i>
                                                                  <span class="badge rounded-pill bg-primary badge-up cartvalue notificatinBadge">`+response.count+`</span>`;
                     $(".notificatinBadge").html(nhtml);
                  }

                  if(response.data.length > 0)
                  {
                     $(".appendNotificationData").empty();
                     response.data.forEach(function(item,index){
                        appendNotificationData += `<a class="d-flex" href="`+item.slug+`">
                                          <div class="list-item d-flex align-items-start">
                                             <div class="me-2">
                                                <div class="avatar">
                                                </div>
                                                <div class="list-item-body d-flex flex-grow-1">
                                                   <div class="notified">
                                                      <h6>`+item.title+`</h6>
                                                      <p class="media-heading">`+item.description+`</p>
                                                   </div>
                                                   <div class="minsago">
                                                      <p class="media-heading">`+item.time+`</p>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </a>`;
                     });


                     $(".appendNotificationData").html(appendNotificationData);
                  }

               }
            },
          });
      }

      var notificatinBadge = $("#sessionUserId").val();
      var requestPage = $("#requestPage").val();
      if(notificatinBadge && (requestPage))
      {
         setInterval(function () {
            console.log("fire")
            getNotificationCount();
         }, 10000);
      }






      $(document).on('draw.dt ajaxComplete', function() {

        // console.log("hit")
         var delimiterMask = $('.autioTimer');
         if (delimiterMask.length) {
            delimiterMask.each(function() {
               new Cleave($(this), {
                  delimiter: ':',
                  numeralPositiveOnly: true,
                  blocks: [2, 2, 2],
                  uppercase: true
               });
            });
         }
      });
      if ($(window).width() > 1199) {

         $('.genres').hover(function() {
            $(this).find('.dropdown-menu').stop(true, true).delay(200).slideDown(200);
            $(this).find('.dropdown-menu').addClass('show');
         }, function() {
            $(this).find('.dropdown-menu').stop(true, true).delay(200).slideUp(200);
            $(this).find('.dropdown-menu').removeClass('show');
         });
      }



      function setCookie(name, value, days) {
         var expires = "";
         if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
         }
         document.cookie = name + "=" + (value || "") + expires + "; path=/";
      }

      function getCookie(name) {
         var nameEQ = name + "=";
         var ca = document.cookie.split(';');
         for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
         }
         return null;
      }
   </script>
   <!-- <script rel="stylesheet" type="text/css" href="app.js"></script> -->
   @yield('scripts')
</body>

</html>