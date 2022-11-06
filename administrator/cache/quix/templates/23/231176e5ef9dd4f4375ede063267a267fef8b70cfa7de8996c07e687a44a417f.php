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

/* dual-button/partials/style.twig */
class __TwigTemplate_0d27be2cb88d6354f6d4e1b720800867d472cba42ba1a48ce4498939bcd7c9ce extends \Twig\Template
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
        $this->loadTemplate("global.twig", "dual-button/partials/style.twig", 1)->display($context);
        // line 2
        echo "
";
        // line 3
        $context["id"] = ("#" . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "identifier", [], "any", false, false, false, 3), "id", [], "any", false, false, false, 3));
        // line 4
        echo "
";
        // line 6
        $context["btnPrimaryBgColor"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_primary_fields_group", [], "any", false, false, false, 6), "btn_primary_bg_color", [], "any", false, false, false, 6);
        // line 7
        $context["btnSecondaryBgColor"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_secondary_fields_group", [], "any", false, false, false, 7), "btn_secondary_bg_color", [], "any", false, false, false, 7);
        // line 8
        $context["btnConnectorBgColor"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_connector_fields_group", [], "any", false, false, false, 8), "btn_connector_bg_color", [], "any", false, false, false, 8);
        // line 9
        echo "
";
        // line 11
        $context["btnPrimaryTextColor"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_primary_fields_group", [], "any", false, false, false, 11), "btn_primary_text_color", [], "any", false, false, false, 11);
        // line 12
        $context["btnSecondaryTextColor"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_secondary_fields_group", [], "any", false, false, false, 12), "btn_secondary_text_color", [], "any", false, false, false, 12);
        // line 13
        $context["btnConnectorTextColor"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_connector_fields_group", [], "any", false, false, false, 13), "btn_connector_text_color", [], "any", false, false, false, 13);
        // line 14
        echo "
";
        // line 16
        $context["btnPrimaryTextHoverColor"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_primary_fields_group", [], "any", false, false, false, 16), "btn_primary_text_hover_color", [], "any", false, false, false, 16);
        // line 17
        $context["btnSecondaryTextHoverColor"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_secondary_fields_group", [], "any", false, false, false, 17), "btn_secondary_text_hover_color", [], "any", false, false, false, 17);
        // line 18
        $context["btnConnectorTextHoverColor"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_connector_fields_group", [], "any", false, false, false, 18), "btn_connector_text_hover_color", [], "any", false, false, false, 18);
        // line 19
        echo "
";
        // line 21
        $context["btnTypography"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_common_fields_group", [], "any", false, false, false, 21), "btn_typography", [], "any", false, false, false, 21);
        // line 22
        $context["btnPrimaryTypo"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_primary_fields_group", [], "any", false, false, false, 22), "btn_primary_typo", [], "any", false, false, false, 22);
        // line 23
        $context["btnSecondaryTypo"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_secondary_fields_group", [], "any", false, false, false, 23), "btn_secondary_typo", [], "any", false, false, false, 23);
        // line 24
        $context["btnConnectorTypo"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_connector_fields_group", [], "any", false, false, false, 24), "btn_connector_typo", [], "any", false, false, false, 24);
        // line 25
        echo "
";
        // line 27
        $context["btnPadding"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_common_fields_group", [], "any", false, false, false, 27), "btn_padding", [], "any", false, false, false, 27);
        // line 28
        echo "
";
        // line 30
        $context["btnDualSpacing"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_common_fields_group", [], "any", false, false, false, 30), "btn_space", [], "any", false, false, false, 30);
        // line 31
        echo "
";
        // line 33
        $context["iconPriSpacing"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_primary_fields_group", [], "any", false, false, false, 33), "icon_primary_spacing", [], "any", false, false, false, 33);
        // line 34
        $context["iconPrimaryHoverColor"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_primary_fields_group", [], "any", false, false, false, 34), "icon_primary_hover_color", [], "any", false, false, false, 34);
        // line 35
        echo "
";
        // line 37
        $context["iconSecSpacing"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_secondary_fields_group", [], "any", false, false, false, 37), "icon_secondary_spacing", [], "any", false, false, false, 37);
        // line 38
        $context["iconSecondaryHoverColor"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_secondary_fields_group", [], "any", false, false, false, 38), "icon_secondary_hover_color", [], "any", false, false, false, 38);
        // line 39
        echo "
";
        // line 41
        $context["btnConnWidthHeight"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_connector_fields_group", [], "any", false, false, false, 41), "btn_connector_width_height", [], "any", false, false, false, 41);
        // line 42
        echo "
";
        // line 44
        $context["btnPrimaryBorder"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_primary_fields_group", [], "any", false, false, false, 44), "btn_primary_border", [], "any", false, false, false, 44);
        // line 45
        $context["btnSecondaryBorder"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_secondary_fields_group", [], "any", false, false, false, 45), "btn_secondary_border", [], "any", false, false, false, 45);
        // line 46
        $context["btnConnectorBorder"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "button_connector_fields_group", [], "any", false, false, false, 46), "btn_connector_border", [], "any", false, false, false, 46);
        // line 47
        echo "
";
        // line 49
        $context["btnFirst"] = " .btn-first a";
        // line 50
        $context["btnSecond"] = " .btn-second a";
        // line 51
        $context["connectorWrapperSelector"] = " .connector-wrapper";
        // line 52
        $context["connectorSelector"] = " .connector-wrapper .connector-text";
        // line 53
        $context["connectorIcon"] = " .connector-wrapper i";
        // line 54
        $context["btnWrapper"] = " .qx-element-dual-button-wrapper";
        // line 55
        $context["iconPriSelector"] = (((($context["id"] ?? null) . " .btn-first a svg,") . ($context["id"] ?? null)) . " .btn-first a i");
        // line 56
        $context["iconPriLeftSelector"] = (((($context["id"] ?? null) . " .btn-first a .qx-flex-row svg,") . ($context["id"] ?? null)) . " .btn-first a .qx-flex-row i");
        // line 57
        $context["iconPriRightSelector"] = (((($context["id"] ?? null) . " .btn-first a .qx-flex-row-reverse svg,") . ($context["id"] ?? null)) . " .btn-first a .qx-flex-row-reverse i");
        // line 58
        $context["iconPriHovSelector"] = (((($context["id"] ?? null) . " .btn-first a:hover svg,") . ($context["id"] ?? null)) . " .btn-first a:hover i");
        // line 59
        $context["iconSecSelector"] = (((($context["id"] ?? null) . " .btn-second a svg,") . ($context["id"] ?? null)) . " .btn-second a i");
        // line 60
        $context["iconSecLeftSelector"] = (((($context["id"] ?? null) . " .btn-second a .qx-flex-row svg,") . ($context["id"] ?? null)) . " .btn-second a .qx-flex-row i");
        // line 61
        $context["iconSecRightSelector"] = (((($context["id"] ?? null) . " .btn-second a .qx-flex-row-reverse svg,") . ($context["id"] ?? null)) . " .btn-second a .qx-flex-row-reverse i");
        // line 62
        $context["iconSecHovSelector"] = (((($context["id"] ?? null) . " .btn-second a:hover svg,") . ($context["id"] ?? null)) . " .btn-second a:hover i");
        // line 63
        $context["iconConnSelector"] = (((((($context["id"] ?? null) . ($context["connectorWrapperSelector"] ?? null)) . " svg,") . ($context["id"] ?? null)) . ($context["connectorWrapperSelector"] ?? null)) . " i");
        // line 64
        $context["hrLayout"] = (($context["id"] ?? null) . " .qx-element-dual-button-horizontal");
        // line 65
        $context["vrLayout"] = (($context["id"] ?? null) . " .qx-element-dual-button-vertical");
        // line 66
        echo "
";
        // line 68
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "background", [0 => (($context["id"] ?? null) . ($context["btnFirst"] ?? null)), 1 => ($context["btnPrimaryBgColor"] ?? null)], "method", false, false, false, 68), "html", null, true);
        echo "
";
        // line 69
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "background", [0 => (($context["id"] ?? null) . ($context["btnSecond"] ?? null)), 1 => ($context["btnSecondaryBgColor"] ?? null)], "method", false, false, false, 69), "html", null, true);
        echo "
";
        // line 70
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "background", [0 => (($context["id"] ?? null) . ($context["connectorSelector"] ?? null)), 1 => ($context["btnConnectorBgColor"] ?? null)], "method", false, false, false, 70), "html", null, true);
        echo "

";
        // line 73
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ((($context["id"] ?? null) . ($context["btnFirst"] ?? null)) . " span"), 1 => "color", 2 => ($context["btnPrimaryTextColor"] ?? null)], "method", false, false, false, 73), "html", null, true);
        echo "
";
        // line 74
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ((($context["id"] ?? null) . ($context["btnSecond"] ?? null)) . " span"), 1 => "color", 2 => ($context["btnSecondaryTextColor"] ?? null)], "method", false, false, false, 74), "html", null, true);
        echo "
";
        // line 75
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => (($context["id"] ?? null) . ($context["connectorSelector"] ?? null)), 1 => "color", 2 => ($context["btnConnectorTextColor"] ?? null)], "method", false, false, false, 75), "html", null, true);
        echo "

";
        // line 78
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ((($context["id"] ?? null) . ($context["btnFirst"] ?? null)) . ":hover span"), 1 => "color", 2 => ($context["btnPrimaryTextHoverColor"] ?? null)], "method", false, false, false, 78), "html", null, true);
        echo "
";
        // line 79
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ((($context["id"] ?? null) . ($context["btnSecond"] ?? null)) . ":hover span"), 1 => "color", 2 => ($context["btnSecondaryTextHoverColor"] ?? null)], "method", false, false, false, 79), "html", null, true);
        echo "
