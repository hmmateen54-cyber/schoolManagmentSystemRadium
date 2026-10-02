<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('section', 50)->default('');
            $table->foreignId('teacher_id')->nullable()->unique()->constrained('teachers')->nullOnDelete();
            $table->timestamps();

            $table->unique(['name', 'section']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->restrictOnDelete();
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('student')->index();
        });

        $legacyClassNames = DB::table('students')
            ->select('class')
            ->whereNotNull('class')
            ->distinct()
            ->orderBy('class')
            ->pluck('class');

        foreach ($legacyClassNames as $legacyClassName) {
            if (trim($legacyClassName) === '') {
                continue;
            }

            $now = now();
            $classId = DB::table('classes')->insertGetId([
                'name' => $legacyClassName,
                'section' => '',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('students')
                ->where('class', $legacyClassName)
                ->update(['class_id' => $classId]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn('role');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id']);
            $table->dropForeign(['class_id']);
            $table->dropColumn(['user_id', 'class_id']);
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::dropIfExists('classes');
    }
};
