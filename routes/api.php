<?php

use App\Http\Controllers\admin\AdminAuthController;
use App\Http\Controllers\beneficiary\BeneficiaryController;
use App\Http\Controllers\beneficiary\BeneficiaryRequestController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Donation_Type\Campaign\CampaignBeneficiaryController;
use App\Http\Controllers\Donation_Type\Campaign\CampaignController;
use App\Http\Controllers\Donation_Type\Campaign\CampaignFilterController;
use App\Http\Controllers\Donation_Type\Campaign\CampaignVolunteerController;
use App\Http\Controllers\Donation_Type\HumanCase\HumanCaseController;
use App\Http\Controllers\Donation_Type\Sponsorship\PlanController;
use App\Http\Controllers\Donation_Type\Sponsorship\SponsorshipController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\user\PhoneAuthController;
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
        Route::post('/changePassword', [UserController::class, 'changePassword']);
    });
});

Route::prefix('otp')->group(function () {
Route::post('/otp/send-login', [PhoneAuthController::class, 'sendLoginOtp']);
Route::post('/otp/send-reset', [PhoneAuthController::class, 'sendPasswordResetOtp']);
Route::post('/otp/verify', [PhoneAuthController::class, 'verifyOtp']);
Route::post('/password/reset', [PhoneAuthController::class, 'resetPassword']);
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
    Route::get('/getFilterByStatus', [VolunteerRequestController::class, 'getVolunteerRequestsByStatusForAdmin']);
    Route::get('/getUnreadRequests', [VolunteerRequestController::class, 'getUnreadVolunteerRequests']);
    Route::post('/updateStatus/{id}', [VolunteerRequestController::class, 'updateVolunteerRequestStatus']);
});
Route::prefix('beneficiary_request')->group(function () {
    Route::post('/add', [BeneficiaryRequestController::class, 'addBeneficiaryRequest']);
    Route::get('/getAllUserRequests', [BeneficiaryRequestController::class, 'getAllUserBeneficiaryRequests']);
    Route::get('/getDetails/{id}', [BeneficiaryRequestController::class, 'getBeneficiaryRequestDetails']);
    Route::get('/getFilterByStatus', [BeneficiaryRequestController::class, 'getBeneficiaryRequestsByStatusForAdmin']);
    Route::get('/getUnreadRequests', [BeneficiaryRequestController::class, 'getUnreadBeneficiaryRequests']);
    Route::post('/updateStatus/{id}', [BeneficiaryRequestController::class, 'updateBeneficiaryRequestStatus']);
    Route::get('/getFilterByPriority', [BeneficiaryRequestController::class, 'getBeneficiaryRequestsByPriority']);
    Route::get('/getFilterByCategory', [BeneficiaryRequestController::class, 'getBeneficiaryRequestsByCategory']);
});
Route::prefix('category')->group(function () {
    Route::post('/add',[CategoryController::class,'addCategory']);
    Route::get('/getAll',[CategoryController::class,'getAllCategories']);
    Route::get('/get/{categoryId}',[CategoryController::class,'getCategoryById']);
    Route::delete('/delete/{categoryId}',[CategoryController::class,'deleteCategory']);
    Route::post('/update/{categoryId}',[CategoryController::class,'updateCategory']);
});
Route::prefix('campaigns')->group(function () {
    // Admin
    Route::post('/add', [CampaignController::class, 'addCampaign']);
    Route::get('/getAll', [CampaignController::class, 'getAllCampaigns']);
    Route::get('/get/{Id}', [CampaignController::class, 'getCampaignDetails']);
    Route::get('/category/{categoryId}', [CampaignController::class, 'getCampaignsByCategory']);
    Route::post('/update/{Id}', [CampaignController::class, 'updateCampaign']);
    Route::get('category/{categoryId}/status/{status}', [CampaignController::class, 'getCampaignsByStatus']);
    Route::get('byCreationDate', [CampaignController::class, 'getCampaignsByCreationDate']);
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
    Route::post('/update/{Id}', [HumanCaseController::class, 'updateHumanCase']);
    Route::post('/activeEmergency/{Id}', [HumanCaseController::class, 'activateEmergency']);
    Route::post('/activate/{Id}', [HumanCaseController::class, 'activateHumanCase']);
    Route::post('/archive/{Id}', [HumanCaseController::class, 'archiveHumanCase']);
    Route::get('/getAll', [HumanCaseController::class, 'getAllHumanCases']);
    Route::get('/get/{Id}', [HumanCaseController::class, 'getHumanCaseDetails']);
    Route::get('/category/{categoryId}', [HumanCaseController::class, 'getHumanCasesByCategory']);
    Route::get('/emergency', [HumanCaseController::class, 'getEmergencyHumanCases']);
    Route::get('/archivedHumanCases', [HumanCaseController::class, 'getArchivedHumanCases']);
    Route::get('/category/{categoryId}/byStatus/{status}', [HumanCaseController::class, 'getHumanCasesByStatus']);
    Route::get('byCreationDate', [HumanCaseController::class, 'getHumanCasesByCreationDate']);
});

