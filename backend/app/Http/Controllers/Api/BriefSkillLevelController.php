<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BriefSkillLevel;
use Illuminate\Http\Request;

class BriefSkillLevelController extends Controller
{
    /**
     * Get brief skill levels by brief ID.
     */
    public function getBriefSkillLevelsByBriefId(int $briefId)
    {
        try {
            $briefSkillLevels = BriefSkillLevel::with(['skill', 'level'])
                ->where('brief_id', $briefId)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $briefSkillLevels,
                'message' => 'Successfully fetched brief skill levels.'
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => "Something went wrong. Please try again.",
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
