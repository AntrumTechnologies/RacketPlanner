<?php

namespace Database\Seeders;

use App\Models\AdminOrganizationalAssignment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminsOrganizationalAssignmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins_organizational_assignment')->truncate();

        Organization::create([
            "organization_id" => 1,
            "user_id" => 1,
        ]);
    }
}
