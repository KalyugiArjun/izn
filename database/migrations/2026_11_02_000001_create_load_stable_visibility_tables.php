<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables to manage load stable visibility mappings.
     * 7 sections:
     * 1. New Entry
     * 2. New Entry Forwarded To
     * 3. New Entry Finaliz By
     * 4. Load Clearance Forward By SM To
     * 5. Load Clearance Entry By
     * 6. Load Clearance Forward By Pointsman To
     * 7. Load Clearance Finaliz By
     */
    protected array $tables = [
        'load_stable_new_entries',
        'load_stable_forwarded_tos',
        'load_stable_finaliz_bies',
        'load_clearance_forward_sm_tos',
        'load_clearance_entry_bies',
        'load_clearance_forward_pointsman_tos',
        'load_clearance_finaliz_bies',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                Schema::create($tableName, function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('department_id')->index();
                    $table->unsignedBigInteger('designation_id')->index();
                    $table->unsignedBigInteger('created_by')->nullable();
                    $table->unsignedBigInteger('updated_by')->nullable();
                    $table->timestamps();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tables as $tableName) {
            Schema::dropIfExists($tableName);
        }
    }
};

