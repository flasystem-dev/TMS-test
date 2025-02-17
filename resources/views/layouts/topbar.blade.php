<header id="page-topbar">
@inject('DB', 'Illuminate\Support\Facades\DB')

    <div class="navbar-header">
        <div class="d-flex text-xl-center">
            <!-- LOGO --><p class="font-size-20 fw-bold mt-3 mx-4" style="text-align:center">@yield('title')</p>

        </div>

        <div class="d-flex main_top_bar">

            @php
                $notifications = App\Models\TMS_User_Noti::check_read(Auth::user() -> user_id);
                $count_noti = count($notifications);
            @endphp
            @if(Auth::user()->auth>9)
            <div>
                <button type="button" class="btn btn-outline-danger" style="margin: 15px;" onclick="update_tms();">업데이트</button>
            </div>
            @endif
            <div class="main_top_bar">
                <button type="button" class="btn btn-outline-secondary" style="margin: 15px;" onclick="vendor_popup();">사업자 조회</button>
            </div>
            <div class="dropdown top_menu_in_block main_top_bar">
                <button type="button" class="btn header-item noti-icon waves-effect dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="uil-bell fs-2"></i>
                    @if(isset($notifications) && $count_noti > 0)
                        <span class="badge bg-danger rounded-pill">{{ $count_noti }}</span>
                    @endif
                </button>

                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">
                    @if(isset($notifications) && $count_noti > 0)
                        <div data-simplebar style="max-height: 230px;">
                            @foreach($notifications as $noti)
                                <a href="{{ url('Board/Notification/'.$noti -> noti -> noti_id) }}" class="text-reset notification-item">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-xs">
                                                @if($noti -> noti -> type == 'error')
                                                    <span class="avatar-title bg-danger rounded-circle font-size-20 text-center" style="padding-left: 1.5px;">
                                                        <i class="uil-ban"></i>
                                                    </span>
                                                @elseif($noti -> noti -> type == 'noti')
                                                    <span class="avatar-title bg-info rounded-circle font-size-20 text-center" style="padding-left: 1.5px;">
                                                        <i class="uil-info-circle"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mt-0 mb-1">{{ $noti -> noti -> title }}</h6>
                                            <div class="font-size-12 text-muted">
                                                <p class="mb-1">{{ $noti -> noti -> content }}</p>
                                                @php
                                                    $text = App\Utils\Common::diff_now($noti -> noti -> created_at);
                                                @endphp
                                                <p class="mb-0"><i class="mdi mdi-clock-outline"></i> {{ $text }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                          @endforeach
                        </div>
                        <div class="p-2 border-top">
                            <div class="d-grid">
                                <a class="btn btn-sm btn-link font-size-14 text-center" href="{{ url('Board/Notification/all') }}">
                                    <i class="uil-arrow-circle-right me-1"></i> 모두 확인
                                </a>
                            </div>
                        </div>
                    @else
                        <a class="dropdown-item py-3 px-4" href="#">알림이 없습니다.</a>
                    @endif
                </div>
            </div>

            <div class="dropdown top_menu_in_block ms-1 main_top_bar">
                <button class="btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight" id="brand_btn"><b>브랜드</b></button>
                <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                    <i class="uil-minus-path"></i>
                </button>
            </div>
            <div class="dropdown top_menu_in_block main_top_bar">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="d-none d-xl-inline-block ms-1 fw-medium font-size-15">{{Str::ucfirst(Auth::user()->name)}}</span>
                    <i class="uil-angle-down d-none d-xl-inline-block font-size-15"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
{{--                    <a class="dropdown-item" href="#"><i class="uil uil-user-circle font-size-18 align-middle text-muted me-1"></i> <span class="align-middle">@lang('translation.View_Profile')</span></a>--}}
{{--                    <a class="dropdown-item" href="#"><i class="uil uil-wallet font-size-18 align-middle me-1 text-muted"></i> <span class="align-middle">@lang('translation.My_Wallet')</span></a>--}}
{{--                    <a class="dropdown-item d-block" href="#"><i class="uil uil-cog font-size-18 align-middle me-1 text-muted"></i> <span class="align-middle">@lang('translation.Settings')</span> <span class="badge bg-soft-success rounded-pill mt-1 ms-2">03</span></a>--}}
{{--                    <a class="dropdown-item" href="#"><i class="uil uil-lock-alt font-size-18 align-middle me-1 text-muted"></i> <span class="align-middle">@lang('translation.Lock_screen')</span></a>--}}
                   <a class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="uil uil-sign-out-alt font-size-18 align-middle me-1 text-muted"></i> <span class="align-middle">로그아웃</span></a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
            <!-- top offcanvas -->
                    <div class="card-body">
                        <!-- right offcanvas -->
                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
                            <div class="brand_session_area">
{{--                                <div class="session_area1">--}}
{{--                                    <div class="offcanvas-header">--}}
{{--                                        <h5 id="offcanvasRightLabel"><b>세부 선택</b></h5>--}}
{{--                                    </div>--}}
{{--                                    <div class="offcanvas-body">--}}

{{--                                    </div>--}}
{{--                                </div>--}}
                                <div class="session_area2">
                                    <div class="offcanvas-header">
                                        <h5 id="offcanvasRightLabel"><b>브랜드 선택</b></h5>
                                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                    </div>
                                    <div class="offcanvas-body">
                                        <div>
                                            <input type="checkbox" class="btn-check" id="session_BTCP" name="session_brand" value="BTCP" autocomplete="off" {{ session('BTCP')=='Y'? 'checked' : '' }}>
                                            <label class="brand_btn session_label checkbox_BTCP" for="session_BTCP"><i class="bx bxs-circle check_circle"></i>꽃파는총각</label>
                                            <input type="checkbox" class="btn-check" id="session_BTCC" name="session_brand" value="BTCC" autocomplete="off" {{ session('BTCC')=='Y'? 'checked' : '' }}>
                                            <label class="brand_btn session_label checkbox_BTCC" for="session_BTCC"><i class="bx bxs-circle check_circle"></i>칙칙폭폭플라워</label>
                                            <input type="checkbox" class="btn-check" id="session_BTSP" name="session_brand" value="BTSP" autocomplete="off" {{ session('BTSP')=='Y'? 'checked' : '' }}>
                                            <label class="brand_btn session_label checkbox_BTSP" for="session_BTSP"><i class="bx bxs-circle check_circle"></i>사팔플라워</label>
                                            <input type="checkbox" class="btn-check" id="session_BTBR" name="session_brand" value="BTBR" autocomplete="off" {{ session('BTBR')=='Y'? 'checked' : '' }}>
                                            <label class="brand_btn session_label checkbox_BTBR" for="session_BTBR"><i class="bx bxs-circle check_circle"></i>바로플라워</label>
                                            <input type="checkbox" class="btn-check" id="session_BTOM" name="session_brand" value="BTOM" autocomplete="off" {{ session('BTOM')=='Y'? 'checked' : '' }}>
                                            <label class="brand_btn session_label checkbox_BTOM" for="session_BTOM"><i class="bx bxs-circle check_circle"></i>오만플라워</label>
                                            <input type="checkbox" class="btn-check" id="session_BTCS" name="session_brand" value="BTCS" autocomplete="off" {{ session('BTCS')=='Y'? 'checked' : '' }}>
                                            <label class="brand_btn session_label checkbox_BTCS" for="session_BTCS"><i class="bx bxs-circle check_circle"></i>꽃파는사람들</label>
                                            <input type="checkbox" class="btn-check" id="session_BTFC" name="session_brand" value="BTFC" autocomplete="off" {{ session('BTFC')=='Y'? 'checked' : '' }}>
                                            <label class="brand_btn session_label checkbox_BTFC" for="session_BTFC"><i class="bx bxs-circle check_circle"></i>플라체인</label>
                                        </div>
                                        <div style="margin-top: 20px; text-align: end;">
                                            <button type="button" class="btn btn-primary btn-lg" onclick="brand_session()">설정</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end card body  -->
{{--            <div class="dropdown d-inline-block">--}}
{{--                <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">--}}
{{--                    <i class="uil-cog"></i>--}}
{{--                </button>--}}
{{--            </div>--}}
        </div>
    </div>
</header>
<script>
    // 브랜드 오프캔버스 닫을 시 페이지 보내기
    document.getElementById('offcanvasRight').addEventListener('hidden.bs.offcanvas', event => {
        const uri = window.location.pathname;
        if(uri === '/order/ecommerce_orders') {
            window.location.href = '/';
        }
    })
    // 사업자 조회
    function vendor_popup() {
        var url = '{{url('/vendor/search')}}';
        open_win(url, "사업자 조회", 1300,900, 50, 50);
    }

    function update_tms() {
        if(confirm("업데이트 완료하시겠습니까?")) {
            $.ajax({
                url : '{{url('dev/update')}}',
                type: "GET",
                success: function() {
                    showToast('업데이트 완료')
                },
                error: function(e) {
                    alert('실패');
                    console.log(e)
                }
            });
        }
    }
</script>