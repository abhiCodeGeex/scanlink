<?php

namespace Database\Seeders;

use App\Enums\CodeOrderStatus;
use App\Enums\PhysicalOrderStatus;
use App\Models\Client;
use App\Models\CodePrising;
use App\Models\FormBuilderOrder;
use App\Models\FormBuilderOrderDetail;
use App\Models\Gallery;
use App\Models\Order;
use App\Models\Profile;
use App\Models\ResellerPricing;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class Phase5Seeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedPricing();
        $this->seedOrders();
        $this->seedCms();
    }

    private function seedSettings(): void
    {
        $defaults = [
            'paypal_email' => 'payments@scanlink.example',
            'youtube_username' => 'scanlink',
            'youtube_password' => 'secret',
            'contact_email' => 'admin@scanlink.com',
            'youtube_developer_key' => 'dev-key',
            'youtube_client_id' => 'client-id',
            'youtube_client_secret' => 'client-secret',
            'youtube_refresh_token' => '',
            'youtube_application_id' => 'app-id',
        ];

        foreach ($defaults as $title => $values) {
            Setting::query()->updateOrCreate(['title' => $title], ['values' => $values]);
        }
    }

    private function seedPricing(): void
    {
        if (CodePrising::query()->exists()) {
            return;
        }

        CodePrising::query()->create([
            'code_min_qty' => 1,
            'code_max_qty' => 10,
            'amount' => 120,
            'reseller_amount' => 100,
        ]);

        CodePrising::query()->create([
            'code_min_qty' => 11,
            'code_max_qty' => 50,
            'amount' => 100,
            'reseller_amount' => 85,
        ]);

        ResellerPricing::query()->create(['code_qty' => 10, 'amount' => 95]);
        ResellerPricing::query()->create(['code_qty' => 25, 'amount' => 80]);
    }

    private function seedOrders(): void
    {
        $acme = Client::query()->where('url', 'acme-inspections')->first();
        $profile = Profile::query()->where('client_id', $acme?->id)->active()->first();

        if (! $acme || ! $profile || Order::query()->exists()) {
            return;
        }

        Order::query()->create([
            'client_id' => $acme->id,
            'profile_id' => $profile->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'zip' => '2000',
            'email' => 'jane@example.com',
            'contact' => '0400111222',
            'status' => PhysicalOrderStatus::New,
            'ordered_on' => now(),
        ]);

        $fbOrder = FormBuilderOrder::query()->create([
            'client_id' => $acme->id,
            'email' => $acme->email,
            'first_name' => 'Acme',
            'last_name' => 'Admin',
            'postal_code' => '2000',
            'phone' => '0299999999',
            'no_of_codes' => 1,
            'per_code_amount' => 50,
            'total_amount' => 50,
            'status' => CodeOrderStatus::New,
        ]);

        FormBuilderOrderDetail::query()->create([
            'form_builder_order_id' => $fbOrder->id,
            'profile_id' => $profile->id,
        ]);
    }

    private function seedCms(): void
    {
        if (Testimonial::query()->exists()) {
            return;
        }

        Testimonial::query()->create([
            'title' => 'ScanLink Overview',
            'video' => '<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ"></iframe>',
        ]);

        Gallery::query()->create([
            'name' => 'sample-banner.jpg',
            'approve' => true,
        ]);
    }
}
