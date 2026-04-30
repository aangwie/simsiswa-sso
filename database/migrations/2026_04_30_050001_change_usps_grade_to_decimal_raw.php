<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Using raw SQL to avoid needing doctrine/dbal package
        DB::statement('ALTER TABLE usps MODIFY grade DECIMAL(5,2) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE usps MODIFY grade INT NULL');
    }
};
