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

Route::group(
	[
		'prefix' => '/admin',
		'middleware' => [
//			'check-admin'
		]
	],
	function() {
		Route::get('/', function() {
			return redirect('admin/login');
		});

		Route::get('/login', 'AdminController@showLoginForm')->name('admin.login');

		Route::post('/login', 'AdminController@login')->name('admin.create');

		Route::post('/logout', 'AdminController@logout')->name('admin.logout');

		Route::get('/appointments', 'AppointmentController@list')
			->middleware(['check-admin'])
			->name('appointment.list');

		Route::put('/appointments/{id}/cancel', 'AppointmentController@cancel')
			->middleware(['check-admin'])
			->name('appointment.cancel');

		Route::get('/appointments/{id}/reschedule', 'AppointmentController@reschedule')
			->middleware(['check-admin'])
			->name('appointment.reschedule');

		Route::get('/system-parameters', 'SystemParameterController@edit')
			->middleware(['check-admin'])
			->name('system-parameters.edit');

		Route::put('/system-parameters/{id}', 'SystemParameterController@update')
			->middleware(['check-admin'])
			->name('system-parameters.update');
	}
);