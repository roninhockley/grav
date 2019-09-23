<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* partials/featured.html.twig */
class __TwigTemplate_2a6e42d229fd59105e6ec76b3a865cde083a6f9eaffb8c290eec350b6f51848d extends \Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        $context["featuredPages"] = $this->getAttribute($this->getAttribute($this->getAttribute(($context["page"] ?? null), "collection", [0 => ["items" => ["@taxonomy" => ["category" => "featured"]], "order" => ["by" => "default", "dir" => "asc"]]], "method"), "remove", [0 => ($context["page"] ?? null)], "method"), "slice", [0 => 0, 1 => 3], "method");
        // line 2
        if ((count(($context["featuredPages"] ?? null)) > 0)) {
            // line 3
            echo "<section>
    <header class=\"major\">
            <h2>Featured</h2>
    </header>
    <div class=\"mini-posts\">
        ";
            // line 8
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["featuredPages"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["p"]) {
                // line 9
                echo "            <article>
                <a href=\"";
                // line 10
                echo $this->getAttribute($context["p"], "url", []);
                echo "\" class=\"featured-title\"><h2>";
                echo $this->getAttribute($context["p"], "title", []);
                echo "</h2></a>
                ";
                // line 11
                if ($this->getAttribute($this->getAttribute($context["p"], "header", []), "primaryImage", [])) {
                    // line 12
                    echo "                    ";
                    $context["image"] = twig_first($this->env, $this->getAttribute($this->getAttribute($context["p"], "header", []), "primaryImage", []));
                    // line 13
                    echo "                    <a href=\"";
                    echo $this->getAttribute($context["p"], "url", []);
                    echo "\" class=\"image\">";
                    echo $this->getAttribute($this->getAttribute($this->getAttribute($context["p"], "media", []), $this->getAttribute(($context["image"] ?? null), "name", []), [], "array"), "html", [0 => "", 1 => $this->getAttribute($context["p"], "title", [])], "method");
                    echo " </a>
                ";
                }
                // line 15
                echo "
                <p>";
                // line 16
                echo $this->getAttribute($context["p"], "summary", [0 => 100], "method");
                echo "</p>
            </article>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['p'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 19
            echo "    </div>
    <!--
    <ul class=\"actions\">
            <li><a href=\"#\" class=\"button\">More</a></li>
    </ul>
    -->

</section> <!-- End of featured Section -->
";
        }
    }

    public function getTemplateName()
    {
        return "partials/featured.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  79 => 19,  70 => 16,  67 => 15,  59 => 13,  56 => 12,  54 => 11,  48 => 10,  45 => 9,  41 => 8,  34 => 3,  32 => 2,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("{% set featuredPages = page.collection({'items':{'@taxonomy':{'category': 'featured'}},'order': {'by': 'default', 'dir': 'asc'}}).remove(page).slice(0,3) %}
{% if count(featuredPages) > 0 %}
<section>
    <header class=\"major\">
            <h2>Featured</h2>
    </header>
    <div class=\"mini-posts\">
        {% for p in featuredPages %}
            <article>
                <a href=\"{{ p.url }}\" class=\"featured-title\"><h2>{{ p.title }}</h2></a>
                {% if p.header.primaryImage %}
                    {% set image = p.header.primaryImage|first %}
                    <a href=\"{{ p.url }}\" class=\"image\">{{ p.media[image.name].html(\"\",p.title) }} </a>
                {% endif %}

                <p>{{ p.summary(100) }}</p>
            </article>
        {% endfor %}
    </div>
    <!--
    <ul class=\"actions\">
            <li><a href=\"#\" class=\"button\">More</a></li>
    </ul>
    -->

</section> <!-- End of featured Section -->
{% endif %}", "partials/featured.html.twig", "/var/www/html/grav/user/themes/editorial/templates/partials/featured.html.twig");
    }
}
