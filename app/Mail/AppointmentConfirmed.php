<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        return $this
			->from([
				'address' => config('mail.username'),
				'name' => config('app.name')
			])
			->subject(__('Appointment Confirmed'))
			->view('emails.appointment-confirmed')
			->with([
				'sName' => $this->oContent->sName,
				'sDate' => $this->oContent->sDate,
				'sTime' => $this->oContent->sTime,
			]);
    }
}
