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

Route::get('/', 'IndexController@index')->name('index.index');

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
		})->name('admin');

		Route::group(
			[
				'prefix' => '/login'
			],
			function() {
				Route::get('/', 'AdminController@showLoginForm')->name('admin.login');

				Route::post('/', 'AdminController@login')->name('admin.create');
			}
		);

		Route::post('/logout', 'AdminController@logout')->name('admin.logout');


		Route::group(
			[
				'prefix' => '/system-parameters',
				'middleware' => [
					'role:Sysadmin|Admin'
				]
			],
			function() {
				Route::get('/', 'SystemParameterController@edit')
					->middleware(['permission:admin.system-parameters.edit'])
					->name('system-parameters.edit');

				Route::put('/{id}', 'SystemParameterController@update')
					->middleware(['permission:admin.system-parameters.update'])
					->name('system-parameters.update');
			}
		);

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

				Route::group(
					[
						'prefix' => '/{id}'
					],
					function() {
						Route::put('/cancel', 'AppointmentController@cancel')
							->middleware(['permission:admin.appointment.cancel'])
							->name('appointment.cancel');

						Route::group(
							[
								'prefix' => '/reschedule'
							],
							function() {
								Route::get('/', 'AppointmentController@reschedule')
									->middleware(['permission:admin.appointment.reschedule'])
									->name('appointment.reschedule');

								Route::post('/', 'AppointmentController@update')
									->middleware(['permission:admin.appointment.update'])
									->name('appointment.update');
							}
						);
					}
				);
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

				Route::get('/create', 'ExceptionController@create')
					->middleware(['permission:admin.exception.create'])
					->name('exception.create');

				Route::post('/', 'ExceptionController@store')
					->middleware(['permission:admin.exception.store'])
					->name('exception.store');

				Route::group(
					[
						'prefix' => '/{id}'
					],
					function() {
						Route::get('/edit', 'ExceptionController@edit')
							->middleware(['permission:admin.exception.edit'])
							->name('exception.edit');

						Route::put('', 'ExceptionController@update')
							->middleware(['permission:admin.exception.update'])
							->name('exception.update');

						Route::put('/delete', 'ExceptionController@delete')
							->middleware(['permission:admin.exception.delete'])
							->name('exception.delete');
					}
				);
			}
		);

		Route::group(
			[
				'prefix' => '/users',
				'middleware' => [
					'role:Sysadmin'
				]
			],
			function() {
				Route::get('/', 'UserController@list')
					->middleware(['permission:admin.user.list'])
					->name('user.list');

				Route::group(
					[
						'prefix' => '/{id}'
					],
					function() {
						Route::group(
							[
								'prefix' => '/permissions'
							],
							function() {
								Route::get('/', 'PermissionController@edit')
									->middleware(['permission:admin.permission.edit'])
									->name('permission.edit');

								Route::put('/', 'PermissionController@update')
									->middleware(['permission:admin.permission.update'])
									->name('permission.update');
							}
						);
					}
				);
			}
		);

		Route::group(
			[
				'prefix' => '/site-parameters',
				'middleware' => [
					'role:Sysadmin|Admin'
				]
			],
			function() {
				Route::get('/', 'SiteParameterController@edit')
					->middleware(['permission:admin.site-parameters.edit'])
					->name('site-parameters.edit');

				Route::put('/{id}', 'SiteParameterController@update')
					->middleware(['permission:admin.site-parameters.update'])
					->name('site-parameters.update');
			}
		);
	}
);