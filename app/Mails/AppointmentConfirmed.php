<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    protected $oContent;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($poContent)
    {
        $this->oContent = $poContent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		$aSystemParameters = \App\Models\SystemParameter::find(1)->toArray();

        return $this
			->from([
				'address' => config('mail.username'),
				'name' => config('app.name')
			])
			->subject($aSystemParameters['appointment_confirmed_email_subject'])
			->view('emails.appointment-confirmed')
			->with([
				'sBody' => html_entity_decode($aSystemParameters['appointment_confirmed_email_body']),
				'sName' => $this->oContent->sName,
				'sDate' => $this->oContent->sDate,
				'sTime' => $this->oContent->sTime
			]);
    }
}
