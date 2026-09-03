<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBankAddressToShopsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('shops', 'bank_address')) {
            Schema::table('shops', function (Blueprint $table) {
                $table->string('bank_address', 500)->nullable()->after('bank_routing_no');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('shops', 'bank_address')) {
            Schema::table('shops', function (Blueprint $table) {
                $table->dropColumn('bank_address');
            });
        }
    }
}
