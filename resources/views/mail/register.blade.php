@component('mail::message')

<?=str_replace('{{username}}', $user->name, $email->value)?>

@component('mail::button', ['url' => $url])
Activate account
@endcomponent

Best regards,<br>
{{ config('app.name') }}


{{-- Subcopy --}}
@isset($url)
@component('mail::subcopy')
If you’re having trouble clicking the "Activate account" button, copy and paste the URL below.
into your web browser: {{$url}}
@endcomponent
@endisset
@endcomponent
