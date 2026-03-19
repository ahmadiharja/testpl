@component('mail::message')

<?=str_replace('{{raw_password}}', $user->sync_password_raw,str_replace('{{sync_user}}',$user->sync_user,str_replace('{{username}}', $user->name, $email->value)))?>


Best regards,<br>
{{ config('app.name') }}
@endcomponent
