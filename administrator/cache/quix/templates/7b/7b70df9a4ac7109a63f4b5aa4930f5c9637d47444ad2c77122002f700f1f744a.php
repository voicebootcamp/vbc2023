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

/* dual-button/partials/html.twig */
class __TwigTemplate_9b1580d9b1e442667b90f901fa3123c55f47917825aba2144d16259d5d0633d1 extends \Twig\Template
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
        $context["id"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "identifier", [], "any", false, false, false, 1), "id", [], "any", false, false, false, 1);
        // line 2
        $context["class"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "identifier", [], "any", false, false, false, 2), "class", [], "any", false, false, false, 2);
        // line 3
        $context["btnClass"] = "";
        // line 4
        echo "
";
        // line 6
        $context["iconPri"] = "";
        // line 7
        $context["srcPri"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "primary_button_fields_group", [], "any", false, false, false, 7), "primary_icon", [], "any", false, false, false, 7);
        // line 8
        $context["textPri"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "primary_button_fields_group", [], "any", false, false, false, 8), "primary_text", [], "any", false, false, false, 8);
        // line 9
        $context["linkPri"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "primary_button_fields_group", [], "any", false, false, false, 9), "primary_link", [], "any", false, false, false, 9);
        // line 10
        $context["iconAlignmentPri"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "primary_button_fields_group", [], "any", false, false, false, 10), "primary_button_icon_alignment", [], "any", false, false, false, 10);
        // line 11
        $context["enableSSPri"] = ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "primary_button_fields_group", [], "any", false, true, false, 11), "primary_enable_smoothscroll", [], "any", true, true, false, 11)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "primary_button_fields_group", [], "any", false, true, false, 11), "primary_enable_smoothscroll", [], "any", false, false, false, 11), false)) : (false));
        // line 12
        $context["scrollOffsetPri"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "primary_button_fields_group", [], "any", false, false, false, 12), "primary_scroll_offset", [], "any", false, false, false, 12);
        // line 13
        $context["SSTagsPri"] = "";
        // line 14
        if (($context["enableSSPri"] ?? null)) {
            // line 15
            echo "    ";
            if ((($context["scrollOffsetPri"] ?? null) != "")) {
                // line 16
                echo "        ";
                $context["SSTagsPri"] = ((" data-qx-scroll=\"offset:" . ($context["scrollOffsetPri"] ?? null)) . " \"");
                // line 17
                echo "    ";
            } else {
                // line 18
                echo "        ";
                $context["SSTagsPri"] = " data-qx-scroll ";
                // line 19
                echo "    ";
            }
        }
        // line 21
        $context["textAlignmentPri"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "primary_button_fields_group", [], "any", false, false, false, 21), "primary_text_alignment", [], "any", false, false, false, 21);
        // line 22
        echo "
";
        // line 24
        $context["iconSec"] = "";
        // line 25
        $context["srcSec"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "secondary_button_fields_group", [], "any", false, false, false, 25), "secondary_icon", [], "any", false, false, false, 25);
        // line 26
        $context["textSec"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "secondary_button_fields_group", [], "any", false, false, false, 26), "secondary_text", [], "any", false, false, false, 26);
        // line 27
        $context["linkSec"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "secondary_button_fields_group", [], "any", false, false, false, 27), "secondary_link", [], "any", false, false, false, 27);
        // line 28
        $context["iconAlignmentSec"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "secondary_button_fields_group", [], "any", false, false, false, 28), "secondary_button_icon_alignment", [], "any", false, false, false, 28);
        // line 29
        $context["enableSSSec"] = ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "secondary_button_fields_group", [], "any", false, true, false, 29), "secondary_enable_smoothscroll", [], "any", true, true, false, 29)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "secondary_button_fields_group", [], "any", false, true, false, 29), "secondary_enable_smoothscroll", [], "any", false, false, false, 29), false)) : (false));
        // line 30
        $context["scrollOffsetSec"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "secondary_button_fields_group", [], "any", false, false, false, 30), "secondary_scroll_offset", [], "any", false, false, false, 30);
        // line 31
        $context["SSTagsSec"] = "";
        // line 32
        if (($context["enableSSSec"] ?? null)) {
            // line 33
            echo "    ";
            if ((($context["scrollOffsetSec"] ?? null) != "")) {
                // line 34
                echo "        ";
                $context["SSTagsSec"] = ((" data-qx-scroll=\"offset:" . ($context["scrollOffsetSec"] ?? null)) . " \"");
                // line 35
                echo "    ";
            } else {
                // line 36
                echo "        ";
                $context["SSTagsSec"] = " data-qx-scroll ";
                // line 37
                echo "    ";
            }
        }
        // line 39
        $context["textAlignmentSec"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "secondary_button_fields_group", [], "any", false, false, false, 39), "secondary_text_alignment", [], "any", false, false, false, 39);
        // line 40
        echo "

