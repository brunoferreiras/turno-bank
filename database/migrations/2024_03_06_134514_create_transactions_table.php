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
            id, ROUND(amount / 100, 2) as amount, description, account_id, 'income' AS type, created_at, updated_at
        FROM deposits
        WHERE status = 1

        UNION

        SELECT
            id, ROUND(amount / 100, 2) as amount, description, account_id, 'expense' AS type, created_at, updated_at
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
