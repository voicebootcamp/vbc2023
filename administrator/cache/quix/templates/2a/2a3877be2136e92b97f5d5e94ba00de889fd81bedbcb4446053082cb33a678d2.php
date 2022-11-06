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

/* column/partials/html.twig */
class __TwigTemplate_aa6d5886417228a9db58ab3860974e3eafc6acdaf1de95c6e10142bdec3a99c0 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'element' => [$this, 'block_element'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        $context["id"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "identifier", [], "any", false, false, false, 1), "id", [], "any", false, false, false, 1);
        // line 2
        echo "
";
        // line 3
        $context["class"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "identifier", [], "any", false, false, false, 3), "class", [], "any", false, false, false, 3);
        // line 4
        $context["classes"] = $this->env->getFunction('classNames')->getCallable()("qx-column", $this->env->getFunction('visibilityClassNode')->getCallable()(($context["visibility"] ?? null)), ($context["grid"] ?? null), ($context["class"] ?? null));
        // line 5
        $context["bg_overlay"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "background_overlay_fields_group", [], "any", false, false, false, 5), "background_overlay", [], "any", false, false, false, 5);
        // line 6
        echo "
";
        // line 7
        $context["animation"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 7), "animation", [], "any", false, false, false, 7);
        // line 8
        $context["animationRepeat"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 8), "animation_repeat", [], "any", false, false, false, 8);
        // line 9
        $context["animationDelay"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 9), "animation_delay", [], "any", false, false, false, 9);
        // line 10
        $context["background"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "background_fields_group", [], "any", false, false, false, 10), "background", [], "any", false, false, false, 10);
        // line 11
        echo "
";
        // line 12
        if ((($context["animation"] ?? null) != "none")) {
            // line 13
            echo "
    ";
            // line 14
            if ( !twig_test_empty(($context["animation"] ?? null))) {
                // line 15
                echo "        ";
                $context["animation"] = (("cls:" . ($context["animation"] ?? null)) . ";");
                // line 16
                echo "    ";
            }
            // line 17
            echo "
    ";
            // line 19
            echo "    ";
            if (($context["animationRepeat"] ?? null)) {
                // line 20
                echo "        ";
                $context["animation"] = (($context["animation"] ?? null) . "repeat:ture;");
                // line 21
                echo "    ";
            }
            // line 22
            echo "
    ";
            // line 24
            echo "    ";
            if (($context["animationDelay"] ?? null)) {
                // line 25
                echo "        ";
                $context["animation"] = (((($context["animation"] ?? null) . "delay:") . twig_get_attribute($this->env, $this->source, ($context["animationDelay"] ?? null), "value", [], "any", false, false, false, 25)) . twig_get_attribute($this->env, $this->source, ($context["animationDelay"] ?? null), "unit", [], "any", false, false, false, 25));
                // line 26
                echo "    ";
            }
        }
        // line 28
        echo "
<";
        // line 29
        echo twig_escape_filter($this->env, ((array_key_exists("tagName", $context)) ? (_twig_default_filter(($context["tagName"] ?? null), "div")) : ("div")), "html", null, true);
        echo "
  ";
        // line 30
        if ((array_key_exists("id", $context) && ($context["id"] ?? null))) {
            echo " id=\"";
            echo twig_escape_filter($this->env, ($context["id"] ?? null), "html", null, true);
            echo "\" ";
        }
        // line 31
        echo "  ";
        if ((array_key_exists("classes", $context) && ($context["classes"] ?? null))) {
            echo " class=\"";
            echo twig_escape_filter($this->env, ($context["classes"] ?? null), "html", null, true);
            echo "\" ";
        }
        // line 32
        echo "  ";
        if (( !twig_test_empty(($context["animation"] ?? null)) && (($context["animation"] ?? null) != "none"))) {
            echo " qx-scrollspy=\"";
            echo twig_escape_filter($this->env, ($context["animation"] ?? null), "html", null, true);
            echo "\" ";
        }
        // line 33
        echo ">

";
        // line 35
        $this->displayBlock('element', $context, $blocks);
        // line 48
        echo "
</";
        // line 49
        echo twig_escape_filter($this->env, ((array_key_exists("tagName", $context)) ? (_twig_default_filter(($context["tagName"] ?? null), "div")) : ("div")), "html", null, true);
        echo ">
";
    }

    // line 35
    public function block_element($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 36
        echo "    ";
        if (($context["renderer"] ?? null)) {
            // line 37
            echo "        <div class=\"qx-col-wrap lazyload\" ";
            echo twig_escape_filter($this->env, $this->env->getFunction('lazyBackground')->getCallable()(($context["background"] ?? null)), "html", null, true);
            echo ">
            ";
            // line 38
            if ((($context["bg_overlay"] ?? null) && ($this->env->getFunction('getOpacity')->getCallable()(($context["bg_overlay"] ?? null), "normal") || $this->env->getFunction('getOpacity')->getCallable()(($context["bg_overlay"] ?? null), "hover")))) {
                // line 39
                echo "                ";
                $context["overlayClass"] = (("qx-background-overlay " . ($context["id"] ?? null)) . "-background-overlay");
                // line 40
                echo "                <div class=\"";
                echo twig_escape_filter($this->env, ($context["overlayClass"] ?? null), "html", null, true);
                echo "\"></div>
            ";
            }
            // line 42
            echo "            <div class=\"qx-elements-wrap\">
                ";
            // line 43
            echo twig_get_attribute($this->env, $this->source, ($context["renderer"] ?? null), "render", [0 => (($__internal_compile_0 = ($context["node"] ?? null)) && is_array($__internal_compile_0) || $__internal_compile_0 instanceof ArrayAccess ? ($__internal_compile_0["children"] ?? null) : null), 1 => null, 2 => "frontend"], "method", false, false, false, 43);
            echo "
            </div>
        </div>
    ";
        }
    }

    public function getTemplateName()
    {
        return "column/partials/html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  169 => 43,  166 => 42,  160 => 40,  157 => 39,  155 => 38,  150 => 37,  147 => 36,  143 => 35,  137 => 49,  134 => 48,  132 => 35,  128 => 33,  121 => 32,  114 => 31,  108 => 30,  104 => 29,  101 => 28,  97 => 26,  94 => 25,  91 => 24,  88 => 22,  85 => 21,  82 => 20,  79 => 19,  76 => 17,  73 => 16,  70 => 15,  68 => 14,  65 => 13,  63 => 12,  60 => 11,  58 => 10,  56 => 9,  54 => 8,  52 => 7,  49 => 6,  47 => 5,  45 => 4,  43 => 3,  40 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "column/partials/html.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/column/partials/html.twig");
    }
}
