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

/* partials/contact.html.twig */
class __TwigTemplate_f9db26a4b77fc5fb688c355e841be82e7898cfb266708e2834e44245fed1c3be extends \Twig\Template
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
        echo "<!-- Section -->
<section>
    <header class=\"major\">
        <h2>Get in touch</h2>
    </header>
    <p>";
        // line 6
        echo $this->getAttribute($this->getAttribute(($context["theme_config"] ?? null), "contact", []), "statement", []);
        echo "</p>
    <ul class=\"contact\">
        <li class=\"fa-envelope-o\"><a href=\"#\">";
        // line 8
        echo $this->getAttribute($this->getAttribute(($context["theme_config"] ?? null), "contact", []), "email", []);
        echo "</a></li>
        <li class=\"fa-phone\">";
        // line 9
        echo $this->getAttribute($this->getAttribute(($context["theme_config"] ?? null), "contact", []), "phone", []);
        echo "</li>
        <li class=\"fa-home\">";
        // line 10
        echo $this->getAttribute($this->getAttribute(($context["theme_config"] ?? null), "contact", []), "street", []);
        echo "<br />
            ";
        // line 11
        echo $this->getAttribute($this->getAttribute(($context["theme_config"] ?? null), "contact", []), "city", []);
        echo ", ";
        echo $this->getAttribute($this->getAttribute(($context["theme_config"] ?? null), "contact", []), "state", []);
        echo " ";
        echo $this->getAttribute($this->getAttribute(($context["theme_config"] ?? null), "contact", []), "zip", []);
        echo "</li>
    </ul>
</section>";
    }

    public function getTemplateName()
    {
        return "partials/contact.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  54 => 11,  50 => 10,  46 => 9,  42 => 8,  37 => 6,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<!-- Section -->
<section>
    <header class=\"major\">
        <h2>Get in touch</h2>
    </header>
    <p>{{ theme_config.contact.statement }}</p>
    <ul class=\"contact\">
        <li class=\"fa-envelope-o\"><a href=\"#\">{{ theme_config.contact.email }}</a></li>
        <li class=\"fa-phone\">{{ theme_config.contact.phone }}</li>
        <li class=\"fa-home\">{{ theme_config.contact.street }}<br />
            {{ theme_config.contact.city }}, {{ theme_config.contact.state }} {{ theme_config.contact.zip }}</li>
    </ul>
</section>", "partials/contact.html.twig", "/var/www/html/grav/user/themes/editorial/templates/partials/contact.html.twig");
    }
}
