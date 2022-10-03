<div class="container">
    {{($login_error??'')}}
    <form method="POST" action="{{route('login')}}">
        @csrf
        <label for="login" class="control-label">Login</label>
        <input type="text" name="email" placeholder="Email" required />
        <br>
        <label for="password" class="control-label">Password</label>
        <input type="text" name="password" placeholder="Password" required />
        <br>
        <button type="submit" name="Submit">Submit</button>
    </form>
</div>