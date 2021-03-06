<?php

namespace SocialLinks\Metas;

class Twittercard extends MetaBase implements MetaInterface
{
    protected $prefix = 'twitter:';

    protected $characterLimits = [
        'title' => 65,
        'description' => 200,
    ];


    /**
     * {@inheritdoc}
     */
    protected function generateTags()
    {
        $this->addMeta('card', 'summary');

        $data = $this->page->get(array(
            'title',
            'image',
            'text' => 'description',
            'twitterUser' => 'site',
        ));

        foreach ($data as $property => $content) {
            if (!empty($content)) {
                $this->addMeta($property, $content);
            }
        }
    }
}
