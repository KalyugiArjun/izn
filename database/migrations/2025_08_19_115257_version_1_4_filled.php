<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->text('sms_message')->nullable();
            $table->integer('enabled_sms')->default(0);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('fabric_attachment')->nullable()->after('measurement');
            $table->string('sewing_pattern')->nullable()->after('fabric_attachment');
            $table->integer('invoice')->nullable()->after('notes');

        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('employee_limit')->default(0)->after('customer_limit');
        });

        Schema::table('invoice_payments', function (Blueprint $table) {
            $table->text('invoice_transactions_id')->nullable()->after('notes');
            $table->string('payment_status')->nullable()->after('invoice_transactions_id');
            $table->string('receipt')->nullable()->after('payment_status');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->integer('category_id')->nullable()->after('parent_id');
            $table->integer('sub_category_id')->nullable()->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('sms_message');
            $table->dropColumn('enabled_sms');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('fabric_attachment');
            $table->dropColumn('sewing_pattern');
            $table->dropColumn('invoice');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('employee_limit');
        });

        Schema::table('invoice_payments', function (Blueprint $table) {
            $table->dropColumn('invoice_transactions_id');
            $table->dropColumn('payment_status');
            $table->dropColumn('receipt');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('category_id');
            $table->dropColumn('sub_category_id');
        });
    }
};