";
        // line 43
        $context["iconConn"] = "";
        // line 44
        $context["srcConn"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "connector_button_fields_group", [], "any", false, false, false, 44), "connector_icon", [], "any", false, false, false, 44);
        // line 45
        $context["enable_connector"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "connector_button_fields_group", [], "any", false, false, false, 45), "enable_connector", [], "any", false, false, false, 45);
        // line 46
        $context["connectorText"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "connector_button_fields_group", [], "any", false, false, false, 46), "connector_text", [], "any", false, false, false, 46);
        // line 47
        echo "

";
        // line 50
        $context["chooseLayout"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "layout_button_fields_group", [], "any", false, false, false, 50), "choose_button_layout", [], "any", false, false, false, 50);
        // line 51
        $context["layoutClass"] = ("qx-element-dual-button-" . ($context["chooseLayout"] ?? null));
        // line 52
        if ((($context["chooseLayout"] ?? null) == "horizontal")) {
            // line 53
            echo "    ";
            $context["btnClass"] = "qx-btn qx-display-block";
        } elseif ((        // line 54
($context["chooseLayout"] ?? null) == "vertical")) {
            // line 55
            echo "    ";
            $context["btnClass"] = "qx-btn qx-display-inline-block";
        }
        // line 57
        echo "
";
        // line 58
        $context["layoutAlignment"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "layout_button_fields_group", [], "any", false, false, false, 58), "button_layout_alignment", [], "any", false, false, false, 58);
        // line 59
        $context["layoutAlign"] = ((((((((((($context["chooseLayout"] ?? null) . "-alignDesktop-") . twig_get_attribute($this->env, $this->source, ($context["layoutAlignment"] ?? null), "desktop", [], "any", false, false, false, 59)) . " ") . ($context["chooseLayout"] ?? null)) . "-alignTablet-") . twig_get_attribute($this->env, $this->source, ($context["layoutAlignment"] ?? null), "tablet", [], "any", false, false, false, 59)) . " ") . ($context["chooseLayout"] ?? null)) . "-alignPhone-") . twig_get_attribute($this->env, $this->source, ($context["layoutAlignment"] ?? null), "phone", [], "any", false, false, false, 59));
        // line 60
        echo "
";
        // line 62
        $context["btnPriWrapper"] = $this->env->getFunction('classNames')->getCallable()("qx-element-dual-button-wrapper", (((        // line 63
($context["iconAlignmentPri"] ?? null) == "left")) ? ("qx-flex qx-flex-row") : ("")), (((        // line 64
($context["iconAlignmentPri"] ?? null) == "right")) ? ("qx-flex qx-flex-row-reverse") : ("")));
        // line 67
        $context["btnSecWrapper"] = $this->env->getFunction('classNames')->getCallable()("qx-element-dual-button-wrapper", (((        // line 68
($context["iconAlignmentSec"] ?? null) == "left")) ? ("qx-flex qx-flex-row") : ("")), (((        // line 69
($context["iconAlignmentSec"] ?? null) == "right")) ? ("qx-flex qx-flex-row-reverse") : ("")));
        // line 71
        echo "
