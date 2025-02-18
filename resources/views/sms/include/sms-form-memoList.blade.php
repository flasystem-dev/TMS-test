@foreach($msg_list as $msg)
    <div class="msg_container cursor_p" onclick="select_msg(event)"><pre onclick="select_msg(event)">{!! $msg -> msg !!}</pre></div>
@endforeach