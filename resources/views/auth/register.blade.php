@extends('layouts.master-without-nav')
@section('title')
    Register
@endsection
@section('content')
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
{{--                    <div class="text-center">--}}
{{--                        <a href="{{ url('index') }}" class="mb-5 d-block auth-logo">--}}
{{--                            <img src="{{ URL::asset('/assets/images/logo-dark.png') }}" alt="" height="100"--}}
{{--                                class="logo logo-dark">--}}
{{--                            <img src="{{ URL::asset('/assets/images/logo-light.png') }}" alt="" height="22"--}}
{{--                                class="logo logo-light">--}}
{{--                        </a>--}}
{{--                    </div>--}}
                </div>
            </div>
            <div class="row align-items-center justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">

                        <div class="card-body p-4">

                            <div class="text-center mt-2">
                                <h5 class="text-primary">회원 가입</h5>
                                <p class="text-muted">Flasystem NET</p>
                            </div>
                            <div class="p-2 mt-4">
                                <form method="POST" action="{{ route('register') }}">
                                    @csrf

                                    <div class="mb-3">
                                        <label class="form-label" for="user_id">ID</label>
                                        <input type="text" class="form-control @error('user_id') is-invalid @enderror"
                                               name="user_id" value="{{ old('user_id') }}" id="user_id" placeholder="Enter ID">
                                        @error('user_id')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="email">Email (그룹웨어)</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            name="email" value="{{ old('email') }}" id="email" placeholder="Enter email">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="username">이름</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            name="name" value="{{ old('name') }}" id="username"
                                            placeholder="Enter username">
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="userpassword">비밀번호</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            name="password" id="userpassword" placeholder="Enter password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="password_confirmation">비밀번호 확인</label>
                                        <input type="password"
                                            class="form-control @error('password_confirmation') is-invalid @enderror"
                                            name="password_confirmation" id="password_confirmation"
                                            placeholder="Enter confirm password">
                                        @error('password_confirmation')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="phone">전화번호</label>
                                        @csrf
                                        <input type="phone" class="form-control @error('phone') is-invalid @enderror"
                                               name="phone" value="{{ old('phone') }}" id="phone"
                                               placeholder="Enter Phone Number" onkeyup="auto_hyphen(event);">
                                        @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="dep">소속 부서</label><br>
                                        <select name="dep" class="form-select mb-3" aria-label="Large select example" @error('dep') is-invalid @enderror">
                                            <option>부서 선택</option>
                                            <option value="임원">임원</option>
                                            <option value="전국플라워센터">전국플라워센터</option>
                                            <option value="꽃파는사람들">꽃파는사람들</option>
                                            <option value="위탁운영">위탁운영</option>
                                            <option value="꽃파는총각">꽃파는총각</option>
                                            <option value="마케팅">마케팅</option>
                                            <option value="경영지원">경영지원</option>
                                            <option value="기획개발/디자인">기획개발/디자인</option>
                                        </select>

{{--                                        <input type="dep" class="form-control @error('dep') is-invalid @enderror"--}}
{{--                                               name="dep" value="{{ old('dep') }}" id="dep"--}}
{{--                                               placeholder="Enter Department">--}}
                                        @error('dep')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mt-3 text-end">
                                        <button class="btn btn-primary w-sm waves-effect waves-light"
                                            type="submit">회원가입</button>
                                    </div>

                                    <div class="mt-3 text-center">
                                        <p class="text-muted mb-0">이미 회원이신가요 ? <a href="{{ url('login') }}"
                                                class="fw-medium text-primary"> 로그인</a></p>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                    <div class="mt-2 text-center">
                        <p>© <script>
                                document.write(new Date().getFullYear())

                            </script> (주) 플라시스템 <i class="uil-building"></i> by Dev</p>
                    </div>

                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>

@endsection
