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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->unique(); // e.g., AST-2026-0001
            $table->string('name');
            $table->string('category'); // e.g., Electronics, Furniture, Vehicle, Machinery, Tools
            $table->string('location'); // e.g., Room 301, Lab A, Warehouse
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->date('purchase_date');
            $table->enum('status', ['available', 'borrowed', 'broken'])->default('available');
            $table->string('borrowed_by')->nullable();
            $table->timestamp('borrowed_at')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes(); // [AT-101] Soft deletes for financial reporting integrity
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
