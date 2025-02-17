@extends('layouts.master-without-nav')
@section('content')
    <style>
        .column_container { margin-bottom: 5px;}
        .column_log_text { display: inline-block; width: 160px; vertical-align: top; }
        .origin_value_text { display: inline-block; width: 155px; vertical-align: top; white-space: normal }
        .log_value_text { display: inline-block; width: 155px; vertical-align: top; white-space: normal }
        .log_text { display: inline-block; text-align: center; width: 50px; }
        .product_log_text { display: inline-block; width: 200px; vertical-align: top; white-space: normal }
    </style>
    <div class="row p-3 px-4">
        @foreach($logs as $log)
            <div class="card col-12">
                <div class="card-body px-1">
                    <table class="table m-0">
                        <tr>
                            <td style="width: 9%" class="fw-bold">
                                {{ $log -> log_by_name }}
                            </td>
                            <td style="width: 11%" class="fw-bold text-primary pe-0">
                                {{ $log -> log_status }}
                            </td>
                            <td style="overflow: auto; max-width: 400px">
                                <div style="overflow: auto; white-space: nowrap">{!! $log -> log_content !!}</div>
                            </td>
                            <td style="width: 15%">
                                {{ $log -> log_time }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

@endsection