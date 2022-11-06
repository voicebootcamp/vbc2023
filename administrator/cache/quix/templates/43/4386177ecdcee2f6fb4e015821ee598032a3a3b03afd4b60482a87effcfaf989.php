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

/* section/partials/style.twig */
class __TwigTemplate_3fa361da4e2055ee4775e5dc44e0737b2d1c9edfff387ac1654ea104ebc145c9 extends \Twig\Template
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
        $context["backgroundOverlay"] = (("." . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "identifier", [], "any", false, false, false, 3), "id", [], "any", false, false, false, 3)) . "-background-overlay.qx-background-overlay");
        // line 4
        $context["topDivider"] = (($context["id"] ?? null) . " .qx-shape-top");
        // line 5
        $context["topDividerSvg"] = (($context["topDivider"] ?? null) . " svg");
        // line 6
        $context["bottomDivider"] = (($context["id"] ?? null) . " .qx-shape-bottom");
        // line 7
        $context["bottomDividerSvg"] = (($context["bottomDivider"] ?? null) . " svg");
        // line 8
        $context["containerType"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 8), "container_type", [], "any", false, false, false, 8);
        // line 9
        $context["heightType"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 9), "height", [], "any", false, false, false, 9);
        // line 10
        $context["containerClass"] = (((($context["containerType"] ?? null) == "boxed")) ? (" .qx-container") : (" .qx-container-fluid"));
        // line 11
        echo "
 ";
        // line 13
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "margin", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "spacing_fields_group", [], "any", false, false, false, 13), "margin", [], "any", false, false, false, 13)], "method", false, false, false, 13), "html", null, true);
        echo "

 ";
        // line 16
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "padding", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "spacing_fields_group", [], "any", false, false, false, 16), "padding", [], "any", false, false, false, 16)], "method", false, false, false, 16), "html", null, true);
        echo "

 ";
        // line 19
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "z-index", 2 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "spacing_fields_group", [], "any", false, false, false, 19), "zindex", [], "any", false, false, false, 19)], "method", false, false, false, 19), "html", null, true);
        echo "

 ";
        // line 22
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "background", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "background_fields_group", [], "any", false, false, false, 22), "background", [], "any", false, false, false, 22)], "method", false, false, false, 22), "html", null, true);
        echo "

";
        // line 25
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "background", [0 => ($context["backgroundOverlay"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "background_overlay_fields_group", [], "any", false, false, false, 25), "background_overlay", [], "any", false, false, false, 25), 2 => ($context["id"] ?? null)], "method", false, false, false, 25), "html", null, true);
        echo "

";
        // line 28
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "border", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "border_fields_group", [], "any", false, false, false, 28), "border", [], "any", false, false, false, 28)], "method", false, false, false, 28), "html", null, true);
        echo "


 ";
        // line 32
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 32), "global_position_width", [], "any", false, false, false, 32) == "full-width")) {
            // line 33
            echo "  ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "width", 2 => "100%"], "method", false, false, false, 33), "html", null, true);
            echo "
  ";
            // line 34
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "max-width", 2 => "100%"], "method", false, false, false, 34), "html", null, true);
            echo "

";
        } elseif ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 36
($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 36), "global_position_width", [], "any", false, false, false, 36) == "inline")) {
            // line 37
            echo "  ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "width", 2 => "auto"], "method", false, false, false, 37), "html", null, true);
            echo "
  ";
            // line 38
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "max-width", 2 => "100%"], "method", false, false, false, 38), "html", null, true);
            echo "
";
        } elseif ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 39
($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 39), "global_position_width", [], "any", false, false, false, 39) == "custom")) {
            // line 40
            echo "  ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 40), "custom_width", [], "any", false, false, false, 40), 2 => "width", 3 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 40), "custom_width", [], "any", false, false, false, 40), "unit", [], "any", false, false, false, 40)], "method", false, false, false, 40), "html", null, true);
            echo "
  ";
            // line 41
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 41), "custom_width", [], "any", false, false, false, 41), 2 => "max-width", 3 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 41), "custom_width", [], "any", false, false, false, 41), "unit", [], "any", false, false, false, 41)], "method", false, false, false, 41), "html", null, true);
            echo "
";
        }
        // line 43
        echo "
";
        // line 44
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 44), "global_position", [], "any", false, false, false, 44) != "default")) {
            // line 45
            echo "  ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "position", 2 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 45), "global_position", [], "any", false, false, false, 45)], "method", false, false, false, 45), "html", null, true);
            echo "
  ";
            // line 46
            if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 46), "horizontal", [], "any", false, false, false, 46) == "left")) {
                // line 47
                echo "    ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 47), "horizontal_offset", [], "any", false, false, false, 47), 2 => "left", 3 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 47), "horizontal_offset", [], "any", false, false, false, 47), "unit", [], "any", false, false, false, 47)], "method", false, false, false, 47), "html", null, true);
                echo "

  ";
            } elseif ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,             // line 49
