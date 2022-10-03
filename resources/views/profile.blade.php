<div class="container">
    <h3>{{$title??''}}</h3>
    <form method="post" action="{{ $url??'' }}">
        {{$error??''}}
        @csrf
        <input type="text" placeholder="Address" name="address" value="{{ old('address')??$data->address??"" }}" required /><br>
        <input type="text" placeholder="Profile Image URL" name="profile_image" value="{{ old('profile_image')??$data->profile_image??'' }}" required /><br>
        <input type="text" placeholder="Current School" name="current_school" value="{{ old('current_school')??$data->current_school??'' }}" required /><br>
        <input type="text" placeholder="Previous School" name="previous_school" value="{{ old('previous_school')??$data->previous_school??'' }}" required /><br>
        <button type="submit" name="Submit">Submit</button><br>
    </form>
</div>