";
        // line 72
        $context["classes"] = $this->env->getFunction('classNames')->getCallable()("qx-element qx-element-dual-button", $this->env->getFunction('visibilityClass')->getCallable()(($context["visibility"] ?? null)), ($context["class"] ?? null));
        // line 73
        $context["animation"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 73), "animation", [], "any", false, false, false, 73);
        // line 74
        $context["animationRepeat"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 74), "animation_repeat", [], "any", false, false, false, 74);
        // line 75
        $context["animationDelay"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 75), "animation_delay", [], "any", false, false, false, 75);
        // line 76
        $context["background"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "background_fields_group", [], "any", false, false, false, 76), "background", [], "any", false, false, false, 76);
        // line 77
        echo "
";
        // line 78
        $this->loadTemplate("dual-button/partials/html.twig", "dual-button/partials/html.twig", 78, "1937893751")->display(twig_array_merge($context, ["id" =>         // line 79
($context["id"] ?? null), "classes" =>         // line 80
($context["classes"] ?? null), "animation" =>         // line 81
($context["animation"] ?? null), "animationRepeat" =>         // line 82
($context["animationRepeat"] ?? null), "animationDelay" =>         // line 83
($context["animationDelay"] ?? null), "background" =>         // line 84
($context["background"] ?? null)]));
    }

    public function getTemplateName()
    {
        return "dual-button/partials/html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  192 => 84,  191 => 83,  190 => 82,  189 => 81,  188 => 80,  187 => 79,  186 => 78,  183 => 77,  181 => 76,  179 => 75,  177 => 74,  175 => 73,  173 => 72,  170 => 71,  168 => 69,  167 => 68,  166 => 67,  164 => 64,  163 => 63,  162 => 62,  159 => 60,  157 => 59,  155 => 58,  152 => 57,  148 => 55,  146 => 54,  143 => 53,  141 => 52,  139 => 51,  137 => 50,  133 => 47,  131 => 46,  129 => 45,  127 => 44,  125 => 43,  121 => 40,  119 => 39,  115 => 37,  112 => 36,  109 => 35,  106 => 34,  103 => 33,  101 => 32,  99 => 31,  97 => 30,  95 => 29,  93 => 28,  91 => 27,  89 => 26,  87 => 25,  85 => 24,  82 => 22,  80 => 21,  76 => 19,  73 => 18,  70 => 17,  67 => 16,  64 => 15,  62 => 14,  60 => 13,  58 => 12,  56 => 11,  54 => 10,  52 => 9,  50 => 8,  48 => 7,  46 => 6,  43 => 4,  41 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "dual-button/partials/html.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/dual-button/partials/html.twig");
    }
}


