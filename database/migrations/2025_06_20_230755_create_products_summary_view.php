<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE VIEW products_summary_view AS
            SELECT
                products.id,
                products.name,
                products.price,
                products.discount,
                products.stock,
                (products.price - COALESCE(products.discount, 0)) AS final_price,
                categories.name AS category_name
            FROM products
            LEFT JOIN categories ON categories.id = products.category_id
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS products_summary_view");
    }
};
