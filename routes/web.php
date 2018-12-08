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
		'prefix' => '/admin'
	],
	function() {
		Route::get('/', function() {
			return redirect('admin/login');
		});

		Route::get('/login', 'AdminController@showLoginForm')->name('admin.login');

		Route::post('/login', 'AdminController@login')->name('admin.create');

		Route::post('/logout', 'AdminController@logout')->name('admin.logout');

		Route::get('/system-parameters', 'SystemParameterController@edit')
			->middleware([
				'role:Sysadmin|Admin',
				'permission:admin.system-parameters.edit'
			])
			->name('system-parameters.edit');

		Route::put('/system-parameters/{id}', 'SystemParameterController@update')
			->middleware([
				'role:Sysadmin|Admin',
				'permission:admin.system-parameters.update'
			])
			->name('system-parameters.update');

		Route::group(
			[
				'prefix' => '/appointments',
				'middleware' => [
					'role:Sysadmin|Admin'
				]
			],
			function() {
				Route::get('/', 'AppointmentController@list')
					->middleware(['permission:admin.appointment.list'])
					->name('appointment.list');

				Route::put('/{id}/cancel', 'AppointmentController@cancel')
					->middleware(['permission:admin.appointment.cancel'])
					->name('appointment.cancel');

				Route::get('/{id}/reschedule', 'AppointmentController@reschedule')
					->middleware(['permission:admin.appointment.reschedule'])
					->name('appointment.reschedule');

				Route::post('/{id}/reschedule', 'AppointmentController@update')
					->middleware(['permission:admin.appointment.update'])
					->name('appointment.update');
			}
		);

		Route::group(
			[
				'prefix' => '/exceptions',
				'middleware' => [
					'role:Sysadmin|Admin'
				]
			],
			function() {
				Route::get('/', 'ExceptionController@list')
					->middleware(['permission:admin.exception.list'])
					->name('exception.list');

				Route::get('/{id}/edit', 'ExceptionController@edit')
					->middleware(['permission:admin.exception.edit'])
					->name('exception.edit');

				Route::put('/{id}', 'ExceptionController@update')
					->middleware(['permission:admin.exception.update'])
					->name('exception.update');

				Route::put('/{id}/delete', 'ExceptionController@delete')
					->middleware(['permission:admin.exception.delete'])
					->name('exception.delete');

				Route::get('/create', 'ExceptionController@create')
					->middleware(['permission:admin.exception.create'])
					->name('exception.create');

				Route::post('/', 'ExceptionController@store')
					->middleware(['permission:admin.exception.store'])
					->name('exception.store');
			}
		);
	}
);