@component('mail::message')

<?=
    str_replace(
        [
            '{{error}}',
            '{{newstatus}}',
            '{{serial}}'
        ],
        [
            print_r($display->errors,true),
            $newStatus,
            $display->link
        ],
        $email->value
    )
?>
<br>
Best regards,<br>
{{ config('app.name') }}
@endcomponent
