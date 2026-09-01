<?php
namespace Tests\Feature;

use App\Livewire\Admin\SiteSettings;
use App\Models\ShippingRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SiteSettingsRateTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_rate_works(): void
    {
        $lw = Livewire::test(SiteSettings::class)
            ->set('newGovernorate', 'TESTZONE')
            ->set('newFee', '9.99')
            ->call('addRate');

        $this->assertDatabaseHas('shipping_rates', [
            'governorate' => 'TESTZONE',
            'fee' => '9.99',
        ]);
    }

    public function test_delete_rate_works(): void
    {
        $rate = ShippingRate::create([
            'governorate' => 'DELETEZONE',
            'fee' => '5.00',
        ]);

        Livewire::test(SiteSettings::class)
            ->call('deleteRate', $rate->id);

        $this->assertDatabaseMissing('shipping_rates', [
            'id' => $rate->id,
        ]);
    }
}