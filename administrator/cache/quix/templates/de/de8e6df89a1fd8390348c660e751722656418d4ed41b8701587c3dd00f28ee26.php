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

/* joomla-module/partials/html.twig */
class __TwigTemplate_8ee85ac5e60180c63e51670ec8b42829c4efa10d1d232562ff2c98aa48659b05 extends \Twig\Template
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
        $context["module_id"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "modules_core", [], "any", false, false, false, 4), "module_id", [], "any", false, false, false, 4);
        // line 5
        $context["classes"] = $this->env->getFunction('classNames')->getCallable()("qx-element qx-element-joomla-mod-v2", $this->env->getFunction('visibilityClass')->getCallable()(($context["visibility"] ?? null)), ($context["class"] ?? null));
        // line 6
        $context["animation"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 6), "animation", [], "any", false, false, false, 6);
        // line 7
        $context["animationRepeat"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 7), "animation_repeat", [], "any", false, false, false, 7);
        // line 8
        $context["animationDelay"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 8), "animation_delay", [], "any", false, false, false, 8);
        // line 9
        $context["background"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "background_fields_group", [], "any", false, false, false, 9), "background", [], "any", false, false, false, 9);
        // line 10
        echo "
";
        // line 11
        $this->loadTemplate("joomla-module/partials/html.twig", "joomla-module/partials/html.twig", 11, "1967332899")->display(twig_array_merge($context, ["id" =>         // line 12
($context["id"] ?? null), "classes" =>         // line 13
($context["classes"] ?? null), "animation" =>         // line 14
($context["animation"] ?? null), "animationRepeat" =>         // line 15
($context["animationRepeat"] ?? null), "animationDelay" =>         // line 16
($context["animationDelay"] ?? null), "background" =>         // line 17
($context["background"] ?? null)]));
    }

    public function getTemplateName()
    {
        return "joomla-module/partials/html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  65 => 17,  64 => 16,  63 => 15,  62 => 14,  61 => 13,  60 => 12,  59 => 11,  56 => 10,  54 => 9,  52 => 8,  50 => 7,  48 => 6,  46 => 5,  44 => 4,  41 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "joomla-module/partials/html.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/joomla-module/partials/html.twig");
    }
}


/* joomla-module/partials/html.twig */
class __TwigTemplate_8ee85ac5e60180c63e51670ec8b42829c4efa10d1d232562ff2c98aa48659b05___1967332899 extends \Twig\Template
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
        // line 11
        return "animation.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("animation.twig", "joomla-module/partials/html.twig", 11);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 19
    public function block_element($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 20
        echo "        ";
        echo $this->env->getFunction('getJoomlaModule')->getCallable()(($context["module_id"] ?? null), "none");
        echo "
    ";
    }

    public function getTemplateName()
    {
        return "joomla-module/partials/html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  125 => 20,  121 => 19,  110 => 11,  65 => 17,  64 => 16,  63 => 15,  62 => 14,  61 => 13,  60 => 12,  59 => 11,  56 => 10,  54 => 9,  52 => 8,  50 => 7,  48 => 6,  46 => 5,  44 => 4,  41 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "joomla-module/partials/html.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/joomla-module/partials/html.twig");
    }
}
