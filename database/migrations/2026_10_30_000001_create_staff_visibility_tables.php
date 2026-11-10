<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables to manage staff visibility mappings.
     */
    protected array $tables = [
        'staff_visibility_counselling_onlines',
        'staff_visibility_counselling_offlines',
        'staff_visibility_ambush_shuntings',
        'staff_visibility_ambush_ba_tests',
        'staff_visibility_medicals',
        'staff_visibility_refreshers',
        'staff_visibility_gradations',
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

