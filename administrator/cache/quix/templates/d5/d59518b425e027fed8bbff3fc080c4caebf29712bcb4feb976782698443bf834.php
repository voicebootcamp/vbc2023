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

/* icon-list/partials/style.twig */
class __TwigTemplate_13b193aa922d219cda013720ec577affe31169c804bdc5d6f1ffccae6e434861 extends \Twig\Template
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
        $this->loadTemplate("global.twig", "icon-list/partials/style.twig", 1)->display($context);
        // line 2
        echo "
";
        // line 3
        $context["id"] = ("#" . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "identifier", [], "any", false, false, false, 3), "id", [], "any", false, false, false, 3));
        // line 4
        echo "
";
        // line 6
        $context["textFont"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "spacing_setting", [], "any", false, false, false, 6), "typo_for_text", [], "any", false, false, false, 6);
        // line 7
        echo "
";
        // line 9
        $context["iconHoverColor"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "color_setting", [], "any", false, false, false, 9), "icon_hover", [], "any", false, false, false, 9);
        // line 10
        $context["textColor"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "color_setting", [], "any", false, false, false, 10), "text_color", [], "any", false, false, false, 10);
        // line 11
        $context["textHoverColor"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "color_setting", [], "any", false, false, false, 11), "text_hover", [], "any", false, false, false, 11);
        // line 12
        echo "
";
        // line 14
        $context["itemSpacing"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "spacing_setting", [], "any", false, false, false, 14), "item_spacing", [], "any", false, false, false, 14);
        // line 15
        $context["textIndent"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "spacing_setting", [], "any", false, false, false, 15), "text_indent", [], "any", false, false, false, 15);
        // line 16
        $context["itemPadding"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "spacing_setting", [], "any", false, false, false, 16), "item_padding", [], "any", false, false, false, 16);
        // line 17
        $context["iconBorder"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "spacing_setting", [], "any", false, false, false, 17), "border_for_icon", [], "any", false, false, false, 17);
        // line 18
        echo "
";
        // line 20
        $context["layoutItem"] = (($context["id"] ?? null) . " ul li ");
        // line 21
        $context["nameSelector"] = (($context["id"] ?? null) . " ul li");
        // line 22
        echo "
";
        // line 23
        $context["deskInlineLayout"] = (($context["id"] ?? null) . " ul.desktop-horizontal-layout ");
        // line 24
        $context["deskInlineSpace"] = (($context["id"] ?? null) . " ul.desktop-horizontal-layout li ");
        // line 25
        $context["deskListLayout"] = (($context["id"] ?? null) . " ul.desktop-vertical-layout ");
        // line 26
        $context["deskListSpace"] = (($context["id"] ?? null) . " ul.desktop-vertical-layout li ");
        // line 27
        echo "
";
        // line 28
        $context["tabInlineLayout"] = (($context["id"] ?? null) . " ul.tablet-horizontal-layout ");
        // line 29
        $context["tabInlineSpace"] = (($context["id"] ?? null) . " ul.tablet-horizontal-layout li ");
        // line 30
        $context["tabListLayout"] = (($context["id"] ?? null) . " ul.tablet-vertical-layout ");
        // line 31
        $context["tabListSpace"] = (($context["id"] ?? null) . " ul.tablet-vertical-layout li ");
        // line 32
        echo "
";
        // line 33
        $context["phnInlineLayout"] = (($context["id"] ?? null) . " ul.phone-horizontal-layout ");
        // line 34
        $context["phnInlineSpace"] = (($context["id"] ?? null) . " ul.phone-horizontal-layout li ");
        // line 35
        $context["phnListLayout"] = (($context["id"] ?? null) . " ul.phone-vertical-layout ");
        // line 36
        $context["phnListSpace"] = (($context["id"] ?? null) . " ul.phone-vertical-layout li ");
        // line 37
        echo "
";
        // line 38
        $context["iconSelector"] = (($context["id"] ?? null) . " ul li ");
        // line 39
        $context["iconColor"] = (($context["id"] ?? null) . " ul li i svg path ");
        // line 40
        echo "
";
        // line 42
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "desktop", [0 => ".qx-element-icon-list svg", 1 => "width: 30px;"], "method", false, false, false, 42), "html", null, true);
        echo "

";
        // line 45
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "typography", [0 => (($context["nameSelector"] ?? null) . " span.qx-icon-text"), 1 => ($context["textFont"] ?? null)], "method", false, false, false, 45), "html", null, true);
        echo "

";
        // line 48
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => (((((((($context["iconSelector"] ?? null) . ":hover i") . ",") . ($context["iconSelector"] ?? null)) . "a:hover i") . ",") . ($context["iconSelector"] ?? null)) . "a:hover i svg path"), 1 => "color", 2 => ($context["iconHoverColor"] ?? null)], "method", false, false, false, 48), "html", null, true);
        echo "
";
        // line 49
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => (($context["nameSelector"] ?? null) . " span.qx-icon-text"), 1 => "color", 2 => ($context["textColor"] ?? null)], "method", false, false, false, 49), "html", null, true);
        echo "
