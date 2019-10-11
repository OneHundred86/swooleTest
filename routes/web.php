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

Route::get('/', 'IndexController@indexPage');
Route::get('/chat', 'IndexController@chatPage');
Route::get('/chat1', 'IndexController@chat1Page');



// private api
Route::group(
  [
    'middleware' => 'privateApi',
    'prefix' => 'private',
  ],
  function(){
    Route::post('/site/app/list', 'SiteAppController@lists');

    Route::group(
      [
        'middleware' => 'm1',
      ],
      function(){
        Route::post('/m1/t', 'SiteAppController@xxx');
      }
    );
  }
);