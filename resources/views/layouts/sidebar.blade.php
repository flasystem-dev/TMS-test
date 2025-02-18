<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="{{url('order/ecommerce_orders')}}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('/assets/images/logo-sm.png') }}" alt="" height="30">
            </span>
            <span class="logo-lg" class="center">
                <img src="{{ URL::asset('/assets/images/logo-dark.png') }}" alt="" height="46" class=" ms-3">
            </span>
        </a>

        <a href="{{url('order/ecommerce_orders')}}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('/assets/images/logo-sm.png') }}" alt="" height="30">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('/assets/images/logo-light.png') }}" alt="" height="50">
            </span>
        </a>
    </div>

    <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect vertical-menu-btn">
        <i class="fa fa-fw fa-bars"></i>
    </button>

    <div data-simplebar class="sidebar-menu-scroll">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
              <li>
                <a href="javascript: void(0);" class="has-arrow"> <i class="uil-store"></i><span>주문관리</span></a>
                <ul class="sub-menu" aria-expanded="true">
                    <li class="disable"><a href="{{url('order/ecommerce_orders')}}">전체주문관리</a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>미수현황</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>클레임</strike></a></li>
                    <li class="disable"><a href="{{ url('shop/account')}}">오픈마켓 계정정보</a></li>
                    <li class="disable"><a href="{{ url('Board/Notification') }}">알림</a></li>
                    <li class="disable"><a href="{{ url('order/excel/files') }}">엑셀다운로드</a></li>
                </ul>
             </li>
             <li>
                <a href="javascript: void(0);" class="has-arrow"> <i class="uil-receipt-alt"></i><span>증빙</span></a>
                <ul class="sub-menu" aria-expanded="true">
                    <li class="disable"><a href="javascript: void(0);"><strike>전체주문관리</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>거래내역서</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>영수증</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>계산서</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>견적서</strike></a></li>
                    <li class="disable"><a href="{{ url('document/client/index') }}">거래처목록</a></li>
                </ul>
            </li>
            <li class="auth_check">
                <a href="javascript: void(0);" class="has-arrow"> <i class="uil-money-withdraw"></i><span><strike>미수금관리</strike></span></a>
                <ul class="sub-menu" aria-expanded="true">
                    <li class="disable"><a href="javascript: void(0);"><strike>개인미수</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>법인미수</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>내역서</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>영수증</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>계산서</strike></a></li>
                </ul>
            </li>
            <li class="auth_check">
                <a href="javascript: void(0);" class="has-arrow"> <i class="uil-user"></i><span>회원</span></a>
                <ul class="sub-menu" aria-expanded="true">
                    <li class="disable"><a href="{{ route('user-list') }}">회원목록</a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>접속로그</strike></a></li>
                </ul>
            </li>
            <li class="auth_check">
                <a href="javascript: void(0);" class="has-arrow"> <i class="uil-shop"></i><span><strike>쇼핑몰</strike></span></a>
                <ul class="sub-menu" aria-expanded="true">
                    <li class="disable"><a href="javascript: void(0);"><strike>기본정보</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>공통안내</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>이용약관</strike></a></li>
                    <li><a href="{{ url('/shop/banners') }}">배너관리</a></li>
                    <li><a href="{{ url('/shop/popups') }}">팝업관리</a></li>
                    <li><a href="{{ url('/shop/products') }}">상품관리</a></li>
                    <li><a href="{{ url('/shop/deliveryPrice') }}">배송비관리</a></li>
                </ul>
            </li>
            <li class="auth_check">
                <a href="javascript: void(0);" class="has-arrow"> <i class="fas fa-air-freshener"></i><span><strike>프로모션</strike></span></a>
                <ul class="sub-menu" aria-expanded="true">
                    <li class="disable"><a href="javascript: void(0);"><strike>쿠폰</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>할인/적립</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>배너/팝업</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>랜딩페이지</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>이벤트</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>메세지</strike></a></li>
                </ul>
            </li>
            <li class="auth_check">
                <a href="javascript: void(0);" class="has-arrow"> <i class="uil-chart-bar"></i><span>통계</span></a>
                <ul class="sub-menu" aria-expanded="true">
                    <li class="disable"><a href="{{url('statistics/brand/index')}}">브랜드 매출</a></li>
                    <li class="disable"><a href="{{url('statistics/vendor/index')}}">사업자 매출</a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>회원</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>상품</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>클레임</strike></a></li>
                </ul>
            </li>
            <li class="auth_check">
                <a href="javascript: void(0);" class="has-arrow"> <i class="uil-meeting-board"></i><span><strike>게시판</strike></span></a>
                <ul class="sub-menu" aria-expanded="true">
                    <li><a href="{{ url('Board/board/notice') }}">공지사항</a></li>
                    <li><a href="{{ url('Board/faq-list') }}">Faq</a></li>
                    <li><a href="{{ url('Board/board/event') }}">이벤트</a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>배송사진</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>오픈마켓</strike></a></li>
                </ul>
            </li>
            <li class="auth_check">
                <a href="javascript: void(0);" class="has-arrow"> <i class="uil-parking-square"></i><span>파트너사업자</span></a>
                <ul class="sub-menu" aria-expanded="false">
                    <li class="disable"><a href="{{url('vendor/fla-business-list')}}">사업자목록</a></li>
                    <li class="disable"><a href="{{url('pass/index')}}">PASS사업자목록</a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>추천인</strike></a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>분양몰</strike></a></li>
                    <li class="disable"><a href="{{url('vendor/statistics/index')}}">통계</a></li>
                    <li class="disable"><a href="{{url('vendor/fla-cal-list')}}">정산</a></li>
                    @if(Auth::user()->auth>9)
                    <li class="disable"><a href="{{url('vendor/fla-cal-list-test')}}">테스트-정산</a></li>
                    @endif
                </ul>
            </li>
            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="uil-envelope"></i>
                    <span>알림톡/SMS</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li><a href="{{ url('KakaoTalk/Page/alimLog') }}">알림톡내역</a></li>
                    <li><a href="{{ url('/SMS/index') }}">SMS내역</a></li>
                    <li class="disable"><a href="javascript: void(0);"><strike>플러스친구</strike></a></li>
                    <li><a href="{{ url('KakaoTalk/Page/Templates') }}">카카오톡</a></li>
                    <li><a href="javascript:open_win('{{ url('/SMS/form') }}', 'send_SMS' ,700, 600, 50,50)">SMS</a></li>
                </ul>
            </li>
            <li class="auth_check">
                <a href="javascript: void(0);" class="has-arrow"> <i class="uil-monitor-heart-rate"></i><span><strike>운영</strike></span></a>
                <ul class="sub-menu" aria-expanded="false">
                    @if(Auth::user()->auth == 10)
                    <li><a href="{{url('dev/index') }}">개발</a></li>
                    @endif
                </ul>
            </li>
            </ul>

        </div>
        <!-- Sidebar -->


    </div>
</div>
<!-- Left Sidebar End -->
@if(Auth::user() -> auth < 3)
    <script>
        window.onload = function() {
            const check_menu_list = document.querySelectorAll('.auth_check')

            for (var element of check_menu_list){
                element.style.display = 'none';
            }
        }

    </script>
@endif