";
        // line 80
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ((($context["id"] ?? null) . ($context["connectorSelector"] ?? null)) . ":hover"), 1 => "color", 2 => ($context["btnConnectorTextHoverColor"] ?? null)], "method", false, false, false, 80), "html", null, true);
        echo "
";
        // line 81
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => (($context["iconConnSelector"] ?? null) . ":hover"), 1 => "color", 2 => ($context["btnConnectorTextHoverColor"] ?? null)], "method", false, false, false, 81), "html", null, true);
        echo "

";
        // line 84
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "typography", [0 => (($context["id"] ?? null) . " .qx-btn"), 1 => ($context["btnTypography"] ?? null)], "method", false, false, false, 84), "html", null, true);
        echo "
";
        // line 85
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "typography", [0 => (($context["id"] ?? null) . ($context["btnFirst"] ?? null)), 1 => ($context["btnPrimaryTypo"] ?? null)], "method", false, false, false, 85), "html", null, true);
        echo "
";
        // line 86
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "typography", [0 => (($context["id"] ?? null) . ($context["btnSecond"] ?? null)), 1 => ($context["btnSecondaryTypo"] ?? null)], "method", false, false, false, 86), "html", null, true);
        echo "
";
        // line 87
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "typography", [0 => (($context["id"] ?? null) . ($context["connectorSelector"] ?? null)), 1 => ($context["btnConnectorTypo"] ?? null)], "method", false, false, false, 87), "html", null, true);
        echo "

