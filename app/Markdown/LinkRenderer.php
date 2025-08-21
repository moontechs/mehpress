<?php

namespace App\Markdown;

use App\Models\Link;
use Illuminate\Support\Facades\View;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link as LinkNode;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

class LinkRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable
    {
        if (! $node instanceof LinkNode) {
            throw new \InvalidArgumentException('Incompatible node type: '.get_class($node));
        }

        $url = $node->getUrl();
        $linkText = $childRenderer->renderNodes($node->children());

        $link = Link::where('url', $url)->first();

        if ($link && $link->metadata) {
            return $this->renderRichLink($link, $linkText);
        }

        return $this->renderRegularLink($node, $childRenderer);
    }

    private function renderRichLink(Link $link, string $linkText): HtmlElement
    {
        $metadata = $link->metadata;

        $title = $metadata['og:title'] ?? $metadata['title'] ?? $linkText;
        $description = $metadata['og:description'] ?? $metadata['description'] ?? '';
        $image = $metadata['og:image'] ?? null;
        $siteName = $metadata['og:site_name'] ?? '';

        if (strlen($description) > 160) {
            $description = mb_substr($description, 0, 157).'...';
        }

        $html = View::make('components.link-preview', [
            'url' => $link->url,
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'siteName' => $siteName,
        ])->render();

        return new HtmlElement('div', [], $html);
    }

    private function renderRegularLink(LinkNode $node, ChildNodeRendererInterface $childRenderer): HtmlElement
    {
        $attrs = $node->data->get('attributes') ?? [];
        $attrs['href'] = $node->getUrl();

        if (isset($node->data['title'])) {
            $attrs['title'] = $node->data['title'];
        }

        return new HtmlElement('a', $attrs, $childRenderer->renderNodes($node->children()));
    }
}
