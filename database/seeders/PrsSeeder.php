<?php

namespace Database\Seeders;

use App\Models\PrsRequest;
use App\Models\Product;
use App\Models\RequestAction;
use App\Models\User;
use Illuminate\Database\Seeder;

class PrsSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Juan Dela Cruz', 'email' => 'juan@dict.gov.ph', 'role' => 'user'],
            ['name' => 'Maria Santos', 'email' => 'maria@dict.gov.ph', 'role' => 'user'],
            ['name' => 'Pedro Reyes', 'email' => 'pedro@dict.gov.ph', 'role' => 'user'],
            ['name' => 'Ana Lopez', 'email' => 'ana@dict.gov.ph', 'role' => 'approver'],
            ['name' => 'Super Admin', 'email' => 'admin@dict.gov.ph', 'role' => 'superadmin'],
        ];

        $createdUsers = [];
        foreach ($users as $u) {
            $createdUsers[$u['email']] = User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'role' => $u['role'],
                    'password' => bcrypt('password'),
                ]
            );
        }

        $products = ['Laptop', 'Office Chair', 'Monitor', 'Keyboard', 'Standing Desk', 'Webcam', 'Headset'];
        foreach ($products as $p) {
            Product::firstOrCreate(
                ['name' => $p],
                ['description' => "Standard {$p} for office use"]
            );
        }

        $juan = $createdUsers['juan@dict.gov.ph'] ?? User::where('email', 'juan@dict.gov.ph')->first();
        $maria = $createdUsers['maria@dict.gov.ph'] ?? User::where('email', 'maria@dict.gov.ph')->first();
        $pedro = $createdUsers['pedro@dict.gov.ph'] ?? User::where('email', 'pedro@dict.gov.ph')->first();

        if ($juan) {
            $r1 = PrsRequest::updateOrCreate(
                ['request_id' => 'REQ-2025-02-00001'],
                [
                    'user_id' => $juan->id,
                    'item_name' => 'Laptop Upgrade',
                    'description' => 'Need upgrade for development work',
                    'status' => 'Pending',
                ]
            );
            if (! RequestAction::where('request_id', $r1->id)->exists()) {
                RequestAction::create([
                    'request_id' => $r1->id,
                    'description' => 'Send to approver',
                    'due_date' => now()->addDays(2),
                    'status' => 'pending',
                ]);
            }
        }

        if ($maria) {
            $r2 = PrsRequest::updateOrCreate(
                ['request_id' => 'REQ-2025-02-00002'],
                [
                    'user_id' => $maria->id,
                    'item_name' => 'Office Chair',
                    'description' => 'Ergonomic chair',
                    'status' => 'Pending',
                ]
            );
            if (! RequestAction::where('request_id', $r2->id)->exists()) {
                RequestAction::create([
                    'request_id' => $r2->id,
                    'description' => 'Verify budget approval',
                    'due_date' => now()->addDays(1),
                    'status' => 'pending',
                ]);
            }
        }

        if ($pedro) {
            PrsRequest::updateOrCreate(
                ['request_id' => 'REQ-2025-02-00003'],
                [
                    'user_id' => $pedro->id,
                    'item_name' => 'Standing Desk',
                    'description' => 'Adjustable standing desk',
                    'status' => 'Pending',
                ]
            );
        }

        $this->command->info('PRS sample data seeded.');
    }
}
