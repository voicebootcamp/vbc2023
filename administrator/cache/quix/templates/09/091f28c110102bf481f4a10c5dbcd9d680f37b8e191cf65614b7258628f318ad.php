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

/* section/partials/html.twig */
class __TwigTemplate_253ead70c9c50b084437634bf820a91fb1a8795c34a9d44373d0246c964ef602 extends \Twig\Template
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
        $context["layout"] = twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 3);
        // line 4
        $context["container"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 4), "container_type", [], "any", false, false, false, 4);
        // line 5
        $context["bg_overlay"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "background_overlay_fields_group", [], "any", false, false, false, 5), "background_overlay", [], "any", false, false, false, 5);
        // line 6
        $context["background"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "background_fields_group", [], "any", false, false, false, 6), "background", [], "any", false, false, false, 6);
        // line 7
        echo "
";
        // line 8
        $context["classes"] = $this->env->getFunction('classNames')->getCallable()("qx-section", $this->env->getFunction('visibilityClassNode')->getCallable()(($context["visibility"] ?? null)), ["qx-section--stretch" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 9
($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 9), "section_stretch", [], "any", false, false, false, 9), "qx-section-height-full" => (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 10
($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 10), "height", [], "any", false, false, false, 10) == "full"), "qx-section-height-custom" => (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 11
($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 11), "height", [], "any", false, false, false, 11) == "custom"), "qx-section-has-divider" => ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 12
($context["styles"] ?? null), "divider_top_fields_group", [], "any", false, false, false, 12), "top_divider_style", [], "any", false, false, false, 12) != "none") || (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "divider_bottom_fields_group", [], "any", false, false, false, 12), "bottom_divider_style", [], "any", false, false, false, 12) != "none")), "qx-cover-container" => (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 13
($context["background"] ?? null), "state", [], "any", false, false, false, 13), "normal", [], "any", false, false, false, 13), "type", [], "any", false, false, false, 13) == "video"), "lazyload lazyload-bg" => $this->env->getFunction('ifElementHasBackground')->getCallable()(        // line 14
($context["background"] ?? null))],         // line 15
($context["class"] ?? null));
        // line 16
        echo "
";
        // line 17
        $context["containerClass"] = $this->env->getFunction('classNames')->getCallable()(["qx-container" => (        // line 18
($context["container"] ?? null) == "boxed"), "qx-container-fluid" => (        // line 19
($context["container"] ?? null) != "boxed")], twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 20
($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 20), "v_align", [], "any", false, false, false, 20));
        // line 21
        echo "
";
        // line 22
        $context["overlayClass"] = $this->env->getFunction('classNames')->getCallable()((($context["id"] ?? null) . "-background-overlay"), "qx-background-overlay", ["lazyload lazyload-bg blur-up" => $this->env->getFunction('ifElementHasBackground')->getCallable()(        // line 23
($context["bg_overlay"] ?? null))]);
        // line 25
        echo "


";
        // line 28
        $context["sectionID"] = (("id='" . ($context["id"] ?? null)) . "'");
        // line 29
        $context["sectionClasses"] = (("class='" . ($context["classes"] ?? null)) . "'");
        // line 30
        echo "
";
        // line 31
        $context["parallax"] = "";
        // line 32
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["background"] ?? null), "state", [], "any", false, false, false, 32), "normal", [], "any", false, false, false, 32), "properties", [], "any", false, false, false, 32), "parallax_method", [], "any", false, false, false, 32) == "js")) {
            // line 33
            echo "    ";
            $context["parallaxInfo"] = "";
            // line 34
            echo "    ";
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["background"] ?? null), "state", [], "any", false, false, false, 34), "normal", [], "any", false, false, false, 34), "properties", [], "any", false, false, false, 34), "js_parallax_y", [], "any", false, false, false, 34)) {
                // line 35
                echo "        ";
                $context["parallaxInfo"] = (((($context["parallaxInfo"] ?? null) . "bgy:") . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["background"] ?? null), "state", [], "any", false, false, false, 35), "normal", [], "any", false, false, false, 35), "properties", [], "any", false, false, false, 35), "js_parallax_y", [], "any", false, false, false, 35)) . ";");
                // line 36
                echo "    ";
            }
            // line 37
            echo "    ";
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["background"] ?? null), "state", [], "any", false, false, false, 37), "normal", [], "any", false, false, false, 37), "properties", [], "any", false, false, false, 37), "js_parallax_x", [], "any", false, false, false, 37)) {
                // line 38
                echo "        ";
                $context["parallaxInfo"] = (((($context["parallaxInfo"] ?? null) . "bgx:") . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["background"] ?? null), "state", [], "any", false, false, false, 38), "normal", [], "any", false, false, false, 38), "properties", [], "any", false, false, false, 38), "js_parallax_x", [], "any", false, false, false, 38)) . ";");
                // line 39
                echo "    ";
            }
            // line 40
            echo "    ";
            $context["parallax"] = ((" data-qx-parallax=\"" . ($context["parallaxInfo"] ?? null)) . "\"");
        }
        // line 42
        echo "