";
        // line 90
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "padding", [0 => (($context["id"] ?? null) . " .qx-btn"), 1 => ($context["btnPadding"] ?? null)], "method", false, false, false, 90), "html", null, true);
        echo "

";
        // line 93
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => (($context["hrLayout"] ?? null) . ($context["btnFirst"] ?? null)), 1 => ($context["btnDualSpacing"] ?? null), 2 => "margin-right", 3 => twig_get_attribute($this->env, $this->source, ($context["btnDualSpacing"] ?? null), "unit", [], "any", false, false, false, 93)], "method", false, false, false, 93), "html", null, true);
        echo "
";
        // line 94
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => (($context["hrLayout"] ?? null) . ($context["btnSecond"] ?? null)), 1 => ($context["btnDualSpacing"] ?? null), 2 => "margin-left", 3 => twig_get_attribute($this->env, $this->source, ($context["btnDualSpacing"] ?? null), "unit", [], "any", false, false, false, 94)], "method", false, false, false, 94), "html", null, true);
        echo "
";
        // line 95
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => (($context["vrLayout"] ?? null) . ($context["btnFirst"] ?? null)), 1 => ($context["btnDualSpacing"] ?? null), 2 => "margin-bottom", 3 => twig_get_attribute($this->env, $this->source, ($context["btnDualSpacing"] ?? null), "unit", [], "any", false, false, false, 95)], "method", false, false, false, 95), "html", null, true);
        echo "
";
        // line 96
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => (($context["vrLayout"] ?? null) . ($context["btnSecond"] ?? null)), 1 => ($context["btnDualSpacing"] ?? null), 2 => "margin-top", 3 => twig_get_attribute($this->env, $this->source, ($context["btnDualSpacing"] ?? null), "unit", [], "any", false, false, false, 96)], "method", false, false, false, 96), "html", null, true);
        echo "

";
        // line 99
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => ($context["iconPriLeftSelector"] ?? null), 1 => ($context["iconPriSpacing"] ?? null), 2 => "margin-right", 3 => twig_get_attribute($this->env, $this->source, ($context["iconPriSpacing"] ?? null), "unit", [], "any", false, false, false, 99)], "method", false, false, false, 99), "html", null, true);
        echo "
";
        // line 100
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => ($context["iconPriRightSelector"] ?? null), 1 => ($context["iconPriSpacing"] ?? null), 2 => "margin-left", 3 => twig_get_attribute($this->env, $this->source, ($context["iconPriSpacing"] ?? null), "unit", [], "any", false, false, false, 100)], "method", false, false, false, 100), "html", null, true);
        echo "
