@if($users->isNotEmpty())
    <option value="">- 선택안함 -</option>
    @foreach($users as $user)
        <option value="{{$user->user_id}}" data-id="{{$user->user_id}}" data-name="{{$user->name}}" data-phone="{{$user->phone}}">{{$user->user_id}}{{$user->name}}{{$user->phone}}</option>
    @endforeach
@endif
