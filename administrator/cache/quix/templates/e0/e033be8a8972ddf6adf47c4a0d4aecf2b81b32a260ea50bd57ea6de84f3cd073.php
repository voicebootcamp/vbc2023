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

/* column/partials/style.twig */
class __TwigTemplate_6efb8bd6224da8de87e53363b1008281de29128d8a15921f0f6cdb3c6a8e162e extends \Twig\Template
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
        $context["wrapper"] = (("#" . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "identifier", [], "any", false, false, false, 2), "id", [], "any", false, false, false, 2)) . " > .qx-col-wrap");
        // line 3
        $context["css"] = "";
        // line 4
        $context["backgroundOverlay"] = (((($context["id"] ?? null) . ".") . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "identifier", [], "any", false, false, false, 4), "id", [], "any", false, false, false, 4)) . "-background-overlay");
        // line 5
        $context["elementBuilderSelector"] = (($context["id"] ?? null) . " .qx-fb-elements .qx-fb-element+.qx-fb-element");
        // line 6
        $context["elementSelector"] = (($context["id"] ?? null) . " .qx-element-wrap+.qx-element-wrap");
        // line 7
        $context["width"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 7), "col_width", [], "any", false, false, false, 7);
        // line 8
        echo "
";
        // line 10
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "margin", [0 => ($context["wrapper"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "spacing_fields_group", [], "any", false, false, false, 10), "margin", [], "any", false, false, false, 10)], "method", false, false, false, 10), "html", null, true);
        echo "

";
        // line 13
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "padding", [0 => ($context["wrapper"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "spacing_fields_group", [], "any", false, false, false, 13), "padding", [], "any", false, false, false, 13)], "method", false, false, false, 13), "html", null, true);
        echo "

";
        // line 16
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "z-index", 2 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "spacing_fields_group", [], "any", false, false, false, 16), "zindex", [], "any", false, false, false, 16)], "method", false, false, false, 16), "html", null, true);
        echo "

";
        // line 19
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "background", [0 => ($context["wrapper"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "background_fields_group", [], "any", false, false, false, 19), "background", [], "any", false, false, false, 19)], "method", false, false, false, 19), "html", null, true);
        echo "

";
        // line 22
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "background", [0 => ($context["backgroundOverlay"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "background_overlay_fields_group", [], "any", false, false, false, 22), "background_overlay", [], "any", false, false, false, 22), 2 => ($context["id"] ?? null)], "method", false, false, false, 22), "html", null, true);
        echo "

";
        // line 25
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "border", [0 => ($context["wrapper"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "border_fields_group", [], "any", false, false, false, 25), "border", [], "any", false, false, false, 25)], "method", false, false, false, 25), "html", null, true);
        echo "

";
        // line 27
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => ($context["elementBuilderSelector"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 27), "element_spacing", [], "any", false, false, false, 27), 2 => "margin-top", 3 => "px"], "method", false, false, false, 27), "html", null, true);
        echo "
";
        // line 28
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => ($context["elementSelector"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 28), "element_spacing", [], "any", false, false, false, 28), 2 => "margin-top", 3 => "px"], "method", false, false, false, 28), "html", null, true);
        echo "


";
        // line 32
        $context["rawStyle"] = $this->env->getFunction('replaceAll')->getCallable()(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "custom_css_group", [], "any", false, false, false, 32), "custom_css", [], "any", false, false, false, 32), "code", [], "any", false, false, false, 32), "selector", ($context["id"] ?? null));
        // line 33
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "raw", [0 => ($context["rawStyle"] ?? null)], "method", false, false, false, 33), "html", null, true);
        echo "

";
        // line 36
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "width", [0 => ($context["id"] ?? null), 1 => ($context["width"] ?? null)], "method", false, false, false, 36), "html", null, true);
        echo "

";
        // line 38
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "load", [0 => ($context["id"] ?? null)], "method", false, false, false, 38), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "column/partials/style.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  106 => 38,  101 => 36,  96 => 33,  94 => 32,  88 => 28,  84 => 27,  79 => 25,  74 => 22,  69 => 19,  64 => 16,  59 => 13,  54 => 10,  51 => 8,  49 => 7,  47 => 6,  45 => 5,  43 => 4,  41 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "column/partials/style.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/column/partials/style.twig");
    }
}
