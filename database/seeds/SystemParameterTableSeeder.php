<?php

use Illuminate\Database\Seeder;

class SystemParameterTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		\App\Models\SystemParameter::create([
			'appointment_minutes' => '10',
			'appointment_until_days' => '60',
			'appointment_confirmed_email_subject' => 'Turno Confirmado',
			'appointment_confirmed_email_body' => '<p>Hola @_NAME_@,</p><p>Su turno ha sido confirmado. Si tiene alguna pregunta o desea reprogramar su turno, contáctenos.</p><p>Hasta pronto.</p><p>Detalles de su turno:</p><p>Fecha: @_DATE_@</p><p>Hora: @_TIME_@</p>',
			'appointment_cancelled_email_subject' => 'Turno Cancelado',
			'appointment_cancelled_email_body' => '<p>Hola @_NAME_@,</p><p>Su turno ha sido cancelado. Si tiene alguna pregunta o desea reprogramar su turno, contáctenos.</p><p>Hasta pronto.</p><p>Detalles de su turno cancelado:</p><p>Fecha: @_DATE_@</p><p>Hora: @_TIME_@</p>',
			'created_at' => date('Y-m-d H:m:s'),
			'updated_at' => date('Y-m-d H:m:s')
		]);
	}
}
