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

/* video/partials/style.twig */
class __TwigTemplate_5dda3c868cea5998a9437fd479a2296d65a90b40dbc633c996faac3aadb1bcb6 extends \Twig\Template
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
        $this->loadTemplate("global.twig", "video/partials/style.twig", 1)->display($context);
        // line 2
        echo "
";
        // line 3
        $context["id"] = ("#" . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "identifier", [], "any", false, false, false, 3), "id", [], "any", false, false, false, 3));
        // line 4
        echo "
";
        // line 6
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "height", [0 => (((((($context["id"] ?? null) . " .plyr,") . ($context["id"] ?? null)) . " iframe,") . ($context["id"] ?? null)) . " .plyr__video-wrapper"), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "custom_options", [], "any", false, false, false, 6), "height", [], "any", false, false, false, 6)], "method", false, false, false, 6), "html", null, true);
        echo "

";
        // line 9
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "width", [0 => (($context["id"] ?? null) . " .plyr"), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "custom_options", [], "any", false, false, false, 9), "width", [], "any", false, false, false, 9)], "method", false, false, false, 9), "html", null, true);
        echo "

";
        // line 11
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => (($context["id"] ?? null) . " .plyr"), 1 => "margin", 2 => "0 auto"], "method", false, false, false, 11), "html", null, true);
        echo "

";
        // line 13
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "load", [0 => ($context["id"] ?? null)], "method", false, false, false, 13), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "video/partials/style.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 13,  57 => 11,  52 => 9,  47 => 6,  44 => 4,  42 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "video/partials/style.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/video/partials/style.twig");
    }
}
