<?php

Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\HostetskiGPT\Http\Controllers'], function()
{
    Route::post('/hostetskigpt/generate', ['uses' => 'HostetskiGPTController@generate']);
    Route::get('/hostetskigpt/answers', 'HostetskiGPTController@answers');
});
