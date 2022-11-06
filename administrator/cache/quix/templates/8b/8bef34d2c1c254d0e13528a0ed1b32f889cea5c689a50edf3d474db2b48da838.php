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

/* icon-list/partials/html.twig */
class __TwigTemplate_215b4b70cd4818902c9e471f4e1abf75d18b3c1d872b75553da492151e24b264 extends \Twig\Template
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
        $context["dataAll"] = $this->env->getFunction('allfield')->getCallable()();
        // line 2
        echo "
";
        // line 3
        $context["id"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "identifier", [], "any", false, false, false, 3), "id", [], "any", false, false, false, 3);
        // line 4
        $context["class"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "identifier", [], "any", false, false, false, 4), "class", [], "any", false, false, false, 4);
        // line 5
        $context["fieldsGroup"] = twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "icon_list", [], "any", false, false, false, 5);
        // line 6
        $context["classes"] = $this->env->getFunction('classNames')->getCallable()("qx-element qx-element-icon-list", $this->env->getFunction('visibilityClass')->getCallable()(($context["visibility"] ?? null)), ($context["class"] ?? null));
        // line 7
        $context["commonIcon"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "iconlist_fg_layout", [], "any", false, false, false, 7), "common_icon", [], "any", false, false, false, 7);
        // line 8
        echo "
";
        // line 9
        $context["chooseLayout"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "iconlist_fg_layout", [], "any", false, false, false, 9), "choose_layout", [], "any", false, false, false, 9);
        // line 10
        echo "
";
        // line 11
        $context["layoutClass"] = (((((((((("desktop-" . twig_get_attribute($this->env, $this->source, ($context["chooseLayout"] ?? null), "desktop", [], "any", false, false, false, 11)) . "-layout") . " ") . "tablet-") . twig_get_attribute($this->env, $this->source, ($context["chooseLayout"] ?? null), "tablet", [], "any", false, false, false, 11)) . "-layout") . " ") . "phone-") . twig_get_attribute($this->env, $this->source, ($context["chooseLayout"] ?? null), "phone", [], "any", false, false, false, 11)) . "-layout");
        // line 12
        $context["layoutAlignment"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["general"] ?? null), "iconlist_fg_layout", [], "any", false, false, false, 12), "alignment", [], "any", false, false, false, 12);
        // line 13
        $context["layoutAlign"] = ((((((((((twig_get_attribute($this->env, $this->source, ($context["chooseLayout"] ?? null), "desktop", [], "any", false, false, false, 13) . "-alignDesktop-") . twig_get_attribute($this->env, $this->source, ($context["layoutAlignment"] ?? null), "desktop", [], "any", false, false, false, 13)) . " ") . twig_get_attribute($this->env, $this->source, ($context["chooseLayout"] ?? null), "tablet", [], "any", false, false, false, 13)) . "-alignTablet-") . twig_get_attribute($this->env, $this->source, ($context["layoutAlignment"] ?? null), "tablet", [], "any", false, false, false, 13)) . " ") . twig_get_attribute($this->env, $this->source, ($context["chooseLayout"] ?? null), "phone", [], "any", false, false, false, 13)) . "-alignPhone-") . twig_get_attribute($this->env, $this->source, ($context["layoutAlignment"] ?? null), "phone", [], "any", false, false, false, 13));
        // line 14
        echo "
";
        // line 16
        $context["animation"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 16), "animation", [], "any", false, false, false, 16);
        // line 17
        $context["animationRepeat"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 17), "animation_repeat", [], "any", false, false, false, 17);
        // line 18
        $context["animationDelay"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "animation_fields_group", [], "any", false, false, false, 18), "animation_delay", [], "any", false, false, false, 18);
        // line 19
        $context["background"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["advanced"] ?? null), "background_fields_group", [], "any", false, false, false, 19), "background", [], "any", false, false, false, 19);
        // line 20
        echo "
