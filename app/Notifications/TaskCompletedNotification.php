<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Models\History;

class TaskCompletedNotification extends Notification
{
    use Queueable;

    protected $history;

    /**
     * Create a new notification instance.
     *
     * @param  History  $history
     * @return void
     */
    public function __construct(History $history)
    {
        $this->history = $history;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $history = $this->history;
        $resultText = $history->status_text; // 'OK' or 'Failed'
        $facilityName = '';
        $displayInfo = '';
        $productName = config('app.name');
        
        // Try to get recipient name from Alert -> User
        $recipientName = 'Recipient';
        if ($notifiable instanceof \App\Models\Alert && $notifiable->user) {
            $recipientName = $notifiable->user->fullname ?: $notifiable->user->name;
        }

        if ($history->display) {
            $displayInfo = $history->display->treetext;
            if ($history->display->workstation 
                && $history->display->workstation->workgroup 
                && $history->display->workstation->workgroup->facility) {
                $facilityName = $history->display->workstation->workgroup->facility->name;
            }
        }

        // Subject: PerfectLum — Task Completed: Calibration Task (OK) — Report Attached
        $subject = "{$productName} — Task Completed: {$history->name} ({$resultText}) — Report Attached";

        // Generate PDF report
        $version = trim(file_get_contents(base_path('version.txt')));
        $pdf = PDF::loadView('mail.task_report_pdf', [
            'item' => $history,
            'version' => $version
        ]);

        $filename = str_replace(' ', '_', $history->name) . '_' . date('Ymd_His', $history->time) . '.pdf';

        return (new MailMessage)
            ->subject($subject)
            ->markdown('mail.task_completed', [
                'history' => $history,
                'resultText' => $resultText,
                'facilityName' => $facilityName,
                'displayInfo' => $displayInfo,
                'recipientName' => $recipientName,
                'productName' => $productName,
            ])
            ->attachData($pdf->output(), $filename, [
                'mime' => 'application/pdf',
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
