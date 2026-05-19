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
        Schema::create('stocks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('item_name');
            $table->string('category');
            $table->integer('quantity')->unsigned()->default(0);
            $table->string('unit');
            $table->string('location')->nullable();
            $table->integer('minimum_stock')->unsigned()->default(0);
            $table->foreignUuid('request_item_id')->nullable()->constrained('request_items')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('item_name');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
