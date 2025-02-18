@if($users)
    <option value=""></option>
    @foreach($users as $user)
        <option value="{{$user->id}}"
                data-channel="{{$user->channel()}}"
                data-name="{{$user->name}}"
                data-id="{{$user->user_id}}"
                data-vendor="{{$user->is_vendor===2 ? "사업자" : ""}}">
            <span class="user_channel">{{$user->channel()}}</span>
            <span class="user_name">{{$user->name}}</span>
            <span class="user_id">{{$user->user_id}}</span>
            <span class="user_vendor">{{$user->is_vendor===2 ? "사업자" : ""}}</span>
        </option>
    @endforeach
@endif