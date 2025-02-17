
<div style="display: flex; justify-content: space-between; padding: 20px; border: 1px solid #ced4da; border-radius: 10px;">
    <div style="flex-basis: 50%">
        <pre id="talk_template">{{ $template -> template }}</pre>
    </div>
    <div style="flex-basis: 50%;  border: 1px solid #ced4da; border-radius: 10px; padding: 20px;">
        @foreach($variables as $key => $value)
            <div class="input-group mb-3">
                <input type="text" class="form-control" name="variables[]" value="{{ $key ?? "" }}" style="width: 15%" readonly>
                <span class="input-group-text" style="width: 10%; text-align: center; display: inline-block"> => </span>
                <input type="text" name="values[]" class="form-control ps-3" value="{{ $value ?? "" }}" style="width: auto;">
            </div>
        @endforeach
    </div>
</div>
<div style="margin-top: 10px; text-align: end">
    <button type="button" class="btn btn-secondary" onclick="insert_value()">변수 적용</button>
</div>