<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Single-value site copy (hero, about, SEO, contact details, section headings…), one row per key.
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('summary', 300);
            $table->longText('body')->nullable();
            $table->string('image')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('stack_groups', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('stack_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stack_group_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_featured')->default(false); // shown in the hero "Core stack"
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('industries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('tags')->nullable();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->string('thumbnail')->nullable();
            $table->string('alt');
            $table->string('kicker')->nullable();
            $table->string('title')->nullable();
            $table->string('caption')->nullable();
            $table->string('size')->default('standard');
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable();
            $table->string('company')->nullable();
            $table->string('country')->nullable();
            $table->text('quote');
            $table->string('avatar')->nullable();
            $table->string('video_type')->default('none'); // none | file | youtube
            $table->string('video')->nullable();            // URL/path, or a YouTube video ID
            $table->string('poster')->nullable();
            $table->string('project')->nullable();          // shown on the card when there is no video
            $table->string('note')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('message');
            $table->string('ip', 45)->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('gallery_items');
        Schema::dropIfExists('industries');
        Schema::dropIfExists('stack_items');
        Schema::dropIfExists('stack_groups');
        Schema::dropIfExists('services');
        Schema::dropIfExists('settings');
    }
};
