<?php

Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\HostetskiGPT\Http\Controllers'], function()
{
    Route::get('/hostetskigpt/get', 'HostetskiGPTController@get');
    Route::get('/hostetskigpt/answers', 'HostetskiGPTController@answers');
});
