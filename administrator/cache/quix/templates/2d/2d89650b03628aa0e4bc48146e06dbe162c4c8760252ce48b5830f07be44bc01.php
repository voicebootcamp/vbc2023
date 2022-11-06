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

/* row/partials/style.twig */
class __TwigTemplate_e6c12d83ff03e37298b1866db1d1c170ff2fec19733a3a7394c7c2e82e007c0c extends \Twig\Template
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
        $context["id"] = ("#" . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "identifier", [], "any", false, false, false, 1), "id", [], "any", false, false, false, 1));
        // line 2
        $context["wrapper"] = (($context["id"] ?? null) . " > .qx-row");
        // line 3
        $context["css"] = "";
        // line 4
        $context["backgroundOverlay"] = (((($context["id"] ?? null) . " .") . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "identifier", [], "any", false, false, false, 4), "id", [], "any", false, false, false, 4)) . "-background-overlay");
        // line 5
        echo "
";
        // line 7
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "margin", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "spacing_fields_group", [], "any", false, false, false, 7), "margin", [], "any", false, false, false, 7)], "method", false, false, false, 7), "html", null, true);
        echo "

";
        // line 10
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "padding", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "spacing_fields_group", [], "any", false, false, false, 10), "padding", [], "any", false, false, false, 10)], "method", false, false, false, 10), "html", null, true);
        echo "

";
        // line 13
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "z-index", 2 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "spacing_fields_group", [], "any", false, false, false, 13), "zindex", [], "any", false, false, false, 13)], "method", false, false, false, 13), "html", null, true);
        echo "

";
        // line 16
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "background", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "background_fields_group", [], "any", false, false, false, 16), "background", [], "any", false, false, false, 16)], "method", false, false, false, 16), "html", null, true);
        echo "

";
        // line 19
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "background", [0 => ($context["backgroundOverlay"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "background_overlay_fields_group", [], "any", false, false, false, 19), "background_overlay", [], "any", false, false, false, 19), 2 => ($context["id"] ?? null)], "method", false, false, false, 19), "html", null, true);
        echo "

";
        // line 22
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "border", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "border_fields_group", [], "any", false, false, false, 22), "border", [], "any", false, false, false, 22)], "method", false, false, false, 22), "html", null, true);
        echo "

";
        // line 25
        $context["heightType"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 25), "height", [], "any", false, false, false, 25);
        // line 26
        echo "
";
        // line 27
        if ((($context["heightType"] ?? null) == "custom")) {
            // line 28
            echo "    ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "minHeight", [0 => ($context["wrapper"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 28), "custom_height", [], "any", false, false, false, 28)], "method", false, false, false, 28), "html", null, true);
            echo "
";
        }
        // line 30
        echo "
";
        // line 32
        $context["rawCss"] = $this->env->getFilter('removeLines')->getCallable()(((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "custom_css", [], "any", false, true, false, 32), "code", [], "any", true, true, false, 32)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "custom_css", [], "any", false, true, false, 32), "code", [], "any", false, false, false, 32), "")) : ("")));
        // line 33
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "raw", [0 => ($context["rawCss"] ?? null)], "method", false, false, false, 33), "html", null, true);
        echo "
";
        // line 34
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "load", [0 => ($context["id"] ?? null)], "method", false, false, false, 34), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "row/partials/style.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  100 => 34,  96 => 33,  94 => 32,  91 => 30,  85 => 28,  83 => 27,  80 => 26,  78 => 25,  73 => 22,  68 => 19,  63 => 16,  58 => 13,  53 => 10,  48 => 7,  45 => 5,  43 => 4,  41 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "row/partials/style.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/row/partials/style.twig");
    }
}
