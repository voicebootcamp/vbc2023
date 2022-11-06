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

/* animation.twig */
class __TwigTemplate_cd8275b6cfe730f94ccb814c4989c5fa2e5b7b5bbd404026db8b8d3fa72ed762 extends \Twig\Template
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
        if ((($context["animation"] ?? null) != "none")) {
            // line 2
            echo "
    ";
            // line 3
            if ( !twig_test_empty(($context["animation"] ?? null))) {
                // line 4
                echo "        ";
                $context["animation"] = (("cls:" . ($context["animation"] ?? null)) . ";");
                // line 5
                echo "    ";
            }
            // line 6
            echo "
    ";
            // line 8
            echo "    ";
            if (($context["animationRepeat"] ?? null)) {
                // line 9
                echo "        ";
                $context["animation"] = (($context["animation"] ?? null) . "repeat:ture;");
                // line 10
                echo "    ";
            }
            // line 11
            echo "
    ";
            // line 13
            echo "    ";
            if (($context["animationDelay"] ?? null)) {
                // line 14
                echo "        ";
                $context["animation"] = (((($context["animation"] ?? null) . "delay:") . twig_get_attribute($this->env, $this->source, ($context["animationDelay"] ?? null), "value", [], "any", false, false, false, 14)) . twig_get_attribute($this->env, $this->source, ($context["animationDelay"] ?? null), "unit", [], "any", false, false, false, 14));
                // line 15
                echo "    ";
            }
        }
        // line 17
        echo "
";
        // line 18
        $context["parallax"] = "";
        // line 19
        if ((($context["background"] ?? null) && (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["background"] ?? null), "state", [], "any", false, false, false, 19), "normal", [], "any", false, false, false, 19), "properties", [], "any", false, false, false, 19), "parallax_method", [], "any", false, false, false, 19) == "js"))) {
            // line 20
            echo "    ";
            $context["parallaxInfo"] = "";
            // line 21
            echo "    ";
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["background"] ?? null), "state", [], "any", false, false, false, 21), "normal", [], "any", false, false, false, 21), "properties", [], "any", false, false, false, 21), "js_parallax_y", [], "any", false, false, false, 21)) {
                // line 22
                echo "        ";
                $context["parallaxInfo"] = (((($context["parallaxInfo"] ?? null) . "bgy:") . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["background"] ?? null), "state", [], "any", false, false, false, 22), "normal", [], "any", false, false, false, 22), "properties", [], "any", false, false, false, 22), "js_parallax_y", [], "any", false, false, false, 22)) . ";");
                // line 23
                echo "    ";
            }
            // line 24
            echo "    ";
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["background"] ?? null), "state", [], "any", false, false, false, 24), "normal", [], "any", false, false, false, 24), "properties", [], "any", false, false, false, 24), "js_parallax_x", [], "any", false, false, false, 24)) {
                // line 25
                echo "        ";
                $context["parallaxInfo"] = (((($context["parallaxInfo"] ?? null) . "bgx:") . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["background"] ?? null), "state", [], "any", false, false, false, 25), "normal", [], "any", false, false, false, 25), "properties", [], "any", false, false, false, 25), "js_parallax_x", [], "any", false, false, false, 25)) . ";");
                // line 26
                echo "    ";
            }
            // line 27
            echo "    ";
            $context["parallax"] = ((" qx-parallax=\"" . ($context["parallaxInfo"] ?? null)) . "\"");
        }
        // line 29
        echo "


";
        // line 32
        $context["classes"] = $this->env->getFunction('classNames')->getCallable()(($context["classes"] ?? null), ["lazyload lazyload-bg" => $this->env->getFunction('ifElementHasBackground')->getCallable()(        // line 34
($context["background"] ?? null))]);
        // line 37
        echo "<div class=\"qx-element-wrap\">
    <";
        // line 38
        echo twig_escape_filter($this->env, ((array_key_exists("tagName", $context)) ? (_twig_default_filter(($context["tagName"] ?? null), "div")) : ("div")), "html", null, true);
        echo "
    ";
        // line 39
        if ((array_key_exists("id", $context) && ($context["id"] ?? null))) {
            echo " id=\"";
            echo twig_escape_filter($this->env, ($context["id"] ?? null), "html", null, true);
            echo "\" ";
        }
        // line 40
        echo "    ";
        if ((array_key_exists("classes", $context) && ($context["classes"] ?? null))) {
            echo " class=\"";
            echo twig_escape_filter($this->env, ($context["classes"] ?? null), "html", null, true);
            echo "\" ";
        }
        // line 41
        echo "    ";
        if (( !twig_test_empty(($context["animation"] ?? null)) && (($context["animation"] ?? null) != "none"))) {
            echo " qx-scrollspy=\"";
            echo twig_escape_filter($this->env, ($context["animation"] ?? null), "html", null, true);
            echo "\" ";
        }
        // line 42
        echo "    ";
        echo twig_escape_filter($this->env, ($context["parallax"] ?? null), "html", null, true);
        echo "
    ";
        // line 43
        echo twig_escape_filter($this->env, $this->env->getFunction('lazyBackground')->getCallable()(($context["background"] ?? null)), "html", null, true);
        echo "
    ";
        // line 44
        echo twig_escape_filter($this->env, ((array_key_exists("attributes", $context)) ? (_twig_default_filter(($context["attributes"] ?? null), " ")) : (" ")), "html", null, true);
        echo "
    >
    ";
        // line 46
        $this->displayBlock('element', $context, $blocks);
        // line 49
        echo "
</";
        // line 50
        echo twig_escape_filter($this->env, ((array_key_exists("tagName", $context)) ? (_twig_default_filter(($context["tagName"] ?? null), "div")) : ("div")), "html", null, true);
        echo ">
</div>
";
    }

    // line 46
    public function block_element($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 47
        echo "
    ";
    }

    public function getTemplateName()
    {
        return "animation.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  173 => 47,  169 => 46,  162 => 50,  159 => 49,  157 => 46,  152 => 44,  148 => 43,  143 => 42,  136 => 41,  129 => 40,  123 => 39,  119 => 38,  116 => 37,  114 => 34,  113 => 32,  108 => 29,  104 => 27,  101 => 26,  98 => 25,  95 => 24,  92 => 23,  89 => 22,  86 => 21,  83 => 20,  81 => 19,  79 => 18,  76 => 17,  72 => 15,  69 => 14,  66 => 13,  63 => 11,  60 => 10,  57 => 9,  54 => 8,  51 => 6,  48 => 5,  45 => 4,  43 => 3,  40 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "animation.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/shared/animation.twig");
    }
}
