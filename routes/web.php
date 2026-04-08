<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisplaysController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\HistoriesController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QualityAssuranceController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ApplicationSettingsController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\TreeController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WorkgroupController;
use App\Http\Controllers\WorkstationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AccountController::class, 'login']);

Route::get('login', [AccountController::class, 'login'])->name('login');
Route::post('login', [AccountController::class, 'login']);
Route::get('signup', [AccountController::class, 'signup']);
Route::post('create-account', [AccountController::class, 'create_account']);
Route::get('forgot-password', [AccountController::class, 'forgot_password']);
Route::post('reset-password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('logout', [AccountController::class, 'logout']);
Route::post('locale', [LocaleController::class, 'update'])->name('locale.update');
Route::get('choose-platform', [AccountController::class, 'choose_platform']);
Route::get('select-platform/{platform}', [AccountController::class, 'select_platform']);

// Password reset form
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');

// Handle the reset password form submission
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/verify-user/{code}', [AccountController::class, 'activateUser'])->name('activate.user');

  //Route::get('/verify-user/{code}', 'Auth\RegisterController@activateUser')->name('activate.user');
  //Resend
  //Route::get('/auth/resend', 'Auth\LoginController@resend');
  //Route::post('/auth/re-activation', 'Auth\RegisterController@reauthenticated');
  // Notification mail
  //Route::get('/email/notification', 'Auth\RegisterController@RegisterEmailNotification');

Route::group(['middleware' => 'auth'], function(){
    Route::post('update-sidebar', [DashboardController::class, 'update_sidebar']);
    Route::post('session/heartbeat', [AccountController::class, 'heartbeat']);
    
  // Test routes for debugging Livewire tables
  Route::get('/test-displays-data', function() {
      $user = auth()->user();
      $facility_id = $user->facility_id;
      
      $failedDisplays = \App\Models\Display::where('status', 2)->count();
      $allDisplays = \App\Models\Display::count();
      
      $dueTasks = \App\Models\Task::where('nextrun', '<=', time() + (7 * 24 * 60 * 60))
          ->where('deleted', 0)
          ->count();
      $allTasks = \App\Models\Task::count();
      
      return response()->json([
          'user_facility_id' => $facility_id,
          'failed_displays_count' => $failedDisplays,
          'all_displays_count' => $allDisplays,
          'due_tasks_count' => $dueTasks,
          'all_tasks_count' => $allTasks,
          'sample_failed_display' => \App\Models\Display::where('status', 2)->first(),
          'sample_task' => \App\Models\Task::first(),
      ]);
  });
  Route::get('dashboard', [DashboardController::class, 'dashboard']);
  Route::get('search', [DashboardController::class, 'search']);
  Route::get('d-fail', [DashboardController::class, 'd_fail']);
  Route::get('d-ok', [DashboardController::class, 'd_ok']);
  Route::get('due-tasks', [DashboardController::class, 'due_tasks']);
  Route::get('client-monitor', [DashboardController::class, 'client_monitor']);
  Route::get('api/due-tasks', [DashboardController::class, 'api_due_tasks']);
  Route::get('api/client-monitor', [DashboardController::class, 'api_client_monitor']);
  Route::get('api/displays-failed', [DashboardController::class, 'api_displays_failed']);
  Route::get('api/latest-performed', [DashboardController::class, 'api_latest_performed']);
  Route::get('api/connection-watchlist', [DashboardController::class, 'api_connection_watchlist']);
  // Grid.js API endpoints for sidebar pages
  Route::get('api/displays',      [DashboardController::class, 'api_displays']);
  Route::get('api/workstations',  [DashboardController::class, 'api_workstations']);
  Route::get('api/workstation-modal/{id}', [WorkstationController::class, 'api_workstation_modal']);
  Route::get('api/workstation-modal/{id}/move-options', [WorkstationController::class, 'workstation_move_options']);
  Route::get('api/workstation-modal/workgroups/{facilityId}', [WorkstationController::class, 'workstation_move_workgroups']);
  Route::post('api/workstation-modal/{id}/move', [WorkstationController::class, 'move_workstation_modal']);
  Route::get('api/workgroup-modal/{id}', [WorkgroupController::class, 'api_workgroup_modal']);
  Route::post('api/workgroup-modal/{id}/save', [WorkgroupController::class, 'save_workgroup_modal']);
  Route::get('api/workgroups',    [DashboardController::class, 'api_workgroups']);
  Route::get('api/histories',     [DashboardController::class, 'api_histories']);
  Route::get('api/tasks',         [DashboardController::class, 'api_tasks']);
  Route::get('api/calibration-tasks', [DashboardController::class, 'api_calibration_tasks']);
  Route::get('api/alerts',        [DashboardController::class, 'api_alerts']);
  Route::post('api/alerts/{id}/toggle', [DashboardController::class, 'api_alerts_toggle']);
  Route::get('api/notifications', [NotificationController::class, 'index']);
  Route::post('api/notifications/read-all', [NotificationController::class, 'readAll']);
  Route::post('api/notifications/{id}/read', [NotificationController::class, 'read']);
  Route::get('api/global-search', [DashboardController::class, 'api_global_search']);
  Route::get('notifications', [NotificationController::class, 'page']);
  Route::get('api/facilities',    [DashboardController::class, 'api_facilities']);
  Route::get('api/display-modal/{id}/move-options', [DisplaysController::class, 'display_move_options']);
  Route::get('api/display-modal/workgroups/{facilityId}', [DisplaysController::class, 'display_move_workgroups']);
  Route::get('api/display-modal/workstations/{workgroupId}', [DisplaysController::class, 'display_move_workstations']);
  Route::get('api/display-modal/{id}/edit', [DisplaysController::class, 'edit_display_modal']);
  Route::post('api/display-modal/{id}/move', [DisplaysController::class, 'move_display_modal']);
  Route::post('api/display-modal/{id}/quick-calibrate', [DisplaysController::class, 'quick_calibrate_display']);
  Route::get('api/display-modal/{id}', [DisplaysController::class, 'api_display_modal']);
  Route::post('api/display-modal/{id}/save', [DisplaysController::class, 'save_display_modal']);

  Route::post('create-build', [UpdateController::class, 'create_build']);
  
  Route::get('load-tree/{WORKSTATION}/{LEAF}', [TreeController::class, 'load_tree']);
  Route::post('load-tree/{WORKSTATION}/{LEAF}', [TreeController::class, 'load_tree']);
    
  Route::get('displays', [DisplaysController::class, 'displays']);
  Route::post('displays', [DisplaysController::class, 'displays']);
  Route::post('displaysettings/{id}', [DisplaysController::class, 'load_display_settings']);
  Route::post('displaysettings/save/{id}', [DisplaysController::class, 'save_display_settings']);
  Route::post('displaysettings/save/finance/{id}', [DisplaysController::class, 'save_display_fn']);
  
  Route::get('display-calibration', [DisplaysController::class, 'display_calibration']);
  Route::post('display-calibration', [DisplaysController::class, 'display_calibration']);

  Route::post('create-task', [TasksController::class, 'edit_task']);
  Route::post('edit-task', [TasksController::class, 'edit_task']);
  Route::post('update-task', [TasksController::class, 'update_task']);
  Route::post('delete-task', [TasksController::class, 'delete_task']);
  
  Route::post('delete-display', [DisplaysController::class, 'delete_display']);
  
  Route::post('fetch-groups', [DisplaysController::class, 'fetch_workgroups']);
  Route::post('fetch-workstations', [DisplaysController::class, 'fetch_workstations']);
  Route::post('fetch-displays', [DisplaysController::class, 'fetch_displays']);
  Route::post('fetch-displays-checklist', [DisplaysController::class, 'fetch_displays_checklist']);
  
  Route::get('display-settings/{ID}', [DisplaysController::class, 'display_settings']);
  Route::post('display-settings/{ID}', [DisplaysController::class, 'display_settings']);
  
  Route::post('fetch-data-settings', [DisplaysController::class, 'fetch_data_settings']);
  
  Route::get('site-settings', [SettingsController::class, 'site_settings']);
  Route::post('site-settings', [SettingsController::class, 'site_settings']);
    Route::get('subscription', [SettingsController::class, 'subscription']);
  Route::post('subscription', [SettingsController::class, 'subscription']);
  
  Route::get('profile-settings', [SettingsController::class, 'profile_settings']);
  Route::post('profile-settings', [SettingsController::class, 'profile_settings']);
  Route::post('remove-image', [SettingsController::class, 'remove_image']);
  
  Route::get('alert-settings', [SettingsController::class, 'alert_settings']);
  Route::post('alert-settings', [SettingsController::class, 'alert_settings']);

  Route::get('build-version', [SettingsController::class, 'build_version']);
  Route::post('build-version', [SettingsController::class, 'build_version']);

  Route::post('sendtestmail', [SettingsController::class, 'sendTestEmail']);
  //Route::post('alert-store', [SettingsController::class, 'alert_store']);
  Route::post('alert-form', [SettingsController::class, 'form']);
  Route::post('delete-alert', [SettingsController::class, 'delete_alert']);
  Route::post('errorlimit-update', [SettingsController::class, 'errorlimit_update']);
  Route::post('errorsmtp-update', [SettingsController::class, 'errorsmtp_update']);
  Route::post('update-alert', [SettingsController::class, 'update_alert']);

  Route::get('application-settings/{ID}', [SettingsController::class, 'application_settings']);
  Route::get('global-settings', [SettingsController::class, 'global_settings']);
  Route::get('app-settings/{ID}', [ApplicationSettingsController::class, 'app_settings']);
  Route::get('app-settings/get/categories', [ApplicationSettingsController::class, 'getCategories']);
  Route::post('app-settings/save/app/{id}', [ApplicationSettingsController::class, 'saveapp']);
  Route::post('app-settings/save/location/{id}', [ApplicationSettingsController::class, 'savelocation']);
  Route::post('app-settings/save/dc/{id}', [ApplicationSettingsController::class, 'savedc']);
  Route::post('app-settings/save/qa/{id}', [ApplicationSettingsController::class, 'saveqa']);
  
  Route::get('facility-info', [FacilityController::class, 'facility_information']);
  Route::post('facility-info', [FacilityController::class, 'facility_information']);
  Route::get('facility-info/{ID}', [FacilityController::class, 'facility_information']);
  Route::post('facility-info/{ID}', [FacilityController::class, 'facility_information']);
  Route::post('fetch-description', [FacilityController::class, 'fetch_description'] );
  Route::post('fetch-location', [FacilityController::class, 'fetch_location']);
  Route::post('fetch-timezone', [FacilityController::class, 'fetch_timezone']);
  Route::get('facilities-management', [FacilityController::class, 'facilities_management']);
  Route::post('facilities-management', [FacilityController::class, 'facilities_management']);
  Route::post('facility-form', [FacilityController::class, 'form']);
  Route::post('delete-facility', [FacilityController::class, 'delete']);
  Route::get('api/facility-modal/{id}', [FacilityController::class, 'api_facility_modal']);
  Route::post('api/facility-modal/{id}/save', [FacilityController::class, 'save_facility_modal']);
  
  Route::get('workgroups', [WorkgroupController::class, 'workgroups']);
  Route::post('workgroups', [WorkgroupController::class, 'workgroups']);
  Route::post('workgroup-form', [WorkgroupController::class, 'form']);
  Route::post('delete-workgroup', [WorkgroupController::class, 'delete_workgroup']);
  Route::get('workgroups-info/{ID}', [WorkgroupController::class, 'workgroups_info']);
  Route::get('workstations', [WorkstationController::class, 'workstations']);
  Route::post('workstations', [WorkstationController::class, 'workstations']);
  Route::post('workstation-form', [WorkstationController::class, 'form']);
  Route::post('delete-workstation', [WorkstationController::class, 'delete_workstation']);
  Route::get('workstations-info/{ID}', [WorkstationController::class, 'workstations_info']);
  
  Route::get('scheduler', [QualityAssuranceController::class, 'quality_assuarance']);
  Route::post('scheduler', [QualityAssuranceController::class, 'quality_assuarance']);
  Route::post('fetch-groups2', [QualityAssuranceController::class, 'fetch_workgroups2']);
  Route::post('fetch-workstations2', [QualityAssuranceController::class, 'fetch_workstations2']);
  Route::post('fetch-displays-checklist2', [QualityAssuranceController::class, 'fetch_displays_checklist2']);

  Route::get('/calendar/events', [CalendarController::class, 'events']);
  
  Route::get('histories/{ID}', [HistoriesController::class, 'view_histories']);
  Route::get('histories/{ID}/preview', [HistoriesController::class, 'print_preview']);
  Route::get('api/history-modal/{id}', [HistoriesController::class, 'api_history_modal']);
  Route::get('/graph/spect/{history_id}/{step_id}/{graph_id}', [ExportController::class, 'generateSpectralGraph']);
  Route::get('/graph/image/{history_id}/{step_id}/{graph_id}', [ExportController::class, 'convertGraphToImage']);

  Route::get('histories-reports', [HistoriesController::class, 'histories']);
  Route::post('histories-reports', [HistoriesController::class, 'histories']);
  Route::post('histories/export/pdf', [ExportController::class, 'exportPDF']);

  Route::get('reports/display-calibration', [ReportsController::class, 'exportDisplayCalibration']);
  Route::get('reports/displays', [ReportsController::class, 'exportDisplays']);
  Route::get('reports/all-tasks', [ReportsController::class, 'exportAllTasks']);
  Route::get('reports/histories-reports', [ReportsController::class, 'exportHistoriesReports']);
  Route::get('reports/workgroups', [ReportsController::class, 'exportWorkgroups']);
  Route::get('reports/workstations', [ReportsController::class, 'exportWorkstations']);
  
  Route::get('users-management', [UsersController::class, 'users_management']);
  Route::post('users-management', [UsersController::class, 'users_management']);
  Route::get('users-list', [UsersController::class, 'users_list']);
  Route::get('api/user-modal/{id?}', [UsersController::class, 'user_modal_json']);
  Route::post('api/user-modal/save', [UsersController::class, 'save_modal']);
  Route::post('user-form', [UsersController::class, 'user_form']);
  Route::post('delete-user', [UsersController::class, 'delete']);
  Route::post('update-user', [UsersController::class, 'update_user']);
  
});
