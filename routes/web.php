<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	return redirect('book');
//    return view('welcome');
});

Auth::routes();

// Define global parameter patterns
Route::pattern('iYear', '^(19\d|2\d\d)\d$');
Route::pattern('iMonth', '^(0[1-9]|1[012])$');
Route::pattern('iDay', '^(0[1-9]|[1-2][0-9]|3[0-1])$');

Route::group(
	[
		'prefix' => '/book',
		'middleware' => [
			'auth'
		]
	],
	function() {
		Route::get('/', 'BookController@index')->name('book.index');

		Route::get('/confirm', 'BookController@create')
			->middleware(['check-appointment'])
			->name('book.create');
	}
);

Route::group(
	[
		'prefix' => '/appointments',
		'middleware' => [
			'auth'
		]
	],
	function() {
		Route::get('/{iYear}/{iMonth}/{iDay}', 'AppointmentController@index')->name('appointment.index');

		Route::post('/set', 'AppointmentController@set')
			->name('appointment.set');

		Route::post('/', 'AppointmentController@store')
			->middleware(['check-appointment'])
			->name('appointment.store');
	}
);

Route::get('/calendars/{iYear}/{iMonth}', 'CalendarController@index')
	->middleware(['auth'])
	->name('calendar.index');
