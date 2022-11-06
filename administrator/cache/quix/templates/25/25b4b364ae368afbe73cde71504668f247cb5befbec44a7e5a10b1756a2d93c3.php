<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* raw-html/partials/style.twig */
class __TwigTemplate_1d30aa72c4250c086f2e6b04beeeb0b1f9cb0be2f2b716ba68182b05b37adb44 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        $this->loadTemplate("global.twig", "raw-html/partials/style.twig", 1)->display($context);
    }

    public function getTemplateName()
    {
        return "raw-html/partials/style.twig";
    }

    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "raw-html/partials/style.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/raw-html/partials/style.twig");
    }
}
