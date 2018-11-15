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

Route::get('/book', 'BookController@index')
	->middleware(['auth'])
	->name('book.index');

Route::get('/calendar/{iYear}/{iMonth}', 'CalendarController@index')
	->where([
		'iYear' => '^(19\d|2\d\d)\d$',
		'iMonth' => '^(0[1-9]|1[012])$'
	])
	->middleware(['auth'])
	->name('calendar.index');