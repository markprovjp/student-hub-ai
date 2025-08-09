<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ConsultationResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Get consultation statistics for admin dashboard
     */
    public function getConsultationStatistics(Request $request)
    {
        try {
            // Basic statistics
            $totalConsultations = ConsultationResult::count();
            $totalUsers = User::count();
            $thisWeekConsultations = ConsultationResult::where('created_at', '>=', now()->startOfWeek())->count();
            $thisMonthConsultations = ConsultationResult::where('created_at', '>=', now()->startOfMonth())->count();
            
            // Popular majors statistics
            $popularMajors = ConsultationResult::select('recommended_majors')
                ->whereNotNull('recommended_majors')
                ->get()
                ->flatMap(function ($consultation) {
                    return json_decode($consultation->recommended_majors, true) ?? [];
                })
                ->countBy()
                ->sortDesc()
                ->take(10)
                ->map(function ($count, $major) {
                    return [
                        'major' => $major,
                        'count' => $count
                    ];
                })
                ->values();

            // Consultation trends by day (last 30 days)
            $consultationTrends = ConsultationResult::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Average confidence score
            $avgConfidence = ConsultationResult::avg('confidence_score') ?? 0;

            // User activity
            $activeUsers = ConsultationResult::select('user_id')
                ->where('created_at', '>=', now()->subDays(30))
                ->distinct()
                ->count();

            // Interest categories statistics
            $interestStats = ConsultationResult::select('input_data')
                ->get()
                ->flatMap(function ($consultation) {
                    $inputData = json_decode($consultation->input_data, true);
                    return $inputData['interests'] ?? [];
                })
                ->countBy()
                ->sortDesc()
                ->take(15);

            return response()->json([
                'success' => true,
                'data' => [
                    'overview' => [
                        'total_consultations' => $totalConsultations,
                        'total_users' => $totalUsers,
                        'this_week_consultations' => $thisWeekConsultations,
                        'this_month_consultations' => $thisMonthConsultations,
                        'active_users_30_days' => $activeUsers,
                        'average_confidence' => round($avgConfidence, 2)
                    ],
                    'popular_majors' => $popularMajors,
                    'consultation_trends' => $consultationTrends,
                    'interest_statistics' => $interestStats
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Không thể tải thống kê',
                'details' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get detailed consultation list for admin
     */
    public function getConsultationList(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 20);
            $search = $request->get('search');

            $query = ConsultationResult::with('user')
                ->orderBy('created_at', 'desc');

            if ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $consultations = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $consultations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Không thể tải danh sách tư vấn',
                'details' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Update AI training data (placeholder for future implementation)
     */
    public function updateTrainingData(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
            'type' => 'required|string|in:majors,faq,guidelines'
        ]);

        // TODO: Implement training data update logic
        // This could involve:
        // 1. Storing training data in database
        // 2. Updating AI model prompts
        // 3. Versioning training datasets

        return response()->json([
            'success' => true,
            'message' => 'Dữ liệu training đã được cập nhật (placeholder)'
        ]);
    }

    /**
     * Export consultation data to CSV
     */
    public function exportConsultations(Request $request)
    {
        try {
            $consultations = ConsultationResult::with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            $csvData = [];
            $csvData[] = [
                'ID', 'User', 'Email', 'Created At', 'Recommended Majors', 
                'Confidence Score', 'Interests', 'Skills', 'Career Goal'
            ];

            foreach ($consultations as $consultation) {
                $inputData = json_decode($consultation->input_data, true) ?? [];
                $recommendedMajors = json_decode($consultation->recommended_majors, true) ?? [];
                
                $csvData[] = [
                    $consultation->id,
                    $consultation->user->name ?? 'N/A',
                    $consultation->user->email ?? 'N/A',
                    $consultation->created_at->format('Y-m-d H:i:s'),
                    implode(', ', $recommendedMajors),
                    $consultation->confidence_score,
                    implode(', ', $inputData['interests'] ?? []),
                    implode(', ', $inputData['skills'] ?? []),
                    $inputData['careerGoal'] ?? ''
                ];
            }

            $filename = 'consultations_' . now()->format('Y-m-d_H-i-s') . '.csv';
            
            return response()->json([
                'success' => true,
                'data' => $csvData,
                'filename' => $filename
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Không thể export dữ liệu',
                'details' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }
}
