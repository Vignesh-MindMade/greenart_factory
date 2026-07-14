<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Filament\Resources\BlogPosts\BlogPostsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogPosts extends CreateRecord
{
    protected static string $resource = BlogPostsResource::class;
}