";
        // line 44
        $context["sticky"] = "";
        // line 45
        $context["position"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, false, false, 45), "global_position", [], "any", false, false, false, 45);
        // line 46
        if ((($context["position"] ?? null) == "sticky")) {
            // line 47
            echo "    ";
            $context["stickyValue"] = "";
            // line 48
            echo "    ";
            $context["sticky_animation"] = ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, true, false, 48), "sticky_animation", [], "any", true, true, false, 48)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, true, false, 48), "sticky_animation", [], "any", false, false, false, 48), "")) : (""));
            // line 49
            echo "    ";
            $context["stickyValue"] = (($context["stickyValue"] ?? null) . ((($context["sticky_animation"] ?? null)) ? ((("animation:" . ($context["sticky_animation"] ?? null)) . ";")) : ("")));
            // line 50
            echo "
    ";
            // line 51
            $context["sticky_bottom"] = ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, true, false, 51), "sticky_bottom", [], "any", true, true, false, 51)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, true, false, 51), "sticky_bottom", [], "any", false, false, false, 51), "")) : (""));
            // line 52
            echo "    ";
            $context["stickyValue"] = (($context["stickyValue"] ?? null) . ((($context["sticky_bottom"] ?? null)) ? ((("bottom:" . ($context["sticky_bottom"] ?? null)) . ";")) : ("")));
            // line 53
            echo "
    ";
            // line 54
            $context["sticky_show_on_up"] = ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, true, false, 54), "sticky_show_on_up", [], "any", true, true, false, 54)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, true, false, 54), "sticky_show_on_up", [], "any", false, false, false, 54), false)) : (false));
            // line 55
            echo "    ";
            $context["stickyValue"] = (($context["stickyValue"] ?? null) . ((($context["sticky_show_on_up"] ?? null)) ? ((("show-on-up:" . ($context["sticky_show_on_up"] ?? null)) . ";")) : ("")));
            // line 56
            echo "
    ";
            // line 57
            $context["sticky_media"] = ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, true, false, 57), "sticky_media", [], "any", true, true, false, 57)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, true, false, 57), "sticky_media", [], "any", false, false, false, 57), "")) : (""));
            // line 58
            echo "    ";
            $context["stickyValue"] = (($context["stickyValue"] ?? null) . ((($context["sticky_media"] ?? null)) ? ((("media:" . ($context["sticky_media"] ?? null)) . ";")) : ("")));
            // line 59
            echo "
    ";
            // line 60
            $context["sticky_offset"] = ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, true, false, 60), "sticky_offset", [], "any", true, true, false, 60)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "positioning_fields_group", [], "any", false, true, false, 60), "sticky_offset", [], "any", false, false, false, 60), "")) : (""));
            // line 61
            echo "    ";
            $context["stickyValue"] = (($context["stickyValue"] ?? null) . ((twig_get_attribute($this->env, $this->source, ($context["sticky_offset"] ?? null), "value", [], "any", false, false, false, 61)) ? ((("offset:" . twig_get_attribute($this->env, $this->source, ($context["sticky_offset"] ?? null), "value", [], "any", false, false, false, 61)) . ";")) : ("")));
            // line 62
            echo "
    ";
            // line 64
            echo "    ";
            $context["sticky"] = ((" qx-sticky=\"" . ($context["stickyValue"] ?? null)) . "\"");
        }
        // line 66
        echo "
 ";
        // line 68
        echo twig_escape_filter($this->env, ((((((((("<" . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 68), "html_tag", [], "any", false, false, false, 68)) . " ") . ($context["sectionID"] ?? null)) . " ") . ($context["sectionClasses"] ?? null)) . ($context["sticky"] ?? null)) . ($context["parallax"] ?? null)) . $this->env->getFunction('lazyBackground')->getCallable()(($context["background"] ?? null))) . " >"), "html", null, true);
        echo "

    ";
        // line 70
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["background"] ?? null), "state", [], "any", false, false, false, 70), "normal", [], "any", false, false, false, 70), "type", [], "any", false, false, false, 70) == "video")) {
            // line 71
            echo "        <video class=\"qx-background-video\" src=\"";
            echo twig_escape_filter($this->env, $this->env->getFunction('imageUrl')->getCallable()(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["background"] ?? null), "state", [], "any", false, false, false, 71), "normal", [], "any", false, false, false, 71), "properties", [], "any", false, false, false, 71), "url", [], "any", false, false, false, 71), "source", [], "any", false, false, false, 71)), "html", null, true);
            echo "\"
               autoplay=\"\" loop=\"\" muted=\"\" playsinline=\"\" qx-cover=\"\"
               qx-video=\"automute: true;autoplay: inview\"></video>
    ";
        }
        // line 75
        echo "
  ";
        // line 76
        if ((($context["bg_overlay"] ?? null) && ($this->env->getFunction('getOpacity')->getCallable()(($context["bg_overlay"] ?? null), "normal") || $this->env->getFunction('getOpacity')->getCallable()(($context["bg_overlay"] ?? null), "hover")))) {
            // line 77
            echo "      <div class=\"";
            echo twig_escape_filter($this->env, ($context["overlayClass"] ?? null), "html", null, true);
            echo "\" ";
            echo twig_escape_filter($this->env, $this->env->getFunction('lazyBackground')->getCallable()(($context["bg_overlay"] ?? null)), "html", null, true);
            echo "></div>
  ";
        }
        // line 79
        echo "
  ";
        // line 80
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "divider_top_fields_group", [], "any", false, false, false, 80), "top_divider_style", [], "any", false, false, false, 80) != "none")) {
            // line 81
            echo "      ";
            $context["topDividerFile"] = (("/images/shapes/" . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "divider_top_fields_group", [], "any", false, false, false, 81), "top_divider_style", [], "any", false, false, false, 81)) . ".svg");
            // line 82
            echo "      <div class=\"qx-shape qx-shape-top\">
          ";
            // line 83
            echo twig_escape_filter($this->env, $this->env->getFunction('mediaFile')->getCallable()(($context["topDividerFile"] ?? null)), "html", null, true);
            echo "
      </div>
  ";
        }
        // line 86
        echo "
  ";
        // line 87
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "divider_bottom_fields_group", [], "any", false, false, false, 87), "bottom_divider_style", [], "any", false, false, false, 87) != "none")) {
            // line 88
            echo "      ";
            $context["bottomDividerFile"] = (("/images/shapes/" . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "divider_bottom_fields_group", [], "any", false, false, false, 88), "bottom_divider_style", [], "any", false, false, false, 88)) . ".svg");
            // line 89
            echo "      <div class=\"qx-shape qx-shape-bottom\">
          ";
            // line 90
            echo twig_escape_filter($this->env, $this->env->getFunction('mediaFile')->getCallable()(($context["bottomDividerFile"] ?? null)), "html", null, true);
            echo "
      </div>
  ";
        }
        // line 93
        echo "
