<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $createCommand = app()->environment('testing') ? 'CREATE' : 'CREATE OR REPLACE';
        DB::statement("{$createCommand} VIEW transactions AS
        SELECT
            id, amount, description, user_id, 'income' AS type, created_at, updated_at
        FROM deposits

        UNION

        SELECT
            id, amount, description, user_id, 'expense' AS type, created_at, updated_at
        FROM purchases;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW transactions");
    }
};
