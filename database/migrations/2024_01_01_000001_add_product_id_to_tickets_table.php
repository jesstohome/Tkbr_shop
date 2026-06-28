<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdToTicketsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('tickets', 'product_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->unsignedBigInteger('product_id')->nullable()->after('order_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('tickets', 'product_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('product_id');
            });
        }
    }
}
