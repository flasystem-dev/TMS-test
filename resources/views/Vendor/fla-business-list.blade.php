@extends('layouts.master')
@section('title')
    사업자목록
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-body pb-2 pt-3">
            <div class="row">
                @foreach($member_info as $info)
                <div class="col-3 px-3">
                    <table class="table table-borderless text-center">
                        <tr>
                            @php
                                $brand = !empty($info['brand']) ? CommonCodeName($info['brand'])?? '미등록' : '미등록' ;
                            @endphp
                            <th rowspan="2" class="fs-5 p-0">{{ $brand }}</th>
                            <td class="p-0">사업자</td>
                            <td class="p-0">신규</td>
                            <td class="p-0">탈퇴</td>
                        </tr>
                        <tr>
                            <!-- 사업자 -->
                            <td class="fs-5 text-danger p-0">{{ $info['member'] }}명</td>
                            <!-- 신규 -->
                            <td class="fs-5 text-danger p-0">{{ $info['new'] }}명</td>
                            <!-- 탈퇴 -->
                            <td class="fs-5 text-danger p-0">{{ $info['withdraw'] }}명</td>
                        </tr>
                    </table>
                </div>
                @endforeach
            </div>
        </div><!-- end card -->
    </div>
</div>
<!-- row e -->

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <form method="get" action="{{ route('vendor-list') }}">
                    <div class="row">
                        <div class="col-4 py-1">
                            <div class="input-group">
                                <a class="btn btn-light me-3 rounded px-5" href="">브랜드</a>
                                <div class="btn-group">
                                    <input type="radio" class="btn-check" name="brand_type" value="BTCS" id="BTCS_check" {{request('brand_type')=="BTCS" ? "checked" : ""}}>
                                    <label class="btn btn-outline-secondary" for="BTCS_check">꽃파는사람들</label>
                                    <input type="radio" class="btn-check" name="brand_type" value="BTFCC" id="BTFCC_check" {{request('brand_type')=="BTFCC" ? "checked" : ""}}>
                                    <label class="btn btn-outline-secondary" for="BTFCC_check">플라체인 B2C</label>
                                    <input type="radio" class="btn-check" name="brand_type" value="BTFCB" id="BTFCB_check" {{request('brand_type')=="BTFCB" ? "checked" : ""}}>
                                    <label class="btn btn-outline-secondary" for="BTFCB_check">플라체인 B2B</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-3 py-1">
                            <div class="input-group">
                                <div class="btn-group me-3">
                                    <button type="button" class="btn btn-light waves-effect" style="width: 110px;">
                                        <span id="sw_1_title">
                                            {{request('sw_1_view') ?? "1차 조회 항목"}}
                                        </span>
                                    </button>
                                    <input type="hidden" id="sw_1" name="sw_1" value="{{request('sw_1') ?? "all"}}">
                                    <input type="hidden" id="sw_1_view" name="sw_1_view" value=""{{request('sw_1_view')}}"">
                                    <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="mdi mdi-chevron-down"></i>
                                    </button>
                                    <div class="dropdown-menu" style="">
                                        <a class="dropdown-item" href="javascript:select_btn('sw_1','1차 조회 항목','all');" >1차 조회 항목</a>
                                        <a class="dropdown-item" href="javascript:select_btn('sw_1','대표자','rep_name');" >대표자 이름</a>
                                        <a class="dropdown-item" href="javascript:select_btn('sw_1','연락처','rep_tel');"  >연락처</a>
                                        <a class="dropdown-item" href="javascript:select_btn('sw_1','상호명','mall_name');"  >상호명</a>
                                        <a class="dropdown-item" href="javascript:select_btn('sw_1','대표번호','gen_number');" >대표번호</a>
                                        <a class="dropdown-item" href="javascript:select_btn('sw_1','DID','did_number');" >DID</a>
                                        <a class="dropdown-item" href="javascript:select_btn('sw_1','추천인','recommend_person');" >추천인</a>
                                    </div>
                                </div>
                                <input class="form-control rounded" name="word1" type="text" id="selectedName" aria-label="word1" value="{{request('word1')}}">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="input-group">
                                <div class="btn-group me-3">
                                    <button type="button" class="btn btn-light waves-effect" style="width: 110px;">
                                        <span id="sw_2_title">
                                            {{request('sw_2_view') ?? "2차 조회 항목"}}
                                        </span>
                                    </button>
                                    <input type="hidden" id="sw_2" name="sw_2" value="{{request('sw_2') ?? "all"}}">
                                    <input type="hidden" id="sw_2_view" name="sw_2_view" value="{{request('sw_2_view')}}">
                                    <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="mdi mdi-chevron-down"></i>
                                    </button>
                                    <div class="dropdown-menu" style="">
                                        <a class="dropdown-item" href="javascript:select_btn('sw_2','2차 조회 항목','all');"  >2차 조회 항목</a>
                                        <a class="dropdown-item" href="javascript:select_btn('sw_2','대표자','rep_name');"  >대표자</a>
                                        <a class="dropdown-item" href="javascript:select_btn('sw_2','연락처','rep_tel');"  >연락처</a>
                                        <a class="dropdown-item" href="javascript:select_btn('sw_2','상호명','mall_name');"  >상호명</a>
                                        <a class="dropdown-item" href="javascript:select_btn('sw_2','대표번호','gen_number');"  >대표번호</a>
                                        <a class="dropdown-item" href="javascript:select_btn('sw_2','DID','did_number');"  >DID</a>
                                        <a class="dropdown-item" href="javascript:select_btn('sw_2','추천인','recommend_person');" >추천인</a>
                                    </div>
                                </div>
                                <input class="form-control rounded" name="word2" type="text" id="selectedName" aria-label="word2" value="{{request('word2')}}">
                            </div>
                        </div>
                        <div class="col-2">
                            <button style="border-radius:3px;" class="btn btn-secondary waves-effect waves-light me-2">검색하기</button>
                            <button style="border-radius:3px;" type="button" class="btn btn-secondary waves-effect waves-light" onclick="add_vendor();">+ 사업자추가</button>
                        </div>
                    </div>
                </form>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div><!-- end col -->
