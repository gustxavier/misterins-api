<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPanelLinkHotmartToCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('panel_link_hotmart')->after('can_affiliate')->nullable();
            $table->string('panel_access_limit')->after('can_affiliate')->nullable();
            $table->string('support_limit')->after('can_affiliate')->nullable();
            $table->string('link_checkout')->after('can_affiliate')->nullable();
            $table->string('product_link_site')->after('can_affiliate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('panel_link_hotmart');
            $table->dropColumn('panel_access_limit');
            $table->dropColumn('support_limit');
            $table->dropColumn('link_checkout');
            $table->dropColumn('product_link_site');
        });
    }
}
