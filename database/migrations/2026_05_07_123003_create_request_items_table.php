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
        Schema::create('request_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('request_id')->constrained('requests')->cascadeOnDelete();
            $table->string('item_name');
            $table->string('category');
            $table->integer('quantity')->unsigned();
            $table->string('unit')->nullable();
            $table->decimal('estimated_price',15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('stock_checked')->default(false);
            $table->boolean('stock_available')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('request_id');
            $table->index('category');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_items');
    }
};