</div>
<!-- row e -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table id="vendors_tbl" class="table table-striped table-bordered " style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>브랜드</th>
                            <th>대표자<br>(동업자)</th>
                            <th>연락처</th>
                            <th>가입일</th>
                            <th>상호명<br>(URL)</th>
                            <th>대표번호<br>DID</th>
                            <th>추천인</th>
                            <th>보증<br>보증잔액</th>
                            <th>상태</th>
                            <th>특이사항</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendors as $vendor)
                            <tr class="text-center align-middle px-0">
                                <!-- 브랜드 -->
                                @php
                                    $brand_name = !empty($vendor -> brand_type) ? CommonCodeName($vendor -> brand_type)?? '미등록' : '미등록';
                                @endphp
                                <td class="text-center">
                                    {{ $brand_name }}
                                </td>
                                <!-- 대표자 / 동업자 -->
                                <td class="text-center">
                                    <a href="#" onclick="popup_vendor_info('{{ url('vendor/fla-business/view/'). "/" . $vendor -> idx }}')">
                                        {{ $vendor ->  rep_name}}
                                        @if(!empty($vendor -> partner_name))
                                            <br>({{ $vendor ->  partner_name}})
                                        @endif
                                    </a>
                                </td>
                                <!-- 연락처 -->
                                <td>
                                    {{ $vendor -> rep_tel }}
                                </td>
                                <!-- 가입일 -->
                                <td>
                                    {{ date('Y-m-d', strtotime($vendor -> registered_date)) }}
                                </td>
                                <!-- 상호명 / URL -->
                                <td>
                                    {{ $vendor -> mall_name }} <br>
                                    <a href="javascript:domain_popup('{{$vendor -> domain}}', '{{$vendor->brand_type}}');">{{ !empty($vendor -> domain) ? "(".$vendor -> domain.")" : "" }}</a>
                                </td>
                                <!-- 대표번호 / DID -->
                                <td>
                                    {{ $vendor -> gen_number }} <br>
                                    <span class="text-success">{{ $vendor -> did_number }}</span>
                                </td>
                                <!-- 추천인 -->
                                <td>
                                    @if($vendor->recommend_person!='')
                                        {{ $vendor->recommend_name($vendor -> recommend_person) }}
                                    @endif
                                </td>
                                <!-- 보증 / 보증잔액 -->
                                <td>
                                    {{ CommonCodeName($vendor -> assurance) }} <br>
                                    {{ number_format($vendor -> assurance_amount) }}
                                </td>
                                <!-- 상태 -->
                                <td>
                                    @if($vendor -> is_valid === 'Y')
                                        가입
                                    @else
                                        탈퇴
                                    @endif
                                </td>
                                <!-- 특이사항 -->
                                <td>
                                    {{ $vendor -> memo }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $vendors -> links() }}
            </div>
        </div>
    </div> <!-- end col -->
</div>
<!-- end row -->

@endsection
@section('script')
    <script>
        var count_num = 0;

        @if(isset($search_arr['type']))
            $(function(){
                $('#{{ $search_arr['type'] }}').prop('checked', true);
            });
        @endif


        function select_btn(type,title,col){
            $('#'+type+"_title").text(title);
            $('#'+type).val(col);
            $('#'+type+"_view").val(title);
        }

        function add_vendor() {
            const url = "{{ url('vendor/fla-business/view') . "/0" }}";
            open_win(url, "add_vendor",1300, 900, 0, 10);
        }

        function popup_vendor_info(url) {
            open_win(url, "vendor_info"+count_num ,1300, 900, 0, 10);
            count_num++;
        }

        function domain_popup(domain, brand) {
            let url = "http://"+domain+".flachain.net";
            if(brand==="BTCS") {
                url = "http://"+domain+".flapeople.com";
            }

            open_win(url, "domain"+count_num , 1200, 900, 0 , 0)
        }
    </script>
@endsection




