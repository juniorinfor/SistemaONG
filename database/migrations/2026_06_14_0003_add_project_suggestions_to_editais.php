<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('editais', function (Blueprint $table) {
            $table->json('project_suggestions')->nullable()->after('compatibility_details');
            $table->timestamp('suggestions_at')->nullable()->after('project_suggestions');
        });
    }

    public function down(): void
    {
        Schema::table('editais', function (Blueprint $table) {
            $table->dropColumn(['project_suggestions', 'suggestions_at']);
        });
    }
};
