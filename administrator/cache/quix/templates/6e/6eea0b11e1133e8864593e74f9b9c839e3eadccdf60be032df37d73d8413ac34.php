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

/* video/partials/script.twig */
class __TwigTemplate_e5c60a2f077f79158e8a496492f072317148ddf7ab4f13883ec4c3344744e53a extends \Twig\Template
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
        echo "(function(){var v=\"";
        echo twig_escape_filter($this->env, ("#video-" . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "identifier", [], "any", false, false, false, 1), "id", [], "any", false, false, false, 1)), "html", null, true);
        echo "\";if(typeof Plyr === 'function'){new Plyr(v)}else{window.PlyrQueue=window.PlyrQueue||[];window.PlyrQueue.push(v)}})();
";
    }

    public function getTemplateName()
    {
        return "video/partials/script.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "video/partials/script.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/video/partials/script.twig");
    }
}
