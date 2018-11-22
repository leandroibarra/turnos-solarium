<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
		DB::table('system_parameters')->insert([
			'appointment_minutes' => '10',
			'appointment_until_days' => '60',
			'appointment_confirmed_email_subject' => 'Turno Confirmado',
			'appointment_confirmed_email_body' => '<p>Hola @_NAME_@,</p><p>Su turno ha sido confirmado. Si tiene alguna pregunta o desea reprogramar su turno, contáctenos.</p><p>Hasta pronto.</p><p>Detalles de su turno:</p><p>Fecha: @_DATE_@</p><p>Hora: @_TIME_@</p>',
			'created_at' => date('Y-m-d H:m:s'),
			'updated_at' => date('Y-m-d H:m:s')
		]);
    }
}
