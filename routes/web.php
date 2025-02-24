<?php

use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Scramble::registerUiRoute('docs/admin', api: 'admin');
Scramble::registerJsonSpecificationRoute('docs/admin/admin.json', api: 'admin');

