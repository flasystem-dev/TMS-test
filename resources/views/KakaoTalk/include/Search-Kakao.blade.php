<div class="row search_template_menu used_template" id="used_template_area">
    <div class="col-3">
        <select class="form-select" name="brand_type_code" id="used_template_brand" aria-label="brand_type_code">
            <option value="BTCP">꽃파는총각</option>
            <option value="BTCC">칙칙폭폭플라워</option>
            <option value="BTSP">사팔플라워</option>
            <option value="BTBR">바로플라워</option>
            <option value="BTOM">오만플라워</option>
            <option value="BTCS">꽃파는사람들</option>
            <option value="BTFC">플라체인</option>
        </select>
    </div>
    <div class="col-3">
        <select class="form-select" name="template_type" id="used_template_type" aria-label="template_type">
            <option value="order_check">주문확인</option>
            <option value="VA_guide">가상계좌안내</option>
            <option value="pay_complete">결제완료</option>
            <option value="deli_done">배송완료</option>
            <option value="deli_photo">배송사진</option>
            <option value="send_link">링크전송</option>
            <option value="without_bank_account">무통장안내</option>
        </select>
    </div>
    <div class="col-3">
        <button type="button" class="btn btn-outline-primary" onclick="used_template();">검색</button>
    </div>
</div>
<div class="row d-none search_template_menu manage_channel" id="manage_channel_area">
    <div class="col-3">
        <select class="form-select" name="brand_code1" id="brand_code1">
            <option value="">채널 관리</option>
            <option value="BTCP">꽃파는총각</option>
            <option value="BTCC">칙칙폭폭플라워</option>
            <option value="BTSP">사팔플라워</option>
            <option value="BTBR">바로플라워</option>
            <option value="BTOM">오만플라워</option>
        </select>
    </div>
    <div class="col-3">
        <button type="button" class="btn btn-outline-primary" id="manage_channel">검색</button>
    </div>
</div>
<div class="row d-none search_template_menu manage_template" id="manage_template_area">
    <div class="col-3">
        <select class="form-select" name="brand_code2" id="brand_code2">
            <option value="">템플릿 관리</option>
            <option value="BTCP">꽃파는총각</option>
            <option value="BTCC">칙칙폭폭플라워</option>
            <option value="BTSP">사팔플라워</option>
            <option value="BTBR">바로플라워</option>
            <option value="BTOM">오만플라워</option>
        </select>
    </div>
    <div class="col-3">
        <button type="button" class="btn btn-outline-primary" id="manage_template">검색</button>
    </div>
</div>
<div class="row d-none search_template_menu all_template" id="search_template_area">
    <div class="col-3">
        <select class="form-select" name="plusFriendID" id="plusFriendID">
            <option value="">플러스친구</option>
            @foreach($templates as $template)
                <option value="{{ $template -> plusFriendID }}">{{ $template -> plusFriendID }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-3 d-none" id="templateName_area">
        <select class="form-select" name="templateName" id="templateName">
        </select>
    </div>
    <div class="col-3">
        <button type="button" class="btn btn-outline-primary" id="find_template">검색</button>
    </div>
</div>
