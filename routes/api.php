<?php

use App\Http\Controllers\admin\AdminAuthController;
use App\Http\Controllers\beneficiary\BeneficiaryController;
use App\Http\Controllers\beneficiary\BeneficiaryRequestController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Donation_Type\Campaign\CampaignBeneficiaryController;
use App\Http\Controllers\Donation_Type\Campaign\CampaignController;
use App\Http\Controllers\Donation_Type\Campaign\CampaignFilterController;
use App\Http\Controllers\Donation_Type\Campaign\CampaignVolunteerController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\user\UserAuthController;
use App\Http\Controllers\user\UserController;
use App\Http\Controllers\volunteer\VolunteerController;
use App\Http\Controllers\volunteer\VolunteerRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::middleware('auth:sanctum')
    ->post('/setLanguage', [LanguageController::class, 'setLanguage']);


// Routes for user
Route::prefix('user')->group(function () {
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'checkLanguage'])->group(function () {
        Route::post('/logout', [UserAuthController::class, 'logout']);
        Route::get('/showProfile', [UserController::class, 'showProfile']);
        Route::post('/updateProfile', [UserController::class, 'updateProfile']);
    });
});
// Routes for admin
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/forgotPassword', [AdminAuthController::class, 'forgotPassword']);
    Route::post('/checkCode', [AdminAuthController::class, 'checkCode']);
    Route::post('/resetPassword', [AdminAuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
    });
});

Route::middleware(['auth:sanctum', 'checkLanguage'])->group(function () {

Route::prefix('volunteer_request')->group(function () {
    Route::post('/add', [VolunteerRequestController::class, 'addVolunteerRequest']);
    Route::get('/getAllUserRequests', [VolunteerRequestController::class, 'getAllUserVolunteerRequests']);
    Route::get('/getDetails/{id}', [VolunteerRequestController::class, 'getVolunteerRequestDetails']);
});
Route::prefix('beneficiary_request')->group(function () {
    Route::post('/add', [BeneficiaryRequestController::class, 'add']);
});
Route::prefix('category')->group(function () {
    Route::post('/add',[CategoryController::class,'addCategory']);
    Route::get('/showAll',[CategoryController::class,'getAllCategories']);
    Route::get('/get/{categoryId}',[CategoryController::class,'getCategoryById']);
    Route::delete('delete/{categoryId}',[CategoryController::class,'deleteCategory']);
    Route::post('/update/{categoryId}',[CategoryController::class,'updateCategory']);
});
Route::prefix('campaign')->group(function () {
    Route::post('/add', [CampaignController::class, 'addCampaign']);
    Route::get('/getAll', [CampaignController::class, 'getAllCampaigns']);
    Route::get('/archivedCampaigns', [CampaignController::class, 'getArchivedCampaigns']);
    Route::get('/get/{campaignId}', [CampaignController::class, 'getCampaignDetails']);
    Route::get('/category/{categoryId}', [CampaignController::class, 'getCampaignsByCategory']);
    Route::post('/update/{campaignId}', [CampaignController::class, 'updateCampaign']);
    Route::get('/by-status', [CampaignController::class, 'getCampaignsByStatus']);
    Route::delete('delete/{campaignId}',[CampaignController::class,'deleteCampaign']);
    Route::get('/filter/byDate', [CampaignFilterController::class, 'filterCampaignsByDate']);
    Route::get('/filter/byGoalAmount', [CampaignFilterController::class, 'filterCampaignsByGoalAmount']);
    Route::get('/filter/byBeneficiariesCount', [CampaignFilterController::class, 'filterCampaignsByBeneficiariesCount']);

//
    //campaign with beneficiary

    Route::post('/{campaignId}/addBeneficiaries', [CampaignBeneficiaryController::class, 'addBeneficiariesToCampaign']);
    Route::get('/{campaignId}/getBeneficiaries', [CampaignBeneficiaryController::class, 'getCampaignWithBeneficiaries']);
    Route::delete('/{campaignId}/deleteBeneficiaries/{beneficiaryId}', [CampaignBeneficiaryController::class, 'removeBeneficiaryFromCampaign']);


    //campaign with volunteer

    Route::post('/{campaignId}/addVolunteers', [CampaignVolunteerController::class, 'addVolunteersToCampaign']);
    Route::get('/{campaignId}/getVolunteers', [CampaignVolunteerController::class, 'getCampaignWithVolunteers']);
    Route::delete('/{campaignId}/deleteVolunteers/{volunteerId}', [CampaignVolunteerController::class, 'removeVolunteerFromCampaign']);



});

    Route::get('/beneficiary/{beneficiaryId}/campaigns', [BeneficiaryController::class, 'getBeneficiaryCampaigns']);
    Route::get('/volunteer/{volunteerId}/campaigns', [VolunteerController::class, 'getVolunteerCampaigns']);



});
