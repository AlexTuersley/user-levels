<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('user_levels', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('level_name', 20);
            $table->timestamps();
        });

        if(!Schema::hasColumn('users', 'user_level_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('user_level_id')
                    ->nullable()
                    ->constrained('user_levels')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            });
        }

        Artisan::call('db:seed', [ '--class' => 'UserLevelSeeder', '--force' => true ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_levels');
         if(Schema::hasColumn('users', 'user_level_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign('user_level_id');
            });
        }
    }
};
