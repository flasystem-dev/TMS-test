@foreach($options as $key => $option)
<option value="{{$option['name']}}" ><span class="column_comment">{{$option['comment']}}</span></option>
@endforeach
