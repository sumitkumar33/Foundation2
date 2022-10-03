<div class="container">
    <h3>{{$title}}</h3>
    <form method="post" action="{{ $url }}">
        {{$error??''}}
        @csrf
        <input type="text" placeholder="Parent Name" name="parent_name" value="{{ old('parent_name')??$data->parent_name??'' }}" required /><br>
        <input type="number" placeholder="Parent Contact" name="parent_contact" value="{{ old('parent_contact')??$data->parent_contact??'' }}" required /><br>
        <button type="submit" name="Submit">Submit</button><br>
    </form>
</div>
