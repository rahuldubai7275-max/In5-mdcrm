@extends('layouts/fullLayoutMaster')

@section('title', 'Login Page')

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/pages/authentication.css')) }}">

    <style>
        /*html body {*/
        /*    background: url(/images/login-bg.webp) no-repeat center center;*/
        /*    background-size: cover;*/
        /*}*/

        body .body-overlay {
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }
        .bg-authentication {
            background-color: #00000026 !important;
            color: #fff;
        }
        .card-title h4{
            color: #fff;
        }
        .form-label-group > input:not(:focus):not(:placeholder-shown) ~ label{
            color: unset !important;
        }
        .form-label-group > label {
            color: #fff !important;
        }
    </style>
@endsection

@section('content')
    <video class="body-overlay" muted="" autoplay="" loop="">
        <source src="/images/login-bg-video.mov" type="video/mp4">
    </video>
    <section class="row flexbox-container">
        <div class="col-xl-8 col-11 d-flex justify-content-center mx-auto">
            <div class="card bg-authentication rounded-0 mb-0 mt-5">
                <div class="row m-0">
                    <div class="col-lg-12 col-12 p-0">
                        <div class="rounded-0 mb-0 px-2">
                            <div class="card-header pb-1">
                                <div class="card-title">
                                    <h4 class="mb-0">Login</h4>
                                </div>
                            </div>
                            <p class="px-2">Welcome back, please login to your account.</p>
                            <div class="card-content">
                                <div class="card-body pt-1">
                                    <form class="clearfix" method="POST" action="{{ route('admin.login.submit') }}">
                                        @csrf
                                        <fieldset class="form-label-group form-group position-relative has-icon-left">

                                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                                   name="email" placeholder="E-Mail Address" value="{{ old('email') }}" required autocomplete="email"
                                                   autofocus>

                                            <div class="form-control-position">
                                                <i class="feather icon-user"></i>
                                            </div>
                                            <label for="email">E-Mail Address</label>
                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </fieldset>

                                        <fieldset class="form-label-group position-relative has-icon-left">

                                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                                   name="password" placeholder="Password" required autocomplete="current-password">

                                            <div class="form-control-position">
                                                <i class="feather icon-lock"></i>
                                            </div>
                                            <label for="password">Password</label>
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </fieldset>
                                        <div class="form-group d-flex justify-content-between align-items-center">
                                            <div class="text-left">
                                                <fieldset class="checkbox">
                                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                                        <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                  <i class="vs-icon feather icon-check"></i>
                                                </span>
                                              </span>
                                                        <span class="">Remember me</span>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @if (Route::has('password.request'))
                                                <div class="text-right"><a class="card-link" href="{{ route('password.request') }}">
                                                        Forgot Password?
                                                    </a></div>
                                            @endif

                                        </div>
                                        {{--                                        <a href="register" class="btn btn-outline-primary float-left btn-inline">Register</a>--}}
                                        <button type="submit" class="btn btn-primary w-100 float-sm-right btn-inline">Login</button>
                                    </form>
                                </div>
                            </div>
                            {{--                            <div class="login-footer">
                                                            <div class="divider">
                                                                <div class="divider-text">OR</div>
                                                            </div>
                                                            <div class="footer-btn d-inline">
                                                                <a href="#" class="btn btn-facebook"><span class="fa fa-facebook"></span></a>
                                                                <a href="#" class="btn btn-twitter white"><span class="fa fa-twitter"></span></a>
                                                                <a href="#" class="btn btn-google"><span class="fa fa-google"></span></a>
                                                                <a href="#" class="btn btn-github"><span class="fa fa-github-alt"></span></a>
                                                            </div>
                                                        </div>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