<div class=\"";
        // line 94
        echo twig_escape_filter($this->env, ($context["containerClass"] ?? null), "html", null, true);
        echo "\">
    ";
        // line 95
        echo twig_get_attribute($this->env, $this->source, ($context["renderer"] ?? null), "render", [0 => (($__internal_compile_0 = ($context["node"] ?? null)) && is_array($__internal_compile_0) || $__internal_compile_0 instanceof ArrayAccess ? ($__internal_compile_0["children"] ?? null) : null), 1 => null, 2 => "frontend"], "method", false, false, false, 95);
        echo "
</div>

";
        // line 98
        echo twig_escape_filter($this->env, (("</" . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 98), "html_tag", [], "any", false, false, false, 98)) . ">"), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "section/partials/html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  251 => 98,  245 => 95,  241 => 94,  238 => 93,  232 => 90,  229 => 89,  226 => 88,  224 => 87,  221 => 86,  215 => 83,  212 => 82,  209 => 81,  207 => 80,  204 => 79,  196 => 77,  194 => 76,  191 => 75,  183 => 71,  181 => 70,  176 => 68,  173 => 66,  169 => 64,  166 => 62,  163 => 61,  161 => 60,  158 => 59,  155 => 58,  153 => 57,  150 => 56,  147 => 55,  145 => 54,  142 => 53,  139 => 52,  137 => 51,  134 => 50,  131 => 49,  128 => 48,  125 => 47,  123 => 46,  121 => 45,  119 => 44,  116 => 42,  112 => 40,  109 => 39,  106 => 38,  103 => 37,  100 => 36,  97 => 35,  94 => 34,  91 => 33,  89 => 32,  87 => 31,  84 => 30,  82 => 29,  80 => 28,  75 => 25,  73 => 23,  72 => 22,  69 => 21,  67 => 20,  66 => 19,  65 => 18,  64 => 17,  61 => 16,  59 => 15,  58 => 14,  57 => 13,  56 => 12,  55 => 11,  54 => 10,  53 => 9,  52 => 8,  49 => 7,  47 => 6,  45 => 5,  43 => 4,  41 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "section/partials/html.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/section/partials/html.twig");
    }
}
