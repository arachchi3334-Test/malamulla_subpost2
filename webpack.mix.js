let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
 mix.js('resources/js/ckeditor.js', 'public/js').react();
 mix.js('resources/js/app.js', 'public/js').react().sass('resources/sass/app.scss', 'public/css');

 mix
 	.js('resources/js/helpers/helpers.js', 'public/js/helpers/helpers.js')
 	.js('resources/assets/js/custom.js', 'public/assets/js/custom.js')
 	.js('resources/assets/js/pages/assets/common.js', 'public/assets/js/pages/assets/common.js')
 	.js('resources/assets/js/pages/assets/index.js', 'public/assets/js/pages/assets/index.js')
 	.js('resources/assets/js/pages/assets/create.js', 'public/assets/js/pages/assets/create.js')
 	.js('resources/assets/js/pages/assets/edit.js', 'public/assets/js/pages/assets/edit.js')
 	.js('resources/assets/js/pages/assets/view.js', 'public/assets/js/pages/assets/view.js')
 	.js('resources/assets/js/pages/assets/complete_files_list.js', 'public/assets/js/pages/assets/complete_files_list.js')

 	.js('resources/assets/js/pages/author-deliverable-review/index.js', 'public/assets/js/pages/author-deliverable-review/index.js')
 	.js('resources/assets/js/pages/author-deliverable-review/review-file.js', 'public/assets/js/pages/author-deliverable-review/review-file.js')

 	.js('resources/assets/js/pages/planning/index.js', 'public/assets/js/pages/planning/index.js')
 	.js('resources/assets/js/pages/planning/create.js', 'public/assets/js/pages/planning/create.js')
 	.js('resources/assets/js/pages/planning/view.js', 'public/assets/js/pages/planning/view.js')

 	.js('resources/assets/js/pages/orders/index.js', 'public/assets/js/pages/orders/index.js')
 	.js('resources/assets/js/pages/orders/create.js', 'public/assets/js/pages/orders/create.js')
 	.js('resources/assets/js/pages/orders/view.js', 'public/assets/js/pages/orders/view.js')
 	.js('resources/assets/js/pages/orders/auto-number-sequencing.js', 'public/assets/js/pages/orders/auto-number-sequencing.js')

 	.js('resources/assets/js/pages/authoring/file-upload.js', 'public/assets/js/pages/authoring/file-upload.js')
 	.js('resources/assets/js/pages/authoring/assigned-file-list.js', 'public/assets/js/pages/authoring/assigned-file-list.js')

 	.js('resources/assets/js/pages/dashboard/author_dashboard.js', 'public/assets/js/pages/dashboard/author_dashboard.js')

 	.js('resources/assets/js/pages/composition-requests/create.js', 'public/assets/js/pages/composition-requests/create.js')
 	.js('resources/assets/js/pages/composition-requests/view.js', 'public/assets/js/pages/composition-requests/view.js')

 	.js('resources/assets/js/pages/projects-admin/projects_index.js', 'public/assets/js/pages/projects-admin/projects_index.js')
	.js('resources/assets/js/pages/projects-admin/log_history_summery.js', 'public/assets/js/pages/projects-admin/log_history_summery.js')
	.js('resources/assets/js/pages/projects-admin/log_history_summery_for_extra_tabs.js', 'public/assets/js/pages/projects-admin/log_history_summery_for_extra_tabs.js')
 	.js('resources/assets/js/pages/projects-admin/log_history.js', 'public/assets/js/pages/projects-admin/log_history.js')
	.js('resources/assets/js/pages/projects-admin/log_history_for_articale.js', 'public/assets/js/pages/projects-admin/log_history_for_articale.js')
 	.js('resources/assets/js/pages/projects-admin/log_history_for_tree_view.js', 'public/assets/js/pages/projects-admin/log_history_for_tree_view.js')
 	.js('resources/assets/js/pages/projects-admin/log_history_for_dp.js', 'public/assets/js/pages/projects-admin/log_history_for_dp.js')
 	.js('resources/assets/js/pages/projects-admin/projects_edit.js', 'public/assets/js/pages/projects-admin/projects_edit.js')
 	.js('resources/assets/js/pages/projects-admin/product_edit.js', 'public/assets/js/pages/projects-admin/product_edit.js')
 	.js('resources/assets/js/pages/projects-admin/deliverable_edit.js', 'public/assets/js/pages/projects-admin/deliverable_edit.js')
 	.js('resources/assets/js/pages/projects-admin/metadata_edit.js', 'public/assets/js/pages/projects-admin/metadata_edit.js')
 	.js('resources/assets/js/pages/projects-admin/pdf_template_edit.js', 'public/assets/js/pages/projects-admin/pdf_template_edit.js')
 	.js('resources/assets/js/pages/projects-admin/task_edit.js', 'public/assets/js/pages/projects-admin/task_edit.js')
	 .js('resources/assets/js/pages/projects-admin/language_edit.js', 'public/assets/js/pages/projects-admin/language_edit.js')

 	.js('resources/assets/js/pages/reports/index.js', 'public/assets/js/pages/reports/index.js')
 	.js('resources/assets/js/pages/reports/cross_reference_db_index.js', 'public/assets/js/pages/reports/cross_reference_db_index.js')
 	.js('resources/assets/js/pages/reports/variables_index_index.js', 'public/assets/js/pages/reports/variables_index_index.js')

 	.js('resources/assets/js/pages/roles/create.js', 'public/assets/js/pages/roles/create.js')
 	.js('resources/assets/js/pages/users/index.js', 'public/assets/js/pages/users/index.js')
 	.js('resources/assets/js/pages/users/edit.js', 'public/assets/js/pages/users/edit.js')

 	.js('resources/assets/js/pages/asset_references/changed-file-list.js', 'public/assets/js/pages/asset_references/changed-file-list.js')
    
    .js('resources/assets/js/pages/translation-titles/view-file.js', 'public/assets/js/pages/translation-titles/view-file.js')

    .js('resources/assets/js/pages/authoring/file-edit.js', 'public/assets/js/pages/authoring/file-edit.js')
    .js('resources/assets/js/pages/orders/ongoing_tasks.js', 'public/assets/js/pages/orders/ongoing_tasks.js')

    .js('resources/assets/js/pages/article-pool/view.js', 'public/assets/js/pages/article-pool/view.js')
	
	.js('resources/assets/js/pages/composition-entry/upload-file.js', 'public/assets/js/pages/composition-entry/upload-file.js')
	.js('resources/assets/js/pages/composition-entry/view-list.js', 'public/assets/js/pages/composition-entry/view-list.js')

    .js('resources/assets/js/pages/asset-conversion/pending-list.js', 'public/assets/js/pages/asset-conversion/pending-list.js')
    .js('resources/assets/js/pages/asset-conversion/upload-file.js', 'public/assets/js/pages/asset-conversion/upload-file.js')
    .js('resources/assets/js/pages//medicine-data-mgt/drug/index.js', 'public/assets/js/pages/medicine-data-mgt/drug/index.js')
    .js('resources/assets/js/pages//medicine-data-mgt/drug-table/index.js', 'public/assets/js/pages/medicine-data-mgt/drug-table/index.js')
    .js('resources/assets/js/pages//medicine-data-mgt/row-data/index.js', 'public/assets/js/pages/medicine-data-mgt/row-data/index.js')

	.js('resources/assets/js/pages/variables/form.js', 'public/assets/js/pages/variables/form.js')
	.js('resources/assets/js/pages/variables/index.js', 'public/assets/js/pages/variables/index.js')

	.version();
