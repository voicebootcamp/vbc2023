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

/* global.twig */
class __TwigTemplate_43a30c4639878640cf806e189940533f1e5fdc1514ba117c8d2e3af1b287bb71 extends \Twig\Template
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
        $context["css"] = "";
        // line 3
        echo "
";
        // line 5
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "margin", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "spacing_fields_group", [], "any", false, false, false, 5), "margin", [], "any", false, false, false, 5)], "method", false, false, false, 5), "html", null, true);
        echo "

";
        // line 8
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "padding", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "spacing_fields_group", [], "any", false, false, false, 8), "padding", [], "any", false, false, false, 8)], "method", false, false, false, 8), "html", null, true);
        echo "

";
        // line 11
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "z-index", 2 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "spacing_fields_group", [], "any", false, false, false, 11), "zindex", [], "any", false, false, false, 11)], "method", false, false, false, 11), "html", null, true);
        echo "

";
        // line 14
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "background", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "background_fields_group", [], "any", false, false, false, 14), "background", [], "any", false, false, false, 14)], "method", false, false, false, 14), "html", null, true);
        echo "

";
        // line 17
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "border", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "border_fields_group", [], "any", false, false, false, 17), "border", [], "any", false, false, false, 17)], "method", false, false, false, 17), "html", null, true);
        echo "

";
        // line 20
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 20), "global_position_width", [], "any", false, false, false, 20) == "full-width")) {
            // line 21
            echo "  ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "width", 2 => "100%"], "method", false, false, false, 21), "html", null, true);
            echo "
  ";
            // line 22
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "max-width", 2 => "100%"], "method", false, false, false, 22), "html", null, true);
            echo "

";
        } elseif ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 24
($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 24), "global_position_width", [], "any", false, false, false, 24) == "inline")) {
            // line 25
            echo "  ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "width", 2 => "auto"], "method", false, false, false, 25), "html", null, true);
            echo "
  ";
            // line 26
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "max-width", 2 => "100%"], "method", false, false, false, 26), "html", null, true);
            echo "
";
        } elseif ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 27
($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 27), "global_position_width", [], "any", false, false, false, 27) == "custom")) {
            // line 28
            echo "  ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 28), "custom_width", [], "any", false, false, false, 28), 2 => "width", 3 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 28), "custom_width", [], "any", false, false, false, 28), "unit", [], "any", false, false, false, 28)], "method", false, false, false, 28), "html", null, true);
            echo "
  ";
            // line 29
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 29), "custom_width", [], "any", false, false, false, 29), 2 => "max-width", 3 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 29), "custom_width", [], "any", false, false, false, 29), "unit", [], "any", false, false, false, 29)], "method", false, false, false, 29), "html", null, true);
            echo "
";
        }
        // line 31
        echo "
";
        // line 32
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 32), "global_position", [], "any", false, false, false, 32) != "default")) {
            // line 33
            echo "  ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "position", 2 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 33), "global_position", [], "any", false, false, false, 33)], "method", false, false, false, 33), "html", null, true);
            echo "
  ";
            // line 34
            if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 34), "horizontal", [], "any", false, false, false, 34) == "left")) {
                // line 35
                echo "    ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 35), "horizontal_offset", [], "any", false, false, false, 35), 2 => "left", 3 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 35), "horizontal_offset", [], "any", false, false, false, 35), "unit", [], "any", false, false, false, 35)], "method", false, false, false, 35), "html", null, true);
                echo "
  ";
            } elseif ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,             // line 36
($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 36), "horizontal", [], "any", false, false, false, 36) == "right")) {
                // line 37
                echo "    ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 37), "horizontal_offset", [], "any", false, false, false, 37), 2 => "right", 3 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 37), "horizontal_offset", [], "any", false, false, false, 37), "unit", [], "any", false, false, false, 37)], "method", false, false, false, 37), "html", null, true);
                echo "
  ";
            }
            // line 39
            echo "  ";
            if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 39), "vertical", [], "any", false, false, false, 39) == "top")) {
                // line 40
                echo "    ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 40), "vertical_offset", [], "any", false, false, false, 40), 2 => "top", 3 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 40), "vertical_offset", [], "any", false, false, false, 40), "unit", [], "any", false, false, false, 40)], "method", false, false, false, 40), "html", null, true);
                echo "
  ";
            } elseif ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,             // line 41
($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 41), "vertical", [], "any", false, false, false, 41) == "bottom")) {
                // line 42
                echo "    ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 42), "vertical_offset", [], "any", false, false, false, 42), 2 => "bottom", 3 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "positioning_fields_group", [], "any", false, false, false, 42), "vertical_offset", [], "any", false, false, false, 42), "unit", [], "any", false, false, false, 42)], "method", false, false, false, 42), "html", null, true);
                echo "
  ";
            }
        }
        // line 45
        echo "

";
        // line 47
        $context["rawCss"] = $this->env->getFilter('removeLines')->getCallable()(((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "custom_css_group", [], "any", false, true, false, 47), "custom_css", [], "any", false, true, false, 47), "code", [], "any", true, true, false, 47)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "custom_css_group", [], "any", false, true, false, 47), "custom_css", [], "any", false, true, false, 47), "code", [], "any", false, false, false, 47), "")) : ("")));
        // line 48
        echo "
";
        // line 50
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "raw", [0 => ($context["rawCss"] ?? null)], "method", false, false, false, 50), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "global.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  155 => 50,  152 => 48,  150 => 47,  146 => 45,  139 => 42,  137 => 41,  132 => 40,  129 => 39,  123 => 37,  121 => 36,  116 => 35,  114 => 34,  109 => 33,  107 => 32,  104 => 31,  99 => 29,  94 => 28,  92 => 27,  88 => 26,  83 => 25,  81 => 24,  76 => 22,  71 => 21,  69 => 20,  64 => 17,  59 => 14,  54 => 11,  49 => 8,  44 => 5,  41 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "global.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/shared/global.twig");
    }
}
