<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('editais', function (Blueprint $table) {
            $table->string('link_submissao', 1000)->nullable()->after('link_oficial');
        });
    }

    public function down(): void
    {
        Schema::table('editais', function (Blueprint $table) {
            $table->dropColumn('link_submissao');
        });
    }
};
