<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;

class GroupSeeder extends Seeder
{
    public function run()
    {
        Group::create(['name' => 'Admin', 'can_upload' => true, 'can_download' => true, 'can_delete' => true]);
        Group::create(['name' => 'Uploader', 'can_upload' => true, 'can_download' => false, 'can_delete' => false]);
        Group::create(['name' => 'Viewer', 'can_upload' => false, 'can_download' => true, 'can_delete' => false]);
    }
}
