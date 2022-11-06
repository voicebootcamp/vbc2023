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

/* row/partials/html.twig */
class __TwigTemplate_b01aeb236d3e83e9693946d112140c2222af7e9e8d8a94d94a364972d17d974b extends \Twig\Template
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
        $context["classes"] = $this->env->getFunction('classNames')->getCallable()("qx-row", twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 2), "v_align", [], "any", false, false, false, 2), ["qx-no-gutters" => (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 3
($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 3), "columns_gap", [], "any", false, false, false, 3) == "no-gutters"), "qx-flex-md-row qx-flex-column-reverse" => (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 4
($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 4), "mobile_reverse", [], "any", false, false, false, 4) == true)], twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 5
($context["advanced"] ?? null), "identifier", [], "any", false, false, false, 5), "class", [], "any", false, false, false, 5));
        // line 6
        echo "
";
        // line 7
        $context["wrapClasse"] = $this->env->getFunction('classNames')->getCallable()("qx-row-wrap", "lazyload", $this->env->getFunction('visibilityClassNode')->getCallable()(($context["visibility"] ?? null)));
        // line 8
        $context["rowId"] = (("id='" . ($context["id"] ?? null)) . "'");
        // line 9
        $context["rowClasses"] = (("class='" . ($context["classes"] ?? null)) . "'");
        // line 10
        $context["wrapClasses"] = (("class='" . ($context["wrapClasse"] ?? null)) . "'");
        // line 11
        $context["bg_overlay"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "background_overlay_fields_group", [], "any", false, false, false, 11), "background_overlay", [], "any", false, false, false, 11);
        // line 12
        $context["background"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "background_fields_group", [], "any", false, false, false, 12), "background", [], "any", false, false, false, 12);
        // line 13
        echo "
";
        // line 14
        $context["parallax"] = "";
        // line 15
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["background"] ?? null), "state", [], "any", false, false, false, 15), "normal", [], "any", false, false, false, 15), "properties", [], "any", false, false, false, 15), "parallax_method", [], "any", false, false, false, 15) == "js")) {
            // line 16
            echo "    ";
            $context["parallaxInfo"] = "";
            // line 17
            echo "    ";
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["background"] ?? null), "state", [], "any", false, false, false, 17), "normal", [], "any", false, false, false, 17), "properties", [], "any", false, false, false, 17), "js_parallax_y", [], "any", false, false, false, 17)) {
                // line 18
                echo "        ";
                $context["parallaxInfo"] = (((($context["parallaxInfo"] ?? null) . "bgy:") . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["background"] ?? null), "state", [], "any", false, false, false, 18), "normal", [], "any", false, false, false, 18), "properties", [], "any", false, false, false, 18), "js_parallax_y", [], "any", false, false, false, 18)) . ";");
                // line 19
                echo "    ";
            }
            // line 20
            echo "    ";
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["background"] ?? null), "state", [], "any", false, false, false, 20), "normal", [], "any", false, false, false, 20), "properties", [], "any", false, false, false, 20), "js_parallax_x", [], "any", false, false, false, 20)) {
                // line 21
                echo "        ";
                $context["parallaxInfo"] = (((($context["parallaxInfo"] ?? null) . "bgx:") . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["background"] ?? null), "state", [], "any", false, false, false, 21), "normal", [], "any", false, false, false, 21), "properties", [], "any", false, false, false, 21), "js_parallax_x", [], "any", false, false, false, 21)) . ";");
                // line 22
                echo "    ";
            }
            // line 23
            echo "    ";
            $context["parallax"] = ((" data-qx-parallax=\"" . ($context["parallaxInfo"] ?? null)) . "\"");
        }
        // line 25
        echo "
";
        // line 27
        echo twig_escape_filter($this->env, (((((((("<" . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 27), "html_tag", [], "any", false, false, false, 27)) . " ") . ($context["rowId"] ?? null)) . " ") . ($context["wrapClasses"] ?? null)) . " ") . $this->env->getFunction('lazyBackground')->getCallable()(($context["background"] ?? null))) . ">"), "html", null, true);
        echo "
";
        // line 28
        echo twig_escape_filter($this->env, ((("<div " . ($context["rowClasses"] ?? null)) . ($context["parallax"] ?? null)) . " >"), "html", null, true);
        echo "

  ";
        // line 30
        if ((($context["bg_overlay"] ?? null) && ($this->env->getFunction('getOpacity')->getCallable()(($context["bg_overlay"] ?? null), "normal") || $this->env->getFunction('getOpacity')->getCallable()(($context["bg_overlay"] ?? null), "hover")))) {
            // line 31
            echo "      ";
            $context["overlayClass"] = (("qx-background-overlay " . ($context["id"] ?? null)) . "-background-overlay");
            // line 32
            echo "      <div class=\"";
            echo twig_escape_filter($this->env, ($context["overlayClass"] ?? null), "html", null, true);
            echo "\"></div>
  ";
        }
        // line 34
        echo "   ";
        echo twig_get_attribute($this->env, $this->source, ($context["renderer"] ?? null), "render", [0 => (($__internal_compile_0 = ($context["node"] ?? null)) && is_array($__internal_compile_0) || $__internal_compile_0 instanceof ArrayAccess ? ($__internal_compile_0["children"] ?? null) : null), 1 => null, 2 => "frontend"], "method", false, false, false, 34);
        echo "
";
        // line 35
        echo "</div>";
        echo "
";
        // line 36
        echo twig_escape_filter($this->env, (("</" . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "layout_fields_group", [], "any", false, false, false, 36), "html_tag", [], "any", false, false, false, 36)) . ">"), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "row/partials/html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  123 => 36,  119 => 35,  114 => 34,  108 => 32,  105 => 31,  103 => 30,  98 => 28,  94 => 27,  91 => 25,  87 => 23,  84 => 22,  81 => 21,  78 => 20,  75 => 19,  72 => 18,  69 => 17,  66 => 16,  64 => 15,  62 => 14,  59 => 13,  57 => 12,  55 => 11,  53 => 10,  51 => 9,  49 => 8,  47 => 7,  44 => 6,  42 => 5,  41 => 4,  40 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "row/partials/html.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/row/partials/html.twig");
    }
}
