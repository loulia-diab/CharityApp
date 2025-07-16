<?php

namespace App\Http\Controllers\Donation_Type\Sponsorship;

use App\Enums\CampaignStatus;
use App\Http\Controllers\Controller;
use App\Models\Campaigns\Campaign;
use App\Models\Sponsorship;
use Illuminate\Http\Request;

class SponsorshipController extends Controller
{
    // Admin
    public function addSponsorship(Request $request)
    {
        $request->validate([
            'beneficiary_id' => 'required|exists:beneficiaries,id',
            'sponsorship_name_en' => 'required|string|max:255',
            'sponsorship_name_ar' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'monthly_amount' => 'nullable|numeric|min:0',
            'image' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json([
                'message' => 'Unauthorized',
                'status' => 401
            ], 401);
        }

        try {
            $campaign = Campaign::create([
                'title_en' => $request->sponsorship_name_en,
                'title_ar' => $request->sponsorship_name_ar,
                'description_en' => $request->description_en ?? '',
                'description_ar' => $request->description_ar ?? '',
                'category_id' => $request->category_id,
                'goal_amount' => $request->monthly_amount ?? 0,
                'collected_amount' => 0,
                'start_date' => $request->start_date ?? now(),
                'end_date' => $request->end_date,
                'status' => CampaignStatus::Active,
                'image' => $request->image ?? '',
                'admin_id' => $admin->id,
            ]);

            $sponsorship = Sponsorship::create([
                'campaign_id' => $campaign->id,
                'beneficiary_id' => $request->beneficiary_id,
            ]);

            // تحميل علاقة المستفيد عشان ترجع بالرد
            $sponsorship->load('beneficiary');

            return response()->json([
                'message' => 'Sponsorship created successfully',
                'data' => [
                    'sponsorship_id' => $sponsorship->id,
                    'campaign_id' => $campaign->id,
                    'title_en' => $campaign->title_en,
                    'title_ar' => $campaign->title_ar,
                    'start_date' => $campaign->start_date,
                    'end_date' => $campaign->end_date,
                    'status' => $campaign->status,
                    'beneficiary' => $sponsorship->beneficiary,
                ],
                'status' => 201
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating sponsorship',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function getSponsorships(Request $request)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => 'Unauthorized - Admin access only',
            ], 401);
        }

        try {
            $perPage = $request->get('per_page', 10);

            $sponsorships = Sponsorship::with(['campaign', 'beneficiary'])
                ->latest()
                ->paginate($perPage);

            $data = $sponsorships->map(function ($sponsorship) {
                $campaign = $sponsorship->campaign;
                return [
                    'id' => $sponsorship->id,
                    'beneficiary_id' => $sponsorship->beneficiary_id,
                    'beneficiary' => $sponsorship->beneficiary,
                    'campaign_id' => $campaign?->id,
                    'title_en' => $campaign?->title_en,
                    'title_ar' => $campaign?->title_ar,
                    'description_en' => $campaign?->description_en,
                    'description_ar' => $campaign?->description_ar,
                    'category_id' => $campaign?->category_id,
                    'monthly_amount' => $campaign?->goal_amount,
                    'collected_amount' => $campaign?->collected_amount,
                    'start_date' => $campaign?->start_date,
                    'end_date' => $campaign?->end_date,
                    'status' => $campaign?->status,
                    'image' => $campaign?->image,
                ];
            });

            return response()->json([
                'message' => 'Sponsorships fetched successfully',
                'data' => $data,
                'pagination' => [
                    'current_page' => $sponsorships->currentPage(),
                    'last_page' => $sponsorships->lastPage(),
                    'per_page' => $sponsorships->perPage(),
                    'total' => $sponsorships->total(),
                ],
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching sponsorships',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    public function getSponsorshipDetails($id)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json([
                'message' => 'Unauthorized - Admin access only',
            ], 401);
        }

        try {
            $sponsorship = Sponsorship::with(['campaign', 'beneficiary'])->findOrFail($id);
            $campaign = $sponsorship->campaign;

            return response()->json([
                'message' => 'Sponsorship fetched successfully',
                'data' => [
                    'id' => $sponsorship->id,
                    'beneficiary_id' => $sponsorship->beneficiary_id,
                    'beneficiary' => $sponsorship->beneficiary,
                    'campaign_id' => $campaign?->id,
                    'title_en' => $campaign?->title_en,
                    'title_ar' => $campaign?->title_ar,
                    'description_en' => $campaign?->description_en,
                    'description_ar' => $campaign?->description_ar,
                    'category_id' => $campaign?->category_id,
                    'goal_amount' => $campaign?->goal_amount,
                    'collected_amount' => $campaign?->collected_amount,
                    'start_date' => $campaign?->start_date,
                    'end_date' => $campaign?->end_date,
                    'status' => $campaign?->status,
                    'image' => $campaign?->image,
                ],
                'status' => 200
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Sponsorship not found',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching sponsorship',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
    // get By Category

    // update

    // activate

    public function archiveSponsorship(Request $request, $id)
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $sponsorship = Sponsorship::findOrFail($id);
            $campaign = $sponsorship->campaign;

            if (!$campaign) {
                return response()->json(['message' => 'Associated campaign not found'], 404);
            }

            $campaign->status = \App\Enums\CampaignStatus::Archived;
            $campaign->save();

            return response()->json([
                'message' => 'Sponsorship archived successfully',
                'data' => $sponsorship,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error archiving sponsorship',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }


    // User
    public function getAllVisibleSponsorshipsForUsers(Request $request)
    {
        try {

            $sponsorships = Sponsorship::with('campaign', 'beneficiary')
                ->whereHas('campaign', function ($query) {
                    $query->whereColumn('collected_amount', '<', 'goal_amount')
                        ->where('status', CampaignStatus::Active);
                })
                ->latest()
              ->get();

            $data = $sponsorships->map(function ($sponsorship) {
                $campaign = $sponsorship->campaign;
                return [
                    'id' => $sponsorship->id,
                    'beneficiary_id' => $sponsorship->beneficiary_id,
                    // name
                    'beneficiary' => $sponsorship->beneficiary,
                    'campaign_id' => $campaign?->id,
                    'title_en' => $campaign?->title_en,
                    'title_ar' => $campaign?->title_ar,
                    'description_en' => $campaign?->description_en,
                    'description_ar' => $campaign?->description_ar,
                    'category_id' => $campaign?->category_id,
                    'monthly_amount' => $campaign?->goal_amount,
                   //remaining amount
                    'image' => $campaign?->image,
                ];
            });

            return response()->json([
                'message' => 'Visible sponsorships fetched successfully',
                'data' => $data,
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching visible sponsorships',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }
    public function getVisibleSponsorshipForUser($id)
    {
        try {
            $sponsorship = Sponsorship::with('campaign', 'beneficiary')
                ->where('id', $id)
                ->whereHas('campaign', function ($query) {
                    $query->whereColumn('collected_amount', '<', 'goal_amount')
                        ->where('status', CampaignStatus::Active);
                })
                ->firstOrFail();

            $campaign = $sponsorship->campaign;

            return response()->json([
                'message' => 'Visible sponsorship fetched successfully',
                'data' => [
                    'id' => $sponsorship->id,
                    'beneficiary_id' => $sponsorship->beneficiary_id,
                    'beneficiary' => $sponsorship->beneficiary,
                    'campaign_id' => $campaign?->id,
                    'title_en' => $campaign?->title_en,
                    'title_ar' => $campaign?->title_ar,
                    'description_en' => $campaign?->description_en,
                    'description_ar' => $campaign?->description_ar,
                    'category_id' => $campaign?->category_id,
                    'monthly_amount' => $campaign?->goal_amount,
                    //remaining
                    'start_date' => $campaign?->start_date,
                    'end_date' => $campaign?->end_date,
                    'image' => $campaign?->image,
                ],
                'status' => 200,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Visible sponsorship not found',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching visible sponsorship',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    // CATEGORY



// Admin & User
    public function getArchivedSponsorships(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        try {
            $sponsorships = Sponsorship::with('campaign')
                ->whereHas('campaign', function ($query) {
                    $query->where('status', \App\Enums\CampaignStatus::Archived);
                })
                ->latest()
                ->paginate($perPage);

            $data = $sponsorships->map(function ($sponsorship) {
                $campaign = $sponsorship->campaign;
                return [
                    'id' => $sponsorship->id,
                    'beneficiary_id' => $sponsorship->beneficiary_id,
                    'title_en' => $campaign?->title_en,
                    'title_ar' => $campaign?->title_ar,
                    'description_en' => $campaign?->description_en,
                    'description_ar' => $campaign?->description_ar,
                    'category_id' => $campaign?->category_id,
                    'goal_amount' => $campaign?->goal_amount,
                    'collected_amount' => $campaign?->collected_amount,
                    'start_date' => $campaign?->start_date,
                    'end_date' => $campaign?->end_date,
                    'status' => $campaign?->status,
                    'image' => $campaign?->image,
                ];
            });

            return response()->json([
                'message' => 'Archived sponsorships fetched successfully',
                'data' => $data,
                'pagination' => [
                    'current_page' => $sponsorships->currentPage(),
                    'last_page' => $sponsorships->lastPage(),
                    'per_page' => $sponsorships->perPage(),
                    'total' => $sponsorships->total(),
                ],
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching archived sponsorships',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }


}
