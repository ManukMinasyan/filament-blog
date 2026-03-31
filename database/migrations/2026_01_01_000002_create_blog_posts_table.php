<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->text('excerpt')->nullable();
            $table->string('featured_image')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('blog_categories')->nullOnDelete();

            $userIdColumn = Schema::getColumns('users')[0];
            if (str_contains($userIdColumn['type'], 'char')) {
                $table->char('author_id', 26);
            } else {
                $table->unsignedBigInteger('author_id');
            }
            $table->foreign('author_id')->references('id')->on('users')->cascadeOnDelete();

            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }
};
