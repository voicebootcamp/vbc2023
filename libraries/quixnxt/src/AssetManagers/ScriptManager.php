<?php

namespace QuixNxt\AssetManagers;

use QuixNxt\Engine\Foundation\AssetManager;

class ScriptManager extends AssetManager
{
    protected $webfontConfig = [
        'shouldLoad' => false,
        'families'   => [],
    ];

    /**
     *
     * @param  string  $family
     *
     * @since 3.0.0
     */
    public function loadWebfont(string $family): void
    {
        $this->webfontConfig['shouldLoad'] = true;
        $this->webfontConfig['families'][] = $family;
    }

    /**
     * @return string
     *
     * @since 3.0.0
     */
    public function compile(): string
    {
        $scripts = parent::compile();

        return preg_replace('/\s+/', ' ', $scripts);
    }

    public function load(string $id): string
    {
        return '';
    }

    /**
     * @return string|null
     * @since 3.0.0
     */
    public function getWebFonts(): ?string
    {

        if ($this->webfontConfig['shouldLoad']) {
            $families = json_encode($this->webfontConfig['families']);
            $script   = ";var qWebfont = document.createElement('script');";
            $script   .= "qWebfont.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js';";
            $script   .= "qWebfont.onload = () => WebFont.load({ google: { families: {$families} } });";
            $script   .= "document.head.appendChild(qWebfont);";

            return $script;

            //return ";setTimeout(function(){jQuery.getScript('https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js', function () { WebFont.load({ google: { families: {$families} } }); });}, 2000)";
        }

        return null;
    }
}
