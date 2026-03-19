@component('mail::message')

<?=
    str_replace(
        [
            '{{error}}',
            '{{newstatus}}',
            '{{serial}}'
        ],
        [
            $history->link,
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