";
        // line 50
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ((((($context["nameSelector"] ?? null) . ":hover span.qx-icon-text") . ",") . ($context["nameSelector"] ?? null)) . "a:hover span"), 1 => "color", 2 => ($context["textHoverColor"] ?? null)], "method", false, false, false, 50), "html", null, true);
        echo "

";
        // line 53
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "desktop", [0 => ($context["deskListSpace"] ?? null), 1 => (("margin-bottom:" . twig_get_attribute($this->env, $this->source, ($context["itemSpacing"] ?? null), "desktop", [], "any", false, false, false, 53)) . twig_get_attribute($this->env, $this->source, ($context["itemSpacing"] ?? null), "unit", [], "any", false, false, false, 53))], "method", false, false, false, 53), "html", null, true);
        echo "
";
        // line 54
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "tablet", [0 => ($context["tabListSpace"] ?? null), 1 => (("margin-bottom:" . twig_get_attribute($this->env, $this->source, ($context["itemSpacing"] ?? null), "tablet", [], "any", false, false, false, 54)) . twig_get_attribute($this->env, $this->source, ($context["itemSpacing"] ?? null), "unit", [], "any", false, false, false, 54))], "method", false, false, false, 54), "html", null, true);
        echo "
";
        // line 55
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "phone", [0 => ($context["phnListSpace"] ?? null), 1 => (("margin-bottom:" . twig_get_attribute($this->env, $this->source, ($context["itemSpacing"] ?? null), "phone", [], "any", false, false, false, 55)) . twig_get_attribute($this->env, $this->source, ($context["itemSpacing"] ?? null), "unit", [], "any", false, false, false, 55))], "method", false, false, false, 55), "html", null, true);
        echo "

";
        // line 57
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "desktop", [0 => ($context["deskInlineSpace"] ?? null), 1 => (("margin-right:" . twig_get_attribute($this->env, $this->source, ($context["itemSpacing"] ?? null), "desktop", [], "any", false, false, false, 57)) . twig_get_attribute($this->env, $this->source, ($context["itemSpacing"] ?? null), "unit", [], "any", false, false, false, 57))], "method", false, false, false, 57), "html", null, true);
        echo "
";
        // line 58
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "tablet", [0 => ($context["tabInlineSpace"] ?? null), 1 => (("margin-right:" . twig_get_attribute($this->env, $this->source, ($context["itemSpacing"] ?? null), "tablet", [], "any", false, false, false, 58)) . twig_get_attribute($this->env, $this->source, ($context["itemSpacing"] ?? null), "unit", [], "any", false, false, false, 58))], "method", false, false, false, 58), "html", null, true);
        echo "
";
        // line 59
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "phone", [0 => ($context["phnInlineSpace"] ?? null), 1 => (("margin-right:" . twig_get_attribute($this->env, $this->source, ($context["itemSpacing"] ?? null), "phone", [], "any", false, false, false, 59)) . twig_get_attribute($this->env, $this->source, ($context["itemSpacing"] ?? null), "unit", [], "any", false, false, false, 59))], "method", false, false, false, 59), "html", null, true);
        echo "

";
        // line 61
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => (($context["iconSelector"] ?? null) . " .qx-icon-text"), 1 => ($context["textIndent"] ?? null), 2 => "margin-left", 3 => twig_get_attribute($this->env, $this->source, ($context["textIndent"] ?? null), "unit", [], "any", false, false, false, 61)], "method", false, false, false, 61), "html", null, true);
        echo "
";
        // line 62
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "padding", [0 => ($context["layoutItem"] ?? null), 1 => ($context["itemPadding"] ?? null)], "method", false, false, false, 62), "html", null, true);
        echo "
";
        // line 63
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => (($context["iconSelector"] ?? null) . " i"), 1 => "display", 2 => "block"], "method", false, false, false, 63), "html", null, true);
        echo "

";
        // line 66
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "border", [0 => ($context["layoutItem"] ?? null), 1 => ($context["iconBorder"] ?? null)], "method", false, false, false, 66), "html", null, true);
        echo "


";
        // line 69
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "load", [0 => ($context["id"] ?? null)], "method", false, false, false, 69), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "icon-list/partials/style.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  187 => 69,  181 => 66,  176 => 63,  172 => 62,  168 => 61,  163 => 59,  159 => 58,  155 => 57,  150 => 55,  146 => 54,  142 => 53,  137 => 50,  133 => 49,  129 => 48,  124 => 45,  119 => 42,  116 => 40,  114 => 39,  112 => 38,  109 => 37,  107 => 36,  105 => 35,  103 => 34,  101 => 33,  98 => 32,  96 => 31,  94 => 30,  92 => 29,  90 => 28,  87 => 27,  85 => 26,  83 => 25,  81 => 24,  79 => 23,  76 => 22,  74 => 21,  72 => 20,  69 => 18,  67 => 17,  65 => 16,  63 => 15,  61 => 14,  58 => 12,  56 => 11,  54 => 10,  52 => 9,  49 => 7,  47 => 6,  44 => 4,  42 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "icon-list/partials/style.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/icon-list/partials/style.twig");
    }
}
