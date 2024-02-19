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

use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index')->name('home');

// Auth
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
Route::get('/password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('/password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

// Users
// Route::get('/users', 'UserController@list')->name('users.list');
// Route::get('/users/datatables', 'UserController@datatables')->name('users.datatables');
// Route::get('/users/create', 'UserController@showCreateForm')->name('users.create');
// Route::post('/users/create', 'UserController@create');
// Route::get('/users/edit/{user}', 'UserController@showEditForm')->name('users.edit');
// Route::patch('/users/edit/{user}', 'UserController@edit');
// Route::post('/users/multideactivate', 'UserController@multiDeactivate')->name('users.multideactivate');
// Route::post('/users/deactivate', 'UserController@deactivate')->name('users.deactivate');
// Route::post('/users/reactivate', 'UserController@reactivate')->name('users.reactivate');
// Route::get('/meta/{id}', 'UserController@meta')->name('users.meta');

Route::middleware(['auth.timeout', '2fa','checkStatus'])->group(function () {
    Route::get('/', 'HomeController@index')->name('home');
    Route::post('/terms-of-use', 'HomeController@acceptTos');
    Route::get('/terms-of-use', 'HomeController@tos')->name('home.tos');

    // 2fa
    Route::get('/two-factor', 'TwoFAController@index')->name('2fa.index');
    Route::patch('/two-factor/enable', 'TwoFAController@enable')->name('2fa.enable');
    Route::patch('/two-factor/disable', 'TwoFAController@disable')->name('2fa.disable');
    Route::patch('/two-factor/reset/{user}', 'TwoFAController@adminReset')->name('2fa.admin.reset');
    Route::post('/two-factor/verify', 'TwoFAController@verify')->name('2fa.verify');

    // Users
    Route::get('/users', 'UserController@list')->name('users.list');
    Route::get('/users/datatables', 'UserController@datatables')->name('users.datatables');
    Route::get('/users/create', 'UserController@showCreateForm')->name('users.create');
    Route::post('/users/create', 'UserController@create');
    Route::get('/users/edit/{user}', 'UserController@showEditForm')->name('users.edit');
    Route::patch('/users/edit/{user}', 'UserController@edit');
    Route::post('/users/multideactivate', 'UserController@multiDeactivate')->name('users.multideactivate');
    Route::post('/users/deactivate', 'UserController@deactivate')->name('users.deactivate');
    Route::post('/users/reactivate', 'UserController@reactivate')->name('users.reactivate');
    Route::get('/meta/{id}', 'UserController@meta')->name('users.meta');
    Route::post('users/signtos', 'UserController@signtos')->name('users.signtos');

    // Students
    Route::get('/students', 'StudentController@list')->name('students.list');
    Route::get('/students/datatables', 'StudentController@datatables')->name('students.datatables');
    Route::get('/students/create', 'StudentController@showCreateForm')->name('students.create');
    Route::get('/students/import', 'StudentController@showImportForm')->name('students.import');
    Route::post('/students/import', 'StudentController@import');
    Route::post('/students/create', 'StudentController@create');
    Route::get('/students/edit/{student}', 'StudentController@showEditForm')->name('students.edit');
    Route::patch('/students/edit/{student}', 'StudentController@edit');
    Route::post('/students/deactivate', 'StudentController@deactivate')->name('students.deactivate');
    Route::post('/students/allocate', 'StudentController@allocate')->name('students.allocate');
    Route::get('/students/allocate/{hash}', 'StudentController@allocated')->name('students.allocated');

    // Treshold
    Route::get('/tresholds', 'TresholdController@list')->name('tresholds.list');
    Route::get('/tresholds/update/{amount}', 'TresholdController@update')->name('tresholds.update');

    // Donations
    Route::get('/donations', 'DonationController@list')->name('donations.list');
    Route::get('/donations/datatables', 'DonationController@datatables')->name('donations.datatables');
    Route::get('/donations/create', 'DonationController@showCreateForm')->name('donations.create');

    Route::get('/donations/import', 'DonationController@showImportForm')->name('donations.import');
    Route::get('/donations/import/list', 'DonationController@showImportList')->name('donations.import-list');
    Route::get('/donations/importDetails', 'DonationController@showImportDetails')->name('donations.import-details');
    Route::post('/donations/import', 'DonationController@import');
    Route::get('/donations/importDatatables', 'DonationController@importDatatables')->name('donations.import-datatables');
    Route::get('/donations/importDetailsDatatables', 'DonationController@importDetailsDatatables')->name('donations.import-details-datatables');
    Route::get('/donations/import/{file}', 'DonationController@showImportDetailsForm')->name('donations.import-details');
    Route::get('/donations/import/approve/{file}', 'DonationController@importApprove')->name('donations.import-approve');
    Route::get('/donations/import/edit/{donation}', 'DonationController@showTempEditForm')->name('donations.import-edit');
    Route::patch('/donations/import/edit/{donation}', 'DonationController@tempEdit');
    Route::get('/donations/download/{document}', 'DonationController@download')->name('donations.download');
    Route::get('/donations/import/delete/{id}','DonationController@deleteImport')->name('donations.import-delete');
    Route::post('/donations/import/delete', 'DonationController@bulkDeleteImport')->name('donations.import-bulk-delete');

    Route::post('/donations/create', 'DonationController@create');
    Route::get('/donations/edit/{donation}', 'DonationController@showEditForm')->name('donations.edit');
    Route::patch('/donations/edit/{donation}', 'DonationController@edit');
    Route::post('/donations/deactivate', 'DonationController@deactivate')->name('donations.deactivate');
    Route::post('/donations/generate', 'DonationController@generate')->name('donations.allocate');
    Route::post('/donations/students/{donation}', 'DonationController@students')->name('donations.students');
    Route::get('/donations/settings', 'DonationController@showSettings')->name('donations.settings');
    Route::patch('/donations/settings', 'DonationController@updateSettings');

    // Certificates
    Route::get('/certificates/{hash}', 'CertificateController@index')->name('certificates.index');
    Route::get('/certificates/download/{hash}', 'CertificateController@download')->name('certificates.download');

    // Stats
    Route::get('/stats', 'StatsController@index')->name('stats.index');

    Route::prefix('documents')->group(function () {
        Route::get('/', 'DocumentController@list')->name('documents.list');
        Route::post('/create', 'DocumentController@create')->name('documents.create');
        Route::patch('/edit/{document}', 'DocumentController@edit')->name('documents.edit');
        Route::post('/delete/{document}', 'DocumentController@delete')->name('documents.delete');
        Route::get('/download/{document}', 'DocumentController@download')->name('documents.download');
});

// Galleries
    // Reports
    Route::get('/reports', 'ReportsController@index')->name('reports.index');
    Route::post('/reports', 'ReportsController@export')->name('reports.export');
    Route::post('/donationsReports', 'ReportsController@donationsExport')->name('reports.donations-export');

    // Documents
    Route::get('/docs/{file}', 'HomeController@download');

    // Galleries

    Route::prefix('galleries')->group(function () {
        Route::get('/', 'GalleryController@index')->name('galleries.index');
        Route::get('/admin', 'GalleryController@admin')->name('galleries.admin');
        Route::delete('/admin', 'GalleryController@bulkDelete');
        Route::post('/', 'GalleryController@store');
        Route::get('/create', 'GalleryController@create')->name('galleries.create');
        Route::get('/{gallery}/edit', 'GalleryController@edit')->name('galleries.edit');
        Route::patch('/{gallery}', 'GalleryController@update')->name('galleries.update');
        Route::get('/{gallery}/download', 'GalleryController@download')->name('galleries.download');
        Route::post('/download', 'GalleryController@generateBulkDownloadUrl');
        Route::get('/download/{hash}', 'GalleryController@bulkDownload')->name('galleries.bulk-download');

        Route::middleware('optimizeImages')->group(function () {
            Route::post('/upload', 'GalleryController@upload')->name('galleries.upload');
        });
    });
});
