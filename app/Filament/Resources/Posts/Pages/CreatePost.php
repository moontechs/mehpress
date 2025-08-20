<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Constants;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Posts\Schemas\PostForm;
use App\Filament\Resources\Posts\Schemas\ShortPostForm;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    public ?string $postType = null;

    public ?int $blogId = null;

    public function mount(): void
    {
        // Store the post type as component property to persist across requests
        $this->postType = request()->query('type') === Constants::SHORT_POST_TYPE ? Constants::SHORT_POST_TYPE : 'post';
        $this->blogId = request()->query('blog_id');

        parent::mount();

        if ($this->postType === Constants::SHORT_POST_TYPE) {
            $this->form->fill(['type' => Constants::SHORT_POST_TYPE]);
        }
    }

    public function form(Schema $schema): Schema
    {
        return $this->postType === Constants::SHORT_POST_TYPE ?
            ShortPostForm::configure($schema) :
            PostForm::configure($schema);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($this->postType === Constants::SHORT_POST_TYPE) {
            $data['type'] = Constants::SHORT_POST_TYPE;
            $data['title'] = 'temp-'.uniqid();
            $data['slug'] = 'temp-'.uniqid();
        }

        if (! empty($this->blogId)) {
            $data['blog_id'] = $this->blogId;
        }

        return $data;
    }
}
