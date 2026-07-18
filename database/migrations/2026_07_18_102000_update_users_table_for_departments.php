<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('company_id');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });

        // Data Migration
        $companies = DB::table('companies')->get();
        foreach ($companies as $company) {
            if ($company->departments) {
                $deptNames = json_decode($company->departments, true) ?? [];
                foreach ($deptNames as $name) {
                    if (!empty($name)) {
                        DB::table('departments')->updateOrInsert(
                            ['company_id' => $company->id, 'name' => trim($name)],
                            ['created_at' => now(), 'updated_at' => now()]
                        );
                    }
                }
            }
        }

        if (Schema::hasColumn('users', 'department')) {
            $users = DB::table('users')->whereNotNull('department')->get();
            foreach ($users as $user) {
                if ($user->department && $user->company_id) {
                    $dept = DB::table('departments')
                        ->where('company_id', $user->company_id)
                        ->where('name', trim($user->department))
                        ->first();
                    
                    if (!$dept) {
                        $deptId = DB::table('departments')->insertGetId([
                            'company_id' => $user->company_id,
                            'name' => trim($user->department),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    } else {
                        $deptId = $dept->id;
                    }

                    DB::table('users')->where('id', $user->id)->update(['department_id' => $deptId]);
                }
            }

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('department');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
            $table->string('department')->nullable();
        });
    }
};
