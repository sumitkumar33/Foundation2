<div class="container">
    @php
     $temp = $data??''; 
    @endphp
    <h3>{{$title}}</h3>
    <form method="post" action="{{$url}}">
        {{$error??''}}
    @csrf
    <input type="text" placeholder="Name" name="name" value="{{old('name')??(($data->name)??'')}}" required /><br>
    @error('name')
    {{$message}}
    <br>
    @enderror
    <input type="text" placeholder="Email" name="email" value="{{old('email')??(($data->email)??'')}}" required /><br>
    @error('email')
    {{$message}}
    <br>
    @enderror
    @if ($temp == '')
    <input type="text" placeholder="Password" name="password" @if($temp == '') required @endif /><br>
    @error('password')
    {{$message}}
    <br>
    @enderror
    <input type="text" placeholder="Confirm Password" name="confirm_password" @if($temp == '') required @endif /><br>
    @error('confirm_password')
    {{$message}}
    <br>
    @enderror
    <input type="radio" name="role_id" checked value=1 > Student </input>
    <input type="radio" name="role_id" value=2 > Teacher </input><br>
    @error('role_id')
    {{$message}}
    <br>
    @enderror
    @endif
    <button type="submit" name="Submit">Submit</button><br>
    </form>
    @if($temp != '')
    <form method="POST" action="{{route('destroy', [$id])}}">
        @csrf
        <button type="submit" name="delete" class="btn btn-danger" value="{{$id}}" >Delete</button>
    @endif
</div>