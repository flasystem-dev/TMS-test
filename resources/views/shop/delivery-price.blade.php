@extends('layouts.master')
@section('title')
    지역별 추가금 관리
@endsection
@section('content')
{{--<link rel="stylesheet" href="{{ URL::asset('/assets/css/shop/delivery-price.css') }}">--}}
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="get" action="?" name="search_form">
                    <div class="row">
                        <div class="col-2 py-1">
                            <div class="btn-group col-12 me-4">
                                <button type="button" class="btn btn-light waves-effect" id="sido_btn">
                                    <span id="sido_title">
                                        {{request()->sido ?? "시/도 선택"}}
                                    </span>
                                </button>
                                <input type="hidden" id="sido" name="sido" value="@isset($search_arr['sido']) {{$search_arr['sido']}} @endisset">
                                <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu" style="">
                                    <a class="dropdown-item" href="javascript:select_btn('sido','강원특별자치도');">강원특별자치도</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sido','경기');">경기</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sido','경남');">경남</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sido','경북');">경북</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sido','광주');">광주</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sido','대구');">대구</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sido','대전');">대전</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sido','부산');">부산</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sido','서울');">서울</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sido','세종특별자치시');">세종특별자치시</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sido','울산');">울산</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sido','인천');">인천</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sido','전남');">전남</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sido','전북특별자치도');">전북특별자치도</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sido','제주특별자치도');">제주특별자치도</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sido','충남');">충남</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sido','충북');">충북</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-2 py-1">
                            <div class="input-group">
                                <span class="input-group-text">지역 검색</span>
                                <input type="text" class="form-control" name="location_search" value="@isset($search_arr['location_search']){{$search_arr['location_search']}}@endisset">
                            </div>
                        </div>
                        <div class="col-8 py-1">
                            <div class="row">
                                <div class="col-12">
                                    <button style="border-radius:3px;" class="btn btn-secondary waves-effect waves-light me-2">검색</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div><!-- end card-body -->
        </div><!-- end card -->

        <div class="col-12">
            <div class="card">
                <div class="row">
                    <div class="col-12 px-4 py-3">
                        <table id="" class="table table-striped table-bordered " style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                            <tr class="px-0">
                                <th style="width:4%">번호</th>
                                <th style="width:10%">도/시</th>
                                <th style="width:10%">시/군/구</th>
                                <th>축하화환</th>
                                <th style="">근조화환</th>
                                <th style="">축하오브제</th>
                                <th style="">근조오브제</th>
                                <th style="">바구니</th>
                                <th style="">축하(쌀)</th>
                                <th style="">근조(쌀)</th>
                                <th style="">메모</th>
                                <th style="width:5%">수정</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($delivery_info as $row)
                                <tr class="text-center align-middle px-0">
                                    <td class="">
                                        <input type="checkbox" aria-label="products">
                                    </td>
                                    <!-- 상품번호 -->
                                    <td>
                                        {{$row->sido}}
                                    </td>
                                    <td>
                                        {{$row->sigungu}}
                                    </td>
                                    <td>
                                        <input type="text" class="form-control number_format text-end" id="CH{{$row->idx }}" value="{{ number_format($row->CH) }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control number_format text-end" id="GH{{ $row->idx }}" value="{{ number_format($row->GH) }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control number_format text-end" id="CO{{ $row->idx }}" value="{{ number_format($row->CO) }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control number_format text-end" id="GO{{ $row->idx }}" value="{{ number_format($row->GO) }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control number_format text-end" id="BG{{ $row->idx }}" value="{{ number_format($row->BG) }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control number_format text-end" id="CS{{ $row->idx }}" value="{{ number_format($row->CS) }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control number_format text-end" id="GS{{ $row->idx }}" value="{{ number_format($row->GS) }}">
                                    </td>
                                    <td>
                                        <textarea id="memo{{$row->idx}}" class="form-control">{{$row->memo}}</textarea>
                                    </td>
                                    <td class="center">
                                        <div class="center">
                                            <button class="btn btn-success btn-soft-success btn-sm" onclick="priceUpdate({{ $row->idx }});">수정</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $delivery_info->onEachSide(5)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div><!-- end col -->
    <button class="fixed_btn" data-bs-toggle="modal" data-bs-target="#add_product_modal"><i class="uil-box fixed_btn_icon"></i><span class="fixed_btn_text ms-1">+ 상품 추가</span></button>
@endsection
@section('script')
   <script>
       function priceUpdate(idx){
           var CH =$("#CH"+idx).val().replace(',','');
           var GH =$("#GH"+idx).val().replace(',','');
           var CO =$("#CO"+idx).val().replace(',','');
           var GO =$("#GO"+idx).val().replace(',','');
           var BG =$("#BG"+idx).val().replace(',','');
           var CS =$("#CS"+idx).val().replace(',','');
           var GS =$("#GS"+idx).val().replace(',','');
           var memo = $("#memo"+idx).val()
           $.ajax({
               url: main_url + "/shop/updateLocAddPrice",
               method: "GET",
               data: {CH:CH,GH:GH,CO:CO,GO:GO,BG:BG,CS:CS,GS:GS,memo:memo,idx:idx},
               success: function(data) {
                   if(data==="S") {
                       alert("저장되었습니다.");
                       location.reload();
                   }else{
                       alert("오류로 저장되지 않았습니다.");
                   }
               },
               error: function(error) {
                   alert('저장되지 않음 [개발팀에 문의하세요]');
                   console.log(error);
               }
           })
       }
       function select_btn(id,value){
           $('#'+id).val(value);
           $('#'+id+'_title').val(value);
           $('#sido_title').text(value);
       }
   </script>
@endsection