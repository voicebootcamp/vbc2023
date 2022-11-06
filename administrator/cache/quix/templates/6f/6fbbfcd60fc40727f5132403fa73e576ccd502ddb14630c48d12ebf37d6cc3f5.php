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

/* raw-html/partials/html.twig */
class __TwigTemplate_ad71b3c74aa875ed8f4b67a6b5f921810ac54e520855e77c542ab8da28250738 extends \Twig\Template
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
        $context["content"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "raw_html_group", [], "any", false, false, false, 4), "content", [], "any", false, false, false, 4);
        // line 5
        $context["event"] = ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "raw_html_group", [], "any", false, true, false, 5), "prepare_content", [], "any", true, true, false, 5)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "raw_html_group", [], "any", false, true, false, 5), "prepare_content", [], "any", false, false, false, 5), "false")) : ("false"));
        // line 6
        echo "
";
        // line 7
        $context["classes"] = $this->env->getFunction('classNames')->getCallable()("qx-element qx-raw-html-v2", $this->env->getFunction('visibilityClass')->getCallable()(($context["visibility"] ?? null)), ($context["class"] ?? null));
        // line 8
        $context["animation"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 8), "animation", [], "any", false, false, false, 8);
        // line 9
        $context["animationRepeat"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 9), "animation_repeat", [], "any", false, false, false, 9);
        // line 10
        $context["animationDelay"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 10), "animation_delay", [], "any", false, false, false, 10);
        // line 11
        $context["background"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "background_fields_group", [], "any", false, false, false, 11), "background", [], "any", false, false, false, 11);
        // line 12
        echo "
";
        // line 13
        $this->loadTemplate("raw-html/partials/html.twig", "raw-html/partials/html.twig", 13, "1971345199")->display(twig_array_merge($context, ["id" =>         // line 14
($context["id"] ?? null), "classes" =>         // line 15
($context["classes"] ?? null), "animation" =>         // line 16
($context["animation"] ?? null), "animationRepeat" =>         // line 17
($context["animationRepeat"] ?? null), "animationDelay" =>         // line 18
($context["animationDelay"] ?? null), "background" =>         // line 19
($context["background"] ?? null)]));
    }

    public function getTemplateName()
    {
        return "raw-html/partials/html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  70 => 19,  69 => 18,  68 => 17,  67 => 16,  66 => 15,  65 => 14,  64 => 13,  61 => 12,  59 => 11,  57 => 10,  55 => 9,  53 => 8,  51 => 7,  48 => 6,  46 => 5,  44 => 4,  41 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "raw-html/partials/html.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/raw-html/partials/html.twig");
    }
}


/* raw-html/partials/html.twig */
class __TwigTemplate_ad71b3c74aa875ed8f4b67a6b5f921810ac54e520855e77c542ab8da28250738___1971345199 extends \Twig\Template
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
        // line 13
        return "animation.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("animation.twig", "raw-html/partials/html.twig", 13);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 21
    public function block_element($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 22
        echo "        ";
        echo $this->env->getFunction('prepareContent')->getCallable()(($context["content"] ?? null), ($context["event"] ?? null));
        echo "
    ";
    }

    public function getTemplateName()
    {
        return "raw-html/partials/html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  130 => 22,  126 => 21,  115 => 13,  70 => 19,  69 => 18,  68 => 17,  67 => 16,  66 => 15,  65 => 14,  64 => 13,  61 => 12,  59 => 11,  57 => 10,  55 => 9,  53 => 8,  51 => 7,  48 => 6,  46 => 5,  44 => 4,  41 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "raw-html/partials/html.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/raw-html/partials/html.twig");
    }
}
