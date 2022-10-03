@component('mail::message')
# Hello! {{$data->name}}

Your account has been approved by {{$ext->name}}.

@component('mail::button', ['url' => url('/dashboard')])
Check Your Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
