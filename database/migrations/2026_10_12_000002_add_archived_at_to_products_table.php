<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Products are never hard-deleted: sale_items holds a foreign key to
     * products, so deleting a sold product would break history (or 500).
     * Archiving hides the row from lists while keeping every past record.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->after('image_path');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });
    }
};
