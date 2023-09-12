<?php

namespace Database\Seeders;

use App\Models\UserOrganizationalAssignment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersOrganizationalAssignmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users_organizational_assignment')->truncate();

        Organization::create([
            "organization_id" => 1,
            "user_id" => 1,
        ]);
    }
}
