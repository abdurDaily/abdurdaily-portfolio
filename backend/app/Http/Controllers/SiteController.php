<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Industry;
use App\Models\Service;
use App\Models\Setting;
use App\Models\StackGroup;
use App\Models\StackItem;
use App\Models\Testimonial;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SiteController extends Controller
{
    public function home()
    {
        $services = Service::published()->ordered()->get();
        $faqs = Faq::published()->ordered()->get();

        $faqPage = $faqs->isEmpty() ? null : [
            '@type' => 'FAQPage',
            '@id' => route('home') . '#faq',
            'mainEntity' => $faqs->map(fn (Faq $faq) => [
                '@type' => 'Question',
                'name' => $faq->question,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq->answer],
            ])->all(),
        ];

        return view('home', [
            'services' => $services,
            'stackGroups' => StackGroup::ordered()->with('items')->get(),
            'industries' => Industry::ordered()->get(),
            'gallery' => GalleryItem::published()->ordered()->get(),
            'testimonials' => Testimonial::published()->ordered()->get(),
            'faqs' => $faqs,
            'schema' => array_values(array_filter([
                $this->website(),
                $this->person($services),
                [
                    '@type' => 'ProfilePage',
                    '@id' => route('home') . '#webpage',
                    'url' => route('home'),
                    'name' => setting('site.meta_title'),
                    'description' => setting('site.meta_description'),
                    'isPartOf' => ['@id' => route('home') . '#website'],
                    'mainEntity' => ['@id' => route('home') . '#person'],
                    'dateModified' => $this->lastModified()->toAtomString(),
                ],
                $faqPage,
            ])),
        ]);
    }

    public function service(Service $service)
    {
        abort_unless($service->is_published, 404);

        $url = route('services.show', $service);
        $description = $service->meta_description ?: $service->summary;

        return view('service', [
            'service' => $service,
            'others' => Service::published()->ordered()->whereKeyNot($service->id)->get(),
            'title' => $service->meta_title ?: "{$service->title} | " . setting('site.person') . ' — ' . setting('site.name'),
            'description' => $description,
            'schema' => [
                $this->website(),
                $this->person(),
                [
                    '@type' => 'Service',
                    '@id' => $url . '#service',
                    'name' => $service->title,
                    'description' => $description,
                    'url' => $url,
                    'image' => media_url($service->image),
                    'serviceType' => $service->title,
                    'provider' => ['@id' => route('home') . '#person'],
                    'areaServed' => 'Worldwide',
                ],
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Services', 'item' => route('home') . '#services'],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $service->title, 'item' => $url],
                    ],
                ],
            ],
        ]);
    }

    public function sitemap(): Response
    {
        $urls = collect([['loc' => route('home'), 'lastmod' => $this->lastModified(), 'priority' => '1.0']])
            ->concat(Service::published()->ordered()->get()->map(fn (Service $service) => [
                'loc' => route('services.show', $service),
                'lastmod' => $service->updated_at,
                'priority' => '0.8',
            ]));

        return response()->view('sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        // Only production is indexable, so staging/local copies never compete with the real site.
        $lines = app()->isProduction()
            ? ['User-agent: *', 'Disallow: /admin', 'Disallow: /livewire', '', 'Sitemap: ' . route('sitemap')]
            : ['User-agent: *', 'Disallow: /'];

        return response(implode("\n", $lines) . "\n")->header('Content-Type', 'text/plain');
    }

    private function website(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => route('home') . '#website',
            'url' => route('home'),
            'name' => setting('site.name'),
            'inLanguage' => 'en',
            'publisher' => ['@id' => route('home') . '#person'],
        ];
    }

    /** The site owner. On the home page it also lists the services offered. */
    private function person($services = null): array
    {
        $person = [
            '@type' => 'Person',
            '@id' => route('home') . '#person',
            'name' => setting('site.person'),
            'alternateName' => setting('site.name'),
            'jobTitle' => setting('site.job_title'),
            'description' => setting('site.meta_description'),
            'url' => route('home'),
            'image' => media_url(setting('site.og_image')),
            'email' => 'mailto:' . setting('contact.email'),
            'telephone' => '+' . setting('contact.whatsapp'),
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => setting('contact.city'),
                'addressCountry' => setting('contact.country_code'),
            ],
            'knowsAbout' => StackItem::query()->orderBy('sort')->pluck('name')->all(),
            'sameAs' => collect(setting('socials', []))->pluck('url')->filter()->values()->all(),
        ];

        if ($services) {
            $person['makesOffer'] = $services->map(fn (Service $service) => [
                '@type' => 'Offer',
                'itemOffered' => [
                    '@type' => 'Service',
                    'name' => $service->title,
                    'description' => $service->summary,
                    'url' => route('services.show', $service),
                ],
            ])->all();
        }

        return $person;
    }

    /** Newest edit across the home page's content, for sitemap <lastmod> and dateModified. */
    private function lastModified(): Carbon
    {
        return collect([Setting::class, Service::class, StackItem::class, Industry::class, GalleryItem::class, Testimonial::class, Faq::class])
            ->map(fn ($model) => $model::max('updated_at'))
            ->filter()
            ->map(fn ($date) => Carbon::parse($date))
            ->max() ?? now();
    }
}