($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 49), "horizontal", [], "any", false, false, false, 49) == "right")) {
                // line 50
                echo "    ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 50), "horizontal_offset", [], "any", false, false, false, 50), 2 => "right", 3 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 50), "horizontal_offset", [], "any", false, false, false, 50), "unit", [], "any", false, false, false, 50)], "method", false, false, false, 50), "html", null, true);
                echo "
  ";
            }
            // line 52
            echo "
  ";
            // line 53
            if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 53), "vertical", [], "any", false, false, false, 53) == "top")) {
                // line 54
                echo "    ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 54), "vertical_offset", [], "any", false, false, false, 54), 2 => "top", 3 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 54), "vertical_offset", [], "any", false, false, false, 54), "unit", [], "any", false, false, false, 54)], "method", false, false, false, 54), "html", null, true);
                echo "

  ";
            } elseif ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,             // line 56
($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 56), "vertical", [], "any", false, false, false, 56) == "bottom")) {
                // line 57
                echo "    ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 57), "vertical_offset", [], "any", false, false, false, 57), 2 => "bottom", 3 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 57), "vertical_offset", [], "any", false, false, false, 57), "unit", [], "any", false, false, false, 57)], "method", false, false, false, 57), "html", null, true);
                echo "
  ";
            }
        }
        // line 60
        echo "
";
        // line 61
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 61), "global_position", [], "any", false, false, false, 61) == "default")) {
            // line 62
            echo "  ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "position", 2 => "relative"], "method", false, false, false, 62), "html", null, true);
            echo "
  ";
            // line 63
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "left", 2 => "auto"], "method", false, false, false, 63), "html", null, true);
            echo "
  ";
            // line 64
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "right", 2 => "auto"], "method", false, false, false, 64), "html", null, true);
            echo "
  ";
            // line 65
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "top", 2 => "auto"], "method", false, false, false, 65), "html", null, true);
            echo "
  ";
            // line 66
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["id"] ?? null), 1 => "bottom", 2 => "auto"], "method", false, false, false, 66), "html", null, true);
            echo "
";
        }
        // line 68
        echo "
";
        // line 70
        if ((($context["heightType"] ?? null) == "custom")) {
            // line 71
            echo "  ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "minHeight", [0 => (($context["id"] ?? null) . ($context["containerClass"] ?? null)), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 71), "custom_height", [], "any", false, false, false, 71)], "method", false, false, false, 71), "html", null, true);
            echo "
";
        }
        // line 73
        echo "
";
        // line 75
        if ((($context["containerType"] ?? null) == "custom")) {
            // line 76
            echo "  ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "responsiveCss", [0 => (($context["id"] ?? null) . ($context["containerClass"] ?? null)), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 76), "container_width", [], "any", false, false, false, 76), 2 => "width", 3 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 76), "container_width", [], "any", false, false, false, 76), "unit", [], "any", false, false, false, 76)], "method", false, false, false, 76), "html", null, true);
            echo "
";
        }
        // line 78
        echo "
";
        // line 80
        $context["topDividerStyle"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "divider_top_fields_group", [], "any", false, false, false, 80), "top_divider_style", [], "any", false, false, false, 80);
        // line 81
        echo "
";
        // line 82
        if ((($context["topDividerStyle"] ?? null) != "none")) {
            // line 83
            echo "
  ";
            // line 85
            echo "  ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => (($context["topDivider"] ?? null) . " path.qx-shape-fill"), 1 => "fill", 2 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "divider_top_fields_group", [], "any", false, false, false, 85), "top_divider_color", [], "any", false, false, false, 85)], "method", false, false, false, 85), "html", null, true);
            echo "

  ";
            // line 88
            echo "  ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "width", [0 => ($context["topDividerSvg"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "divider_top_fields_group", [], "any", false, false, false, 88), "top_divider_width", [], "any", false, false, false, 88), 2 => "%"], "method", false, false, false, 88), "html", null, true);
            echo "

  ";
            // line 91
            echo "  ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "height", [0 => ($context["topDividerSvg"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "divider_top_fields_group", [], "any", false, false, false, 91), "top_divider_height", [], "any", false, false, false, 91)], "method", false, false, false, 91), "html", null, true);
            echo "

  ";
            // line 94
            echo "  ";
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "divider_top_fields_group", [], "any", false, false, false, 94), "top_divider_flip", [], "any", false, false, false, 94)) {
                // line 95
                echo "    ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["topDivider"] ?? null), 1 => "transform", 2 => "scaleX(-1)"], "method", false, false, false, 95), "html", null, true);
                echo "
  ";
            }
            // line 97
            echo "
  ";
            // line 99
            echo "  ";
            $context["shapeFront"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "divider_top_fields_group", [], "any", false, false, false, 99), "top_divider_front", [], "any", false, false, false, 99);
            // line 100
            echo "  ";
            if (($context["shapeFront"] ?? null)) {
                // line 101
                echo "    ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["topDivider"] ?? null), 1 => "z-index", 2 => 2], "method", false, false, false, 101), "html", null, true);
                echo "
  ";
            }
        }
        // line 104
        echo "
