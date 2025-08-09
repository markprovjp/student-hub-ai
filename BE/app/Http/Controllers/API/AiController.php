<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AiController extends Controller
{
    /**
     * Process AI request
     * This is a placeholder for teams to implement their AI logic
     */
    public function process(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->input('message');
        $userId = $request->user()->id;

        // TODO: Teams should implement their actual AI logic here
        // This is just a placeholder response
        
        // You can analyze the user message and provide different responses
        $aiResponse = $this->generateAiResponse($userMessage);

        return response()->json([
            'result' => $aiResponse,
            'message' => 'AI response generated successfully',
            'user_id' => $userId
        ]);
    }

    /**
     * Generate AI response based on user input
     * Teams should replace this with their actual AI implementation
     */
    private function generateAiResponse($message)
    {
        // Convert message to lowercase for better matching
        $message = strtolower($message);

        // Sample responses based on common student queries
        $responses = [
            // Study-related queries
            'quy chế' => 'Quy chế thi cử của trường bao gồm: phải có mặt trước 15 phút, mang theo thẻ sinh viên, không được sử dụng tài liệu (trừ khi được phép), không gian lận. Vi phạm sẽ bị xử lý nghiêm túc theo quy định.',
            
            'thi cử' => 'Một số lưu ý quan trọng khi thi: chuẩn bị tâm lý tốt, ôn tập kỹ lưỡng, đến sớm, kiểm tra dụng cụ, đọc kỹ đề bài, phân bổ thời gian hợp lý, kiểm tra lại bài làm.',
            
            'học tập' => 'Để cải thiện kết quả học tập: lập kế hoạch học tập rõ ràng, tham gia đầy đủ các buổi học, chủ động đặt câu hỏi, làm bài tập thường xuyên, tạo nhóm học tập, và nghỉ ngơi đầy đủ.',
            
            'công nghệ thông tin' => 'Lộ trình học ngành CNTT: Năm 1-2 học nền tảng (lập trình, cơ sở dữ liệu, mạng máy tính), Năm 3 chuyên sâu (AI, bảo mật, phát triển web/mobile), Năm 4 thực tập và đồ án tốt nghiệp.',
            
            'thời gian' => 'Quản lý thời gian hiệu quả: sử dụng kỹ thuật Pomodoro (25 phút học, 5 phút nghỉ), lập danh sách công việc ưu tiên, tránh trì hoãn, cân bằng học tập và giải trí.',
            
            // Greetings
            'chào' => 'Xin chào! Tôi là trợ lý AI của Student Hub. Tôi có thể hỗ trợ bạn với các câu hỏi về học tập, quy chế trường, lộ trình học tập. Bạn cần hỗ trợ gì?',
            
            'hello' => 'Hello! I am the AI assistant of Student Hub. I can help you with study questions, school regulations, learning paths. What do you need help with?',
        ];

        // Find matching response
        foreach ($responses as $keyword => $response) {
            if (strpos($message, $keyword) !== false) {
                return $response;
            }
        }

        // Default response if no keyword matches
        $defaultResponses = [
            'Đây là một câu hỏi thú vị! Dựa trên kinh nghiệm của tôi, tôi khuyên bạn nên tham khảo thêm tài liệu từ thư viện trường hoặc hỏi trực tiếp giảng viên để có thông tin chính xác nhất.',
            
            'Cảm ơn bạn đã tin tưởng sử dụng Student Hub AI! Tôi đang liên tục học hỏi để cải thiện khả năng hỗ trợ. Bạn có thể chia sẻ thêm chi tiết để tôi hỗ trợ tốt hơn không?',
            
            'Đây là một chủ đề rất quan trọng trong đời sống sinh viên. Tôi khuyên bạn nên kết hợp nhiều nguồn thông tin và trải nghiệm thực tế để có cái nhìn toàn diện nhất.',
            
            'Student Hub AI đang phát triển để hỗ trợ sinh viên tốt hơn mỗi ngày. Câu hỏi của bạn sẽ giúp tôi học thêm nhiều điều mới. Bạn có thể thử đặt câu hỏi khác không?'
        ];

        // Return a random default response
        return $defaultResponses[array_rand($defaultResponses)];
    }
}