";
        // line 21
        $this->loadTemplate("icon-list/partials/html.twig", "icon-list/partials/html.twig", 21, "1145534716")->display(twig_array_merge($context, ["id" =>         // line 22
($context["id"] ?? null), "classes" =>         // line 23
($context["classes"] ?? null), "animation" =>         // line 24
($context["animation"] ?? null), "animationRepeat" =>         // line 25
($context["animationRepeat"] ?? null), "animationDelay" =>         // line 26
($context["animationDelay"] ?? null), "background" =>         // line 27
($context["background"] ?? null)]));
    }

    public function getTemplateName()
    {
        return "icon-list/partials/html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  86 => 27,  85 => 26,  84 => 25,  83 => 24,  82 => 23,  81 => 22,  80 => 21,  77 => 20,  75 => 19,  73 => 18,  71 => 17,  69 => 16,  66 => 14,  64 => 13,  62 => 12,  60 => 11,  57 => 10,  55 => 9,  52 => 8,  50 => 7,  48 => 6,  46 => 5,  44 => 4,  42 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "icon-list/partials/html.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/icon-list/partials/html.twig");
    }
}


/* icon-list/partials/html.twig */
class __TwigTemplate_215b4b70cd4818902c9e471f4e1abf75d18b3c1d872b75553da492151e24b264___1145534716 extends \Twig\Template
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
        // line 21
        return "animation.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("animation.twig", "icon-list/partials/html.twig", 21);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 29
    public function block_element($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 30
        echo "        <div class=\"";
        echo twig_escape_filter($this->env, ($context["id"] ?? null), "html", null, true);
        echo "-wrapper\">
            ";
        // line 31
        if (($context["fieldsGroup"] ?? null)) {
            // line 32
            echo "                <ul class=\"qx-flex ";
            echo twig_escape_filter($this->env, ($context["layoutClass"] ?? null), "html", null, true);
            echo " ";
            echo twig_escape_filter($this->env, ($context["layoutAlign"] ?? null), "html", null, true);
            echo "\">
                    ";
            // line 33
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["fieldsGroup"] ?? null));
            foreach ($context['_seq'] as $context["index"] => $context["fields"]) {
                // line 34
                echo "                        <li class=\"item-";
                echo twig_escape_filter($this->env, $context["index"], "html", null, true);
                echo "\">
                            ";
                // line 35
                $context["data"] = $this->env->getFunction('fieldsGroup')->getCallable()(($context["fieldsGroup"] ?? null), $context["index"]);
                // line 36
                echo "
                            ";
                // line 37
                $context["src"] = twig_get_attribute($this->env, $this->source, ($context["data"] ?? null), "icon", [], "any", false, false, false, 37);
                // line 38
                echo "                            ";
                $context["link"] = twig_get_attribute($this->env, $this->source, ($context["data"] ?? null), "link", [], "any", false, false, false, 38);
                // line 39
                echo "                            ";
                $context["alt_text"] = "";
                // line 40
                echo "
                            ";
                // line 41
                $context["string"] = "";
                // line 42
                echo "
                            ";
                // line 43
                if (twig_get_attribute($this->env, $this->source, ($context["src"] ?? null), "source", [], "any", false, false, false, 43)) {
                    // line 44
                    echo "                                ";
                    if ((twig_get_attribute($this->env, $this->source, ($context["src"] ?? null), "type", [], "any", false, false, false, 44) == "svg")) {
                        // line 45
                        echo "                                    ";
                        $context["string"] = (($context["string"] ?? null) . $this->env->getFunction('icon')->getCallable()(twig_get_attribute($this->env, $this->source, ($context["src"] ?? null), "source", [], "any", false, false, false, 45)));
                        // line 46
                        echo "                                ";
                    }
                    // line 47
                    echo "                            ";
                } elseif (twig_get_attribute($this->env, $this->source, ($context["commonIcon"] ?? null), "source", [], "any", false, false, false, 47)) {
                    // line 48
                    echo "                                ";
                    if ((twig_get_attribute($this->env, $this->source, ($context["commonIcon"] ?? null), "type", [], "any", false, false, false, 48) == "svg")) {
                        // line 49
                        echo "                                    ";
                        $context["string"] = (($context["string"] ?? null) . $this->env->getFunction('icon')->getCallable()(twig_get_attribute($this->env, $this->source, ($context["commonIcon"] ?? null), "source", [], "any", false, false, false, 49)));
                        // line 50
                        echo "                                ";
                    }
                    // line 51
                    echo "                            ";
                } else {
                    // line 52
                    echo "                                ";
                    $context["string"] = (($context["string"] ?? null) . "<svg version=\"1.1\" id=\"Layer_1\" x=\"0px\" y=\"0px\" width=\"512px\" height=\"512px\" viewBox=\"0 0 512 512\" enable-background=\"new 0 0 512 512\" xml:space=\"preserve\"><polygon points=\"480,200 308.519,200 256.029,32 203.519,200 32,200 170.946,304.209 116,480 256,368 396,480 341.073,304.195 \"/></svg>");
                    // line 53
                    echo "                            ";
                }
                // line 54
                echo "
                            ";
                // line 55
                $context["string"] = (((($context["string"] ?? null) . "<span class=\"qx-icon-text\">") . twig_get_attribute($this->env, $this->source, ($context["data"] ?? null), "title", [], "any", false, false, false, 55)) . "</span>");
                // line 56
                echo "
                            ";
                // line 57
                echo twig_escape_filter($this->env, $this->env->getFilter('link')->getCallable()(($context["string"] ?? null), ($context["link"] ?? null)), "html", null, true);
                echo "

                            ";
                // line 59
                if ((twig_get_attribute($this->env, $this->source, ($context["src"] ?? null), "type", [], "any", false, false, false, 59) == "svg")) {
                    // line 60
                    echo "                                ";
                    echo twig_escape_filter($this->env, $this->env->getFunction('addIconStyle')->getCallable()(((("#" . ($context["id"] ?? null)) . " li.item-") . $context["index"]), ($context["src"] ?? null)), "html", null, true);
                    echo "
                            ";
                }
                // line 62
                echo "
                        </li>
                    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['index'], $context['fields'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 65
            echo "                </ul>
            ";
        }
        // line 67
        echo "
            ";
        // line 68
        if ((twig_get_attribute($this->env, $this->source, ($context["commonIcon"] ?? null), "type", [], "any", false, false, false, 68) == "svg")) {
            // line 69
            echo "                ";
            echo twig_escape_filter($this->env, $this->env->getFunction('addIconStyle')->getCallable()((("#" . ($context["id"] ?? null)) . " li"), ($context["commonIcon"] ?? null)), "html", null, true);
            echo "
            ";
        }
        // line 71
        echo "

        </div>
    ";
    }

    public function getTemplateName()
    {
        return "icon-list/partials/html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  266 => 71,  260 => 69,  258 => 68,  255 => 67,  251 => 65,  243 => 62,  237 => 60,  235 => 59,  230 => 57,  227 => 56,  225 => 55,  222 => 54,  219 => 53,  216 => 52,  213 => 51,  210 => 50,  207 => 49,  204 => 48,  201 => 47,  198 => 46,  195 => 45,  192 => 44,  190 => 43,  187 => 42,  185 => 41,  182 => 40,  179 => 39,  176 => 38,  174 => 37,  171 => 36,  169 => 35,  164 => 34,  160 => 33,  153 => 32,  151 => 31,  146 => 30,  142 => 29,  131 => 21,  86 => 27,  85 => 26,  84 => 25,  83 => 24,  82 => 23,  81 => 22,  80 => 21,  77 => 20,  75 => 19,  73 => 18,  71 => 17,  69 => 16,  66 => 14,  64 => 13,  62 => 12,  60 => 11,  57 => 10,  55 => 9,  52 => 8,  50 => 7,  48 => 6,  46 => 5,  44 => 4,  42 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "icon-list/partials/html.twig", "/var/www/html/current/libraries/quixnxt/visual-builder/elements/icon-list/partials/html.twig");
    }
}
