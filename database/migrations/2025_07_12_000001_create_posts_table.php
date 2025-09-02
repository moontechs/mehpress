<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description', 500)->nullable();
            $table->text('text');
            $table->text('slug')->unique();
            $table->text('tags')->nullable()->default('[]');
            $table->jsonb('seo_tags')->default('{}');
            $table->boolean('published')->default(false);
            $table->string('type')->default('post');
            $table->foreignId('blog_id')->constrained('blogs')->cascadeOnDelete();
            $table->string('language')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
