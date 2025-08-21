<?php

namespace App\Markdown;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link as LinkNode;
use Spatie\LaravelMarkdown\MarkdownRenderer;

class CustomMarkdownRenderer extends MarkdownRenderer
{
    public function configureCommonMarkEnvironment(EnvironmentBuilderInterface $environment): void
    {
        parent::configureCommonMarkEnvironment($environment);

        $environment->addRenderer(LinkNode::class, new LinkRenderer, 1000);
    }
}
