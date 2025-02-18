@extends('layouts.master')
@section('title')
    주문 엑셀 다운로드
@endsection
<link href="{{ asset('/assets/css/excel/order/index.css') }}" rel="stylesheet">
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-body">
                <div class="div-container">
                    @foreach($files as $file)
                        <div class="item-container {{$file->status}}" onclick="download_excel('{{$file->file_url}}')">
                            <img src="{{asset('assets/images/excel_icon.png')}}" height="50" width="50">
                            <p class="file-name mb-2">{{$file->file_name}}</p>
                            <p class="time-data">📅 요청 <span class="fw-semibold text-info">{{$file->created_at}}</span></p>
                            @if($file->status === "completed")
                            <p class="time-data">✅ 완료 <span class="fw-semibold text-success">{{$file->completed_time}}</span></p>
                            @endif
                            <p class="status {{$file->status}}">{{$file->status}}</p>
                            <p class="user-name">{{$file->requester}}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function download_excel(url) {
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', '');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
@endsection