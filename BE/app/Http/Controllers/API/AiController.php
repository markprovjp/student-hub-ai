<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    /**
     * Process AI request using Google Gemini AI
     */
    public function process(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $userMessage = $request->input('message');
        $userId = $request->user()->id;

        try {
            // Call Gemini AI API with user context
            $aiResponse = $this->callGeminiAI($userMessage, $userId);

            return response()->json([
                'result' => $aiResponse,
                'message' => 'AI response generated successfully',
                'user_id' => $userId,
                'timestamp' => now()->toISOString(),
                'powered_by' => 'Google Gemini AI'
            ]);

        } catch (\Exception $e) {
            Log::error('AI Processing Error: ' . $e->getMessage(), [
                'user_id' => $userId,
                'message' => $userMessage,
                'error' => $e->getTraceAsString()
            ]);
            
            // Fallback to local response if Gemini fails
            $fallbackResponse = $this->generateLocalResponse($userMessage);
            
            return response()->json([
                'result' => $fallbackResponse,
                'message' => 'AI response generated (fallback mode)',
                'user_id' => $userId,
                'timestamp' => now()->toISOString(),
                'fallback' => true,
                'error' => 'Gemini AI temporarily unavailable'
            ]);
        }
    }

    /**
     * Call Google Gemini AI API với system prompt chuyên biệt cho sinh viên
     */
    private function callGeminiAI($message, $userId = null)
    {
        $apiKey = env('GEMINI_API_KEY');
        
        if (!$apiKey) {
            throw new \Exception('Gemini API key not configured');
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey;

        // System prompt chuyên biệt cho Student Hub AI
        $systemPrompt = "Bạn là STUDENT HUB AI - trợ lý thông minh chuyên biệt hỗ trợ sinh viên 24/7.

🎯 **VAI TRÒ CỦA BẠN:**
- Tư vấn viên học tập chuyên nghiệp
- Chuyên gia về quy chế trường đại học
- Người định hướng lộ trình học tập cá nhân hóa
- Hỗ trợ kỹ thuật lập trình & công nghệ

📚 **CHUYÊN MÔN CHÍNH:**
1. **Quy chế & Thủ tục:** Quy định thi cử, học vụ, điều kiện tốt nghiệp
2. **Lộ trình học tập:** Tư vấn chọn môn, chuyên ngành dựa trên năng lực
3. **Phương pháp học:** Kỹ thuật học hiệu quả, quản lý thời gian
4. **Công nghệ:** Lập trình, AI, Data Science, Web Development
5. **Kỹ năng mềm:** Giao tiếp, thuyết trình, làm việc nhóm
6. **Nghề nghiệp:** Định hướng career, CV, phỏng vấn

🌟 **ĐẶC ĐIỂM GIAO TIẾP:**
- Thân thiện, nhiệt tình như anh/chị mentor
- Sử dụng emoji phù hợp để tạo không khí tích cực  
- Đưa ra lời khuyên cụ thể, thực tế
- Luôn động viên và khích lệ tinh thần học tập
- Cá nhân hóa câu trả lời theo từng sinh viên

💡 **NGUYÊN TẮC:**
- Trả lời bằng tiếng Việt tự nhiên, dễ hiểu
- Cung cấp thông tin chính xác, cập nhật
- Khuyến khích tư duy phản biện
- Hướng dẫn từng bước cụ thể
- Luôn hỏi thêm để hiểu rõ nhu cầu sinh viên";

        // Construct chat history with system prompt
        $chatHistory = [
            [
                'role' => 'user',
                'parts' => [['text' => $systemPrompt]]
            ],
            [
                'role' => 'model', 
                'parts' => [['text' => "Chào bạn! Mình là Student Hub AI - trợ lý học tập thông minh của bạn! 🎓✨\n\nMình có thể hỗ trợ bạn với mọi vấn đề về học tập, từ quy chế trường đến lộ trình nghề nghiệp. Bạn cần tư vấn về điều gì hôm nay?"]]
            ],
            [
                'role' => 'user',
                'parts' => [['text' => $message]]
            ]
        ];

        $response = Http::withoutVerifying()->timeout(30)->post($url, [
            'contents' => $chatHistory,
            'generationConfig' => [
                'temperature' => 0.8,  // Tăng creativity
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,  // Tăng độ dài response
                'candidateCount' => 1,
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_HATE_SPEECH', 
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ]
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'];
                
                // Log successful AI interaction
                Log::info('Gemini AI Success', [
                    'user_id' => $userId,
                    'message_length' => strlen($message),
                    'response_length' => strlen($aiResponse)
                ]);
                
                return $aiResponse;
            }
            
            throw new \Exception('Invalid response format from Gemini AI');
        }
        
        throw new \Exception('Failed to get response from Gemini AI: ' . $response->status() . ' - ' . $response->body());
    }

    /**
     * Generate fallback response when Gemini AI is unavailable
     */
    private function generateLocalResponse($message)
    {
        $message = strtolower($message);

        // Enhanced responses based on common student queries
        $responses = [
            // Study-related queries
            'quy chế' => '📋 **Quy chế thi cử quan trọng:**
• Có mặt trước 15 phút, mang thẻ sinh viên
• Không sử dụng tài liệu (trừ khi được phép)
• Tuyệt đối không gian lận
• Vi phạm sẽ bị xử lý nghiêm túc theo quy định

*Bạn cần thông tin chi tiết gì về quy chế không?* 🤔',

            'thi cử' => '📚 **Tips thi cử thành công:**
✅ Chuẩn bị tâm lý tốt, tự tin
✅ Ôn tập kỹ lưỡng theo đề cương
✅ Đến sớm 15-30 phút
✅ Kiểm tra dụng cụ cần thiết
✅ Đọc kỹ đề bài trước khi làm
✅ Phân bổ thời gian hợp lý
✅ Kiểm tra lại bài trước khi nộp

*Chúc bạn thi tốt!* 💪',

            'học tập' => '🎯 **Cải thiện kết quả học tập:**
📅 Lập kế hoạch học tập rõ ràng
👨‍🏫 Tham gia đầy đủ các buổi học
❓ Chủ động đặt câu hỏi với giảng viên
📝 Làm bài tập thường xuyên
👥 Tạo nhóm học tập cùng bạn
😴 Nghỉ ngơi đầy đủ, tránh thức khuya
🏃‍♂️ Tập thể dục để tăng sức khỏe não bộ

*Bạn gặp khó khăn ở môn nào cụ thể không?* 🤗',

            'công nghệ thông tin' => '💻 **Lộ trình học CNTT:**
**Năm 1-2: Nền tảng**
• Lập trình (C++, Java, Python)
• Cơ sở dữ liệu (MySQL, MongoDB)
• Mạng máy tính & Bảo mật cơ bản

**Năm 3: Chuyên sâu**
• AI/Machine Learning
• Phát triển Web (React, Laravel)
• Mobile App (Flutter, React Native)
• DevOps & Cloud Computing

**Năm 4: Thực chiến**
• Thực tập doanh nghiệp
• Đồ án tốt nghiệp
• Chuẩn bị nghề nghiệp

*Bạn quan tâm chuyên ngành nào nhất?* 🚀',

            'thời gian' => '⏰ **Quản lý thời gian siêu hiệu quả:**
🍅 **Pomodoro Technique:** 25 phút học + 5 phút nghỉ
📝 **To-do List:** Ưu tiên công việc quan trọng
🎯 **SMART Goals:** Mục tiêu cụ thể, đo lường được
📵 **Digital Detox:** Tắt thông báo khi học
⚖️ **Work-Life Balance:** Cân bằng học tập và giải trí
🌅 **Morning Routine:** Dậy sớm để có thời gian cho bản thân

*Bạn đang gặp khó khăn gì trong việc quản lý thời gian?* ⏳',

            // AI and Technology
            'trí tuệ nhân tạo' => '🤖 **AI - Tương lai của công nghệ:**
**Cơ bản cần học:**
• Python Programming
• Machine Learning (Scikit-learn)
• Deep Learning (TensorFlow, PyTorch)
• Data Science & Analytics
• Computer Vision & NLP

**Dự án thực hành:**
• Chatbot thông minh
• Nhận dạng hình ảnh
• Dự đoán giá cổ phiếu
• Hệ thống gợi ý

*Bạn muốn bắt đầu từ đâu với AI?* 🧠',

            // Greetings
            'chào' => '👋 **Xin chào bạn!**
Mình là Student Hub AI - trợ lý thông minh dành riêng cho sinh viên! 

🎓 Mình có thể giúp bạn với:
• Tư vấn học tập & quy chế trường
• Lộ trình phát triển nghề nghiệp  
• Hỗ trợ kỹ thuật lập trình
• Tips quản lý thời gian hiệu quả

*Bạn cần hỗ trợ gì hôm nay?* ✨',

            'hello' => '🌟 **Hello there!**
I\'m Student Hub AI - your smart academic assistant!

📚 I can help you with:
• Study tips & academic guidance
• University regulations & procedures
• Career development roadmap
• Technical programming support
• Time management strategies

*What can I help you with today?* 🚀',
        ];

        // Find matching response
        foreach ($responses as $keyword => $response) {
            if (strpos($message, $keyword) !== false) {
                return $response;
            }
        }

        // Enhanced default responses
        $defaultResponses = [
            '🤔 **Câu hỏi thú vị!** Để được hỗ trợ tốt nhất, bạn có thể:
• Tham khảo thư viện trường với tài liệu chuyên sâu
• Hỏi trực tiếp giảng viên để có thông tin chính xác nhất
• Tham gia nhóm học tập với các bạn cùng lớp

*Bạn có thể chia sẻ thêm chi tiết để mình hỗ trợ tốt hơn không?* 📚',

            '🌟 **Cảm ơn bạn đã tin tưởng Student Hub AI!**
Mình đang liên tục học hỏi để cải thiện khả năng hỗ trợ sinh viên.

💡 **Gợi ý:** Hãy thử hỏi về:
• "Cách học hiệu quả"
• "Quy chế thi cử" 
• "Lộ trình CNTT"
• "Quản lý thời gian"

*Có điều gì khác mình có thể giúp bạn?* 🎯',

            '📈 **Chủ đề rất quan trọng trong đời sinh viên!**
Mình khuyên bạn nên:
• Kết hợp nhiều nguồn thông tin đáng tin cậy
• Trải nghiệm thực tế qua thực tập, dự án
• Tham khảo ý kiến từ các anh chị đi trước
• Không ngại thử nghiệm và học hỏi từ thất bại

*Bạn có muốn mình tư vấn cụ thể về vấn đề nào không?* 💪',

            '🚀 **Student Hub AI luôn sẵn sàng hỗ trợ!**
Dù câu hỏi này hơi mới với mình, nhưng mình tin rằng:
• Mỗi thắc mắc đều có giá trị học tập
• Sự tò mò là động lực phát triển
• Học hỏi là hành trình không có điểm dừng

*Hãy thử đặt câu hỏi theo cách khác, mình sẽ cố gắng hết sức!* ✨'
        ];

        // Return a random enhanced default response
        return $defaultResponses[array_rand($defaultResponses)];
    }
}
