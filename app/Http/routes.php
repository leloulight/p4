<?php

/* new user */
Route::get('newuser', function()
{ return View::make('newuser'); });

/* main check */
Route::get('/', function()
{ 
  if(Auth::check()) { 
     $app = app();
     $controller = $app->make('App\\Http\\Controllers\\SystemController');
     $files = $controller->callAction('getDirectoryFiles', $parameters = array());
     return View::make('main')->with('display', $files);
  }
  return View::make('login'); });

/* login */
Route::get('login', function()
{ return View::make('login'); });

/* Controller routes */
Route::post('login', 'SystemController@login');
Route::post('main', 'SystemController@showFilesForm');
Route::post('main', 'SystemController@uploadFiles');
Route::post('main', 'SystemController@deleteFiles');
Route::post('main', 'SystemController@mainloop');
Route::post('newuser', 'SystemController@createID');

