<?php

use App\Http\Controllers\admin\AdminAuthController;
use App\Http\Controllers\beneficiary\BeneficiaryController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Donation_Type\Campaign\CampaignBeneficiaryController;
use App\Http\Controllers\Donation_Type\Campaign\CampaignController;
use App\Http\Controllers\Donation_Type\Campaign\CampaignFilterController;
use App\Http\Controllers\Donation_Type\Campaign\CampaignVolunteerController;
use App\Http\Controllers\Donation_Type\HumanCase\HumanCaseController;
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



Route::middleware(['auth:sanctum', 'checkLanguage'])->group(function () {
    Route::post('/addVolunteerRequest', [VolunteerRequestController::class, 'addVolunteerRequest']);
    Route::get('/getMyVolunteerRequests', [VolunteerRequestController::class, 'getMyVolunteerRequests']);
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

Route::prefix('category')->group(function () {
    Route::post('/add',[CategoryController::class,'addCategory']);
    Route::get('/getAll',[CategoryController::class,'getAllCategories']);
    Route::get('/get/{categoryId}',[CategoryController::class,'getCategoryById']);
    Route::delete('delete/{categoryId}',[CategoryController::class,'deleteCategory']);
    Route::post('/update/{categoryId}',[CategoryController::class,'updateCategory']);
});

Route::prefix('campaigns')->group(function () {
    // Admin
    Route::post('/add', [CampaignController::class, 'addCampaign']);
    Route::get('/getAll', [CampaignController::class, 'getAllCampaigns']);
    Route::get('/get/{Id}', [CampaignController::class, 'getCampaignDetails']);
    Route::get('/category/{categoryId}', [CampaignController::class, 'getCampaignsByCategory']);
    Route::post('/update/{Id}', [CampaignController::class, 'updateCampaign']);
    Route::get('/byStatus', [CampaignController::class, 'getCampaignsByStatus']);
    Route::get('/byCreationDate', [CampaignController::class, 'getCampaignsByCreationDate']);
    Route::post('/activate/{Id}', [CampaignController::class, 'activateCampaign']);
    Route::post('/archive/{Id}', [CampaignController::class, 'archiveCampaign']);
    Route::get('/archivedCampaigns', [CampaignController::class, 'getArchivedCampaigns']);
    // not used yet
    Route::get('/filter/byDate', [CampaignFilterController::class, 'filterCampaignsByDate']);
    Route::get('/filter/byGoalAmount', [CampaignFilterController::class, 'filterCampaignsByGoalAmount']);
    Route::get('/filter/byBeneficiariesCount', [CampaignFilterController::class, 'filterCampaignsByBeneficiariesCount']);

    //campaign with beneficiary
    Route::post('/{campaignId}/addBeneficiaries', [CampaignBeneficiaryController::class, 'addBeneficiariesToCampaign']);
    Route::get('/{campaignId}/getBeneficiaries', [CampaignBeneficiaryController::class, 'getCampaignBeneficiaries']);
    Route::delete('/{campaignId}/deleteBeneficiaries/{beneficiaryId}', [CampaignBeneficiaryController::class, 'removeBeneficiaryFromCampaign']);
    //campaign with volunteer
    Route::post('/{campaignId}/addVolunteers', [CampaignVolunteerController::class, 'addVolunteersToCampaign']);
    Route::get('/{campaignId}/getVolunteers', [CampaignVolunteerController::class, 'getCampaignVolunteers']);
    Route::delete('/{campaignId}/deleteVolunteers/{volunteerId}', [CampaignVolunteerController::class, 'removeVolunteerFromCampaign']);
});
    Route::get('/beneficiary/{beneficiaryId}/campaigns', [BeneficiaryController::class, 'getBeneficiaryCampaigns']);
    Route::get('/beneficiary/{beneficiaryId}/humanCases', [BeneficiaryController::class, 'getBeneficiaryHumanCases']);
    Route::get('/volunteer/{volunteerId}/campaigns', [VolunteerController::class, 'getVolunteerCampaigns']);

Route::prefix('humanCase')->group(function () {
    // Admin
    Route::post('/add', [HumanCaseController::class, 'addHumanCase']);
    Route::get('/getAll', [HumanCaseController::class, 'getAllHumanCases']);
    Route::get('/get/{Id}', [HumanCaseController::class, 'getHumanCaseDetails']);
    Route::get('/category/{categoryId}', [HumanCaseController::class, 'getHumanCasesByCategory']);
    Route::post('/update/{Id}', [HumanCaseController::class, 'updateHumanCase']);
    Route::get('/byStatus', [HumanCaseController::class, 'getHumanCasesByStatus']);
    Route::get('/emergency', [HumanCaseController::class, 'getEmergencyHumanCases']);
    Route::post('/activate/{Id}', [HumanCaseController::class, 'activateHumanCase']);
    Route::post('/archive/{Id}', [HumanCaseController::class, 'archiveHumanCase']);
    Route::get('/archivedHumanCases', [HumanCaseController::class, 'getArchivedHumanCases']);
});

});
Route::prefix('campaigns')->group(function () {
    Route::get('/getAll/for/user', [CampaignController::class, 'getAllVisibleCampaignsForUser']);
    Route::get('/get/{Id}/for/user', [CampaignController::class, 'getVisibleCampaignByIdForUser']);
    Route::get('/category/{categoryId}/for/user', [CampaignController::class, 'getVisibleCampaignsByCategoryForUser']);
    Route::get('/archivedCampaigns/for/user', [CampaignController::class, 'getVisibleCampaignsByCategoryForUser']);
});

Route::prefix('humanCases')->group(function () {
    Route::get('/getAll/for/user', [HumanCaseController::class, 'getAllVisibleHumanCasesForUser']);
    Route::get('get/{Id}/for/user', [HumanCaseController::class, 'getVisibleHumanCaseByIdForUser']);
    Route::get('/category/{categoryId}/for/user', [HumanCaseController::class, 'getVisibleHumanCasesByCategoryForUser']);
    Route::get('/getAll/emergency/for/user', [HumanCaseController::class, 'getAllVisibleEmergencyHumanCasesForUser']);
    Route::get('/get/{Id}/emergency/for/user', [HumanCaseController::class, 'getVisibleEmergencyHumanCaseByIdForUser']);
    Route::get('/archivedVisibleHumanCases', [HumanCaseController::class, 'getArchivedHumanCases']);
});