Route::prefix('sponsorship')->group(function () {
        // Admin
    Route::post('/add', [SponsorshipController::class, 'addSponsorship']);
    Route::post('/update/{Id}', [SponsorshipController::class, 'updateSponsorship']);
    Route::post('/activate/{Id}', [SponsorshipController::class, 'activateSponsorship']);
    Route::post('/cancelled/{Id}', [SponsorshipController::class, 'cancelledSponsorship']);
    Route::get('/getAll', [SponsorshipController::class, 'getAllSponsorShips']);
    Route::get('/get/{Id}', [SponsorshipController::class, 'getSponsorshipDetails']);
    Route::get('/category/{categoryId}', [SponsorshipController::class, 'getSponsorshipsByCategory']);
    Route::get('category/{categoryId}/byStatus/{status}', [SponsorshipController::class, 'getSponsorShipsByStatus']);
    Route::get('byCreationDate', [SponsorshipController::class, 'getAllSponsorshipsByCreationDate']);
    Route::get('/getCancelled', [SponsorshipController::class, 'getCancelledSponsorships']);
    });

Route::prefix('plans')->group(function () {
    Route::post('/add', [PlanController::class,'createPlanForSponsorship']);
    Route::post('/deactivate/{Id}', [PlanController::class, 'deactivatePlan']);
    Route::post('/activate/{Id}', [PlanController::class, 'activatePlan']);
    Route::post('/cancelled/{Id}', [PlanController::class, 'cancelPlan']);
    Route::get('/getAll', [PlanController::class, 'getPlansForUser']);
    Route::get('/get/{Id}', [PlanController::class, 'getPlanDetails']);

});

});

// NO AUTH FOR USER LIKE GUEST
Route::prefix('category')->group(function () {
  //  Route::get('/getAll/',[CategoryController::class,'getAllCategoriesForUser']);
    Route::get('/{main_category}', [CategoryController::class, 'getAllCategoriesByMainCategory']);
    Route::get('/{categoryId}/for/user', [CategoryController::class, 'getCategoryByIdForUser']);
});
Route::prefix('campaigns')->group(function () {
    Route::get('{mainCategory}', [CampaignController::class, 'getAllVisibleCampaignsForUser']);
    Route::get('/{mainCategory}/show/{id}', [CampaignController::class, 'getVisibleCampaignByIdForUser']);
    Route::get('/{mainCategory}/category/{categoryId}', [CampaignController::class, 'getVisibleCampaignsByCategoryForUser']);
    Route::get('/{mainCategory}/archived/forUser', [CampaignController::class, 'getVisibleArchivedCampaigns']);
    Route::get('/{mainCategory}/byDate', [CampaignController::class, 'getVisibleCampaignsByCreationDate']);
});
Route::prefix('humanCases')->group(function () {
    Route::get('/{mainCategory}/getAll/for/user', [HumanCaseController::class, 'getAllVisibleHumanCasesForUser']);
    Route::get('/{mainCategory}/get/{id}/for/user', [HumanCaseController::class, 'getVisibleHumanCaseByIdForUser']);
    Route::get('/{mainCategory}/category/{categoryId}/for/user', [HumanCaseController::class, 'getVisibleHumanCasesByCategoryForUser']);
    Route::get('/{mainCategory}/getAll/emergency/for/user', [HumanCaseController::class, 'getAllVisibleEmergencyHumanCasesForUser']);
    Route::get('/{mainCategory}/get/{id}/emergency/for/user', [HumanCaseController::class, 'getVisibleEmergencyHumanCaseByIdForUser']);
    Route::get('/{mainCategory}/archived/for/user', [HumanCaseController::class, 'getVisibleArchivedHumanCases']);
});

Route::prefix('sponsorships')->group(function () {
    Route::get('/{mainCategory}', [SponsorshipController::class, 'getAllVisibleSponsorshipsForUsers']);
    Route::get('/{mainCategory}/show/{id}', [SponsorshipController::class, 'getVisibleSponsorshipDetailsForUser']);
    Route::get('/{mainCategory}/category/{categoryId}', [SponsorshipController::class, 'getVisibleSponsorshipsByCategoryForUsers']);

});

