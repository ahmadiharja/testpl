<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;
use App\Exports\DailyReport;
use Excel;

class DailyEmailNotification extends Notification
{
    use Queueable;
    private $facility_name;
    private $body;
    private $data1 = [];
    private $data2 = [];
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($facility_name, $body, $data1=[], $data2=[])
    {
        $this->facility_name = $facility_name;
        $this->body = $body;
        $this->data1 = $data1;
        $this->data2 = $data2;    
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
        return (new MailMessage)
            ->subject('Daily report from Facility "' . $this->facility_name . '"')
            ->line(new HtmlString($this->body))
            ->line('Thank you for using our application!')
            ->attach(
                Excel::download(
                    new DailyReport($this->data1, $this->data2),
                    'dail_report.xlsx'
                )->getFile(),
                ['as' => 'dail_report1.xlsx']
            );
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
