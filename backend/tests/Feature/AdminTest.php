<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettings;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(SiteContentSeeder::class);
        $this->actingAs(User::factory()->create());
    }

    public function test_every_admin_page_renders(): void
    {
        foreach (['/admin', '/admin/site-settings', '/admin/services', '/admin/stack-groups', '/admin/industries',
            '/admin/gallery-items', '/admin/testimonials', '/admin/faqs', '/admin/contact-messages'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_dashboard_shows_brand_widgets(): void
    {
        \App\Models\ContactMessage::create(['name' => 'Jane', 'email' => 'jane@example.com', 'message' => 'Need a SaaS dashboard.']);

        $this->get('/admin')
            ->assertOk()
            ->assertSee('assets/css/admin.css', false)
            ->assertSee('ad-hero', false)
            ->assertSee('1 new inquiry')
            ->assertSee('SEO health');
    }

    public function test_saving_site_content_keeps_images_and_lists_and_updates_the_site(): void
    {
        $before = Setting::allCached();

        Livewire::test(SiteSettings::class)
            ->set('data.hero.greeting', 'Hey, I am')
            ->call('save')
            ->assertHasNoErrors();

        Cache::flush();
        $after = Setting::allCached();

        $this->assertSame('Hey, I am', $after['hero']['greeting']);
        $this->assertSame($before['hero']['image'], $after['hero']['image']);
        $this->assertSame($before['site']['og_image'], $after['site']['og_image']);
        $this->assertSame(array_values($before['hero']['stats']), array_values($after['hero']['stats']));
        $this->assertSame(array_values($before['socials']), array_values($after['socials']));

        $this->get('/')->assertSee('Hey, I am');
    }
}