";
        // line 101
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["iconPriHovSelector"] ?? null), 1 => "color", 2 => ($context["iconPrimaryHoverColor"] ?? null)], "method", false, false, false, 101), "html", null, true);
        echo "

";
        // line 104
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => ($context["iconSecLeftSelector"] ?? null), 1 => ($context["iconSecSpacing"] ?? null), 2 => "margin-right", 3 => twig_get_attribute($this->env, $this->source, ($context["iconSecSpacing"] ?? null), "unit", [], "any", false, false, false, 104)], "method", false, false, false, 104), "html", null, true);
        echo "
";
        // line 105
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => ($context["iconSecRightSelector"] ?? null), 1 => ($context["iconSecSpacing"] ?? null), 2 => "margin-left", 3 => twig_get_attribute($this->env, $this->source, ($context["iconSecSpacing"] ?? null), "unit", [], "any", false, false, false, 105)], "method", false, false, false, 105), "html", null, true);
        echo "
";
        // line 106
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["iconSecHovSelector"] ?? null), 1 => "color", 2 => ($context["iconSecondaryHoverColor"] ?? null)], "method", false, false, false, 106), "html", null, true);
        echo "

";
        // line 109
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "width", [0 => ($context["connectorWrapperSelector"] ?? null), 1 => ($context["btnConnWidthHeight"] ?? null)], "method", false, false, false, 109), "html", null, true);
        echo "
";
        // line 110
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "height", [0 => ($context["connectorWrapperSelector"] ?? null), 1 => ($context["btnConnWidthHeight"] ?? null)], "method", false, false, false, 110), "html", null, true);
        echo "
";
        // line 111
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => ($context["connectorWrapperSelector"] ?? null), 1 => ($context["btnConnWidthHeight"] ?? null), 2 => "line-height", 3 => twig_get_attribute($this->env, $this->source, ($context["btnConnWidthHeight"] ?? null), "unit", [], "any", false, false, false, 111)], "method", false, false, false, 111), "html", null, true);
        echo "

";
        // line 113
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["iconConnSelector"] ?? null), 1 => "line-height", 2 => "inherit"], "method", false, false, false, 113), "html", null, true);
        echo "

";
        // line 116
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "border", [0 => (($context["id"] ?? null) . ($context["btnFirst"] ?? null)), 1 => ($context["btnPrimaryBorder"] ?? null)], "method", false, false, false, 116), "html", null, true);
        echo "
";
        // line 117
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "border", [0 => (($context["id"] ?? null) . ($context["btnSecond"] ?? null)), 1 => ($context["btnSecondaryBorder"] ?? null)], "method", false, false, false, 117), "html", null, true);
        echo "
";
        // line 118
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "border", [0 => (($context["id"] ?? null) . ($context["connectorSelector"] ?? null)), 1 => ($context["btnConnectorBorder"] ?? null)], "method", false, false, false, 118), "html", null, true);
        echo "

";
        // line 120
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "load", [0 => ($context["id"] ?? null)], "method", false, false, false, 120), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "dual-button/partials/style.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  299 => 120,  294 => 118,  290 => 117,  286 => 116,  281 => 113,  276 => 111,  272 => 110,  268 => 109,  263 => 106,  259 => 105,  255 => 104,  250 => 101,  246 => 100,  242 => 99,  237 => 96,  233 => 95,  229 => 94,  225 => 93,  220 => 90,  215 => 87,  211 => 86,  207 => 85,  203 => 84,  198 => 81,  194 => 80,  190 => 79,  186 => 78,  181 => 75,  177 => 74,  173 => 73,  168 => 70,  164 => 69,  160 => 68,  157 => 66,  155 => 65,  153 => 64,  151 => 63,  149 => 62,  147 => 61,  145 => 60,  143 => 59,  141 => 58,  139 => 57,  137 => 56,  135 => 55,  133 => 54,  131 => 53,  129 => 52,  127 => 51,  125 => 50,  123 => 49,  120 => 47,  118 => 46,  116 => 45,  114 => 44,  111 => 42,  109 => 41,  106 => 39,  104 => 38,  102 => 37,  99 => 35,  97 => 34,  95 => 33,  92 => 31,  90 => 30,  87 => 28,  85 => 27,  82 => 25,  80 => 24,  78 => 23,  76 => 22,  74 => 21,  71 => 19,  69 => 18,  67 => 17,  65 => 16,  62 => 14,  60 => 13,  58 => 12,  56 => 11,  53 => 9,  51 => 8,  49 => 7,  47 => 6,  44 => 4,  42 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "dual-button/partials/style.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/dual-button/partials/style.twig");
    }
}
