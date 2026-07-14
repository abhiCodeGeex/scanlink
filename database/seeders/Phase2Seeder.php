<?php

namespace Database\Seeders;

use App\Enums\AdminRole;
use App\Enums\ClientUserRole;
use App\Enums\CodeOrderStatus;
use App\Models\Client;
use App\Models\CodePurchase;
use App\Models\User;
use Illuminate\Database\Seeder;

class Phase2Seeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@scanlink.com'],
            [
                'name' => 'ScanLink Admin',
                'password' => 'Admin@12345',
                'admin_role' => AdminRole::SuperAdmin,
            ],
        );

        if (Client::query()->exists()) {
            return;
        }

        $this->seedSampleClients();
    }

    private function seedSampleClients(): void
    {
        $acme = Client::query()->create([
            'client_name' => 'Acme Inspections',
            'address' => '12 George Street, Sydney NSW',
            'telephone' => '02 9000 1111',
            'contact_person' => 'Jane Cooper',
            'regi_date' => now()->subMonths(6)->toDateString(),
            'email' => 'acme@example.com',
            'password' => 'Acme@12345',
            'url' => 'acme-inspections',
            'approve' => true,
            'reseller_code' => 'ACME001',
            'reseller_email' => 'reseller@acme.example',
            'is_password_change' => true,
        ]);

        $acme->users()->create([
            'email' => 'acme@example.com',
            'password' => 'Acme@12345',
            'role' => ClientUserRole::Primary,
            'status' => true,
            'video_upload' => true,
            'checklist_option' => true,
            'customqr_option' => false,
            'is_password_change' => true,
            'expire_at' => now()->addYear(),
        ]);

        $acme->users()->create([
            'email' => 'field.tech@acme.example',
            'password' => 'SubUser@12345',
            'role' => ClientUserRole::SubUser,
            'status' => true,
            'video_upload' => false,
            'is_sub_user' => true,
            'is_password_change' => true,
            'expire_at' => now()->addMonths(6),
        ]);

        $blocked = Client::query()->create([
            'client_name' => 'Blocked Demo Client',
            'address' => '99 Test Road, Melbourne VIC',
            'telephone' => '03 9000 2222',
            'contact_person' => 'Demo Blocked',
            'regi_date' => now()->subYear()->toDateString(),
            'email' => 'blocked@example.com',
            'password' => 'Blocked@12345',
            'url' => 'blocked-demo',
            'approve' => false,
            'is_password_change' => true,
        ]);

        $blocked->users()->create([
            'email' => 'blocked@example.com',
            'password' => 'Blocked@12345',
            'role' => ClientUserRole::Primary,
            'status' => true,
            'video_upload' => true,
            'is_password_change' => true,
            'expire_at' => now()->addMonths(3),
        ]);

        CodePurchase::query()->create([
            'client_id' => $acme->id,
            'email' => 'acme@example.com',
            'town' => 'Sydney',
            'first_name' => 'Jane',
            'last_name' => 'Cooper',
            'company_name' => 'Acme Inspections',
            'billing_address' => '12 George Street',
            'phone' => '02 9000 1111',
            'postal_code' => '2000',
            'no_of_codes' => 25,
            'per_code_amount' => 12.50,
            'total_amount' => 312.50,
            'status' => CodeOrderStatus::Paid,
            'enable' => true,
            'exipry_date' => now()->addYear(),
            'ordered_on' => now()->subWeek(),
        ]);

        CodePurchase::query()->create([
            'client_id' => $acme->id,
            'email' => 'acme@example.com',
            'town' => 'Sydney',
            'first_name' => 'Jane',
            'last_name' => 'Cooper',
            'company_name' => 'Acme Inspections',
            'no_of_codes' => 5,
            'per_code_amount' => 0,
            'total_amount' => 0,
            'status' => CodeOrderStatus::New,
            'enable' => true,
            'free_code' => true,
            'ordered_on' => now(),
        ]);
    }
}
