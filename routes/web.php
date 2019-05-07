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
    return view('welcome');
});

Route::group(['prefix' => 'mailer'], function () {

    Route::get('/', 'Mailer\MailerController@index');

    Route::group(['prefix' => 'contact'], function() {
        Route::post('get_clients_list', 'Mailer\ContactController@getClientsList');
        Route::post('get_list',         'Mailer\ContactController@getContactList');
        Route::post('save',             'Mailer\ContactController@save');
        Route::post('update',           'Mailer\ContactController@update');
        Route::post('remove',           'Mailer\ContactController@remove');
        Route::post('get_item',         'Mailer\ContactController@getListItem');
    });

    Route::group(['prefix' => 'template'], function() {
        Route::post('get_list',     'Mailer\TemplateController@getList');
        Route::post('get_item',     'Mailer\TemplateController@get');
        Route::post('update',       'Mailer\TemplateController@update');
        Route::post('remove',       'Mailer\TemplateController@remove');
        Route::post('save',         'Mailer\TemplateController@save');
        Route::post('upload',         'Mailer\TemplateController@attachUpload');
        Route::post('attach/remove', 'Mailer\TemplateController@attachRemove');
    });



    Route::group(['prefix' => 'distribution'], function() {
        Route::post('save', 'Mailer\DistributionController@save');
        Route::post('update', 'Mailer\DistributionController@update');
        Route::post('remove', 'Mailer\DistributionController@remove');
        Route::post('get', 'Mailer\DistributionController@get');
        Route::post('send', 'Mailer\DistributionController@send');
        Route::post('test_send', 'Mailer\DistributionController@testSend');
    });

    Route::post('drivers/get', 'Mailer\MailerController@getMailerDrivers');
    Route::post('drivers/save', 'Mailer\MailerController@saveUserMailerDriver');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
