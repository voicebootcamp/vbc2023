<?php

namespace QuixNxt\Concerns;

trait CanProvideTemplate
{
    /**
     * @return array
     *
     * @since 3.0.0
     */
    public function getTemplates(): array
    {
        $this->loadGlobals();

        return [
            'html'         => $this->getHtmlTemplate(),
            'style'        => $this->getStyleTemplate(),
            'script'       => $this->getScriptTemplate(),
            'macro'        => $this->getMacroTemplate(),
            'schema'       => $this->getSchema(),
            'default_node' => $this->getDefaultNode(),
        ];
    }

    /**
     * @return string|null
     *
     * @since 3.0.0
     */
    private function getHtmlTemplate(): ?string
    {
        if ( ! $this->hasHtml()) {
            return null;
        }

        return file_get_contents($this->getElementPath().'/partials/html.twig');
    }

    /**
     * @return string|null
     *
     * @since 3.0.0
     */
    private function getStyleTemplate(): ?string
    {
        if ( ! $this->hasStyle()) {
            return null;
        }

        return file_get_contents($this->getElementPath().'/partials/style.twig');
    }

    /**
     * @return string|null
     *
     * @since 3.0.0
     */
    private function getScriptTemplate(): ?string
    {
        if ( ! $this->hasScript()) {
            return null;
        }

        return file_get_contents($this->getElementPath().'/partials/script.twig');
    }

    /**
     * @return string|null
     *
     * @since 3.0.0
     */
    private function getMacroTemplate(): ?string
    {
        if ( ! $this->hasMacro()) {
            return null;
        }

        return file_get_contents($this->getElementPath('partials/macro.twig'));
    }

    /**
     *
     * @since 3.0.0
     */
    private function loadGlobals(): void
    {
        if ($this->fileExists('global.php')) {
            include $this->getElementPath('global.php');
        }
    }

    /**
     * @return bool
     *
     * @since 3.0.0
     */
    private function hasMacro(): bool
    {
        return $this->fileExists('partials/macro.twig');
    }
}