/* dual-button/partials/html.twig */
class __TwigTemplate_9b1580d9b1e442667b90f901fa3123c55f47917825aba2144d16259d5d0633d1___1937893751 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'element' => [$this, 'block_element'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 78
        return "animation.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("animation.twig", "dual-button/partials/html.twig", 78);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 86
    public function block_element($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 87
        echo "        <div class=\"qx-flex ";
        echo twig_escape_filter($this->env, ($context["layoutClass"] ?? null), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, ($context["layoutAlign"] ?? null), "html", null, true);
        echo "\">
            ";
        // line 88
        echo (((($context["chooseLayout"] ?? null) == "vertical")) ? ("<div class=\"qx-child-width-1-1 qx-display-inline-block\">") : (""));
        echo "
            <div class=\"btn-wrapper btn-first\">
            ";
        // line 91
        echo "            ";
        if (twig_get_attribute($this->env, $this->source, ($context["srcPri"] ?? null), "source", [], "any", false, false, false, 91)) {
            // line 92
            echo "                ";
            if ((twig_get_attribute($this->env, $this->source, ($context["srcPri"] ?? null), "type", [], "any", false, false, false, 92) == "svg")) {
                // line 93
                echo "                    ";
                $context["iconPri"] = $this->env->getFunction('icon')->getCallable()(twig_get_attribute($this->env, $this->source, ($context["srcPri"] ?? null), "source", [], "any", false, false, false, 93));
                // line 94
                echo "                ";
            }
            // line 95
            echo "            ";
        }
        // line 96
        echo "
            ";
        // line 97
        echo twig_escape_filter($this->env, $this->env->getFilter('link')->getCallable()(((((((("<div class=\" " . ($context["btnPriWrapper"] ?? null)) . " \">") . ($context["iconPri"] ?? null)) . " ") . "<span>") . ($context["textPri"] ?? null)) . "</span></div>"), ($context["linkPri"] ?? null), ($context["btnClass"] ?? null), ($context["SSTagsPri"] ?? null)), "html", null, true);
        echo "

            ";
        // line 100
        echo "            ";
        if ((($context["enable_connector"] ?? null) == true)) {
            // line 101
            echo "                <div class=\"connector-wrapper\">
                ";
            // line 102
            if (twig_get_attribute($this->env, $this->source, ($context["srcConn"] ?? null), "source", [], "any", false, false, false, 102)) {
                // line 103
                echo "                    ";
                if ((twig_get_attribute($this->env, $this->source, ($context["srcConn"] ?? null), "type", [], "any", false, false, false, 103) == "svg")) {
                    // line 104
                    echo "                        ";
                    $context["iconConn"] = $this->env->getFunction('icon')->getCallable()(twig_get_attribute($this->env, $this->source, ($context["srcConn"] ?? null), "source", [], "any", false, false, false, 104));
                    // line 105
                    echo "                    ";
                }
                // line 106
                echo "                ";
            }
            // line 107
            echo "
                ";
            // line 108
            if (twig_get_attribute($this->env, $this->source, ($context["srcConn"] ?? null), "source", [], "any", false, false, false, 108)) {
                // line 109
                echo "                    ";
                echo twig_escape_filter($this->env, ($context["iconConn"] ?? null), "html", null, true);
                echo "
                ";
            } else {
                // line 111
                echo "                    <div class=\"connector-text\">";
                echo twig_escape_filter($this->env, ($context["connectorText"] ?? null), "html", null, true);
                echo "</div>
                ";
            }
            // line 113
            echo "                </div>
            ";
        }
        // line 115
        echo "            ";
        // line 116
        echo "            </div>
            ";
        // line 118
        echo "
            ";
        // line 120
        echo "            <div class=\"btn-wrapper btn-second\">

            ";
        // line 122
        if (twig_get_attribute($this->env, $this->source, ($context["srcSec"] ?? null), "source", [], "any", false, false, false, 122)) {
            // line 123
            echo "                ";
            if ((twig_get_attribute($this->env, $this->source, ($context["srcSec"] ?? null), "type", [], "any", false, false, false, 123) == "svg")) {
                // line 124
                echo "                    ";
                $context["iconSec"] = $this->env->getFunction('icon')->getCallable()(twig_get_attribute($this->env, $this->source, ($context["srcSec"] ?? null), "source", [], "any", false, false, false, 124));
                // line 125
                echo "                ";
            }
            // line 126
            echo "            ";
        }
        // line 127
        echo "
            ";
        // line 128
        echo twig_escape_filter($this->env, $this->env->getFilter('link')->getCallable()(((((((("<div class=\" " . ($context["btnSecWrapper"] ?? null)) . " \">") . ($context["iconSec"] ?? null)) . " ") . "<span>") . ($context["textSec"] ?? null)) . "</span></div>"), ($context["linkSec"] ?? null), ($context["btnClass"] ?? null), ($context["SSTagsSec"] ?? null)), "html", null, true);
        echo "

            </div>
            ";
        // line 132
        echo "            ";
        echo (((($context["chooseLayout"] ?? null) == "vertical")) ? ("</div>") : (""));
        echo "

            ";
        // line 134
        if ((twig_get_attribute($this->env, $this->source, ($context["srcPri"] ?? null), "type", [], "any", false, false, false, 134) == "svg")) {
            // line 135
            echo "                ";
            echo twig_escape_filter($this->env, $this->env->getFunction('addIconStyle')->getCallable()((("#" . ($context["id"] ?? null)) . " .btn-first .qx-element-dual-button-wrapper"), ($context["srcPri"] ?? null)), "html", null, true);
            echo "
            ";
        }
        // line 137
        echo "
            ";
        // line 138
        if ((twig_get_attribute($this->env, $this->source, ($context["srcConn"] ?? null), "type", [], "any", false, false, false, 138) == "svg")) {
            // line 139
            echo "                ";
            echo twig_escape_filter($this->env, $this->env->getFunction('addIconStyle')->getCallable()((("#" . ($context["id"] ?? null)) . " .connector-wrapper"), ($context["srcConn"] ?? null)), "html", null, true);
            echo "
            ";
        }
        // line 141
        echo "
            ";
        // line 142
        if ((twig_get_attribute($this->env, $this->source, ($context["srcSec"] ?? null), "type", [], "any", false, false, false, 142) == "svg")) {
            // line 143
            echo "                ";
            echo twig_escape_filter($this->env, $this->env->getFunction('addIconStyle')->getCallable()((("#" . ($context["id"] ?? null)) . " .btn-second .qx-element-dual-button-wrapper"), ($context["srcSec"] ?? null)), "html", null, true);
            echo "
            ";
        }
        // line 145
        echo "
        </div>
    ";
    }

    public function getTemplateName()
    {
        return "dual-button/partials/html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  399 => 145,  393 => 143,  391 => 142,  388 => 141,  382 => 139,  380 => 138,  377 => 137,  371 => 135,  369 => 134,  363 => 132,  357 => 128,  354 => 127,  351 => 126,  348 => 125,  345 => 124,  342 => 123,  340 => 122,  336 => 120,  333 => 118,  330 => 116,  328 => 115,  324 => 113,  318 => 111,  312 => 109,  310 => 108,  307 => 107,  304 => 106,  301 => 105,  298 => 104,  295 => 103,  293 => 102,  290 => 101,  287 => 100,  282 => 97,  279 => 96,  276 => 95,  273 => 94,  270 => 93,  267 => 92,  264 => 91,  259 => 88,  252 => 87,  248 => 86,  237 => 78,  192 => 84,  191 => 83,  190 => 82,  189 => 81,  188 => 80,  187 => 79,  186 => 78,  183 => 77,  181 => 76,  179 => 75,  177 => 74,  175 => 73,  173 => 72,  170 => 71,  168 => 69,  167 => 68,  166 => 67,  164 => 64,  163 => 63,  162 => 62,  159 => 60,  157 => 59,  155 => 58,  152 => 57,  148 => 55,  146 => 54,  143 => 53,  141 => 52,  139 => 51,  137 => 50,  133 => 47,  131 => 46,  129 => 45,  127 => 44,  125 => 43,  121 => 40,  119 => 39,  115 => 37,  112 => 36,  109 => 35,  106 => 34,  103 => 33,  101 => 32,  99 => 31,  97 => 30,  95 => 29,  93 => 28,  91 => 27,  89 => 26,  87 => 25,  85 => 24,  82 => 22,  80 => 21,  76 => 19,  73 => 18,  70 => 17,  67 => 16,  64 => 15,  62 => 14,  60 => 13,  58 => 12,  56 => 11,  54 => 10,  52 => 9,  50 => 8,  48 => 7,  46 => 6,  43 => 4,  41 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "dual-button/partials/html.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/dual-button/partials/html.twig");
    }
}
