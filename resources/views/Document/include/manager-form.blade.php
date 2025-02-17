@if($manager)
    <input type="hidden" name="id" value="{{$manager->id}}">
    <div class="form_row">
        <div class="mb-3 input-group">
            <span class="input-group-text">대표</span>
            <input type="radio" class="btn-check" id="is_default_y" value="1" name="is_default" {{$manager->is_default===1? "checked":""}}>
            <label for="is_default_y" class="form-control cursor_p is_default">대표</label>
            <input type="radio" class="btn-check" id="is_default_f" value="0" name="is_default" {{$manager->is_default===0? "checked":""}}>
            <label for="is_default_f" class="form-control cursor_p is_default">일반</label>
        </div>
    </div>
    <div class="form_row">
        <div class="mb-3 input-group">
            <span class="input-group-text">이름</span>
            <input type="text" class="form-control" name="name" value="{{$manager->name}}">
        </div>
        <div class="mb-3 input-group">
            <span class="input-group-text">연락처</span>
            <input type="text" class="form-control" name="tel" value="{{$manager->tel}}" oninput="auto_hyphen(event)">
        </div>
    </div>
    <div class="form_row">
        <div class="mb-3 input-group">
            <span class="input-group-text">이메일</span>
            <input type="text" class="form-control" name="email" value="{{$manager->email}}">
        </div>
        <div class="mb-3 input-group">
            <span class="input-group-text">팩스</span>
            <input type="text" class="form-control" name="fax" value="{{$manager->fax}}">
        </div>
    </div>
    <div class="form_row">
        <div class="mb-3 input-group">
            <span class="input-group-text">메모</span>
            <textarea class="form-control" name="memo">{{$manager->memo}}</textarea>
        </div>
    </div>
@else
    <input type="hidden" name="id" value="0">
    <div class="form_row">
        <div class="mb-3 input-group">
            <span class="input-group-text">대표</span>
            <input type="radio" class="btn-check" id="is_default_y" name="is_default" value="1">
            <label for="is_default_y" class="form-control cursor_p is_default">대표</label>
            <input type="radio" class="btn-check" id="is_default_f" name="is_default" value="0">
            <label for="is_default_f" class="form-control cursor_p is_default">일반</label>
        </div>
    </div>
    <div class="form_row">
        <div class="mb-3 input-group">
            <span class="input-group-text">이름</span>
            <input type="text" class="form-control" name="name" >
        </div>
        <div class="mb-3 input-group">
            <span class="input-group-text">연락처</span>
            <input type="text" class="form-control" name="tel" oninput="auto_hyphen(event)">
        </div>
    </div>
    <div class="form_row">
        <div class="mb-3 input-group">
            <span class="input-group-text">이메일</span>
            <input type="text" class="form-control" name="email">
        </div>
        <div class="mb-3 input-group">
            <span class="input-group-text">팩스</span>
            <input type="text" class="form-control" name="fax">
        </div>
    </div>
    <div class="form_row">
        <div class="mb-3 input-group">
            <span class="input-group-text">메모</span>
            <textarea class="form-control" name="memo"></textarea>
        </div>
    </div>
@endif