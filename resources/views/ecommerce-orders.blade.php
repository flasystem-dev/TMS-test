@extends('layouts.master')
@section('title')
    <span class="me-5">전체 주문 관리</span>
    <button type="button" class="btn btn-outline-warning ms-1" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-trigger="hover focus" data-bs-content="test">꽃총</button>
    <button type="button" class="btn btn-outline-success ms-1" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-trigger="hover focus" data-bs-content="test">칙폭</button>
    <button type="button" class="btn btn-outline-success ms-1" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-trigger="hover focus" data-bs-content="test">사팔</button>
    <button type="button" class="btn btn-outline-success ms-1" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-trigger="hover focus" data-bs-content="test">바로</button>
    <button type="button" class="btn btn-outline-danger ms-1" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-trigger="hover focus" data-bs-content="test">오만</button>
@endsection
        @section('css')
            <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
        @endsection

        @section('content')
            @component('common-components.breadcrumb')
                @slot('title') 전체주문관리 @endslot
                @slot('pagetitle') 주문관리 @endslot
            @endcomponent

            <div class="row">
                <div class="col-12">
                    <div class="card card-body">
                        <h3 class="card-title">Special title treatment</h3>
                        <p class="card-text">With supporting text below as a natural lead-in to additional
                            content.</p>
                        <a href="#" class="btn btn-primary waves-effect waves-light">Go somewhere</a>
                    </div><!-- end card -->
                </div>
{{--                {{$orders}}--}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                                   style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                <tr>
                                    <th>주문번호</th>
                                    <th>오픈마켓</th>
                                    <th>브랜드</th>
                                    <th>주문일<br>배송일</th>
                                    <th>주문자<br>연락처</th>
                                    <th>주문상품<br>결제금액</th>
                                    <th>배송지<br>인수자</th>
                                    <th>결제상태</th>
                                    <th>배송상태</th>
                                    <th>주문담당자</th>
                                    <th>전송</th>
                                    <th>배송사진</th>
                                </tr>
                                </thead>


                                <tbody>
                           
                                <tr>
                                    <td>Jackson Bradshaw</td>
                                    <td>Director</td>
                                    <td>New York</td>
                                    <td>65</td>
                                    <td>2008/09/26</td>
                                    <td>$645,750</td>
                                    <td>Timothy Mooney</td>
                                    <td>Office Manager</td>
                                    <td>London</td>
                                    <td>37</td>
                                    <td>2008/12/11</td>
                                    <td>$136,200</td>
                                </tr>
                                <tr>
                                    <td>Bruno Nash</td>
                                    <td>Software Engineer</td>
                                    <td>London</td>
                                    <td>38</td>
                                    <td>2011/05/03</td>
                                    <td>$163,500</td>
                                    <td>Olivia Liang</td>
                                    <td>Support Engineer</td>
                                    <td>Singapore</td>
                                    <td>64</td>
                                    <td>2011/02/03</td>
                                    <td>$234,500</td>
                                </tr>
                                <tr>
                                    <td>Sakura Yamamoto</td>
                                    <td>Support Engineer</td>
                                    <td>Tokyo</td>
                                    <td>37</td>
                                    <td>2009/08/19</td>
                                    <td>$139,575</td>
                                    <td>Thor Walton</td>
                                    <td>Developer</td>
                                    <td>New York</td>
                                    <td>61</td>
                                    <td>2013/08/11</td>
                                    <td>$98,540</td>
                                </tr>

                                <tr>
                                    <td>Michael Bruce</td>
                                    <td>Javascript Developer</td>
                                    <td>Singapore</td>
                                    <td>29</td>
                                    <td>2011/06/27</td>
                                    <td>$183,000</td>
                                    <td>Michael Bruce</td>
                                    <td>Javascript Developer</td>
                                    <td>Singapore</td>
                                    <td>29</td>
                                    <td>2011/06/27</td>
                                    <td>$183,000</td>
                                </tr>
                                <tr>
                                    <td>Donna Snider</td>
                                    <td>Customer Support</td>
                                    <td>New York</td>
                                    <td>27</td>
                                    <td>2011/01/25</td>
                                    <td>$112,000</td>
                                    <td>Donna Snider</td>
                                    <td>Customer Support</td>
                                    <td>New York</td>
                                    <td>27</td>
                                    <td>2011/01/25</td>
                                    <td>$112,000</td>
                                </tr>
                                </tbody>
                            </table>
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
        @endsection
