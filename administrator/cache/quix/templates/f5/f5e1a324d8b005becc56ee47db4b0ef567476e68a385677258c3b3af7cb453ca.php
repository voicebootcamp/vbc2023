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

/* heading/partials/html.twig */
class __TwigTemplate_da6a52fa4dddca9b89b9a3f34b99cfc0a36aa16b27c4f609e360c42840107697 extends \Twig\Template
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
        echo "
";
        // line 4
        $context["tag"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "heading_fields_group", [], "any", false, false, false, 4), "html_tag", [], "any", false, false, false, 4);
        // line 5
        $context["link"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "heading_links_fields_group", [], "any", false, false, false, 5), "link", [], "any", false, false, false, 5);
        // line 6
        $context["title"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "heading_fields_group", [], "any", false, false, false, 6), "title", [], "any", false, false, false, 6);
        // line 7
        echo "
";
        // line 8
        $context["classes"] = $this->env->getFunction('classNames')->getCallable()("qx-element qx-element-heading-v2", $this->env->getFunction('visibilityClass')->getCallable()(($context["visibility"] ?? null)), ($context["class"] ?? null));
        // line 9
        $context["animation"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 9), "animation", [], "any", false, false, false, 9);
        // line 10
        $context["animationRepeat"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 10), "animation_repeat", [], "any", false, false, false, 10);
        // line 11
        $context["animationDelay"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 11), "animation_delay", [], "any", false, false, false, 11);
        // line 12
        $context["background"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "background_fields_group", [], "any", false, false, false, 12), "background", [], "any", false, false, false, 12);
        // line 13
        echo "
";
        // line 14
        $this->loadTemplate("heading/partials/html.twig", "heading/partials/html.twig", 14, "1772583845")->display(twig_array_merge($context, ["id" =>         // line 15
($context["id"] ?? null), "classes" =>         // line 16
($context["classes"] ?? null), "animation" =>         // line 17
($context["animation"] ?? null), "animationRepeat" =>         // line 18
($context["animationRepeat"] ?? null), "animationDelay" =>         // line 19
($context["animationDelay"] ?? null), "background" =>         // line 20
($context["background"] ?? null)]));
    }

    public function getTemplateName()
    {
        return "heading/partials/html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  72 => 20,  71 => 19,  70 => 18,  69 => 17,  68 => 16,  67 => 15,  66 => 14,  63 => 13,  61 => 12,  59 => 11,  57 => 10,  55 => 9,  53 => 8,  50 => 7,  48 => 6,  46 => 5,  44 => 4,  41 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "heading/partials/html.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/heading/partials/html.twig");
    }
}


/* heading/partials/html.twig */
class __TwigTemplate_da6a52fa4dddca9b89b9a3f34b99cfc0a36aa16b27c4f609e360c42840107697___1772583845 extends \Twig\Template
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
        // line 14
        return "animation.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("animation.twig", "heading/partials/html.twig", 14);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 22
    public function block_element($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 23
        echo "        ";
        if ((($context["mode"] ?? null) == "builder")) {
            echo " ";
            // line 24
            echo "            ";
            echo twig_escape_filter($this->env, $this->env->getFilter('link')->getCallable()($this->env->getFilter('wrap')->getCallable()((((("<span " . $this->env->getFunction('inlineEditor')->getCallable()("general.heading_fields_group.title")) . ">") . ($context["title"] ?? null)) . "</span>"), ($context["tag"] ?? null)), ($context["link"] ?? null)), "html", null, true);
            echo "
        ";
        } else {
            // line 26
            echo "            ";
            echo twig_escape_filter($this->env, $this->env->getFilter('link')->getCallable()($this->env->getFilter('wrap')->getCallable()($this->env->getFilter('wrap')->getCallable()(($context["title"] ?? null), "span"), ($context["tag"] ?? null)), ($context["link"] ?? null)), "html", null, true);
            echo "
        ";
        }
        // line 28
        echo "    ";
    }

    public function getTemplateName()
    {
        return "heading/partials/html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  148 => 28,  142 => 26,  136 => 24,  132 => 23,  128 => 22,  117 => 14,  72 => 20,  71 => 19,  70 => 18,  69 => 17,  68 => 16,  67 => 15,  66 => 14,  63 => 13,  61 => 12,  59 => 11,  57 => 10,  55 => 9,  53 => 8,  50 => 7,  48 => 6,  46 => 5,  44 => 4,  41 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "heading/partials/html.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/heading/partials/html.twig");
    }
}
