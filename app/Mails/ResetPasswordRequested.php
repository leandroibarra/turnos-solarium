<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordRequested extends Mailable
{
    use Queueable, SerializesModels;

	/**
	 * The password reset token.
	 *
	 * @var string
	 */
	protected $token;

	/**
	 * @var mixed
	 */
	protected $notifiable;

	/**
	 * ResetPassword constructor.
	 *
	 * @param string $token
	 * @param mixed $notifiable
	 */
	public function __construct($token, $notifiable)
    {
		$this->token = $token;
		$this->notifiable = $notifiable;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
    	return $this
			->from([
				'address' => config('mail.username'),
				'name' => config('app.name')
			])
			->subject(__('Reset Password Notification'))
			->view('emails.reset-password')
			->with([
				'sUrl' => url(config('app.url').route('password.reset', $this->token, false))
			]);
    }
}
