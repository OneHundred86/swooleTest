<?php
/*
路由配置
*/
use App\WebSocket\Route;

Route::add('user', 'join');
Route::group(['UserAuth'], function(){
    Route::add('user', 'msg');
});

