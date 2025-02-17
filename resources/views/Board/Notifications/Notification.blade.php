@extends('layouts.master')
@section('title')
    알림
@endsection
@section('content')
    <div class="row mt-2" style="min-width: 800px;">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-12">

                            <table id="" class="table table-striped table-bordered " style="table-layout: fixed;  border-collapse: separate; border-spacing: 0; width: 100%;">
                                <thead>
                                <tr style="max-height: 100px;">
                                    <th style="width: 40px"></th>
                                    <th style="width: 60px">종류</th>
                                    <th style="width: 15%">제목</th>
                                    <th>내용</th>
                                    <th style="width: 15%">생성시간</th>
                                    <th style="width: 60px">확인</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($notifications as $noti)
                                    <tr class="text-center align-middle px-0" style="max-height: 100px;">

                                        <td class="px-0">
                                            <input type="checkbox" name="vendors" aria-label="vendors">
                                        </td>
                                        <!-- 종류 -->
                                        <td class="p-0">
                                            @if($noti -> type == 'error')
                                                <i class="uil-ban fs-2 text-danger"></i>
                                            @elseif($noti -> type == 'noti')
                                                <i class="uil-info-circle fs-2 text-info"></i>

                                            @endif
                                        </td>
                                        <!-- 제목 -->
                                        <td>
                                            {{ $noti -> title }}
                                        </td>
                                        <!-- 내용 -->
                                        <td style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $noti -> content }}
                                        </td>
                                        <!-- 생성시간 -->
                                        <td>
                                            {{ $noti -> created_at }}
                                        </td>
                                        <td class="px-0 py-1">
                                            @if($noti -> is_checked == 'Y')
                                                <button type="button" class="btn p-0">
                                                    <i class="uil-check-circle fs-2 text-success"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn p-0" onclick="check_notification(event)">
                                                    <i class="uil-question-circle fs-2 text-warning" data-idx="{{ $noti -> noti_id }}"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function check_notification(e) {
            if(confirm('알림 확인 체크 하시겠습니까?')){
                $.ajax({
                    url: '{{ route('Notification-Check') }}',
                    type: 'post',
                    data: {
                        'noti_id': e.target.dataset.idx,
                        'name': '{{ Auth::user()->name }}'
                    },
                    success: function(data) {
                        alert(data)
                        console.log(data)
                        location.reload();
                    },
                    error: function(error){
                        alert('[에러발생] 개발팀에 문의하세요.');
                        console.log(error);
                    }
                });
            }
        }
    </script>
@endsection