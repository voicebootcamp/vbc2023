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

/* video/partials/html.twig */
class __TwigTemplate_ade4f00c7431ffcee48fc06694fefe5d6850189d8f747ff8e0b5a510da8df90f extends \Twig\Template
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
        $context["classes"] = $this->env->getFunction('classNames')->getCallable()("qx-element qx-element-video-v2", $this->env->getFunction('visibilityClass')->getCallable()(($context["visibility"] ?? null)), ($context["class"] ?? null));
        // line 5
        $context["animation"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 5), "animation", [], "any", false, false, false, 5);
        // line 6
        $context["animationRepeat"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 6), "animation_repeat", [], "any", false, false, false, 6);
        // line 7
        $context["animationDelay"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 7), "animation_delay", [], "any", false, false, false, 7);
        // line 8
        $context["background"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "background_fields_group", [], "any", false, false, false, 8), "background", [], "any", false, false, false, 8);
        // line 9
        echo "
";
        // line 10
        $this->loadTemplate("video/partials/html.twig", "video/partials/html.twig", 10, "1601547402")->display(twig_array_merge($context, ["id" =>         // line 11
($context["id"] ?? null), "classes" =>         // line 12
($context["classes"] ?? null), "animation" =>         // line 13
($context["animation"] ?? null), "animationRepeat" =>         // line 14
($context["animationRepeat"] ?? null), "animationDelay" =>         // line 15
($context["animationDelay"] ?? null), "background" =>         // line 16
($context["background"] ?? null)]));
    }

    public function getTemplateName()
    {
        return "video/partials/html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  63 => 16,  62 => 15,  61 => 14,  60 => 13,  59 => 12,  58 => 11,  57 => 10,  54 => 9,  52 => 8,  50 => 7,  48 => 6,  46 => 5,  44 => 4,  41 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "video/partials/html.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/video/partials/html.twig");
    }
}


/* video/partials/html.twig */
class __TwigTemplate_ade4f00c7431ffcee48fc06694fefe5d6850189d8f747ff8e0b5a510da8df90f___1601547402 extends \Twig\Template
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
        // line 10
        return "animation.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("animation.twig", "video/partials/html.twig", 10);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 18
    public function block_element($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 19
        echo "        ";
        echo twig_escape_filter($this->env, $this->env->getFunction('video')->getCallable()(($context["id"] ?? null), twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "video_fg_text", [], "any", false, false, false, 19)), "html", null, true);
        echo "
    ";
    }

    public function getTemplateName()
    {
        return "video/partials/html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  123 => 19,  119 => 18,  108 => 10,  63 => 16,  62 => 15,  61 => 14,  60 => 13,  59 => 12,  58 => 11,  57 => 10,  54 => 9,  52 => 8,  50 => 7,  48 => 6,  46 => 5,  44 => 4,  41 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "video/partials/html.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/video/partials/html.twig");
    }
}
