<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Industry;
use App\Models\Service;
use App\Models\Setting;
use App\Models\StackGroup;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * The portfolio's launch content, editable afterwards in the admin panel.
 * Images are copied from database/seeders/media onto the public disk so the admin can replace them.
 */
class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        if (Setting::exists()) {
            $this->command?->warn('Site content already exists; skipping so nothing is overwritten.');

            return;
        }

        $this->seedSettings();
        $this->seedServices();
        $this->seedStack();
        $this->seedIndustries();
        $this->seedGallery();
        $this->seedTestimonials();
        $this->seedFaqs();
    }

    /** Copies a bundled image to the public disk and returns its stored path. */
    private function media(string $source, string $dir): string
    {
        $path = $dir . '/' . basename($source);
        Storage::disk('public')->put($path, file_get_contents(__DIR__ . '/media/' . $source));

        return $path;
    }

    private function seedSettings(): void
    {
        Setting::store([
            'site' => [
                'name' => 'abdurDaily',
                'person' => 'Abdur Rahman',
                'job_title' => 'Full-Stack Web Developer',
                'meta_title' => 'Abdur Rahman — Laravel & Next.js Developer | abdurDaily',
                'meta_description' => 'Full-stack web developer in Dhaka building fast, secure web apps, SaaS and e-commerce with Laravel, PHP, Next.js and React for clients worldwide.',
                'og_image' => $this->media('abdur-portrait.jpg', 'site'),
                'twitter' => '@abdurDaily',
                'google_verification' => null,
            ],
            'contact' => [
                'email' => 'abdurdiary@gmail.com',
                'phone' => '+880 1632-212661',
                'whatsapp' => '8801632212661',
                'city' => 'Dhaka',
                'country' => 'Bangladesh',
                'country_code' => 'BD',
                'timezone' => 'Asia/Dhaka',
                'reply_time' => '12h',
                'available' => true,
                'available_text' => 'Available for work',
            ],
            'socials' => [
                ['label' => 'Facebook', 'icon' => 'bi-facebook', 'url' => 'https://www.facebook.com/abdurDaily', 'in_hero' => true],
                ['label' => 'LinkedIn', 'icon' => 'bi-linkedin', 'url' => 'https://www.linkedin.com/in/abdurDaily', 'in_hero' => true],
                ['label' => 'GitHub', 'icon' => 'bi-github', 'url' => 'https://github.com/abdurDaily', 'in_hero' => true],
                ['label' => 'X (Twitter)', 'icon' => 'bi-twitter-x', 'url' => 'https://x.com/abdurDaily', 'in_hero' => false],
            ],
            'hero' => [
                'greeting' => "Hello, I'm",
                'subtitle' => 'I design and build fast, secure web products with *Laravel* and *Next.js*, for startups and businesses in the USA, Bangladesh and worldwide.',
                'primary_cta' => 'My services',
                'secondary_cta' => 'Book a call',
                'image' => $this->media('abdur-cutout.webp', 'site'),
                'image_alt' => 'Abdur Rahman, full-stack web developer',
                'stats' => [
                    ['value' => '5', 'suffix' => '+', 'label' => 'years experience'],
                    ['value' => '80', 'suffix' => '+', 'label' => 'projects shipped'],
                    ['value' => '40', 'suffix' => '+', 'label' => 'happy clients'],
                ],
                'stack_title' => 'Core stack',
                'contact_title' => "Let's work together",
                'sign' => '*Full-Stack* Developer.',
            ],
            'brands' => [
                'title' => 'Trusted by teams across the USA, Bangladesh & beyond',
                'items' => [
                    ['name' => 'NovaPay', 'icon' => 'bi-hexagon-fill'],
                    ['name' => 'Bridgewise', 'icon' => 'bi-triangle-fill'],
                    ['name' => 'Orbitly', 'icon' => 'bi-circle-square'],
                    ['name' => 'Voltera', 'icon' => 'bi-lightning-charge-fill'],
                    ['name' => 'Bloomcart', 'icon' => 'bi-flower1'],
                    ['name' => 'UrbanNest', 'icon' => 'bi-buildings-fill'],
                    ['name' => 'MediSync', 'icon' => 'bi-heart-pulse-fill'],
                    ['name' => 'Learnly', 'icon' => 'bi-mortarboard-fill'],
                ],
            ],
            'about' => [
                'label' => 'About', 'cue' => 'who I am',
                'title' => 'A developer who thinks like a *product owner*.',
                'lead' => "I'm Abdur Rahman, a full-stack web developer based in Dhaka, Bangladesh. For more than five years I've helped startups and businesses in the USA, UK and Bangladesh turn ideas into dependable web software.",
                'body' => "Clients don't hire me just to write code. They hire me to solve business problems. I own the whole build, from architecture and back-end to front-end, deployment and support after launch, so you work with one accountable engineer instead of a chain of freelancers.",
                'facts' => [
                    ['label' => 'Based in', 'value' => 'Dhaka, Bangladesh · GMT+6'],
                    ['label' => 'Experience', 'value' => '5+ years · 80+ projects shipped'],
                    ['label' => 'Clients', 'value' => 'Startups & businesses in the USA, UK and Bangladesh'],
                    ['label' => 'Specialty', 'value' => 'Laravel, PHP, Next.js and React'],
                ],
                'availability' => 'Open for new projects',
                'primary_cta' => 'Start a project',
                'secondary_cta' => 'My services',
                'principles_title' => 'How I work',
                'principles' => [
                    ['title' => 'Outcomes before code', 'text' => 'Every feature starts with the result it should drive: more sales, hours saved, fewer support tickets.'],
                    ['title' => 'One accountable engineer', 'text' => 'You talk directly to the person building your product. No hand-offs, nothing lost in translation.'],
                    ['title' => 'Clear, regular updates', 'text' => 'A written update every working day and a demo on a staging link every week, in your time zone.'],
                    ['title' => 'Built to last', 'text' => 'Tested, documented code in your own repository from day one, so any team can maintain it.'],
                ],
            ],
            'services_section' => [
                'label' => 'Services', 'cue' => 'what I do',
                'title' => 'What I can *build* for you.',
                'description' => 'End-to-end engineering — from the first database table to the final deploy, with clean, documented code your team can maintain.',
            ],
            'stack_section' => [
                'label' => 'Tech stack', 'cue' => 'the tools I trust',
                'title' => 'Modern tools, *proven* results.',
                'description' => 'Battle-tested technologies, chosen to keep your product fast, secure and easy to maintain for years.',
            ],
            'outline' => [
                'words' => [['word' => 'Web apps'], ['word' => 'E-commerce'], ['word' => 'SaaS'], ['word' => 'APIs'], ['word' => 'Dashboards']],
            ],
            'industries_section' => [
                'title' => 'Software that stops the guesswork and starts the *growth*.',
                'card_cta' => "Let's talk",
            ],
            'process' => [
                'label' => 'Process', 'cue' => "how we'll work",
                'title' => 'A delivery *pipeline*, not guesswork.',
                'description' => "Every project runs through the same proven stages — so you always know what's done, what's next and when it ships.",
                'steps' => [
                    ['icon' => 'bi-search', 'status' => '✓ passed', 'title' => 'Discover', 'text' => 'Goals, users and scope. You get a clear proposal, estimate and timeline.'],
                    ['icon' => 'bi-diagram-3', 'status' => '✓ passed', 'title' => 'Plan', 'text' => 'Architecture, database schema and UI flows — approved before coding.'],
                    ['icon' => 'bi-code-slash', 'status' => '✓ passed', 'title' => 'Build', 'text' => 'Weekly demos on a staging link. Your feedback shapes every milestone.'],
                    ['icon' => 'bi-shield-check', 'status' => '✓ passed', 'title' => 'Test', 'text' => 'Automated tests, QA and performance checks before anything goes live.'],
                    ['icon' => 'bi-rocket-takeoff', 'status' => '✓ deployed', 'title' => 'Ship & Support', 'text' => 'Deployment, handover docs and ongoing support after launch.'],
                ],
            ],
            'statement' => [
                'label' => 'Principles', 'cue' => 'my promise',
                'title' => 'Clean code. Clear communication. *Shipped* on time.',
                'points' => [
                    ['name' => 'quality', 'value' => 'tested & documented'],
                    ['name' => 'updates', 'value' => 'daily, in your timezone'],
                    ['name' => 'handover', 'value' => 'code + docs + support'],
                ],
            ],
            'gallery_section' => [
                'label' => 'Moments', 'cue' => 'the journey',
                'title' => 'The *people* behind the work.',
                'description' => 'The teams, mentors and milestones that shape how I build — a look beyond the screen.',
            ],
            'reviews_section' => [
                'label' => 'Client reviews', 'cue' => 'in their words',
                'title' => 'Reviewed & *approved* by clients.',
                'description' => 'Long-term relationships matter more to me than one-off gigs. Hear it from the clients themselves.',
            ],
            'faq_section' => [
                'label' => 'FAQ', 'cue' => 'before you ask',
                'title' => 'Questions clients *ask* first.',
                'description' => 'Straight answers about cost, time zones and ownership — so we can skip the guesswork and focus on your product.',
                'cta' => 'Ask me anything',
            ],
            'contact_section' => [
                'label' => 'Get in touch',
                'title' => "Let's build something *people remember.*",
                'subtitle' => "I'm always open to new projects. Message me and I reply within 12 hours with honest feedback and next steps.",
                'form_intro' => "Prefer to write it all down? Send the details — what you're building and when you need it.",
                'success' => "Thanks! Your message is in. I'll reply within 12 hours.",
            ],
            'footer' => [
                'cta' => 'Have an idea?',
                'tagline' => "That's a wrap. git push?",
                'links' => [
                    ['label' => 'hosting', 'url' => 'https://my.exonhost.com/affiliate?aff=5910', 'sponsored' => true],
                ],
            ],
        ]);
    }

    private function seedServices(): void
    {
        $services = [
            ['Laravel & PHP Applications', 'laravel-php-development', 'Custom web apps, admin panels, multi-tenant SaaS and secure REST APIs.', 'service-laravel.webp',
                '<p>I build custom Laravel applications that model how your business actually works: customer portals, internal tools, admin panels and multi-tenant SaaS platforms.</p><h2>What you get</h2><ul><li>Clean, strictly typed PHP 8 following Laravel conventions</li><li>Secure authentication, roles and permissions</li><li>Versioned REST APIs with documentation</li><li>Automated tests and a CI pipeline from day one</li></ul><h2>Ideal for</h2><p>Startups launching an MVP, and established businesses replacing spreadsheets or an ageing PHP codebase.</p>'],
            ['Next.js & React Front-ends', 'nextjs-react-development', 'SEO-friendly, lightning-fast interfaces with server rendering and polished UX.', 'service-nextjs.webp',
                '<p>Fast, accessible front-ends built with Next.js and React, server-rendered so search engines and customers see your content instantly.</p><h2>What you get</h2><ul><li>Server-side rendering and static generation for top Core Web Vitals</li><li>Technical SEO: metadata, structured data and sitemaps</li><li>Responsive, accessible UI that works on every device</li><li>Clean integration with your Laravel or headless CMS back-end</li></ul>'],
            ['E-commerce & Payments', 'ecommerce-payment-integration', 'Stores and marketplaces with Stripe, PayPal, SSLCommerz and bKash.', 'service-ecommerce.webp',
                '<p>Online stores and multi-vendor marketplaces with reliable checkout and payments for both international and Bangladeshi customers.</p><h2>What you get</h2><ul><li>Stripe, PayPal, SSLCommerz and bKash integrations</li><li>Inventory, orders, coupons and shipping workflows</li><li>Fast product pages built to rank and convert</li></ul>'],
            ['SaaS Dashboards', 'saas-dashboard-development', 'Role-based dashboards, reporting, subscriptions and real-time data.', 'service-saas.webp',
                '<p>Dashboards that turn your data into decisions: role-based access, reporting, subscription billing and real-time updates.</p><h2>What you get</h2><ul><li>Subscription billing and plan management</li><li>Charts, exports and scheduled reports</li><li>Real-time notifications and activity feeds</li></ul>'],
            ['APIs & Integrations', 'api-integration-services', 'Connect CRMs, ERPs, shipping and messaging services into one workflow.', 'service-api.webp',
                '<p>I connect the tools your business already uses — CRMs, ERPs, couriers, SMS and email providers — so data flows without manual copy-and-paste.</p><h2>What you get</h2><ul><li>Robust, retry-safe integrations with queues and logging</li><li>Webhooks and documented REST APIs</li><li>Monitoring so failures are caught before customers notice</li></ul>'],
            ['Performance, Upgrades & Support', 'laravel-maintenance-support', 'Speed audits, Laravel upgrades, bug fixes and long-term maintenance.', 'service-support.webp',
                '<p>Keep your application fast, secure and up to date with audits, framework upgrades and dependable ongoing support.</p><h2>What you get</h2><ul><li>Performance audits with measurable before/after results</li><li>Laravel and PHP version upgrades</li><li>Security patches, bug fixes and monthly maintenance plans</li></ul>'],
        ];

        foreach ($services as $i => [$title, $slug, $summary, $image, $body]) {
            Service::create([
                'title' => $title, 'slug' => $slug, 'summary' => $summary, 'body' => $body,
                'image' => $this->media("placeholders/{$image}", 'services'), 'sort' => $i + 1,
            ]);
        }
    }

    private function seedStack(): void
    {
        $groups = [
            ['Back-end', 'Business logic, APIs and data', [
                ['Laravel', 'Web apps, admin panels & REST APIs', 'devicon-laravel-original colored', true],
                ['PHP', 'Modern, strictly typed PHP 8', 'devicon-php-plain colored', true],
                ['Node.js', 'Real-time services & tooling', 'devicon-nodejs-plain colored', false],
                ['MySQL', 'Well-modelled, fast databases', 'devicon-mysql-original colored', true],
            ]],
            ['Front-end', 'Fast, accessible interfaces', [
                ['Next.js', 'SEO-friendly, server-rendered sites', 'devicon-nextjs-plain', true],
                ['React', 'Interactive dashboards & apps', 'devicon-react-original colored', true],
                ['TypeScript', 'Safer, self-documenting code', 'devicon-typescript-plain colored', false],
                ['Tailwind & Bootstrap', 'Consistent, responsive styling', 'devicon-tailwindcss-original colored', false],
            ]],
            ['Delivery', 'Shipping safely, and keeping it running', [
                ['Git', 'Clean history & code reviews', 'devicon-git-plain colored', false],
                ['Docker', 'Same setup from laptop to server', 'devicon-docker-plain colored', false],
                ['CI/CD', 'Automated tests & deployments', 'devicon-githubactions-plain colored', false],
                ['Testing & QA', 'Checks before anything goes live', 'bi bi-shield-check', false],
            ]],
        ];

        foreach ($groups as $g => [$title, $subtitle, $items]) {
            $group = StackGroup::create(['title' => $title, 'subtitle' => $subtitle, 'sort' => $g + 1]);
            foreach ($items as $i => [$name, $description, $icon, $featured]) {
                $group->items()->create(['name' => $name, 'description' => $description, 'icon' => $icon, 'is_featured' => $featured, 'sort' => $i + 1]);
            }
        }
    }

    private function seedIndustries(): void
    {
        $industries = [
            ['E-commerce', 'stores · marketplaces', 'bi-bag', 'service-ecommerce.webp'],
            ['SaaS', 'dashboards · fintech', 'bi-bar-chart-line', 'service-saas.webp'],
            ['Real Estate', 'listings · CRM', 'bi-house', 'industry-realestate.webp'],
            ['Education', 'LMS · courses', 'bi-mortarboard', 'industry-education.webp'],
        ];

        foreach ($industries as $i => [$title, $tags, $icon, $image]) {
            Industry::create(['title' => $title, 'tags' => $tags, 'icon' => $icon, 'image' => $this->media("placeholders/{$image}", 'industries'), 'sort' => $i + 1]);
        }
    }

    private function seedGallery(): void
    {
        $items = [
            ['04', 'tall', 'Abdur Rahman and a colleague holding a Creative IT Institute folder at the institute\'s reception', 'Creative IT Institute', 'Building together', 'At Creative IT Institute'],
            ['01', 'wide', 'Three people reviewing work on a laptop around a small table', 'Working session', 'Reviewing a build with the team', 'Reviewing a build with the team'],
            ['02', 'standard', 'Three people gathered around a laptop discussing code', 'Pairing', 'Side by side on the code', 'Working through code side by side'],
            ['03', 'standard', 'Graduates in gowns and caps standing in a row at the IIUC convocation', 'IIUC', 'The graduating class', 'Convocation with the graduating class, IIUC'],
            ['05', 'half', 'Abdur Rahman in a graduation cap in front of the IIUC Convocation 2026 banner', 'Convocation 2026', 'International Islamic University Chittagong', 'Convocation 2026 — International Islamic University Chittagong'],
            ['06', 'half', 'Abdur Rahman seated in front of large Convocation 2026 letters', 'Milestone', 'Convocation day, January 2026', 'Convocation day, January 2026'],
        ];

        foreach ($items as $i => [$n, $size, $alt, $kicker, $title, $caption]) {
            GalleryItem::create([
                'image' => $this->media("gallary/abdur-daily-{$n}.webp", 'gallery'),
                'thumbnail' => $this->media("gallary/abdur-daily-{$n}-thumb.webp", 'gallery'),
                'alt' => $alt, 'kicker' => $kicker, 'title' => $title, 'caption' => $caption, 'size' => $size, 'sort' => $i + 1,
            ]);
        }
    }

    private function seedTestimonials(): void
    {
        // Sample reviews from the original design — replace them with real client reviews in the admin.
        $video = 'https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4';
        $reviews = [
            ['James Miller', 'Founder', 'ShopNest', 'USA', "Abdur rebuilt our marketplace in Laravel and it's twice as fast. Daily updates and always available during our working hours.", 'client-james.webp'],
            ['Tanvir Hasan', 'CEO', 'Learnly', 'Bangladesh', 'Our LMS handles thousands of students without a hiccup. The bKash and SSLCommerz integration worked perfectly from day one.', 'client-tanvir.webp'],
            ['Sarah Collins', 'Marketing Lead', 'UrbanNest', 'USA', 'The Next.js front-end boosted our Lighthouse score and our leads went up noticeably. A true professional who cares about quality.', 'client-sarah.webp'],
        ];

        foreach ($reviews as $i => [$name, $role, $company, $country, $quote, $photo]) {
            $image = $this->media("placeholders/{$photo}", 'testimonials');
            Testimonial::create([
                'name' => $name, 'role' => $role, 'company' => $company, 'country' => $country, 'quote' => $quote,
                'avatar' => $image, 'poster' => $image, 'video_type' => 'file', 'video' => $video, 'sort' => $i + 1,
            ]);
        }

        Testimonial::create([
            'name' => 'Oliver King', 'role' => 'CTO', 'company' => 'FinTrack', 'country' => 'UK',
            'quote' => "Delivered a complex dashboard ahead of schedule with clean, documented code. We've already hired him for two more projects.",
            'project' => 'Analytics dashboard', 'note' => 'Hired again for two more projects', 'sort' => 4,
        ]);
    }

    private function seedFaqs(): void
    {
        $faqs = [
            ['How do you price projects?', 'Fixed price for clearly defined scopes, so you know the total upfront. For ongoing work I offer hourly or monthly retainers. Every estimate is broken into milestones you approve.'],
            ['Can you work in my time zone?', "I'm in Bangladesh (GMT+6) and keep overlapping hours with US and European clients for calls. You also get a written progress update every working day."],
            ['Who owns the code?', 'You do — 100%. The code lives in your repository from day one, and you receive full source, credentials and documentation at handover.'],
            ['Will you sign an NDA?', "Yes. I'm happy to sign your NDA before we discuss any details of your idea or business."],
            ['Do you support the project after launch?', 'Every project includes a post-launch bug-fix window. After that, I offer maintenance plans for updates, security patches and new features.'],
            ['Can you take over an existing codebase?', 'Absolutely. I start with a short code audit, share what I find, then fix, upgrade or extend your Laravel, PHP, React or Next.js application.'],
        ];

        foreach ($faqs as $i => [$question, $answer]) {
            Faq::create(['question' => $question, 'answer' => $answer, 'sort' => $i + 1]);
        }
    }
}
