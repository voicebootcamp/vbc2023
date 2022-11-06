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

/* heading/partials/style.twig */
class __TwigTemplate_89a2e1d637ce5d709b0d3a5e242490b491a39672aa2d12b237d31128c3fa5766 extends \Twig\Template
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
        $this->loadTemplate("global.twig", "heading/partials/style.twig", 1)->display($context);
        // line 2
        $context["tag"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "heading_fields_group", [], "any", false, false, false, 2), "html_tag", [], "any", false, false, false, 2);
        // line 3
        $context["id"] = ("#" . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "identifier", [], "any", false, false, false, 3), "id", [], "any", false, false, false, 3));
        // line 4
        echo "
";
        // line 6
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "typography", [0 => ((($context["id"] ?? null) . " ") . ($context["tag"] ?? null)), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "heading_typo_fields_group", [], "any", false, false, false, 6), "font", [], "any", false, false, false, 6)], "method", false, false, false, 6), "html", null, true);
        echo "

";
        // line 9
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "alignment", [0 => ($context["id"] ?? null), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "opt_fields_group", [], "any", false, false, false, 9), "nalignment", [], "any", false, false, false, 9)], "method", false, false, false, 9), "html", null, true);
        echo "

";
        // line 12
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ((($context["id"] ?? null) . " ") . ($context["tag"] ?? null)), 1 => "color", 2 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "opt_fields_group", [], "any", false, false, false, 12), "text_color", [], "any", false, false, false, 12)], "method", false, false, false, 12), "html", null, true);
        echo "

";
        // line 14
        if (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "opt_fields_group", [], "any", false, true, false, 14), "enable_bg", [], "any", true, true, false, 14)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "opt_fields_group", [], "any", false, true, false, 14), "enable_bg", [], "any", false, false, false, 14), false)) : (false))) {
            // line 15
            echo "\t";
            // line 16
            echo "\t";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => ((($context["id"] ?? null) . " ") . ($context["tag"] ?? null)), 1 => "background-color", 2 => "#222"], "method", false, false, false, 16), "html", null, true);
            echo "
\t";
            // line 17
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "background", [0 => ((($context["id"] ?? null) . " ") . ($context["tag"] ?? null)), 1 => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["styles"] ?? null), "opt_fields_group", [], "any", false, false, false, 17), "text_bg", [], "any", false, false, false, 17)], "method", false, false, false, 17), "html", null, true);
            echo "

\t";
            // line 20
            echo "\t";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => (((((((($context["id"] ?? null) . " ") . ($context["tag"] ?? null)) . ",") . ($context["id"] ?? null)) . " ") . ($context["tag"] ?? null)) . ":hover"), 1 => "background-clip", 2 => "text"], "method", false, false, false, 20), "html", null, true);
            echo "
\t";
            // line 21
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => (((((((($context["id"] ?? null) . " ") . ($context["tag"] ?? null)) . ",") . ($context["id"] ?? null)) . " ") . ($context["tag"] ?? null)) . ":hover"), 1 => "-webkit-background-clip", 2 => "text"], "method", false, false, false, 21), "html", null, true);
            echo "
\t";
            // line 22
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "css", [0 => (((((((($context["id"] ?? null) . " ") . ($context["tag"] ?? null)) . ",") . ($context["id"] ?? null)) . " ") . ($context["tag"] ?? null)) . ":hover"), 1 => "color", 2 => "transparent"], "method", false, false, false, 22), "html", null, true);
            echo "
";
        }
        // line 24
        echo "
";
        // line 25
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["style"] ?? null), "load", [0 => ($context["id"] ?? null)], "method", false, false, false, 25), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "heading/partials/style.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  92 => 25,  89 => 24,  84 => 22,  80 => 21,  75 => 20,  70 => 17,  65 => 16,  63 => 15,  61 => 14,  56 => 12,  51 => 9,  46 => 6,  43 => 4,  41 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "heading/partials/style.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/heading/partials/style.twig");
    }
}