";
        // line 106
        $context["bottomDividerStyle"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "divider_bottom_fields_group", [], "any", false, false, false, 106), "bottom_divider_style", [], "any", false, false, false, 106);
        // line 107
        echo "
";
        // line 108
        if ((($context["bottomDividerStyle"] ?? null) != "none")) {
            // line 109
            echo "  ";
            // line 110
            echo "  ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => (($context["bottomDivider"] ?? null) . " path.qx-shape-fill"), 1 => "fill", 2 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "divider_bottom_fields_group", [], "any", false, false, false, 110), "bottom_divider_color", [], "any", false, false, false, 110)], "method", false, false, false, 110), "html", null, true);
            echo "

  ";
            // line 113
            echo "  ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "width", [0 => ($context["bottomDividerSvg"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "divider_bottom_fields_group", [], "any", false, false, false, 113), "bottom_divider_width", [], "any", false, false, false, 113), 2 => "%"], "method", false, false, false, 113), "html", null, true);
            echo "

  ";
            // line 116
            echo "  ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "height", [0 => ($context["bottomDividerSvg"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "divider_bottom_fields_group", [], "any", false, false, false, 116), "bottom_divider_height", [], "any", false, false, false, 116)], "method", false, false, false, 116), "html", null, true);
            echo "

  ";
            // line 119
            echo "  ";
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "divider_bottom_fields_group", [], "any", false, false, false, 119), "bottom_divider_flip", [], "any", false, false, false, 119)) {
                // line 120
                echo "    ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["bottomDivider"] ?? null), 1 => "transform", 2 => "scaleX(-1) rotate(180deg)"], "method", false, false, false, 120), "html", null, true);
                echo "
  ";
            }
            // line 122
            echo "
  ";
            // line 124
            echo "  ";
            $context["shapeBack"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "divider_bottom_fields_group", [], "any", false, false, false, 124), "bottom_divider_front", [], "any", false, false, false, 124);
            // line 125
            echo "  ";
            if (($context["shapeBack"] ?? null)) {
                // line 126
                echo "    ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ($context["bottomDivider"] ?? null), 1 => "z-index", 2 => 2], "method", false, false, false, 126), "html", null, true);
                echo "
  ";
            }
            // line 128
            echo "
";
        }
        // line 130
        echo "
";
        // line 131
        $context["rawCss"] = $this->env->getFilter('removeLines')->getCallable()(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "custom_css_group", [], "any", false, false, false, 131), "custom_css", [], "any", false, false, false, 131), "code", [], "any", false, false, false, 131));
        // line 132
        echo "
";
        // line 134
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "raw", [0 => ($context["rawCss"] ?? null)], "method", false, false, false, 134), "html", null, true);
        echo "
";
        // line 135
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "load", [0 => ($context["id"] ?? null)], "method", false, false, false, 135), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "section/partials/style.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  347 => 135,  343 => 134,  340 => 132,  338 => 131,  335 => 130,  331 => 128,  325 => 126,  322 => 125,  319 => 124,  316 => 122,  310 => 120,  307 => 119,  301 => 116,  295 => 113,  289 => 110,  287 => 109,  285 => 108,  282 => 107,  280 => 106,  277 => 104,  270 => 101,  267 => 100,  264 => 99,  261 => 97,  255 => 95,  252 => 94,  246 => 91,  240 => 88,  234 => 85,  231 => 83,  229 => 82,  226 => 81,  224 => 80,  221 => 78,  215 => 76,  213 => 75,  210 => 73,  204 => 71,  202 => 70,  199 => 68,  194 => 66,  190 => 65,  186 => 64,  182 => 63,  177 => 62,  175 => 61,  172 => 60,  165 => 57,  163 => 56,  157 => 54,  155 => 53,  152 => 52,  146 => 50,  144 => 49,  138 => 47,  136 => 46,  131 => 45,  129 => 44,  126 => 43,  121 => 41,  116 => 40,  114 => 39,  110 => 38,  105 => 37,  103 => 36,  98 => 34,  93 => 33,  91 => 32,  85 => 28,  80 => 25,  75 => 22,  70 => 19,  65 => 16,  60 => 13,  57 => 11,  55 => 10,  53 => 9,  51 => 8,  49 => 7,  47 => 6,  45 => 5,  43 => 4,  41 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "section/partials/style.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/section/partials/style.twig");
    }
}
