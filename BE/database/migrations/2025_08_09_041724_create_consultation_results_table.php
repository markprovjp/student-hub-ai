<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('consultation_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('input_data'); // Lưu thông tin khảo sát
            $table->text('ai_result'); // Kết quả tư vấn từ AI
            $table->json('recommended_majors')->nullable(); // Danh sách ngành đề xuất
            $table->json('study_suggestions')->nullable(); // Gợi ý môn học/kỹ năng
            $table->float('confidence_score')->nullable(); // Độ tin cậy của tư vấn (0-1)
            $table->string('session_id')->nullable(); // ID phiên tư vấn
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_results');
    }
};
