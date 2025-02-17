@extends('layouts.master')
@section('title')
    알림톡 전송내역
@endsection
@section('content')
    <div class="modal fade" id="cancel_refuse" tabindex="-1" aria-labelledby="cancel_refuseLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="cancel_refuseLabel">전송내용확인</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <textarea name="alimTalkTemplate" class="form-control" id="alimTalkTemplate" style="height: 300px;" disabled>
                    </textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body spacer_zero">
                        <div class="accordion accordion-flush" id="accordionFlushExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-headingTwo">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                        <strong>검색 필터 열기</strong>
                                    </button>
                                </h2>
                                <form method="get" action="?">
                                        <div class="accordion-body">
                                            <div class="input-group mb-3">
                                                <div class="btn-group col-md-2 me-4">
                                                    <button type="button" class="btn btn-light col-md-3 waves-effect">
                                                        <span id="sw_1_title">
                                                            @if($search_arr['sw_1_view'])
                                                                {{$search_arr['sw_1_view']}}
                                                            @else
                                                                검색 항목
                                                            @endif
                                                        </span>
                                                    </button>
                                                    <input type="hidden" id="sw_1" name="sw_1" value="{{$search_arr['sw_1']}}">
                                                    <input type="hidden" id="sw_1_view" name="sw_1_view" value="{{$search_arr['sw_1_view']}}">
                                                    <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="mdi mdi-chevron-down"></i>
                                                    </button>
                                                    <div class="dropdown-menu" style="">
                                                        <a class="dropdown-item" href="javascript:select_btn('sw_1','검색 항목','');">검색 항목</a>
                                                        <a class="dropdown-item" href="javascript:select_btn('sw_1','주문번호','od_id');">주문번호</a>
                                                        <a class="dropdown-item" href="javascript:select_btn('sw_1','휴대폰번호','phone');">휴대폰번호</a>
                                                    </div>
                                                </div>
                                                <input class="form-control col-md-1  me-4" name="word1" type="text" id="selectedName" value="{{$search_arr['word1']}}">
                                                <button style="border-radius:3px;" class="btn btn-secondary waves-effect waves-light col-md-2">검색하기</button>
                                            </div>
                                            <div class="input-group mb-3">
                                                <div class="btn-group col-md-2 me-4">
                                                    <button type="button" class="btn btn-light col-md-3 waves-effect">
                                                        <span id="sw_1_title">
                                                            발송일시
                                                        </span>
                                                    </button>
                                                    <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="mdi mdi-chevron-down"></i>
                                                    </button>
                                                    <div class="dropdown-menu" style="">
                                                        <a class="dropdown-item" href="javascript:select_btn('sw_2','발송일시','');">발송일시</a>
                                                    </div>
                                                </div>
                                                <div id="datepicker1">
                                                    <input type="text" class="form-control col-md-2" id="start_date" name="start_date" value="{{$search_arr['start_date']}}" data-date-format="yyyy-mm-dd" data-date-container='#datepicker1' data-provide="datepicker">
                                                </div>
                                                <strong class="mx-2 mt-2">~</strong>
                                                <div id="datepicker2" class="mh-10">
                                                    <input type="text" class="form-control col-md-2" id="end_date" name='end_date' value="{{$search_arr['end_date']}}" data-date-format="yyyy-mm-dd" data-date-container='#datepicker1' data-provide="datepicker">
                                                </div>
                                                <button type="button" class="btn btn-light ms-4" onclick="dateSel('어제');">어제</button>
                                                <button type="button" class="btn btn-light" onclick="dateSel('오늘');">오늘</button>
                                                <button type="button" class="btn btn-light" onclick="dateSel('내일');">내일</button>
                                                <button type="button" class="btn btn-light" onclick="dateSel('이번주');">이번주</button>
                                                <button type="button" class="btn btn-light" onclick="dateSel('이번달');">이번달</button>
                                                <button type="button" class="btn btn-light" onclick="dateSel('지난주');">지난주</button>
                                                <button type="button" class="btn btn-light" onclick="dateSel('지난달');">지난달</button>
                                                <button type="button" class="btn btn-light" onclick="dateSel('3개월');">3개월</button>
                                                <button type="button" class="btn btn-light" onclick="dateSel('6개월');">6개월</button>
                                                <button type="button" class="btn btn-light" onclick="dateSel('금년');">금년</button>
                                                <button type="button" class="btn btn-light" onclick="dateSel('전년');">전년</button>
                                            </div>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        {{--datatable-buttons--}}
                        <table id="" class="table table-striped table-bordered "
                               style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                            <tr>
                                <th>번호</th>
                                <th>발송일시</th>
                                <th>템플릿</th>
                                <th>주문번호</th>
                                <th>수신번호</th>
                                <th>전송결과</th>
                                <th>내용확인</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($count>0)
                                @foreach($alim_log as $row)
                                    <tr>
                                        <td class="center" style="vertical-align: center;">{{$row->idx}}</td>
                                        <td class="center" style="vertical-align: center;">{{$row->log_time}}</td>
                                        <td class="center" style="vertical-align: center;">{{$row->templateName}}</td>
                                        <td class="center" style="vertical-align: center;">{{$row->od_id}}</td>
                                        <td class="center" style="vertical-align: center;">{{$row->phone}}</td>
                                        <td class="center" style="vertical-align: center;">
                                            @if($row->log_status=='성공')
                                                <button type="button" class="btn btn-success btn-sm">성공</button>
                                            @else
                                                <button type="button" class="btn btn-danger btn-sm">실패</button>
                                            @endif
                                        </td>
                                        <td class="center" style="vertical-align: center;">
                                            <button type="button" class="btn btn-info btn-soft-info btn-sm waves-effect waves-light" onclick="road_template('{{$row->idx}}');" data-bs-toggle="modal" data-bs-target="#cancel_refuse">확인</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        {{ $alim_log->onEachSide(2)->links() }}
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
        @endsection
        @section('script')
            <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
            <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
            <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
            <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
            <script src="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
            <script src="{{ URL::asset('/assets/libs/datepicker/datepicker.min.js') }}"></script>
            <script src="{{ URL::asset('/assets/js/pages/ecommerce_orders.init.js') }}"></script>
            <script>
                function road_template(idx){
                    $.ajax({
                        url: "{{url('/KakaoTalk/Page/roadAlimTalkTemplate')}}",
                        type: "GET",
                        data: {
                            "idx" : idx
                        },
                        success: function(response) {
                            $('#alimTalkTemplate').val(response);
                        },
                        error: function (error){
                            console.log(error);
                            alert('에러발생! 개발팀에 문의하세요.');
                        }
                    });
                }

                function select_btn(id,value,hidden){
                    $('#'+id).val(hidden);
                    $('#'+id+'_title').text(value);
                    $('#'+id+'_view').val(value);
                }

                function dateSel(type){
                    var start_date = '';
                    var end_date = '';
                    if(type=='오늘'){
                        start_date ='{{$commonDate['today']}}';
                        end_date ='{{$commonDate['today']}}';
                    }else if(type=='어제'){
                        start_date ='{{$commonDate['yesterday']}}';
                        end_date ='{{$commonDate['yesterday']}}';
                    }else if(type=='내일'){
                        start_date ='{{$commonDate['tomorrow']}}';
                        end_date ='{{$commonDate['tomorrow']}}';
                    }else if(type=='이번주'){
                        start_date ='{{$commonDate['week']}}';
                        end_date ='{{$commonDate['today']}}';
                    }else if(type=='이번달'){
                        start_date ='{{$commonDate['month']}}';
                        end_date ='{{$commonDate['month_e']}}';
                    }else if(type=='지난주'){
                        start_date ='{{$commonDate['preg_week_s']}}';
                        end_date ='{{$commonDate['preg_week_e']}}';
                    }else if(type=='지난달'){
                        start_date ='{{$commonDate['preg_month_s']}}';
                        end_date ='{{$commonDate['preg_month_e']}}';
                    }else if(type=='3개월'){
                        start_date ='{{$commonDate['month3']}}';
                        end_date ='{{$commonDate['month_e']}}';
                    }else if(type=='6개월'){
                        start_date ='{{$commonDate['month6']}}';
                        end_date ='{{$commonDate['month_e']}}';
                    }else if(type=='금년'){
                        start_date ='{{$commonDate['year']}}';
                        end_date ='{{$commonDate['year_e']}}';
                    }else if(type=='전년'){
                        start_date ='{{$commonDate['preg_year_s']}}';
                        end_date ='{{$commonDate['preg_year_e']}}';
                    }
                    $('#start_date').val(start_date);
                    $('#end_date').val(end_date);
                }

            </script>
@endsection




