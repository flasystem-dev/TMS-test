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
                        <div class="item-container {{$file->status}}" onclick="download_excel('{{$file->id}}', '{{$file->file_name}}')">
                            <img src="{{asset('assets/images/excel_icon.png')}}" height="50" width="50">
                            <p class="file-name">{{$file->file_name}}</p>
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
        function download_excel(id , fileName) {
            fetch(`/order/excel/file/${id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('다운로드 실패');
                    }
                    return response.blob(); // 바이너리 데이터로 변환
                })
                .then(blob => {
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = fileName + '.xlsx';
                    link.click();
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', '다운로드 실패');
                });
        }
    </script>
@endsection