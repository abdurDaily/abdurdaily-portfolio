<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\Service;
use App\Models\Setting;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(SiteContentSeeder::class);
    }

    public function test_home_page_renders_content_from_the_database(): void
    {
        Faq::first()->update(['question' => 'Do you build mobile apps?']);
        Setting::store(['hero' => array_merge(setting('hero'), ['greeting' => 'Hi there, I am'])]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Do you build mobile apps?')
            ->assertSee('Hi there, I am')
            ->assertSee('<span class="hl">product owner</span>', false)
            ->assertSee('<link rel="canonical"', false)
            ->assertSee('"@type":"FAQPage"', false)
            ->assertSee(route('services.show', 'laravel-php-development'), false);
    }

    public function test_highlight_markup_escapes_html(): void
    {
        $this->assertSame('a &lt;b&gt; <span class="hl">c</span>', (string) marked('a <b> *c*'));
    }

    public function test_service_page_has_its_own_seo_and_unpublished_services_are_hidden(): void
    {
        $service = Service::where('slug', 'laravel-php-development')->first();
        $service->update(['meta_title' => 'Hire a Laravel Developer']);

        $this->get('/services/laravel-php-development')
            ->assertOk()
            ->assertSee('<title>Hire a Laravel Developer</title>', false)
            ->assertSee('"@type":"BreadcrumbList"', false);

        $service->update(['is_published' => false]);
        $this->get('/services/laravel-php-development')->assertNotFound();
        $this->get('/sitemap.xml')->assertOk()->assertDontSee('laravel-php-development');
    }

    public function test_sitemap_and_robots(): void
    {
        $this->get('/sitemap.xml')->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee('<loc>' . route('services.show', 'nextjs-react-development') . '</loc>', false);

        // non-production environments must never be indexed
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /');
    }

    public function test_contact_form_stores_the_message(): void
    {
        $this->postJson('/contact', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'message' => 'We need a Laravel dashboard by March.',
        ])->assertOk()->assertJsonStructure(['message']);

        $this->assertDatabaseHas('contact_messages', ['email' => 'jane@example.com']);
    }

    public function test_contact_form_validates_and_ignores_bots(): void
    {
        $this->postJson('/contact', ['name' => '', 'email' => 'nope', 'message' => 'short'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'message']);

        $this->postJson('/contact', [
            'name' => 'Bot', 'email' => 'bot@example.com', 'message' => 'Buy cheap links now!!', 'website' => 'http://spam.test',
        ])->assertOk();

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_admin_requires_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }
}
