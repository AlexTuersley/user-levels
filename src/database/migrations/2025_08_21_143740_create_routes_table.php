<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('route');
            $table->string('action');
            $table->timestamps();
            $table->index(['route', 'action']);
            $table->unique(['route', 'action']);
        });

        Schema::create('routables', function (Blueprint $table) {
            $table->foreignId('route_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->morphs('routable');
        });

        Artisan::call('db:seed', [ '--class' => 'RouteSeeder', '--force' => true ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
        Schema::dropIfExists('routables');
    }
};
