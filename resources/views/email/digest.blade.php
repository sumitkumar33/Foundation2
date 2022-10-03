@component('mail::message')
# DailyDigest Mail is Here

Today the count of unApproved users is : {{$count}}

@component('mail::button', ['url' => url('/admin/dashboard')])
Check Admin Panel
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
