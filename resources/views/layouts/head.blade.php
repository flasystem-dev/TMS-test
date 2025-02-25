@yield('css')
<link href="https://hangeul.pstatic.net/hangeul_static/css/nanum-barun-gothic.css" rel="stylesheet">
<link href="https://hangeul.pstatic.net/hangeul_static/css/nanum-square-round.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/earlyaccess/notosanskr.css" rel="stylesheet">

<!-- jquery -->
<script src="{{ URL::asset('/assets/libs/jquery/jquery.min.js')}}"></script>
<script src="{{ URL::asset('/assets/libs/jquery-ui/jquery-ui.min.js')}}"></script>
<link href="{{ URL::asset('/assets/libs/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet" type="text/css" />
<!-- Bootstrap Css -->
<link href="{{ URL::asset('/assets/css/bootstrap.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
<!-- App Css-->
<link href="{{ URL::asset('/assets/css/app.css')}}" id="app-style" rel="stylesheet" type="text/css" />
<!-- Icons Css -->
<link href="{{ URL::asset('/assets/css/icons.css')}}" id="icons-style" rel="stylesheet" type="text/css" />
<!-- sweetalert2 -->
<script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>
<link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
<!-- Common Css-->
<link href="{{ URL::asset('/assets/css/common.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
<!-- Common JS-->
<script src="{{ URL::asset('/assets/js/common.js') }}?v={{ filemtime(public_path('assets/js/common.js')) }}"></script>
<!-- select2 -->
<link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet">
<script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>