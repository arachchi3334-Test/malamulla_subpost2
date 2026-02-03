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
Route::get('/cache', function() {
    Artisan::call('cache:clear');
	Artisan::call('view:clear');
});

Auth::routes();
Route::get('/login/azure','Auth\AzureLoginController@redirectToAzure')->name('azure.login');
Route::get('/auth/azure-ad-login-success', 'Auth\AzureLoginController@handleAzureCallback');

Route::get('tfa',  'Auth\TwoFAController@index')->name('tfa.index');
Route::post('tfa',  'Auth\TwoFAController@store')->name('tfa.post');
Route::post('/verify_ad',  'Auth\LoginController@verify_ad_login')->name('verify_ad_login');
Route::get('tfa/reset',  'Auth\TwoFAController@resend')->name('tfa.resend');



Route::post('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/edit-submitted-file', ['as' => 'authoring.edit_submitted_file', 'uses' => 'Api\XmlInputController@edit_submitted_file'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);


/*
//test email preview
Route::get('/notification', function () {
    $allocation = \App\Model\AuthorFileAllocation::find(32);
    $author = $allocation->author;
    return (new \App\Notifications\AuthorFileAllocationNotification($author, $allocation))
                ->toMail($author);
});
*/

Route::post('projects/{prj_code}/saxon-get-allowed-items', ['as' => 'saxon.saxon_get_allowed_items', 'uses' => 'Web\AuthoringController@saxon_get_allowed_items'])->where('prj_code', '[A-Za-z0-9]+');
Route::post('projects/{prj_code}/saxon-get-allowed-items-multiple', ['as' => 'saxon.saxon_get_allowed_items_multiple', 'uses' => 'Web\AuthoringController@saxon_get_allowed_items_multiple'])->where('prj_code', '[A-Za-z0-9]+');
Route::post('post-check-content-validity', ['as' => 'saxon.post_check_content_validity', 'uses' => 'Web\AuthoringController@post_check_content_validity'])->where('prj_code', '[A-Za-z0-9]+');
Route::get('assets/download/temp', ['as' => 'assets.download.temp.signed', 'uses' => 'Web\AssetController@download_asset_temp_signed']);

Route::middleware(['auth:web', 'acl', 'tfa', 'fpc'])->group(function () {
    /* remove below later*/
    Route::get('/migrate', function() {
        Artisan::call('migrate');
        Artisan::call('queue:restart');
    });

    //to the purpose of local temp url download
    Route::get('local/temp/{path?}', function (string $path){
        if (! \request()->hasValidSignature()) {
            abort(401);
        }
        $data = request()->ResponseContentDisposition;
        $fname = substr($data, strpos($data, "=") + 1);

        return Storage::disk('local')->download($path,$fname);


    })->where('path', '(.*)')->name('local.temp');

    //to the purpose of typefi temp url download
    Route::get('typefi/temp/{path?}', function (string $path){
        if (! \request()->hasValidSignature()) {
            abort(401);
        }
        $data = request()->ResponseContentDisposition;
        $fname = substr($data, strpos($data, "=") + 1);

        return Storage::disk('typefi')->download($path,$fname);


    })->where('path', '(.*)')->name('typefi.temp');


    Route::get('/change_password', 'Web\HomeController@change_password')->name('change_password.get')->withoutMiddleware(['fpc']);
    Route::get('/profile_picture', 'Web\HomeController@profile')->name('profile_picture.get');
    Route::post('/profile_picture', 'Web\HomeController@update_avatar')->name('profile_picture.post');
    Route::patch('/change_password', 'Web\HomeController@change_password_post')->name('change_password.post')->withoutMiddleware(['fpc']);
    
    /*notifications*/
    Route::get('/notifications', 'Web\NotificationController@get_notifications')->name('dashboard.get_notifications');
    Route::patch('notifications/{type}/{data}/read', ['as' => 'dashboard.notifications.read', 'uses' => 'Web\NotificationController@read_notifications']);

    /* dashboard */
	Route::get('/{prj_code?}', 'Web\HomeController@index')->name('home');
    Route::get('projects/{prj_code}/load_dashboard',  'Web\HomeController@generate')->name('load_dashboard');
    Route::get('projects/{prj_code}/load_author_dashboard',  'Web\HomeController@load_author_dashboard')->name('load_author_dashboard');
    Route::get('reports/get_versions/{title_id}','Web\ReportsController@get_versions')->name('get_versions');
    Route::get('reports/get_version_files/{version_id}','Web\ReportsController@get_version_files')->name('report_get_version_files');
    
    /* Document Pool */
    Route::get('projects/{prj_code}/article-pool', ['as' => 'article-pool.index', 'uses' => 'Web\ArticlePoolController@index', 'can' => 'view-article-pool.article_pool'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/article-pool/load-data', ['as' => 'article-pool.load_data', 'uses' => 'Web\ArticlePoolController@load_data', 'can' => 'view-article-pool.article_pool'])->where('prj_code', '[A-Za-z0-9]+'); 
    Route::post('projects/{prj_code}/article-pool/create', ['as' => 'article-pool.store', 'uses' => 'Web\ArticlePoolController@store', 'can' => 'view-article-pool.article_pool,upload-files.article_pool'])->where('prj_code', '[A-Za-z0-9]+');
    Route::post('projects/{prj_code}/article-pool/upload-article', ['as' => 'article-pool.upload-article', 'uses' => 'Web\ArticlePoolController@upload_article', 'can' => 'view-article-pool.article_pool,upload-files.article_pool'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/article-pool/{pool_id}/file/{file_id}/titles', ['as' => 'article-pool.get-file-titles', 'uses' => 'Web\ArticlePoolController@get_file_titles', 'can' => 'view-article-pool.article_pool'])->where('prj_code', '[A-Za-z0-9]+')->where('pool_id', '[0-9]+')->where('file_id', '[0-9]+'); 
    Route::get('projects/{prj_code}/article-pool/{pool_id}/file/{file_id}/download-source-file', ['as' => 'article-pool.download-source-file', 'uses' => 'Web\ArticlePoolController@download_source_file', 'can' => 'view-article-pool.article_pool'])->where('prj_code', '[A-Za-z0-9]+')->where('pool_id', '[0-9]+')->where('file_id', '[0-9]+'); 
    Route::patch('projects/{prj_code}/article-pool/{pool_id}/file/{file_id}/titles', ['as' => 'article-pool.update-file-titles', 'uses' => 'Web\ArticlePoolController@update_file_titles', 'can' => 'view-article-pool.article_pool'])->where('prj_code', '[A-Za-z0-9]+')->where('pool_id', '[0-9]+')->where('file_id', '[0-9]+'); 
    Route::post('projects/{prj_code}/article-pool/{pool_id}/file/{file_id}/edit', ['as' => 'article-pool.edit-file', 'uses' => 'Web\ArticlePoolController@edit_file', 'can' => 'edit-any-file.article_pool|edit-own-file.article_pool'])->where('prj_code', '[A-Za-z0-9]+')->where('pool_id', '[0-9]+')->where('file_id', '[0-9]+'); 
    Route::delete('projects/{prj_code}/article-pool/{pool_id}/file/{file_id}/delete', ['as' => 'article-pool.delete-file', 'uses' => 'Web\ArticlePoolController@delete_file', 'can' => 'delete-article.article_pool|delete-reference.article_pool'])->where('prj_code', '[A-Za-z0-9]+')->where('pool_id', '[0-9]+')->where('file_id', '[0-9]+');
    Route::get('projects/{prj_code}/article-pool/get-metadata-popup', ['as' => 'article-pool.get-metadata-popup', 'uses' => 'Web\ArticlePoolController@get_metadata_popup', 'can' => 'view-article-pool.article_pool'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/article-pool/{pool_id}/file/{file_id}/view-metadata', ['as' => 'article-pool.view-metadata', 'uses' => 'Web\ArticlePoolController@view_metadata', 'can' => 'view-article-pool.article_pool'])->where('prj_code', '[A-Za-z0-9]+');
    Route::post('projects/{prj_code}/article-pool/{pool_id}/notify-users', ['as' => 'article-pool.notify-users', 'uses' => 'Web\ArticlePoolController@notify_users', 'can' => 'edit-any-file.article_pool|edit-own-file.article_pool'])->where('prj_code', '[A-Za-z0-9]+')->where('pool_id', '[0-9]+');

    /* Assets */
    Route::get('projects/{prj_code}/assets', ['as' => 'assets.index', 'uses' => 'Web\AssetController@index', 'can' => 'view.assets|view-assigned.assets|view-active.assets'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/assets/load-data', ['as' => 'assets.load_data', 'uses' => 'Web\AssetController@load_data', 'can' => 'view.assets|view-assigned.assets|view-active.assets'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/assets/load_ajax_assets_history', [
        'as' => 'assets.load_ajax_assets_history',
        'uses' => 'Web\AssetController@load_ajax_assets_history',
        'can' => 'view.assets|view-assigned.assets|view-active.assets'
    ])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/assets/create', ['as' => 'assets.create', 'uses' => 'Web\AssetController@create', 'can' => 'create.assets'])->where('prj_code', '[A-Za-z0-9]+'); 
    Route::post('projects/{prj_code}/assets/create', ['as' => 'assets.store', 'uses' => 'Web\AssetController@store', 'can' => 'create.assets'])->where('prj_code', '[A-Za-z0-9]+')->middleware('prevent.html.injection'); 
    Route::get('projects/{prj_code}/assets/{asset_id}', ['as' => 'assets.view', 'uses' => 'Web\AssetController@show', 'can' => 'view.assets|view-assigned.assets|view-active.assets'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');
    Route::post('projects/{prj_code}/download-multiple-files', ['as' => 'assets.download-multiple', 'uses' => 'Web\AssetController@download_multiple_files', 'can' => 'view.assets|view-assigned.assets|view-active.assets'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');
    Route::post('projects/{prj_code}/download-full-compile-xml-file', ['as' => 'assets.full-compiled-xml-download', 'uses' => 'Web\AssetController@download_compile_xml_file', 'can' => 'view.assets|view-assigned.assets|view-active.assets'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{asset_id}/cml-generation-status', ['as' => 'assets.get-xml-status', 'uses' => 'Web\AssetController@check_xml_gen_status', 'can' => 'create.assets'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{asset_id}/download/{file_name}', ['as' => 'assets.downlaod-xml-file', 'uses' => 'Web\AssetController@download_full_xml_file', 'can' => 'create.assets'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{asset_id}/download-temp/{file_name}', ['as' => 'assets.download-temp-file', 'uses' => 'Web\AssetController@download_temp_file', 'can' => 'create.assets'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{asset_id}/delete-temp/{file_name}', ['as' => 'assets.delete-temp-file', 'uses' => 'Web\AssetController@delete_temp_file', 'can' => 'create.assets'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');
    
    Route::get('projects/{prj_code}/assets/{asset_id}/status', ['as' => 'assets.status', 'uses' => 'Web\AssetController@get_status', 'can' => 'view.assets|view-assigned.assets|view-active.assets'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');
    Route::get('projects/{prj_code}/assets/{asset_id}/get-meta-plus-tasks', ['as' => 'assets.get_meta', 'uses' => 'Web\AssetController@get_meta'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+'); 
    Route::get('projects/{prj_code}/assets/{asset_id}/load_asset_history', ['as' => 'assets.load_ajax_asset_history', 'uses' => 'Web\AssetController@load_ajax_asset_history'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');
    Route::get('projects/{prj_code}/assets/{asset_id}/load_asset_history-tree-view', ['as' => 'assets.load_ajax_asset_history_tree_view', 'uses' => 'Web\AssetController@load_ajax_asset_history_tree_view'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');
    Route::get('projects/{prj_code}/assets/{asset_id}/load_asset_history-articale', ['as' => 'assets.load_ajax_asset_history_article', 'uses' => 'Web\AssetController@load_ajax_asset_history_articale'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');
    Route::get('projects/{prj_code}/assets/{asset_id}/get-versions', ['as' => 'assets.get_versions', 'uses' => 'Web\AssetController@get_versions'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+'); 
    Route::get('projects/{prj_code}/assets/{asset_id}/edit', ['as' => 'assets.edit', 'uses' => 'Web\AssetController@edit'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+'); 
    Route::patch('projects/{prj_code}/assets/{asset_id}', ['as' => 'assets.update', 'uses' => 'Web\AssetController@update'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+')->middleware('prevent.html.injection'); 
    Route::delete('projects/{prj_code}/assets/{asset_id}/delete', ['as' => 'assets.destroy', 'uses' => 'Web\AssetController@destroy'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+'); 
    Route::get('projects/{prj_code}/assets/{asset_id}/download-style/{style}', ['as' => 'assets.download_style_file', 'uses' => 'Web\AssetController@download_style_file', 'can' => 'edit.assets'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+'); 
    Route::post('projects/{prj_code}/assets/{asset_id}/save-metadata', ['as' => 'assets.save_metadata', 'uses' => 'Web\AssetController@save_metadata', 'can' => 'edit.assets'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');

    Route::get('projects/{prj_code}/assets/{asset_id}/version/{version_id}/get-file-tree', ['as' => 'assets.get-version-file-tree', 'uses' => 'Web\AssetController@get_version_file_tree', 'can' => 'view.assets|view-assigned.assets|view-active.assets'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+')->where('version_id', '[0-9]+');

    Route::get('projects/{prj_code}/assets/{asset_id}/file/{version_file_id}/download', ['as' => 'assets.file.download', 'uses' => 'Web\AssetController@download_file'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+')->where('version_file_id', '[0-9]+'); 
    Route::post('projects/{prj_code}/download-completed-files', ['as' => 'assets.file.download_completed_files', 'uses' => 'Web\AssetController@download_completed_files'])->where('prj_code', '[A-Za-z0-9]+');
    Route::delete('projects/{prj_code}/assets/{asset_id}/file/{version_file_id}/delete', ['as' => 'assets.file.delete', 'uses' => 'Web\AssetController@delete_file'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+')->where('version_file_id', '[0-9]+');
    Route::post('projects/{prj_code}/assets/{asset_id}/version/{asset_version_id}/delete-folder', ['as' => 'assets.file.delete_folder', 'uses' => 'Web\AssetController@delete_folder'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');

    Route::get('projects/{prj_code}/assets/{asset_id}/version/{version_id}/file', ['as' => 'assets.file.create', 'uses' => 'Web\AssetController@create_new_file'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+'); 
    Route::get('projects/{prj_code}/assets/{asset_id}/version/{version_id}/file-from-tree', ['as' => 'assets.file.create_from_tree', 'uses' => 'Web\AssetController@create_new_file_from_tree'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');
    Route::post('projects/{prj_code}/assets/{asset_id}/version/{version_id}/file', ['as' => 'assets.file.store', 'uses' => 'Web\AssetController@store_new_file'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');

    Route::get('projects/{prj_code}/assets/{asset_id}/version/{version_id}/multiple-xml-files', ['as' => 'assets.file.get_add_multiple_xml', 'uses' => 'Web\AssetController@get_add_multiple_xml_files'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+'); 
    Route::post('projects/{prj_code}/assets/{asset_id}/version/{version_id}/multiple-xml-files', ['as' => 'assets.file.post_add_multiple_xml', 'uses' => 'Web\AssetController@post_add_multiple_xml_files'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');

    Route::get('projects/{prj_code}/assets/{asset_id}/version/{version_id}/multiple-image-files', ['as' => 'assets.file.get_add_multiple_image', 'uses' => 'Web\AssetController@get_add_multiple_image_files'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+'); 
    Route::post('projects/{prj_code}/assets/{asset_id}/version/{version_id}/multiple-image-files', ['as' => 'assets.file.post_add_multiple_image', 'uses' => 'Web\AssetController@post_add_multiple_image'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');

    Route::post('projects/{prj_code}/assets/{asset_id}/version/{version_id}/create-compiled-json-file', ['as' => 'assets.create_compiled_json_file', 'uses' => 'Web\AssetController@create_compiled_json_file', 'can' => 'download_compiled_json_file.assets'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');
    Route::post('projects/{prj_code}/assets/{asset_id}/file/{asset_version_file_id}/create-single-json-file', ['as' => 'assets.create_single_json_file', 'uses' => 'Web\AssetController@create_single_json_file', 'can' => 'download_compiled_json_file.assets'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');
    Route::get('projects/{prj_code}/assets/download-generated-json-file', ['as' => 'assets.download_generated_json_file', 'uses' => 'Web\AssetController@download_generated_json_file', 'can' => 'download_compiled_json_file.assets'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');
   // Route::post('projects/{prj_code}/assets/{asset_id}/version/{version_id}/multiple-xml-files', ['as' => 'assets.file.post_add_multiple_xml', 'uses' => 'Web\AssetController@post_add_multiple_xml_files'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');
    Route::get('projects/{prj_code}/assets/content', ['as' => 'assets.get_selected_element_content', 'uses' => 'Web\AssetController@get_selected_element_content']);

    Route::get('projects/{prj_code}/products/{product_id}/get-input-zip-structure', ['as' => 'products.get-input-zip-structure', 'uses' => 'Web\ProductController@get_input_zip_structure'])->where('prj_code', '[A-Za-z0-9]+')->where('product_id', '[0-9]+');


    Route::get('projects/{prj_code}/completed-xml-list', ['as' => 'assets.view_completed_xml_list', 'uses' => 'Web\AssetController@view_completed_xml_list'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/get-completed-xml-list', ['as' => 'assets.get_completed_xml_list', 'uses' => 'Web\AssetController@get_completed_xml_list','can'=>'view_completed_files_list.assets'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/assets/file/{asset_version_file_id}/download-source', ['as' => 'asset_dp.download-source-file', 'uses' => 'Web\AssetController@download_source_file', 'can' => 'download-article.article_pool'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_version_file_id', '[0-9]+');
    Route::get('projects/{prj_code}/assets/{asset_id}/download-full-asset', ['as' => 'assets.download_full_asset', 'uses' => 'Web\AssetController@download_full_asset'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('asset-version-file/{asset_version_file_id}', ['as' => 'asset.get_asset_version_file', 'uses' => 'Web\AssetController@get_asset_version_file'])->where('asset_version_file_id', '[0-9]+');

    /* Conversion */
    Route::get('projects/{prj_code}/file-conversion', ['as' => 'conversion.pending', 'uses' => 'Web\ArticleConversionController@index', 'can' => 'view-article-conversion.article_pool_conversion|can-convert-files.article_pool_conversion'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/file-conversion/load-data', ['as' => 'conversion.load_pending_data', 'uses' => 'Web\ArticleConversionController@load_pending_list', 'can' => 'view-article-conversion.article_pool_conversion|can-convert-files.article_pool_conversion'])->where('prj_code', '[A-Za-z0-9]+'); 
    Route::post('projects/{prj_code}/file-conversion/file/{asset_file_id}/start-conversion', ['as' => 'conversion.file-start-conversion', 'uses' => 'Web\ArticleConversionController@start_conversion', 'can' => 'can-convert-files.article_pool_conversion'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_file_id', '[0-9]+');
    Route::get('projects/{prj_code}/file-conversion/file/{asset_file_id}', ['as' => 'conversion.get-upload-file', 'uses' => 'Web\ArticleConversionController@get_upload_file'/*, 'can' => 'can-convert-files.article_pool_conversion'*/])->where('prj_code', '[A-Za-z0-9]+')->where('asset_file_id', '[0-9]+');
    Route::get('projects/{prj_code}/file-conversion/file/{asset_file_id}/download-source', ['as' => 'conversion.download-source-file', 'uses' => 'Web\ArticleConversionController@download_source_file', 'can' => 'can-convert-files.article_pool_conversion'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_file_id', '[0-9]+');
    Route::post('projects/{prj_code}/file-conversion/file/{asset_file_id}', ['as' => 'conversion.post-upload-file', 'uses' => 'Web\ArticleConversionController@post_upload_file'/*, 'can' => 'can-convert-files.article_pool_conversion'*/])->where('prj_code', '[A-Za-z0-9]+')->where('asset_file_id', '[0-9]+');


    /* Planning */
    Route::get('projects/{prj_code}/planning', ['as' => 'planning.index', 'uses' => 'Web\PlanningController@index', 'can' => 'view.planning|view-assigned.planning'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/planning/load-data', ['as' => 'planning.load_data', 'uses' => 'Web\PlanningController@load_data', 'can' => 'view.planning|view-assigned.planning'])->where('prj_code', '[A-Za-z0-9]+'); 
    Route::get('projects/{prj_code}/planning/load_ajax_plannings_history', ['as' => 'planning.load_ajax_plannings_history', 'uses' => 'Web\PlanningController@load_ajax_plannings_history', 'can' => 'view.planning|view-assigned.planning'])->where('prj_code', '[A-Za-z0-9]+'); 
    Route::get('projects/{prj_code}/planning/create', ['as' => 'planning.create', 'uses' => 'Web\PlanningController@create', 'can' => 'create.planning'])->where('prj_code', '[A-Za-z0-9]+'); 
    Route::post('projects/{prj_code}/planning/create', ['as' => 'planning.store', 'uses' => 'Web\PlanningController@store', 'can' => 'create.planning'])->where('prj_code', '[A-Za-z0-9]+')->middleware('prevent.html.injection'); 
    Route::get('projects/{prj_code}/planning/{planning_id}', ['as' => 'planning.view', 'uses' => 'Web\PlanningController@show', 'can' => 'view.planning|view-assigned.planning'])->where('prj_code', '[A-Za-z0-9]+')->where('planning_id', '[0-9]+'); 
    Route::get('projects/{prj_code}/planning/{planning_id}/edit', ['as' => 'planning.edit', 'uses' => 'Web\PlanningController@edit'])->where('prj_code', '[A-Za-z0-9]+')->where('planning_id', '[0-9]+'); 
    Route::get('projects/{prj_code}/planning/{planning_id}/load_ajax_planning_history', ['as' => 'planning.load_ajax_planning_history', 'uses' => 'Web\PlanningController@load_ajax_planning_history'])->where('prj_code', '[A-Za-z0-9]+')->where('planning_id', '[0-9]+'); 
    Route::patch('projects/{prj_code}/planning/{planning_id}/update', ['as' => 'planning.update', 'uses' => 'Web\PlanningController@update'])->where('prj_code', '[A-Za-z0-9]+')->where('planning_id', '[0-9]+')->middleware('prevent.html.injection'); 
    Route::delete('projects/{prj_code}/planning/{planning_id}/delete', ['as' => 'planning.destroy', 'uses' => 'Web\PlanningController@destroy'])->where('prj_code', '[A-Za-z0-9]+')->where('planning_id', '[0-9]+'); 
    Route::post('projects/{prj_code}/planning/{planning_id}/create-order', ['as' => 'planning.create-order', 'uses' => 'Web\PlanningController@create_order'])->where('prj_code', '[A-Za-z0-9]+')->where('planning_id', '[0-9]+'); 

    /* Orders */
    Route::get('projects/{prj_code}/orders', ['as' => 'orders.index', 'uses' => 'Web\OrderController@index', 'can' => 'view.orders|view-assigned.orders'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/orders/load-data', ['as' => 'orders.load_data', 'uses' => 'Web\OrderController@load_data', 'can' => 'view.orders|view-assigned.orders'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/orders/load_orders_history', [
        'as' => 'orders.load_ajax_orders_history',
        'uses' => 'Web\OrderController@load_ajax_orders_history',
        'can' => 'view.orders|view-assigned.orders'
    ])->where('prj_code', '[A-Za-z0-9]+'); 
    Route::get('projects/{prj_code}/orders/ongoing-tasks', ['as' => 'orders.ongoing_tasks', 'uses' => 'Web\OrderController@ongoing_tasks', 'can' => 'view.orders|view-assigned.orders'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/orders/ongoing-tasks/load-data', ['as' => 'orders.ongoing_tasks_load_data', 'uses' => 'Web\OrderController@get_ongoing_tasks_list', 'can' => 'view.orders|view-assigned.orders'])->where('prj_code', '[A-Za-z0-9]+'); 
   
    Route::get('projects/{prj_code}/orders/create', ['as' => 'orders.create', 'uses' => 'Web\OrderController@create', 'can' => 'create.orders'])->where('prj_code', '[A-Za-z0-9]+'); 
    Route::post('projects/{prj_code}/orders/create', ['as' => 'orders.store', 'uses' => 'Web\OrderController@store', 'can' => 'create.orders'])->where('prj_code', '[A-Za-z0-9]+')->middleware('prevent.html.injection'); 
    Route::get('projects/{prj_code}/orders/{order_id}', ['as' => 'orders.view', 'uses' => 'Web\OrderController@show', 'can' => 'view.orders|view-assigned.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+'); 
    Route::get('projects/{prj_code}/orders/{order_id}/edit', ['as' => 'orders.edit', 'uses' => 'Web\OrderController@edit'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{order_id}/load_order_history', [
        'as' => 'orders.load_ajax_order_history',
        'uses' => 'Web\OrderController@load_ajax_order_history'
    ])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+'); 
    Route::get('projects/{prj_code}/orders/{order_id}/load_order_history_for_dp', [
        'as' => 'orders.load_ajax_order_history_for_dp',
        'uses' => 'Web\OrderController@load_ajax_order_history_for_dp'
    ])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+'); 
    Route::patch('projects/{prj_code}/orders/{order_id}/update', ['as' => 'orders.update', 'uses' => 'Web\OrderController@update'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->middleware('prevent.html.injection'); 
    Route::post('projects/{prj_code}/orders/{order_id}/files/assign-to-author', ['as' => 'orders.assign-to-author', 'uses' => 'Web\OrderController@assign_file_to_author'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->middleware('prevent.html.injection');
    Route::delete('projects/{prj_code}/orders/{order_id}/delete', ['as' => 'orders.destroy', 'uses' => 'Web\OrderController@destroy', 'can' => 'delete.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+'); 
    
    Route::post('projects/{prj_code}/orders/{order_id}/author-allocation/{alloc_id}/messages/create', ['as' => 'orders.send-message', 'uses' => 'Web\OrderController@send_message_to_author'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('alloc_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{order_id}/author-allocation/{alloc_id}/type/{file_type}/download-ongoing', ['as' => 'orders.download_ongoing', 'uses' => 'Web\OrderController@download_ongoing_edit_files'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('alloc_id', '[0-9]+');//todo change permissions
    Route::get('projects/{prj_code}/orders/{order_id}/author-allocation/{alloc_id}/messages', ['as' => 'orders.view-messages', 'uses' => 'Web\OrderController@view_allocation_messages'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('alloc_id', '[0-9]+');
    Route::post('projects/{prj_code}/order/{order_id}/author-allocation/{alloc_id}/upload-ongoing-file', ['as' => 'orders.upload-ongoing-file', 'uses' => 'Web\OrderController@upload_ongoing_edit_files'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');

    Route::get('projects/{prj_code}/orders/{order_id}/author-allocation/{alloc_id}/accept-new-file', ['as' => 'orders.get-accept-new-file', 'uses' => 'Web\OrderController@get_accept_new_file_form'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('alloc_id', '[0-9]+');
    Route::post('projects/{prj_code}/orders/{order_id}/author-allocation/{alloc_id}/accept-new-file', ['as' => 'orders.post-accept-new-file', 'uses' => 'Web\OrderController@post_accept_new_file_form'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('alloc_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{order_id}/accept-new-file-status', ['as' => 'orders.get-accept-new-file-status', 'uses' => 'Web\OrderController@get_accept_new_file_status'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');

    Route::delete('projects/{prj_code}/orders/{order_id}/author-allocation/{alloc_id}/cancel-allocation', ['as' => 'orders.cancel-allocation', 'uses' => 'Web\OrderController@cancel_allocation', 'can' => 'cancel-allocation.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('alloc_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{order_id}/author-allocation/{alloc_id}/checkin-file', ['as' => 'orders.get-checkin-file', 'uses' => 'Web\OrderController@get_checkin_file', 'can' => 'cancel-allocation.orders|download_xml.orders|download_html.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('alloc_id', '[0-9]+');
    Route::post('projects/{prj_code}/orders/{order_id}/author-allocation/{alloc_id}/checkin-file', ['as' => 'orders.post-checkin-file', 'uses' => 'Web\OrderController@post_checkin_file', 'can' => 'cancel-allocation.orders|download_xml.orders|download_html.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('alloc_id', '[0-9]+');
    Route::post('projects/{prj_code}/orders/{order_id}/get-bulk-cancel-allocation', ['as' => 'orders.bulk-cancel-allocation.get', 'uses' => 'Web\OrderController@get_bulk_cancel_allocation', 'can' => 'cancel-allocation.orders|download_xml.orders|download_html.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');
    Route::post('projects/{prj_code}/orders/{order_id}/post-bulk-cancel-allocation', ['as' => 'orders.bulk-cancel-allocation.post', 'uses' => 'Web\OrderController@post_bulk_cancel_allocation', 'can' => 'cancel-allocation.orders|download_xml.orders|download_html.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');

    Route::delete('projects/{prj_code}/orders/{order_id}/delete-avf-file/{avf_id}', ['as' => 'orders.file-destroy', 'uses' => 'Web\OrderController@destoryFile', 'can' => 'delete_file.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('avf_id', '[0-9]+');

    Route::get('projects/{prj_code}/orders/{order_id}/avf/{asset_version_file_id}/re-order-file', ['as' => 'orders.get_re_order_file', 'uses' => 'Web\OrderController@get_re_order_file', 'can' => 'view.assets|view-assigned.assets'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');
    Route::post('projects/{prj_code}/orders/{order_id}/avf/{asset_version_file_id}/re-order-file', ['as' => 'orders.post_re_order_file', 'uses' => 'Web\OrderController@post_re_order_file', 'can' => 'view.assets|view-assigned.assets'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');


    /* Composition*/
    Route::get('projects/{prj_code}/orders/{order_id}/composition-requests/create', ['as' => 'orders.compositions.create', 'uses' => 'Web\OrderController@submit_to_composition', 'can' => 'create_composition.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');
    Route::post('projects/{prj_code}/orders/{order_id}/composition-requests/create', ['as' => 'orders.compositions.store', 'uses' => 'Web\CompositionRequestController@store', 'can' => 'create_composition.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{order_id}/composition-requests/{request_id}', ['as' => 'orders.compositions.show', 'uses' => 'Web\CompositionRequestController@show', 'can' => 'view.orders|view-assigned.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('request_id', '[0-9]+');
     
    Route::get('projects/{prj_code}/composition-entry', ['as' => 'compositions.view', 'uses' => 'Web\CompositionEntryController@composition_request_list_view', 'can' => 'update.composition_request|view.composition_request'])->where('prj_code', '[A-Za-z0-9]+');    
    Route::get('projects/{prj_code}/composition-entry/{canedit}', ['as' => 'compositions.get-file-list', 'uses' => 'Web\CompositionEntryController@get_composition_file_list', 'can' => 'update.composition_request|view.composition_request'])->where('prj_code', '[A-Za-z0-9]+')->where('canedit', '[A-Za-z0-9]+');    
    Route::get('projects/{prj_code}/orders/{order_id}/composition-entry/{request_id}/upload-download', ['as' => 'compositions.download-uploadfile', 'uses' => 'Web\CompositionEntryController@download_upload', 'can' => 'update.composition_request|view.composition_request'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{order_id}/composition-api/{composition_id}/download', ['as' => 'compositions.get-download-file', 'uses' => 'Api\CompositionRequestController@get_download_file', 'can' => 'update.composition_request|view.composition_request'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('composition_id', '[0-9]+');
    Route::post('projects/{prj_code}/orders/{order_id}/composition-api/{composition_id}/upload', ['as' => 'compositions.get-upload-file', 'uses' => 'Api\CompositionRequestController@create_review', 'can' => 'update.composition_request'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('composition_id', '[0-9]+');

    Route::patch('projects/{prj_code}/orders/{order_id}/complete-order', ['as' => 'orders.complete-order', 'uses' => 'Web\OrderController@complete_the_order', 'can' => 'create.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');
    Route::post('projects/{prj_code}/orders/{order_id}/create-pdf', ['as' => 'orders.create-pdf', 'uses' => 'Web\OrderController@create_pdf', 'can' => 'create.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{order_id}/pdf-generation-status', ['as' => 'orders.get-pdf-status', 'uses' => 'Web\OrderController@check_pdf_gen_status', 'can' => 'create.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{order_id}/pdf-generation-status-logs-show', ['as' => 'orders.get-pdf-status-logs', 'uses' => 'Web\OrderController@check_pdf_gen_status_logs', 'can' => 'create.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{order_id}/view-generated-pdf', ['as' => 'orders.view-generated-pdf', 'uses' => 'Web\OrderController@view_generated_pdf', 'can' => 'create.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{order_id}/download-latest-logs-generated-pdf', ['as' => 'orders.download-latest-logs-generated-pdf', 'uses' => 'Web\OrderController@download_latest_log', 'can' => 'create.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');

    Route::post('projects/{prj_code}/orders/{order_id}/create-epub', ['as' => 'orders.create-epub', 'uses' => 'Web\OrderController@create_epub', 'can' => 'create.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{order_id}/epub-generation-status', ['as' => 'orders.get-epub-status', 'uses' => 'Web\OrderController@check_epub_gen_status', 'can' => 'create.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{order_id}/view-generated-epub', ['as' => 'orders.view-generated-epub', 'uses' => 'Web\OrderController@view_generated_epub', 'can' => 'create.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{order_id}/download-completion-error-log', ['as' => 'orders.download-completion-error-log', 'uses' => 'Web\OrderController@download_completion_error_log', 'can' => 'create.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');

    /* Reviews */
    Route::get('projects/{prj_code}/orders/{order_id}/composition-requests/{request_id}/reviews/{review_id}', ['as' => 'reviews.show', 'uses' => 'Web\ReviewController@show', 'can' => 'view.orders|view-assigned.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('request_id', '[0-9]+')->where('review_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{order_id}/composition-requests/{request_id}/reviews/{review_id}/attachment/{attachment_id}/view-file', ['as' => 'reviews.view_attachment', 'uses' => 'Web\ReviewController@view_attachment', 'can' => 'view.orders|view-assigned.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('request_id', '[0-9]+')->where('review_id', '[0-9]+')->where('attachment_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{order_id}/composition-requests/{request_id}/reviews/{review_id}/attachment/{attachment_id}/view-changes', ['as' => 'reviews.view_file_changes', 'uses' => 'Web\ReviewController@view_attachment_content_change', 'can' => 'view.orders|view-assigned.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('request_id', '[0-9]+')->where('review_id', '[0-9]+')->where('attachment_id', '[0-9]+');
    Route::patch('projects/{prj_code}/orders/{order_id}/composition-requests/{request_id}/reviews/{review_id}/attachment/{attachment_id}/update-file-status', ['as' => 'reviews.update_file_status', 'uses' => 'Web\ReviewController@update_attachment_file_status', 'can' => 'accept-reject-files.review'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('request_id', '[0-9]+')->where('review_id', '[0-9]+')->where('attachment_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{order_id}/composition-requests/{request_id}/reviews/{review_id}/send-for-deliverable-review', ['as' => 'reviews.get-deliverable-review-form', 'uses' => 'Web\ReviewController@get_deliverable_review_form', 'can' => 'view.orders|view-assigned.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('request_id', '[0-9]+')->where('review_id', '[0-9]+');
    Route::post('projects/{prj_code}/orders/{order_id}/composition-requests/{request_id}/reviews/{review_id}/send-for-deliverable-review', ['as' => 'reviews.post-deliverable-review-form', 'uses' => 'Web\ReviewController@send_deliverable_for_review', 'can' => 'view.orders|view-assigned.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('request_id', '[0-9]+')->where('review_id', '[0-9]+');

    Route::patch('projects/{prj_code}/orders/{order_id}/composition-requests/{request_id}/reviews/{review_id}/update-edit-permission/{edit_request_id}', ['as' => 'reviews.update-edit-permission', 'uses' => 'Web\AuthorDeliverableReviewController@update_edit_permission'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('request_id', '[0-9]+')->where('review_id', '[0-9]+')->where('edit_request_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{order_id}/composition-requests/{request_id}/reviews/{review_id}/assign-files-to-author', ['as' => 'reviews.assign-files-to-author', 'uses' => 'Web\ReviewController@get_assign_files_to_author', 'can' => 'create.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('request_id', '[0-9]+')->where('review_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{order_id}/composition-requests/{request_id}/reviews/{review_id}/update-status', ['as' => 'reviews.get-update-status', 'uses' => 'Web\ReviewController@get_update_status', 'can' => 'create.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('request_id', '[0-9]+')->where('review_id', '[0-9]+');
    Route::get('projects/{prj_code}/orders/{order_id}/composition-requests/{request_id}/reviews/{review_id}/get-file-allocations', ['as' => 'reviews.get-file-allocations', 'uses' => 'Web\ReviewController@get_file_allocations', 'can' => 'create.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('request_id', '[0-9]+')->where('review_id', '[0-9]+');
    Route::patch('projects/{prj_code}/orders/{order_id}/composition-requests/{request_id}/reviews/{review_id}/update-status', ['as' => 'reviews.post-update-status', 'uses' => 'Web\ReviewController@post_update_status', 'can' => 'create.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('request_id', '[0-9]+')->where('review_id', '[0-9]+');
    Route::get('reviews/{review_id}/{type}/download-files', ['as' => 'reviews.download-review-files', 'uses' => 'Web\ReviewController@downloadReviewFiles']);

    Route::get('projects/{prj_code}/orders/{order_id}/composition-requests/{request_id}/reviews/{review_id}/author-deliverable-reviews/{adr_id}/view-reviewed-pdf-file', ['as' => 'reviews.view_author_deliverable_reviews_pdf', 'uses' => 'Web\ReviewController@view_author_deliverable_reviews_pdf', 'can' => 'view.orders|view-assigned.orders'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('request_id', '[0-9]+')->where('review_id', '[0-9]+')->where('adr_id', '[0-9]+');

    /* Messages */
    Route::get('projects/{prj_code}/messages/{message_id}/attachments/{attachment_id}/view', ['as' => 'messages.view_attachment', 'uses' => 'Web\MessageController@view_attachment'])->where('prj_code', '[A-Za-z0-9]+')->where('message_id', '[0-9]+')->where('attachment_id', '[0-9]+');
    Route::get('projects/{prj_code}/messages/{message_id}/attachments/{attachment_id}/download', ['as' => 'messages.download_attachment', 'uses' => 'Web\MessageController@download_attachment'])->where('prj_code', '[A-Za-z0-9]+')->where('message_id', '[0-9]+')->where('attachment_id', '[0-9]+');

    /* Reports */
    Route::get('reports/title-plannings/{prj_code}', ['as' => 'reports.title-plannings', 'uses' => 'Web\ReportsController@title_plannings', 'can' => 'planning.reports']);
    Route::get('reports/authoring-report/{prj_code}', ['as' => 'reports.authoring-report', 'uses' => 'Web\ReportsController@authoring_report', 'can' => 'authoring.reports']);
    Route::get('reports/asset-report/{prj_code}', ['as' => 'reports.asset-report', 'uses' => 'Web\ReportsController@asset_report', 'can' => 'asset.reports']);
    Route::get('reports/cross-reference-db-report/{prj_code}', ['as' => 'reports.cross-reference-db-report', 'uses' => 'Web\ReportsController@cross_reference_db_report', 'can' => 'reference.reports']);
    Route::get('reports/variables-report/{prj_code}', ['as' => 'reports.variables-report', 'uses' => 'Web\ReportsController@variables_report']);
    Route::post('reports/generate/{prj_code}', ['as' => 'reports.generate', 'uses' => 'Web\ReportsController@generate']);
    Route::post('reports/generate_cross_reference/{prj_code}', ['as' => 'reports.generate-cross-reference-db', 'uses' => 'Web\ReportsController@generate_cross_reference_db']);
    Route::post('reports/generate/{prj_code}/variables', ['as' => 'reports.generate-variables', 'uses' => 'Web\ReportsController@generate_variables_report']);
    
    /* Medicine */
   // Route::view('data_mgt/{prj_code}', 'data_mgt/index');
    Route::get('projects/{prj_code}/data_mgt', ['as' => 'data_mgt.view', 'uses' => 'Web\DataManagementController@index', 'can' => 'view.data_mgt']);


    Route::get('projects/{prj_code}/release_note', function ($project_code){
        return view('release_note')->with(compact('project_code'));
    })->name('release_note.view');

    /*Route::middleware(['author'])->group(function () {*/

        /* Author functionality */
        Route::get('projects/{prj_code}/authoring', ['as' => 'authoring.index', 'uses' => 'Web\AuthoringController@index'])->where('prj_code', '[A-Za-z0-9]+');
        Route::get('projects/{prj_code}/authoring/assigned-files', ['as' => 'authoring.get_assigned_file_list', 'uses' => 'Web\AuthoringController@load_assigned_file_list'])->where('prj_code', '[A-Za-z0-9]+');

        Route::post('projects/{prj_code}/authoring/checkout-multiple-files', ['as' => 'authoring.chechout_multiple_file', 'uses' => 'Web\AuthoringController@checkout_multiple_files'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+');
        Route::post('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/checkout', ['as' => 'authoring.chechout_file', 'uses' => 'Web\AuthoringController@checkout_file'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+');        
        Route::get('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/edit', ['as' => 'authoring.edit_file', 'uses' => 'Web\AuthoringController@edit_file'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+');
        Route::get('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/status', ['as' => 'authoring.get_status', 'uses' => 'Web\AuthoringController@get_file_status'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+');
        Route::patch('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/update', ['as' => 'authoring.update_file', 'uses' => 'Web\AuthoringController@update_file'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+');
        Route::post('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/checkin', ['as' => 'authoring.checkin_file', 'uses' => 'Web\AuthoringController@checkin_file'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+');
        Route::post('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/reset-to-assigned-state', ['as' => 'authoring.reset_file', 'uses' => 'Web\AuthoringController@reset_file'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+');
        Route::post('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/upload-new-file', ['as' => 'authoring.upload_new_file', 'uses' => 'Web\AuthoringController@upload_new_file'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+');
        Route::get('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/download-file', ['as' => 'authoring.download-file', 'uses' => 'Web\AuthoringController@download_existing_file'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+');
        Route::get('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/messages', ['as' => 'authoring.view_allocation_messages', 'uses' => 'Web\AuthoringController@view_allocation_messages'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+');
        Route::post('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/messages/create', ['as' => 'authoring.send_messages', 'uses' => 'Web\OrderController@send_message_to_author'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+');
        Route::get('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/check-pdf-gen-status', ['as' => 'authoring.check-pdf-gen-status', 'uses' => 'Web\AuthoringController@check_pdf_gen_status'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+');
        Route::get('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/view-gen-pdf', ['as' => 'authoring.view-gen-pdf', 'uses' => 'Web\AuthoringController@view_gen_pdf'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+');
        Route::get('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/type/{file_type}/download-gen-file', ['as' => 'authoring.download-gen-file', 'uses' => 'Web\AuthoringController@download_generated_file'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+');
        Route::get('projects/view-temp-pdf', ['as' => 'authoring.view-temp-pdf', 'uses' => 'Web\AuthoringController@view_temp_pdf']);
        Route::post('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/smart-copy-editing', ['as' => 'authoring.process_smart_ce', 'uses' => 'Web\AuthoringController@process_smart_ce'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+');
        Route::get('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/get-entities', ['as' => 'authoring.get-entity-list', 'uses' => 'Web\AuthoringController@get_entities_list'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+');
        Route::get('projects/{prj_code}/authoring//file-allocation/{auth_file_alloc_id}/update-bottom-ribbon-state/state/{state}', ['as' => 'authoring.update_bottom_ribbon_state', 'uses' => 'Web\AuthoringController@update_bottom_ribbon_state'])->where('prj_code', '[A-Za-z0-9]+')->where('auth_file_alloc_id', '[0-9]+');

        Route::get('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/submit-to-pli-plus', ['as' => 'authoring.submit_to_pli_plus', 'uses' => 'Web\PLIPlusPreview\PliPlusPreviewController@submit_file_to_pli_plus'])->where('prj_code', '[A-Za-z0-9]+')->where('auth_file_alloc_id', '[0-9]+');
        Route::get('projects/{prj_code}/authoring/file-allocation/{auth_file_alloc_id}/check-pli-plus-status', ['as' => 'authoring.check_pli_plus_status', 'uses' => 'Web\PLIPlusPreview\PliPlusPreviewController@check_pli_plus_status'])->where('prj_code', '[A-Za-z0-9]+')->where('auth_file_alloc_id', '[0-9]+');

        /*get asset file history */
        Route::get('asset-file/{asset_file_id}/get-file-version-history', ['as' => 'authoring.get-file-version-history', 'uses' => 'Web\OrderController@get_file_version_history'])->where('asset_file_id', '[0-9]+');
        Route::get('asset-version-file/{asset_version_file_id}/download-previous-version-file/type/{file_type}', ['as' => 'authoring.download_previous_version_file', 'uses' => 'Web\OrderController@download_previous_version_file'])->where('asset_version_file_id', '[0-9]+');

        /*Add new file*/
        Route::get('projects/{prj_code}/authoring/order/{order_id}/add-new-file', ['as' => 'authoring.get_add_new_file_to_order', 'uses' => 'Web\AuthoringController@get_add_new_file'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');
        Route::post('projects/{prj_code}/authoring/order/{order_id}/add-new-file', ['as' => 'authoring.post_add_new_file_to_order', 'uses' => 'Web\AuthoringController@post_add_new_file'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');

        Route::get('projects/{prj_code}/authoring/order/{order_id}/add-new-file-to-author', ['as' => 'order.get_add_new_file_to_author', 'uses' => 'Web\OrderController@get_add_new_file_to_author'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');
        Route::post('projects/{prj_code}/authoring/order/{order_id}/add-new-file-to-author', ['as' => 'order.post_add_new_file_to_author', 'uses' => 'Web\OrderController@post_add_new_file_to_author'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->middleware('prevent.html.injection');

        /*Replace from  DP file*/
        Route::get('projects/{prj_code}/orders/{order_id}/avf/{asset_version_file_id}/replace-existing-file', ['as' => 'orders.get_replace_existing_file', 'uses' => 'Web\OrderController@get_replace_existing_file'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');
        Route::post('projects/{prj_code}/orders/{order_id}/avf/{asset_version_file_id}/replace-existing-file', ['as' => 'orders.post_replace_existing_file', 'uses' => 'Web\OrderController@post_replace_existing_file'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');
        
        /*Change auto numbering format*/
        Route::put('projects/{prj_code}/authoring/order/{order_id}/avf/{asset_version_file_id}/change-auto-numbering-format', ['as' => 'order.put_change_auto_numbering_format', 'uses' => 'Web\OrderController@put_change_auto_numbering_format'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->middleware('prevent.html.injection');

        /*Accept from  DP file*/
        Route::get('projects/{prj_code}/orders/{order_id}/avf/{asset_version_file_id}/accept-from-document-pool', ['as' => 'orders.get_accept_from_document_pool', 'uses' => 'Web\OrderController@get_accept_from_document_pool'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');
        Route::post('projects/{prj_code}/orders/{order_id}/avf/{asset_version_file_id}/accept-from-document-pool', ['as' => 'orders.post_accept_from_document_pool', 'uses' => 'Web\OrderController@post_accept_from_document_pool'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');


    Route::post('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/external-process', ['as' => 'authoring.external-process', 'uses' => 'Web\AuthoringController@post_external_process'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+');
        Route::get('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/check-external-process-status', ['as' => 'authoring.check-external-process-status', 'uses' => 'Web\AuthoringController@check_external_process_status'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+');
        /* images in editing file*/
        Route::get('projects/{prj_code}/authoring/order/{order_id}/file-allocation/{auth_file_alloc_id}/{image_name}', ['as' => 'authoring.view_file_image', 'uses' => 'Web\AuthoringController@get_file_image'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('auth_file_alloc_id', '[0-9]+');

        Route::get('projects/{prj_code}/authoring/get-chekout-file-list', ['as' => 'authoring.get_checkout_file_list', 'uses' => 'Web\AuthoringController@get_checkout_file_list'])->where('prj_code', '[A-Za-z0-9]+');
        Route::get('projects/{prj_code}/authoring/get-media-objects-list', ['as' => 'authoring.get_media_objects_list', 'uses' => 'Web\AuthoringController@get_media_objects_list'])->where('prj_code', '[A-Za-z0-9]+');
        Route::get('projects/{prj_code}/authoring/get-publications', ['as' => 'authoring.get_publication_list', 'uses' => 'Web\AssetController@get_publication_list'])->where('prj_code', '[A-Za-z0-9]+');
        Route::get('projects/{prj_code}/authoring/reference-ids/{file_type}/{file_id}/{ref_type}', ['as' => 'authoring.get_reference_ids_list', 'uses' => 'Web\AuthoringController@get_reference_ids_list'])->where('prj_code', '[A-Za-z0-9]+');
        Route::get('projects/{prj_code}/authoring/reference-id-detail/{auth_file_id}/{ref_id}', ['as' => 'authoring.get_reference_id_detail', 'uses' => 'Web\AuthoringController@get_reference_id_detail'])->where('prj_code', '[A-Za-z0-9]+');

        /* Author review deliverables */
        Route::get('projects/{prj_code}/review-deliverables', ['as' => 'authoring.review-deliverables.index', 'uses' => 'Web\AuthorDeliverableReviewController@index'])->where('prj_code', '[A-Za-z0-9]+');
        Route::get('projects/{prj_code}/review-deliverables/load-data', ['as' => 'authoring.review-deliverables.load-data', 'uses' => 'Web\AuthorDeliverableReviewController@load_data'])->where('prj_code', '[A-Za-z0-9]+');

        Route::get('projects/{prj_code}/review-deliverables/review-files/{author_del_rev_id}/review', ['as' => 'authoring.review-deliverables.review-file', 'uses' => 'Web\AuthorDeliverableReviewController@review_file'])->where('prj_code', '[A-Za-z0-9]+')->where('author_del_rev_id', '[0-9]+');
        Route::patch('projects/{prj_code}/review-deliverables/review-files/{author_del_rev_id}/update-status', ['as' => 'authoring.review-deliverables.update-status', 'uses' => 'Web\AuthorDeliverableReviewController@update_file_status'])->where('prj_code', '[A-Za-z0-9]+')->where('author_del_rev_id', '[0-9]+');
        Route::get('projects/{prj_code}/review-deliverables/review-files/{author_del_rev_id}/download', ['as' => 'authoring.review-deliverables.download-file', 'uses' => 'Web\AuthorDeliverableReviewController@review_file_download'])->where('prj_code', '[A-Za-z0-9]+')->where('author_del_rev_id', '[0-9]+');

        /* edit permission*/
        Route::post('projects/{prj_code}/review-deliverables/review-files/{author_del_rev_id}/request-edit-permission/{asset_file_id}', ['as' => 'authoring.request-edit-permission', 'uses' => 'Web\AuthorDeliverableReviewController@request_edit_permission'])->where('prj_code', '[A-Za-z0-9]+')->where('author_del_rev_id', '[0-9]+')->where('asset_file_id', '[0-9]+');

    /*});*/

    Route::get('projects/{prj_code}/reference/changes', ['as' => 'asset_reference.changes', 'uses' => 'Web\AssetReferenceController@get_changes', 'can' => 'view-all-titles.reference-id-changes|view-assigned-titles.reference-id-changes'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/reference/changes/files', ['as' => 'asset_reference.get_change_files', 'uses' => 'Web\AssetReferenceController@get_change_files', 'can' => 'view-all-titles.reference-id-changes|view-assigned-titles.reference-id-changes'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/reference/changes/{file_id}', ['as' => 'asset_reference.view_file_change', 'uses' => 'Web\AssetReferenceController@show_changes', 'can' => 'view-all-titles.reference-id-changes|view-assigned-titles.reference-id-changes'])->where('prj_code', '[A-Za-z0-9]+')->where('file_id', '[0-9]+');
    Route::get('projects/{prj_code}/reference/remarks/{id}', [
        'as' => 'asset_reference.get_remarks', 
        'uses' => 'Web\AssetReferenceController@getReferenceRemarks', 
        'can' => 'view-all-titles.reference-id-changes|view-assigned-titles.reference-id-changes'
    ])->where('prj_code', '[A-Za-z0-9]+')->where('id', '[0-9]+');
    
    Route::post('projects/{prj_code}/reference/remarks/{id}', [
        'as' => 'asset_reference.update_remarks', 
        'uses' => 'Web\AssetReferenceController@updateReferenceRemarks', 
        'can' => 'view-all-titles.reference-id-changes|view-assigned-titles.reference-id-changes'
    ])->where('prj_code', '[A-Za-z0-9]+')->where('id', '[0-9]+');

    /* View Titles*/
    Route::get('projects/{prj_code}/view-titles', ['as' => 'translation-titles.index', 'uses' => 'Web\TranslationTitleController@index', 'can' => 'view-all-titles.translation-titles|view-assigned-titles.translation-titles'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/view-titles/title/{title_id}', ['as' => 'translation-titles.view', 'uses' => 'Web\TranslationTitleController@show', 'can' => 'view-all-titles.translation-titles|view-assigned-titles.translation-titles'])->where('prj_code', '[A-Za-z0-9]+')->where('title_id', '[0-9]+');
    Route::get('projects/{prj_code}/view-titles/title/{title_id}/version/{version_id}', ['as' => 'translation-titles.view-version', 'uses' => 'Web\TranslationTitleController@show_version', 'view-all-titles.translation-titles|view-assigned-titles.translation-titles'])->where('prj_code', '[A-Za-z0-9]+')->where('title_id', '[0-9]+')->where('version_id', '[0-9]+');
    Route::get('projects/{prj_code}/view-titles/title/{title_id}/version/{version_id}/file/{version_file_id}/view/{reference_id?}', ['as' => 'translation-titles.view-version-file', 'uses' => 'Web\TranslationTitleController@show_version_file', 'can' => 'view-all-titles.translation-titles|view-assigned-titles.translation-titles'])->where('prj_code', '[A-Za-z0-9]+')->where('title_id', '[0-9]+')->where('version_id', '[0-9]+')->where('version_file_id', '[0-9]+');
    Route::get('projects/{prj_code}/view-titles/title/{title_id}/version/{version_id}/file/{version_file_id}/{image_name}', ['as' => 'authoring.view_file_image_2', 'uses' => 'Web\TranslationTitleController@get_file_image'])->where('prj_code', '[A-Za-z0-9]+')->where('title_id', '[0-9]+')->where('version_id', '[0-9]+')->where('version_file_id', '[0-9]+');
    Route::get('find-file-based-on-xref/{id}', ['as' => 'authoring.find_file_XML', 'uses' => 'Web\TranslationTitleController@find_file_based_on_xref']);


    /* Role Management */
    Route::get('projects/{prj_code}/roles/{role_type}', ['as' => 'roles.index', 'uses' => 'Web\RoleController@index', 'can' => 'view.role|view.project-role'])->where('role_type', 'U|T');
    Route::get('projects/{prj_code}/roles/{role_type}/create', ['as' => 'roles.create', 'uses' => 'Web\RoleController@create', 'can' => 'create.role|create.project-role'])->where('role_type', 'U|T');
    Route::post('projects/{prj_code}/roles/{role_type}/create', ['as' => 'roles.store', 'uses' => 'Web\RoleController@store', 'can' => 'create.role|create.project-role'])->where('role_type', 'U|T')->middleware('prevent.html.injection');
    Route::get('projects/{prj_code}/roles/{role_type}/{id}', ['as' => 'roles.view', 'uses' => 'Web\RoleController@view', 'can' => 'view.role|view.project-role'])->where('role_type', 'U|T')->where('id', '[0-9]+');
    Route::get('projects/{prj_code}/roles/{role_type}/{id}/edit', ['as' => 'roles.edit', 'uses' => 'Web\RoleController@edit', 'can' => 'edit.role|edit.project-role'])->where('role_type', 'U|T')->where('id', '[0-9]+');
    Route::patch('projects/{prj_code}/roles/{role_type}/{id}', ['as' => 'roles.update', 'uses' => 'Web\RoleController@update', 'can' => 'edit.role|edit.project-role'])->where('role_type', 'U|T')->where('id', '[0-9]+')->middleware('prevent.html.injection');
    Route::delete('projects/{prj_code}/roles/{role_type}/{id}/delete', ['as' => 'roles.destroy', 'uses' => 'Web\RoleController@destroy', 'can' => 'delete.role|delete.project-role'])->where('role_type', 'U|T')->where('id', '[0-9]+');

    /* User Management */
    Route::get('projects/{prj_code}/users', ['as' => 'users.index', 'uses' => 'Web\UserController@index', 'can' => 'view.user|view.project-user']);
    Route::get('projects/{prj_code}/users/get-data', ['as' => 'users.load_ajax', 'uses' => 'Web\UserController@get_list', 'can' => 'view.user|view.project-user']);
    Route::get('projects/{prj_code}/users/create', ['as' => 'users.create', 'uses' => 'Web\UserController@create', 'can' => 'create.user|create.project-user']);
    Route::post('projects/{prj_code}/users/create', ['as' => 'users.store', 'uses' => 'Web\UserController@store', 'can' => 'create.user|create.project-user'])->middleware('prevent.html.injection');
    Route::get('projects/{prj_code}/users/{id}', ['as' => 'users.view', 'uses' => 'Web\UserController@view', 'can' => 'view.user|view.project-user'])->where('id', '[0-9]+');
    Route::get('projects/{prj_code}/users/{id}/edit', ['as' => 'users.edit', 'uses' => 'Web\UserController@edit', 'can' => 'edit.user|edit.project-user'])->where('id', '[0-9]+');
    Route::patch('projects/{prj_code}/users/{id}', ['as' => 'users.update', 'uses' => 'Web\UserController@update', 'can' => 'edit.user|edit.project-user'])->where('id', '[0-9]+')->middleware('prevent.html.injection');
    Route::delete('projects/{prj_code}/users/{id}/delete', ['as' => 'users.destroy', 'uses' => 'Web\UserController@destroy', 'can' => 'delete.user|delete.project-user'])->where('id', '[0-9]+');
    Route::get('projects/{prj_code}/users/get-products', ['as' => 'users.get_products', 'uses' => 'Web\UserController@get_products', 'can' => 'edit.user|edit.project-user']);
    Route::get('projects/{prj_code}/users/get-projects', ['as' => 'users.get_projects', 'uses' => 'Web\UserController@get_projects', 'can' => 'edit.user|edit.project-user']);

    /*Medicine Data Management*/
    Route::get('projects/{prj_code}/med-data-mgt/drug-data', ['as' => 'med_data_mgt.drug.index', 'uses' => 'Web\MedicineDataManagement\DrugDataController@index', 'can' => 'view.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/med-data-mgt/drug-data/get-list', ['as' => 'med_data_mgt.drug.get_list', 'uses' => 'Web\MedicineDataManagement\DrugDataController@get_list', 'can' => 'view.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::post('projects/{prj_code}/med-data-mgt/drug-data/store', ['as' => 'med_data_mgt.drug.store', 'uses' => 'Web\MedicineDataManagement\DrugDataController@store', 'can' => 'create.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/med-data-mgt/drug-data/{id}/edit', ['as' => 'med_data_mgt.drug.edit', 'uses' => 'Web\MedicineDataManagement\DrugDataController@edit', 'can' => 'edit.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::patch('projects/{prj_code}/med-data-mgt/drug-data/{id}/update', ['as' => 'med_data_mgt.drug.update', 'uses' => 'Web\MedicineDataManagement\DrugDataController@update', 'can' => 'edit.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/med-data-mgt/drug-data/get-list-for-dropdown', ['as' => 'med_data_mgt.drug.get_list_for_dropdown', 'uses' => 'Web\MedicineDataManagement\DrugDataController@get_list_for_dropdown', 'can' => 'view.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::post('projects/{prj_code}/med-data-mgt/drug-data/import', ['as' => 'med_data_mgt.drug.import_from_csv', 'uses' => 'Web\MedicineDataManagement\DrugDataController@import_from_csv', 'can' => 'create.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::delete('projects/{prj_code}/med-data-mgt/drug-data/{id}/delete', ['as' => 'med_data_mgt.drug.delete', 'uses' => 'Web\MedicineDataManagement\DrugDataController@destroy', 'can' => 'delete.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');

    Route::get('projects/{prj_code}/med-data-mgt/drug-tables', ['as' => 'med_data_mgt.drug_table.index', 'uses' => 'Web\MedicineDataManagement\DrugTableController@index', 'can' => 'view.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/med-data-mgt/drug-tables/get-list', ['as' => 'med_data_mgt.drug_table.get_list', 'uses' => 'Web\MedicineDataManagement\DrugTableController@get_list', 'can' => 'view.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::post('projects/{prj_code}/med-data-mgt/drug-tables/store', ['as' => 'med_data_mgt.drug_table.store', 'uses' => 'Web\MedicineDataManagement\DrugTableController@store', 'can' => 'create.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/med-data-mgt/drug-tables/{id}/edit', ['as' => 'med_data_mgt.drug_table.edit', 'uses' => 'Web\MedicineDataManagement\DrugTableController@edit', 'can' => 'edit.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::patch('projects/{prj_code}/med-data-mgt/drug-tables/{id}/update', ['as' => 'med_data_mgt.drug_table.update', 'uses' => 'Web\MedicineDataManagement\DrugTableController@update', 'can' => 'edit.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/med-data-mgt/drug-tables/get-list-for-dropdown', ['as' => 'med_data_mgt.drug_table.get_list_for_dropdown', 'uses' => 'Web\MedicineDataManagement\DrugTableController@get_list_for_dropdown', 'can' => 'view.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::post('projects/{prj_code}/med-data-mgt/drug-tables/import', ['as' => 'med_data_mgt.drug_table.import_from_csv', 'uses' => 'Web\MedicineDataManagement\DrugTableController@import_from_csv', 'can' => 'create.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::delete('projects/{prj_code}/med-data-mgt/drug-tables/{id}', ['as' => 'med_data_mgt.drug_table.delete', 'uses' => 'Web\MedicineDataManagement\DrugTableController@destroy', 'can' => 'delete.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');

    Route::get('projects/{prj_code}/med-data-mgt/drug-table-rows', ['as' => 'med_data_mgt.row_data.index', 'uses' => 'Web\MedicineDataManagement\RowDataController@index', 'can' => 'view.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/med-data-mgt/drug-table-rows/get-list', ['as' => 'med_data_mgt.row_data.get_list', 'uses' => 'Web\MedicineDataManagement\RowDataController@get_list', 'can' => 'view.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::post('projects/{prj_code}/med-data-mgt/drug-table-rows/store', ['as' => 'med_data_mgt.row_data.store', 'uses' => 'Web\MedicineDataManagement\RowDataController@store', 'can' => 'create.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/med-data-mgt/drug-table-rows/{id}/edit', ['as' => 'med_data_mgt.row_data.edit', 'uses' => 'Web\MedicineDataManagement\RowDataController@edit', 'can' => 'edit.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::patch('projects/{prj_code}/med-data-mgt/drug-table-rows/{id}/update', ['as' => 'med_data_mgt.row_data.update', 'uses' => 'Web\MedicineDataManagement\RowDataController@update', 'can' => 'edit.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/{prj_code}/med-data-mgt/drug-table-rows/get-list-for-dropdown', ['as' => 'med_data_mgt.row_data.get_list_for_dropdown', 'uses' => 'Web\MedicineDataManagement\RowDataController@get_list_for_dropdown', 'can' => 'view.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::post('projects/{prj_code}/med-data-mgt/drug-table-rows/import', ['as' => 'med_data_mgt.row_data.import_from_csv', 'uses' => 'Web\MedicineDataManagement\RowDataController@import_from_csv', 'can' => 'create.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');
    Route::delete('projects/{prj_code}/med-data-mgt/drug-table-rows/{id}', ['as' => 'med_data_mgt.row_data.delete', 'uses' => 'Web\MedicineDataManagement\RowDataController@destroy', 'can' => 'delete.med_data_mgt'])->where('prj_code', '[A-Za-z0-9]+');


    Route::get('projects/{prj_code}/order/{order_id}/get-subdoc-list', ['as' => 'authoring.get_sub_doc_list', 'uses' => 'Web\AssetController@get_sub_doc_list'])->where('prj_code', '[A-Za-z0-9]+');


    Route::middleware(['admin'])->group(function () {
        /* Admin Part */
    	
        /* Clients */
        Route::get('admin/clients', ['as' => 'clients.index', 'uses' => 'Web\ClientController@index', 'can' => 'view.clients']);
        Route::get('admin/clients/load_ajax', ['as' => 'clients.load_ajax', 'uses' => 'Web\ClientController@load_ajax', 'can' => 'view.clients']);
        Route::get('admin/clients/load_ajax_cilents_history', ['as' => 'clients.load_ajax_cilents_history', 'uses' => 'Web\ClientController@load_ajax_cilents_history', 'can' => 'view.clients']);
        Route::get('admin/clients/create', ['as' => 'clients.create', 'uses' => 'Web\ClientController@create', 'can' => 'create.clients']);   
        Route::post('admin/clients/create', ['as' => 'clients.store', 'uses' => 'Web\ClientController@store', 'can' => 'create.clients'])->middleware('prevent.html.injection');
        Route::get('admin/clients/{id}', ['as' => 'clients.view', 'uses' => 'Web\ClientController@view', 'can' => 'view.clients'])->where('id', '[0-9]+');
        Route::get('admin/clients/{id}/load_ajax_client_history', ['as' => 'clients.load_ajax_client_history', 'uses' => 'Web\ClientController@load_ajax_client_history', 'can' => 'edit.clients'])->where('id', '[0-9]+');
        Route::get('admin/clients/{id}/edit', ['as' => 'clients.edit', 'uses' => 'Web\ClientController@edit', 'can' => 'edit.clients'])->where('id', '[0-9]+');
        Route::patch('admin/clients/{id}/edit', ['as' => 'clients.update', 'uses' => 'Web\ClientController@update', 'can' => 'edit.clients'])->where('id', '[0-9]+')->middleware('prevent.html.injection');
        Route::delete('admin/clients/{id}/delete', ['as' => 'clients.destroy', 'uses' => 'Web\ClientController@destroy', 'can' => 'delete.clients'])->where('id', '[0-9]+');

        /* Projects */
        Route::get('admin/projects', ['as' => 'projects-admin.index', 'uses' => 'Web\ProjectController@index', 'can' => 'view.projects-admin|view_assigned.projects-admin']);
        Route::get('admin/projects/load_ajax', ['as' => 'projects-admin.load_ajax', 'uses' => 'Web\ProjectController@load_ajax', 'can' => 'view.projects-admin|view_assigned.projects-admin']);
        Route::get('admin/projects/load_ajax_projects_history', [
            'as' => 'projects-admin.load_ajax_projects_history',
            'uses' => 'Web\ProjectController@load_ajax_projects_history',
            'can' => 'view.projects-admin|view_assigned.projects-admin'
        ]);
        Route::get('admin/projects/create', ['as' => 'projects-admin.create', 'uses' => 'Web\ProjectController@create', 'can' => 'create.projects-admin']);   
        Route::post('admin/projects/create', ['as' => 'projects-admin.store', 'uses' => 'Web\ProjectController@store', 'can' => 'create.projects-admin'])->middleware('prevent.html.injection');
        Route::get('admin/projects/{id}', ['as' => 'projects-admin.view', 'uses' => 'Web\ProjectController@view', 'can' => 'view.projects-admin'])->where('id', '[0-9]+');
        Route::get(
            'admin/projects/{id}/load_ajax_project_history',
            [
                'as' => 'projects-admin.load_ajax_project_history',
                'uses' => 'Web\ProjectController@load_ajax_project_history',
                'can' => 'edit.projects-admin'
            ]
        )->where('id', '[0-9]+');
        Route::get('admin/projects/{id}/edit', ['as' => 'projects-admin.edit', 'uses' => 'Web\ProjectController@edit', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+');
        Route::patch('admin/projects/{id}/edit', ['as' => 'projects-admin.update', 'uses' => 'Web\ProjectController@update', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->middleware('prevent.html.injection');
        Route::delete('admin/projects/{id}/delete', ['as' => 'projects-admin.destroy', 'uses' => 'Web\ProjectController@destroy', 'can' => 'delete.projects-admin'])->where('id', '[0-9]+');
       
        /* Products */
        Route::get('admin/projects/{id}/products', ['as' => 'products.index', 'uses' => 'Web\ProductController@index', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+');
        Route::get('admin/projects/{id}/products/create', ['as' => 'products.create', 'uses' => 'Web\ProductController@create', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+');
        Route::get(
            'admin/projects/{id}/products/products_history',
            [
                'as' => 'products.load_ajax_products_history',
                'uses' => 'Web\ProductController@load_ajax_products_history',
                'can' => 'edit.projects-admin'
            ]
        )->where('id', '[0-9]+');
        Route::post('admin/projects/{id}/products/create', ['as' => 'products.store', 'uses' => 'Web\ProductController@store', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->middleware('prevent.html.injection');
        Route::get('admin/projects/{id}/products/{tid}/edit', ['as' => 'products.edit', 'uses' => 'Web\ProductController@edit', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('tid', '[0-9]+');
        Route::patch('admin/projects/{id}/products/{tid}/edit', ['as' => 'products.update', 'uses' => 'Web\ProductController@update', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('tid', '[0-9]+')->middleware('prevent.html.injection');
        Route::get('admin/projects/{id}/products/{tid}/product_history', [
            'as' => 'products.load_ajax_product_history',
            'uses' => 'Web\ProductController@load_ajax_product_history',
            'can' => 'edit.projects-admin'
        ])->where('id', '[0-9]+')->where('tid', '[0-9]+');
        Route::delete('admin/projects/{id}/products/{tid}/delete', ['as' => 'products.destroy', 'uses' => 'Web\ProductController@destroy', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('tid', '[0-9]+');
        Route::get('admin/projects/{id}/products/{tid}/download-style/{style}', ['as' => 'products.download_style_file', 'uses' => 'Web\ProductController@download_style_file', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('tid', '[0-9]+');
        Route::get('admin/projects/download-ice-config/{ice_config_id}', ['as' => 'products.download_ice_config_file', 'uses' => 'Web\ProductController@download_ice_config_file', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+');
        Route::get('admin/projects/{id}/products/{tid}/download-processing-instruction-info-config-file', ['as' => 'products.download_processing_instruction_info_config_file', 'uses' => 'Web\ProductController@download_processing_instruction_info_config_file', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+');

        /* log history */
        Route::get('admin/projects/{id}/products/{tid}/product_tasks_history', [
            'as' => 'products.load_ajax_product_tasks_history',
            'uses' => 'Web\ProductController@load_ajax_product_tasks_history',
            'can' => 'edit.projects-admin'
        ])->where('id', '[0-9]+')->where('tid', '[0-9]+');

        Route::get('admin/projects/{id}/products/{tid}/product_task_history/{task_id}', [
            'as' => 'products.load_ajax_product_task_history',
            'uses' => 'Web\ProductController@load_ajax_product_task_history',
            'can' => 'edit.projects-admin'
            ])->where('id', '[0-9]+')->where('tid', '[0-9]+')->where('task_id', '[0-9]+');
            
        Route::get('admin/projects/{id}/products/{tid}/product_metadatas_history', [
            'as' => 'products.load_ajax_product_metadatas_history',
            'uses' => 'Web\ProductController@load_ajax_product_metadatas_history',
            'can' => 'edit.projects-admin'
        ])->where('id', '[0-9]+')->where('tid', '[0-9]+');

        Route::get('admin/projects/{id}/products/{tid}/product_metadata_history/{metadata_id}', [
            'as' => 'products.load_ajax_product_metadata_history',
            'uses' => 'Web\ProductController@load_ajax_product_metadata_history',
            'can' => 'edit.projects-admin'
            ])->where('id', '[0-9]+')->where('tid', '[0-9]+')->where('metadata_id', '[0-9]+');

        Route::get('admin/projects/{id}/products/{tid}/product_deliverables_history', [
            'as' => 'products.load_ajax_product_deliverables_history',
            'uses' => 'Web\ProductController@load_ajax_product_deliverables_history',
            'can' => 'edit.projects-admin'
        ])->where('id', '[0-9]+')->where('tid', '[0-9]+');

        Route::get('admin/projects/{id}/products/{tid}/product_deliverables_history/{deliverable_id}', [
            'as' => 'products.load_ajax_product_deliverable_history',
            'uses' => 'Web\ProductController@load_ajax_product_deliverable_history',
            'can' => 'edit.projects-admin'
            ])->where('id', '[0-9]+')->where('tid', '[0-9]+')->where('deliverable_id', '[0-9]+');

        Route::get('admin/projects/{id}/products/{tid}/product_template_history/{template_id}', [
            'as' => 'products.load_ajax_product_template_history',
            'uses' => 'Web\ProductController@load_ajax_product_template_history',
            'can' => 'edit.projects-admin'
            ])->where('id', '[0-9]+')->where('tid', '[0-9]+')->where('template_id', '[0-9]+');
        
        Route::get('admin/projects/{id}/products/{tid}/product_templates_history', [
            'as' => 'products.load_ajax_product_templates_history',
            'uses' => 'Web\ProductController@load_ajax_product_templates_history',
            'can' => 'edit.projects-admin'
        ])->where('id', '[0-9]+')->where('tid', '[0-9]+');

        Route::get('admin/projects/{id}/products/{tid}/product_languages_history', [
            'as' => 'products.load_ajax_product_languages_history',
            'uses' => 'Web\ProductController@load_ajax_product_languages_history',
            'can' => 'edit.projects-admin'
        ])->where('id', '[0-9]+')->where('tid', '[0-9]+');

        /* Tasks */
        Route::get('admin/projects/{id}/products/{prd_id}/tasks', ['as' => 'tasks.index', 'uses' => 'Web\TaskController@index', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+');
        Route::get('admin/projects/{id}/products/{prd_id}/tasks/create', ['as' => 'tasks.create', 'uses' => 'Web\TaskController@create', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+');
        Route::post('admin/projects/{id}/products/{prd_id}/tasks/create', ['as' => 'tasks.store', 'uses' => 'Web\TaskController@store', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+')->middleware('prevent.html.injection');
        Route::get('admin/projects/{id}/products/{prd_id}/tasks/{tid}/edit', ['as' => 'tasks.edit', 'uses' => 'Web\TaskController@edit', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+')->where('tid', '[0-9]+');
        Route::patch('admin/projects/{id}/products/{prd_id}/tasks/{tid}/edit', ['as' => 'tasks.update', 'uses' => 'Web\TaskController@update', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+')->where('tid', '[0-9]+')->middleware('prevent.html.injection');
        Route::delete('admin/projects/{id}/products/{prd_id}/tasks/{tid}/delete', ['as' => 'tasks.destroy', 'uses' => 'Web\TaskController@destroy', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+')->where('tid', '[0-9]+');

        /* Meta fields */
        Route::get('admin/projects/{id}/products/{prd_id}/meta-data', ['as' => 'meta-data.index', 'uses' => 'Web\MetadataController@index', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+');
        Route::get('admin/projects/{id}/products/{prd_id}/meta-data/create', ['as' => 'meta-data.create', 'uses' => 'Web\MetadataController@create', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+');
        Route::post('admin/projects/{id}/products/{prd_id}/meta-data/create', ['as' => 'meta-data.store', 'uses' => 'Web\MetadataController@store', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+')->middleware('prevent.html.injection');
        Route::get('admin/projects/{id}/products/{prd_id}/meta-data/{mid}/edit', ['as' => 'meta-data.edit', 'uses' => 'Web\MetadataController@edit', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+')->where('mid', '[0-9]+');
        Route::patch('admin/projects/{id}/products/{prd_id}/meta-data/{mid}/edit', ['as' => 'meta-data.update', 'uses' => 'Web\MetadataController@update', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+')->where('mid', '[0-9]+')->middleware('prevent.html.injection');
        Route::delete('admin/projects/{id}/products/{prd_id}/meta-data/{mid}/delete', ['as' => 'meta-data.destroy', 'uses' => 'Web\MetadataController@destroy', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+')->where('mid', '[0-9]+');
        
        /* Deliverables */
        Route::get('admin/projects/{id}/products/{prd_id}/deliverables', ['as' => 'deliverables.index', 'uses' => 'Web\DeliverableController@index', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+');
        Route::get('admin/projects/{id}/products/{prd_id}/deliverables/create', ['as' => 'deliverables.create', 'uses' => 'Web\DeliverableController@create', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+');
        Route::post('admin/projects/{id}/products/{prd_id}/deliverables/create', ['as' => 'deliverables.store', 'uses' => 'Web\DeliverableController@store', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+')->middleware('prevent.html.injection');
        Route::get('admin/projects/{id}/products/{prd_id}/deliverables/{deliverable_id}/edit', ['as' => 'deliverables.edit', 'uses' => 'Web\DeliverableController@edit', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+')->where('deliverable_id', '[0-9]+');
        Route::patch('admin/projects/{id}/products/{prd_id}/deliverables/{deliverable_id}/edit', ['as' => 'deliverables.update', 'uses' => 'Web\DeliverableController@update', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+')->where('deliverable_id', '[0-9]+')->middleware('prevent.html.injection');
        Route::delete('admin/projects/{id}/products/{prd_id}/deliverables/{deliverable_id}/delete', ['as' => 'deliverables.destroy', 'uses' => 'Web\DeliverableController@destroy', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+')->where('deliverable_id', '[0-9]+');
        
        /* PDF Templates */
        Route::get('admin/projects/{id}/products/{prd_id}/pdf-templates', ['as' => 'pdf-templates.index', 'uses' => 'Web\PdfTemplateController@index', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+');
        Route::get('admin/projects/{id}/products/{prd_id}/pdf-templates/create', ['as' => 'pdf-templates.create', 'uses' => 'Web\PdfTemplateController@create', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+');
        Route::post('admin/projects/{id}/products/{prd_id}/pdf-templates/create', ['as' => 'pdf-templates.store', 'uses' => 'Web\PdfTemplateController@store', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+')->middleware('prevent.html.injection');
        Route::get('admin/projects/{id}/products/{prd_id}/pdf-templates/{template_id}/edit', ['as' => 'pdf-templates.edit', 'uses' => 'Web\PdfTemplateController@edit', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+')->where('template_id', '[0-9]+');
        Route::patch('admin/projects/{id}/products/{prd_id}/pdf-templates/{template_id}/edit', ['as' => 'pdf-templates.update', 'uses' => 'Web\PdfTemplateController@update', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+')->where('template_id', '[0-9]+')->middleware('prevent.html.injection');
        Route::delete('admin/projects/{id}/products/{prd_id}/pdf-templates/{template_id}/delete', ['as' => 'pdf-templates.destroy', 'uses' => 'Web\PdfTemplateController@destroy', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+')->where('template_id', '[0-9]+');
        Route::get('admin/projects/{id}/products/{prd_id}/pdf-templates/{template_id}/download-style/{style}', ['as' => 'pdf-templates.download_style_file', 'uses' => 'Web\PdfTemplateController@download_style_file', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+')->where('template_id', '[0-9]+');

        /* Language */
        Route::get('admin/projects/{id}/products/{prd_id}/language/config', ['as' => 'language.config', 'uses' => 'Web\LanguageController@create', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+');
        Route::post('admin/projects/{id}/products/{prd_id}/language/config', ['as' => 'language.update', 'uses' => 'Web\LanguageController@update', 'can' => 'edit.projects-admin'])->where('id', '[0-9]+')->where('prd_id', '[0-9]+');
    });

    Route::get('projects/{prj_code}/assets/{asset_id}/variables', ['as' => 'variables.get_variables_list', 'uses' => 'Web\VariableController@get_variables_list'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_id', '[0-9]+');
    Route::get('projects/{prj_code}/variables/load_ajax_variables_history', ['as' => 'variables.load_ajax_variables_history', 'uses' => 'Web\VariableController@load_ajax_variables_history'])->where('prj_code', '[A-Za-z0-9]+');
    Route::get('projects/variables/load_ajax_variable_history/{id}', ['as' => 'variables.load_ajax_variable_history', 'uses' => 'Web\VariableController@load_ajax_variable_history'])->where('id', '[0-9]+');
    Route::resource('projects/{prj_code}/variables', 'Web\VariableController');
});