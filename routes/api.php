<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::group(['middleware' => 'auth:api'], function() {
    Route::post('update-ch-file/uuid/{uuid}', ['as' => 'api.update-ch-file', 'uses' => 'Api\ContentHarmonizationController@update_ch_file']);

    Route::post('submit-xml-file/order/{order_id}', ['as' => 'api.submit-xml-file', 'uses' => 'Api\XmlInputController@submit_xml_file']);
});
/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
//todo move to auth middleware after demo
Route::get('/med-data-mgt/drug-data/list', ['uses' => 'Api\MedicineDataManagement\DrugDataController@get_list_for_dropdown']);
Route::get('/med-data-mgt/drug/{drug_id}/drug-table/list', ['uses' => 'Api\MedicineDataManagement\DrugTableController@get_list_for_dropdown']);
Route::post('/product/{product_id}/med-data-mgt/get-full-data', ['as' => 'med_data_mgt.drug.get_full_data', 'uses' => 'Api\MedicineDataManagement\DrugDataController@get_data_for_template']);
Route::post('projects/{prj_code}/check-reference-validity',
    ['as' => 'api.check-reference-validity', 'uses' => 'Api\CrossReferenceController@check_reference_validity']);

Route::group(['middleware' => 'auth:api'], function() {
	
	Route::get('test-api', function(){
		return 'Working...';
	});

	Route::get('projects/{prj_code}/orders/{order_id}/composition-requests/{request_id}/download-url', ['as' => 'api.orders.compositions.get-download-url', 'uses' => 'Api\CompositionRequestController@get_download_url'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('request_id', '[0-9]+');
	Route::get('projects/{prj_code}/orders/{order_id}/composition-requests/{request_id}/download-file', ['as' => 'api.orders.compositions.get-download-file', 'uses' => 'Api\CompositionRequestController@get_download_file'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('request_id', '[0-9]+');
	Route::post('projects/{prj_code}/orders/{order_id}/composition-requests/{request_id}/reviews/create', ['as' => 'api.orders.reviews.create', 'uses' => 'Api\CompositionRequestController@create_review'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('request_id', '[0-9]+') ;
	Route::get('projects/{prj_code}/orders/{order_id}/composition-requests/{request_id}/reviews/{review_id}/download-file', ['as' => 'api.orders.reviews.get-download-file', 'uses' => 'Api\CompositionRequestController@get_download_review_file'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+')->where('request_id', '[0-9]+')->where('review_id', '[0-9]+');

	Route::post('projects/{prj_code}/itp-pdf-generation/{allocation_id}/status', ['as' => 'api.itp-pdf-generation.status-update', 'uses' => 'Api\PdfGenRequestController@update_file_status'])->where('prj_code', '[A-Za-z0-9]+')->where('allocation_id', '[0-9]+');
	Route::post('projects/{prj_code}/itp-order-pdf-generation/{order_id}/status', ['as' => 'api.itp-order-pdf-generation.status-update', 'uses' => 'Api\PdfGenRequestController@update_order_file_status'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');
	Route::post('projects/{prj_code}/itp-order-epub-generation/{order_id}/status', ['as' => 'api.itp-order-epub-generation.status-update', 'uses' => 'Api\PdfGenRequestController@update_order_epub_file_status'])->where('prj_code', '[A-Za-z0-9]+')->where('order_id', '[0-9]+');

	Route::post('projects/{prj_code}/auther-file-external-process/{allocation_id}/{process}/status', ['as' => 'api.external-process-status-update', 'uses' => 'Api\ExternalProcessController@update_file_status'])->where('prj_code', '[A-Za-z0-9]+')->where('allocation_id', '[0-9]+');

	Route::get('projects/{prj_code}/assets/{asset_code}/files', ['as' => 'api.assets.files', 'uses' => 'Api\AssetController@get_files_list'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_code', '[A-Za-z0-9-_]+');
	Route::get('projects/{prj_code}/assets/{asset_code}/files/{file_id}/download', ['as' => 'api.assets.download-file', 'uses' => 'Api\AssetController@download_file'])->where('prj_code', '[A-Za-z0-9]+')->where('asset_code', '[A-Za-z0-9-_]+')->where('file_id', '[0-9]+');
});

Route::fallback(function () {
    return response()->json([
        'message' => 'The requested URL could not be found.',
        'status' => 'error'
    ], Response::HTTP_NOT_FOUND);
});
