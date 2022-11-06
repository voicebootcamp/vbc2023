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

/* text/partials/style.twig */
class __TwigTemplate_5f9610ccaac5c213b684414aea29aab853b6a11301ed0fd174e040bb9320e548 extends \Twig\Template
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
        $this->loadTemplate("global.twig", "text/partials/style.twig", 1)->display($context);
        // line 2
        echo "
";
        // line 3
        $context["id"] = ("#" . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "identifier", [], "any", false, false, false, 3), "id", [], "any", false, false, false, 3));
        // line 4
        echo "
";
        // line 6
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "typography", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "typo_fields_group", [], "any", false, false, false, 6), "font", [], "any", false, false, false, 6)], "method", false, false, false, 6), "html", null, true);
        echo "

";
        // line 9
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "alignment", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "text_fields_group", [], "any", false, false, false, 9), "alignment", [], "any", false, false, false, 9)], "method", false, false, false, 9), "html", null, true);
        echo "

";
        // line 12
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "color", 2 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "text_fields_group", [], "any", false, false, false, 12), "text_color", [], "any", false, false, false, 12)], "method", false, false, false, 12), "html", null, true);
        echo "

";
        // line 14
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "load", [0 => ($context["id"] ?? null)], "method", false, false, false, 14), "html", null, true);
    }

    public function getTemplateName()
    {
        return "text/partials/style.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 14,  57 => 12,  52 => 9,  47 => 6,  44 => 4,  42 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "text/partials/style.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/text/partials/style.twig");
    }
}
