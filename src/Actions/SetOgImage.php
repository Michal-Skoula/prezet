<?php

namespace BenBjurstrom\Prezet\Actions;

use BenBjurstrom\Prezet\Exceptions\FrontmatterMissingException;
use BenBjurstrom\Prezet\Models\Document;
use BenBjurstrom\Prezet\Prezet;
use Illuminate\Support\Facades\Storage;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;

class SetOgImage
{
    public function handle(Document $doc, string $imgPath): void
    {
        $md = Prezet::getMarkdown($doc->filepath);
        $content = Prezet::parseMarkdown($md);

        if (! $content instanceof RenderedContentWithFrontMatter) {
            throw new FrontmatterMissingException($doc->filepath);
        }
        $fm = $content->getFrontMatter();
        if (! $fm || ! is_array($fm)) {
            throw new FrontmatterMissingException($doc->filepath);
        }

        $fm['image'] = $imgPath;
        $newMd = Prezet::setFrontmatter($md, $fm);

        $storage = Storage::disk(Prezet::getPrezetDisk());
        $storage->put($doc->filepath, $newMd);
    }
}
