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

/* plugins/toc/toc.html.twig */
class __TwigTemplate_f7ce282c8bbc3ed2c221ede1af9074d70e276b75ad61adeea0cab81cdb830487 extends \Twig\Template
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
        // line 2
        echo "<nav class=\"table-of-contents ";
        echo $this->getAttribute(($context["toc"] ?? null), "type", []);
        echo "\" role=\"navigation\">
  ";
        // line 3
        if ($this->getAttribute(($context["toc"] ?? null), "title", [])) {
            // line 4
            echo "    ";
            // line 5
            echo "    ";
            if (($this->getAttribute(($context["toc"] ?? null), "type", []) == "toc")) {
                // line 6
                echo "      <span class=\"toctitle\">Table of contents:</span>
    ";
            } elseif (($this->getAttribute(            // line 7
($context["toc"] ?? null), "type", []) == "minitoc")) {
                // line 8
                echo "      <span class=\"toctitle\">Overview:</span>
    ";
            }
            // line 10
            echo "  ";
        }
        // line 11
        echo "
  ";
        // line 13
        echo "  ";
        $context["base_indent"] = $this->getAttribute(($context["toc"] ?? null), "headinglevel", []);
        // line 14
        echo "  ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["toc"] ?? null), "list", []));
        foreach ($context['_seq'] as $context["_key"] => $context["entry"]) {
            if ((($this->getAttribute(($context["toc"] ?? null), "baselevel", []) <= $this->getAttribute($context["entry"], "level", [])) && ($this->getAttribute($context["entry"], "level", []) <= $this->getAttribute(($context["toc"] ?? null), "headinglevel", [])))) {
                // line 15
                echo "    ";
                if (($this->getAttribute($context["entry"], "indent", []) < ($context["base_indent"] ?? null))) {
                    // line 16
                    echo "      ";
                    $context["base_indent"] = $this->getAttribute($context["entry"], "indent", []);
                    // line 17
                    echo "    ";
                }
                // line 18
                echo "  ";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entry'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 19
        echo "
  <ul>
  ";
        // line 22
        echo "  ";
        $context["level"] = ($context["base_indent"] ?? null);
        // line 23
        echo "  ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["toc"] ?? null), "list", []));
        foreach ($context['_seq'] as $context["_key"] => $context["entry"]) {
            if ((($this->getAttribute(($context["toc"] ?? null), "baselevel", []) <= $this->getAttribute($context["entry"], "level", [])) && ($this->getAttribute($context["entry"], "level", []) <= $this->getAttribute(($context["toc"] ?? null), "headinglevel", [])))) {
                // line 24
                echo "
    ";
                // line 26
                echo "    ";
                if (($this->getAttribute($context["entry"], "indent", []) > ($context["level"] ?? null))) {
                    // line 27
                    echo "      ";
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(range(1, ($this->getAttribute($context["entry"], "indent", []) - ($context["level"] ?? null))));
                    foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
                        // line 28
                        echo "        <li><ul>
      ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 30
                    echo "    ";
                } elseif (($this->getAttribute($context["entry"], "indent", []) < ($context["level"] ?? null))) {
                    // line 31
                    echo "      ";
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(range(1, (($context["level"] ?? null) - $this->getAttribute($context["entry"], "indent", []))));
                    foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
                        // line 32
                        echo "        </ul></li>
      ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 34
                    echo "    ";
                }
                // line 35
                echo "
    ";
                // line 37
                echo "    ";
                $context["level"] = $this->getAttribute($context["entry"], "indent", []);
                // line 38
                echo "
    ";
                // line 40
                echo "    ";
                if ($this->getAttribute(($context["toc"] ?? null), "anchorlink", [])) {
                    // line 41
                    echo "      <li><a href=\"#";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["entry"], "id", []), "html_attr");
                    echo "\" class=\"toclink\" title=\"";
                    echo strip_tags($this->getAttribute($context["entry"], "text", []));
                    echo "\">";
                    echo $this->getAttribute($context["entry"], "text", []);
                    echo "</a></li>
    ";
                } else {
                    // line 43
                    echo "      <li><span class=\"toclink\">";
                    echo \Grav\Common\Utils::truncate($this->getAttribute($context["entry"], "text", []), 32, " ");
                    echo "</span></li>
    ";
                }
                // line 45
                echo "  ";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entry'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 46
        echo "
  ";
        // line 48
        echo "  ";
        if (((($context["level"] ?? null) - ($context["base_indent"] ?? null)) > 0)) {
            // line 49
            echo "    ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(range(1, (($context["level"] ?? null) - ($context["base_indent"] ?? null))));
            foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
                // line 50
                echo "      </ul></li>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 52
            echo "  ";
        }
        // line 53
        echo "
  </ul>
</nav>

";
    }

    public function getTemplateName()
    {
        return "plugins/toc/toc.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  187 => 53,  184 => 52,  177 => 50,  172 => 49,  169 => 48,  166 => 46,  159 => 45,  153 => 43,  143 => 41,  140 => 40,  137 => 38,  134 => 37,  131 => 35,  128 => 34,  121 => 32,  116 => 31,  113 => 30,  106 => 28,  101 => 27,  98 => 26,  95 => 24,  89 => 23,  86 => 22,  82 => 19,  75 => 18,  72 => 17,  69 => 16,  66 => 15,  60 => 14,  57 => 13,  54 => 11,  51 => 10,  47 => 8,  45 => 7,  42 => 6,  39 => 5,  37 => 4,  35 => 3,  30 => 2,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("{# Render table of contents block #}
<nav class=\"table-of-contents {{ toc.type }}\" role=\"navigation\">
  {% if toc.title %}
    {# Format header according to TOC type #}
    {% if toc.type == \"toc\" %}
      <span class=\"toctitle\">Table of contents:</span>
    {% elseif toc.type == \"minitoc\" %}
      <span class=\"toctitle\">Overview:</span>
    {% endif %}
  {% endif %}

  {# Get base indentation based on config settings #}
  {% set base_indent = toc.headinglevel %}
  {% for entry in toc.list if (toc.baselevel <= entry.level) and (entry.level <= toc.headinglevel) %}
    {% if entry.indent < base_indent %}
      {% set base_indent = entry.indent %}
    {% endif %}
  {% endfor %}

  <ul>
  {# Generate links #}
  {% set level = base_indent %}
  {% for entry in toc.list if (toc.baselevel <= entry.level) and (entry.level <= toc.headinglevel) %}

    {# Create list markup for headings #}
    {% if entry.indent > level %}
      {% for i in 1..(entry.indent - level) %}
        <li><ul>
      {% endfor %}
    {% elseif entry.indent < level %}
      {% for i in 1..(level - entry.indent) %}
        </ul></li>
      {% endfor %}
    {% endif %}

    {# Set current level to heading level #}
    {% set level = entry.indent %}

    {# Show TOC link based on anchorlinks option #}
    {% if toc.anchorlink %}
      <li><a href=\"#{{ entry.id|e('html_attr') }}\" class=\"toclink\" title=\"{{ entry.text|striptags }}\">{{ entry.text }}</a></li>
    {% else %}
      <li><span class=\"toclink\">{{ entry.text|truncate(32, \" \") }}</span></li>
    {% endif %}
  {% endfor %}

  {# Add missing closing tags #}
  {% if (level - base_indent) > 0 %}
    {% for i in 1..(level - base_indent) %}
      </ul></li>
    {% endfor %}
  {% endif %}

  </ul>
</nav>

", "plugins/toc/toc.html.twig", "/var/www/html/grav/user/plugins/toc/templates/plugins/toc/toc.html.twig");
    }
}
