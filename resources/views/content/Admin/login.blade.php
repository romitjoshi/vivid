@extends('layouts/fullLayoutMaster')

@section('title', 'Login Page')

@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/pages/page-auth.css')) }}">
@endsection

@section('content')
<div class="auth-wrapper auth-v1 px-2">
  <div class="auth-inner py-2">
    <!-- Login v1 -->
    <div class="card mb-0">
      <div class="card-body">
        <a href="#" class="brand-logo">
           <img src="{{asset('images/logo/logo.png')}} " height="60%" width="40%"/>
        </a>
        <h4 class="card-title mb-1 text-center">Welcome to Vivid Panel</h4>
        <p class="card-text mb-2 text-center">Please sign-in to your account</p>
         @if(Session::has('error') && !empty(Session::get('error'))) <p class="error-tag">{{ Session::get('error') }}</p> @endif
        <form class="auth-login-form mt-2" action="{{url('/admin/loginAdmin')}}" method="POST">
        @csrf
          <div class="mb-1">
            <label for="email" class="form-label">Email</label>
            <input
              type="text"
              class="form-control"
              id="email"
              name="email"
              placeholder="john@example.com"
              aria-describedby="email"
              tabindex="1"
              autofocus
            />
            @if(Session::has('errors') && !empty(Session::get('errors')->first('email'))) <p class="error-tag">{{ Session::get('errors')->first('email') }}</p> @endif
          </div>

          <div class="mb-1">
            <div class="d-flex justify-content-between">
              <label class="form-label" for="password">Password</label>
              <!-- <a href="{{url('auth/forgot-password-v1')}}">
                <small>Forgot Password?</small>
              </a> -->
            </div>
            <div class="input-group input-group-merge form-password-toggle">
              <input
                type="password"
                class="form-control form-control-merge"
                id="password"
                name="password"
                tabindex="2"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                aria-describedby="password"
              />
              <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
            </div>
            @if(Session::has('errors') && !empty(Session::get('errors')->first('password'))) <p class="error-tag">{{ Session::get('errors')->first('password') }}</p> @endif
          </div>
          <!-- <div class="mb-1">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="remember-me" tabindex="3" />
              <label class="form-check-label" for="remember-me"> Remember Me </label>
            </div>
          </div> -->
           <button class="btn btn-primary w-100" tabindex="4">Sign in</button>
        </form>
      </div>
    </div>
    <!-- /Login v1 -->
  </div>
</div>
@endsection

@section('vendor-script')
<script src="{{asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
@endsection

@section('page-script')
<script src="{{asset(mix('js/scripts/pages/page-auth-login.js'))}}"></script>
@endsection
