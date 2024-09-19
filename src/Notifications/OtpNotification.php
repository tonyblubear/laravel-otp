<?php

namespace Blubear\LaravelOtp\Notifications;

use Illuminate\Bus\Queueable;
use Blubear\LaravelOtp\Mail\OptMail;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class OtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param string $code Otp code
     * @return void
     */
    public function __construct(
        protected string|int $code,
        protected string|int|null $message = null,
        protected string|int|null $expires = null
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): OptMail
    {
        return (new OptMail($this->code, expires: $this->expires)
        )->to($notifiable->routes['mail']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
