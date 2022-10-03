<div class="container">
        {{$error??''}}
        <h3>{{$title}}</h3>
    <form method="post" action="{{ $url }}">
        @csrf
        <input type="text" placeholder="Expertise Subject" name="expertise_subject" value="{{old('expertise_subject')??$data->expertise_subject??''}}" required /><br>
        <input type="number" placeholder="Experience" name="experience" value="{{old('experience')??$data->experience??''}}" required /><br>
        <button type="submit" name="Submit">Submit</button>
    </form>
</div>