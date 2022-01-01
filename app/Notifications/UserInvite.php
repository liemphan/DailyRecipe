<?php

namespace DailyRecipe\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class UserInvite extends MailNotification
{
    public $token;

    /**
     * Create a new notification instance.
     *
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $appName = ['appName' => setting('app-name')];

        return $this->newMailMessage()
                ->subject(trans('auth.user_invite_email_subject', $appName))
                ->greeting(trans('auth.user_invite_email_greeting', $appName))
                ->line(trans('auth.user_invite_email_text'))
                ->action(trans('auth.user_invite_email_action'), url('/register/invite/' . $this->token));
    }
}
