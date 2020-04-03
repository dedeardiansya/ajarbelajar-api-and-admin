<?php

Route::get('/', function(){
  return redirect()->route('admin.dashboard');
});

Route::get('/dashboard', function(){
  return view('admin.dashboard');
})->name('dashboard');

Route::get('/mail/minitutor', 'SendMailToMinitutor@index')->name('mail.minitutor.index');
Route::post('/mail/minitutor', 'SendMailToMinitutor@send')->name('mail.minitutor.send');

Route::resource('users', 'UsersController');
Route::resource('categories', 'CategoriesController')->except('show');
Route::resource('permissions', 'PermissionsController')->except(['show', 'edit', 'update']);
Route::get('roles/{id}/{permission_id}/{action}', 'RolesController@update')->name('roles.update');
Route::resource('roles', 'RolesController')->except(['show', 'destroy', 'update']);
Route::resource('seo', 'SeoController')->except(['show']);

Route::prefix('minitutor')->as('minitutor.')->group(function(){
  Route::get('/request', 'RequestMinitutorController@index')->name('request.index');
  Route::get('/request/{id}', 'RequestMinitutorController@show')->name('request.show');
  Route::put('/request/{id}/accept', 'RequestMinitutorController@accept')->name('request.accept');
  Route::put('/request/{id}/reject', 'RequestMinitutorController@reject')->name('request.reject');
});

Route::prefix('minitutor')->as('minitutor.')->group(function(){
  Route::get('/', 'MinitutorController@index')->name('index');
  Route::get('/{id}', 'MinitutorController@show')->name('show');
  
  Route::delete('/{id}', 'MinitutorController@destroy')->name('destroy');
  Route::get('/{id}/active/toggle', 'MinitutorController@activeToggle')->name('active.toggle');
});

Route::prefix('articles')->as('articles.')->group(function(){
  route::get('/', 'ArticlesController@index')->name('index');
  route::get('/{id}/edit', 'ArticlesController@edit')->name('edit');
  Route::post('{id}/image', 'ArticlesController@image')->name('image');
  route::put('/{id}', 'ArticlesController@update')->name('update');
  route::delete('/{id}', 'ArticlesController@destroy')->name('destroy');
  route::get('/{id}/make-public', 'ArticlesController@makePublic')->name('make.public');
  route::get('/{id}/make-draf', 'ArticlesController@makeDraf')->name('make.draf');

  route::get('/requested', 'ArticlesController@requested')->name('requested');
  route::get('/requested/{id}', 'ArticlesController@showRequested')->name('requested.show');
  route::get('/requested/{id}/accept', 'ArticlesController@acceptRequest')->name('requested.accept');
  route::get('/requested/{id}/reject', 'ArticlesController@rejectRequest')->name('requested.reject');

  route::get('/create', 'CreateArticlesController@index')->name('create.index');
  route::get('/create/{userId}', 'CreateArticlesController@create')->name('create.create');
  route::post('/create/{userId}', 'CreateArticlesController@store')->name('create.store');
});

Route::prefix('videos')->as('videos.')->group(function(){
  route::get('/requested', 'VideosController@requested')->name('requested');
  route::get('/requested/{id}', 'VideosController@showRequested')->name('requested.show');
  route::get('/requested/{id}/accept', 'VideosController@acceptRequest')->name('requested.accept');
  route::get('/requested/{id}/reject', 'VideosController@rejectRequest')->name('requested.reject');

  route::get('/create', 'CreateVideosController@index')->name('create.index');
  route::get('/create/{userId}', 'CreateVideosController@create')->name('create.create');
  route::post('/create/{userId}', 'CreateVideosController@store')->name('create.store');

  route::get('/', 'VideosController@index')->name('index');
  route::get('/{id}/edit', 'VideosController@edit')->name('edit');
  Route::post('{id}/image', 'VideosController@image')->name('image');
  route::put('/{id}', 'VideosController@update')->name('update');
  route::delete('/{id}', 'VideosController@destroy')->name('destroy');
  route::delete('/{id}/videos', 'VideosController@destroyVideos')->name('destroy.videos');
  route::get('/{id}/make-public', 'VideosController@makePublic')->name('make.public');
  route::get('/{id}/make-draf', 'VideosController@makeDraf')->name('make.draf');
});


Route::prefix('comment')->as('comment.')->group(function(){
  route::get('/', 'CommentController@index')->name('index');
  route::get('/{id}', 'CommentController@approveToggle')->name('approve.toggle');
});