<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ConsultationResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Get user profile with statistics
     */
    public function getProfile(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not authenticated'
                ], 401);
            }

            // Calculate user statistics
            $totalConsultations = ConsultationResult::where('user_id', $user->id)->count();
            $thisMonthConsultations = ConsultationResult::where('user_id', $user->id)
                ->where('created_at', '>=', now()->startOfMonth())
                ->count();
            
            $avgConfidence = ConsultationResult::where('user_id', $user->id)
                ->avg('confidence_score') ?? 0;

            // Recent consultations
            $recentConsultations = ConsultationResult::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Calculate days since joining
            $daysSinceJoining = $user->created_at->diffInDays(now());
            Log::info($daysSinceJoining);
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'statistics' => [
                        'total_consultations' => $totalConsultations,
                        'this_month_consultations' => $thisMonthConsultations,
                        'average_confidence' => round($avgConfidence, 1),
                        'days_since_joining' => $daysSinceJoining
                    ],
                    'recent_consultations' => $recentConsultations
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Không thể tải thông tin hồ sơ',
                'details' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get dashboard statistics for homepage
     */
    public function getDashboardStats(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not authenticated'
                ], 401);
            }

            // User's personal statistics
            $userConsultations = ConsultationResult::where('user_id', $user->id)->count();
            $thisWeekConsultations = ConsultationResult::where('user_id', $user->id)
                ->where('created_at', '>=', now()->startOfWeek())
                ->count();
            
            $avgConfidence = ConsultationResult::where('user_id', $user->id)
                ->avg('confidence_score') ?? 0;

            // Recent activity
            $recentActivity = ConsultationResult::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($consultation) {
                    $inputData = $consultation->input_data ?? [];
                    $interests = $inputData['interests'] ?? [];
                    
                    return [
                        'id' => $consultation->id,
                        'activity' => 'Đã thực hiện tư vấn chọn ngành',
                        'description' => 'Sở thích: ' . implode(', ', array_slice($interests, 0, 2)) . 
                                       (count($interests) > 2 ? '...' : ''),
                        'time' => $consultation->created_at->diffForHumans(),
                        'created_at' => $consultation->created_at
                    ];
                });

            // Global platform statistics (for motivation)
            $platformStats = [
                'total_users' => User::count(),
                'total_consultations' => ConsultationResult::count(),
                'active_users_today' => ConsultationResult::whereDate('created_at', today())
                    ->distinct('user_id')
                    ->count()
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'user_stats' => [
                        'total_consultations' => $userConsultations,
                        'this_week_consultations' => $thisWeekConsultations,
                        'average_confidence' => round($avgConfidence, 1),
                        'join_days' => $user->created_at->diffInDays(now())
                    ],
                    'recent_activity' => $recentActivity,
                    'platform_stats' => $platformStats
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Không thể tải thống kê dashboard',
                'details' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not authenticated'
                ], 401);
            }

            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            ]);

            $user->update($request->only(['name', 'email']));

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật hồ sơ thành công',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Không thể cập nhật hồ sơ',
                'details' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }
}
