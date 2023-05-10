<?php

Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\HostetskiGPT\Http\Controllers'], function()
{
    Route::post('/hostetskigpt/generate', 'HostetskiGPTController@generate');
    Route::get('/hostetskigpt/answers', 'HostetskiGPTController@answers');
    Route::get('/hostetskigpt/is_enabled', 'HostetskiGPTController@checkIsEnabled');
    Route::get('/mailbox/{mailbox_id}/hostetskigpt-settings', ['uses' => 'HostetskiGPTController@settings', 'middleware' => ['auth', 'roles'], 'roles' => ['admin']])->name('hostetskigpt.settings');
    Route::post('/mailbox/{mailbox_id}/hostetskigpt-settings', ['uses' => 'HostetskiGPTController@saveSettings', 'middleware' => ['auth', 'roles'], 'roles' => ['admin']]);
});
