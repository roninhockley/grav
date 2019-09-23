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

/* partials/tools-logs.html.twig */
class __TwigTemplate_3caae9421c5fcd4ad71ac375595546081ee71bed93aa4ba58934f18b8389709d extends \Twig\Template
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
        echo "<div class=\"logs-content\">

    ";
        // line 15
        echo "
    ";
        // line 16
        $context["macro"] = $this;
        // line 17
        echo "
        ";
        // line 18
        $context["file"] = (($this->getAttribute($this->getAttribute(($context["grav"] ?? null), "uri", []), "query", [0 => "log"], "method")) ? ($this->getAttribute($this->getAttribute(($context["grav"] ?? null), "uri", []), "query", [0 => "log"], "method")) : ("grav"));
        // line 19
        echo "        ";
        $context["verbose"] = ((($this->getAttribute($this->getAttribute(($context["grav"] ?? null), "uri", []), "query", [0 => "verbose"], "method") == "true")) ? (true) : (false));
        // line 20
        echo "        ";
        $context["lines"] = (($this->getAttribute($this->getAttribute(($context["grav"] ?? null), "uri", []), "query", [0 => "lines"], "method")) ? ($this->getAttribute($this->getAttribute(($context["grav"] ?? null), "uri", []), "query", [0 => "lines"], "method")) : (20));
        // line 21
        echo "        ";
        $context["logfile"] = $this->getAttribute($this->getAttribute(($context["grav"] ?? null), "locator", []), "findResource", [0 => (("log://" . ($context["file"] ?? null)) . ".log")], "method");
        // line 22
        echo "        ";
        $context["logs"] = $this->getAttribute(($context["logviewer"] ?? null), "objectTail", [0 => ($context["logfile"] ?? null), 1 => $this->env->getExtension('Grav\Common\Twig\TwigExtension')->intFilter(($context["lines"] ?? null)), 2 => false], "method");
        // line 23
        echo "
        <div class=\"logs-output\">

        <form id=\"logs-form\">
            <div class=\"block block-select\">
                <div class=\"form-field\">
                    <div class=\"form-data\">
                        ";
        // line 30
        $context["log_files"] = (($this->getAttribute($this->getAttribute($this->getAttribute(($context["config"] ?? null), "plugins", [], "any", false, true), "admin", [], "any", false, true), "log_viewer_files", [], "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($this->getAttribute(($context["config"] ?? null), "plugins", [], "any", false, true), "admin", [], "any", false, true), "log_viewer_files", []), [0 => "grav", 1 => "email"])) : ([0 => "grav", 1 => "email"]));
        // line 31
        echo "                        ";
        $context["lines_list"] = [10 => "10 entries", 25 => "25 entries", 50 => "50 entries", 100 => "100 entries", 200 => "200 entries", 500 => "500 entries"];
        // line 32
        echo "
                        ";
        // line 33
        echo $context["macro"]->getrender_select("log", ($context["log_files"] ?? null), ($context["file"] ?? null), true);
        echo "
                        ";
        // line 34
        echo $context["macro"]->getrender_select("verbose", ["false" => "Essential Output", "true" => "Verbose Output"], ($context["verbose"] ?? null));
        echo "
                        ";
        // line 35
        echo $context["macro"]->getrender_select("lines", ($context["lines_list"] ?? null), ($context["lines"] ?? null));
        echo "
                    </div>
                </div>
            </div>
        </form>

        <h1>";
        // line 41
        echo twig_escape_filter($this->env, $this->env->getExtension('Grav\Common\Twig\TwigExtension')->inflectorFilter("title", ($context["file"] ?? null)), "html", null, true);
        echo " Log File</h1>
        <h3>Display the ";
        // line 42
        echo twig_escape_filter($this->env, ($context["lines"] ?? null), "html", null, true);
        echo " most recent entries...</h3>
        <table class=\"noflex\">
            <thead>
                <tr>
                    <th class=\"date\">Date</th>
                    <th class=\"level\">Level</th>
                    <th class=\"message\">Message</th>
                </tr>
            </thead>
            <tbody>
                ";
        // line 52
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["logs"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["log"]) {
            // line 53
            echo "                <tr>
                    <td class=\"date\">";
            // line 54
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->getAttribute($context["log"], "date", [])), "html", null, true);
            echo "</td>
                    <td class=\"level\"><span class=\"badge ";
            // line 55
            echo twig_escape_filter($this->env, twig_lower_filter($this->env, $this->getAttribute($context["log"], "level", [])), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($context["log"], "level", []), "html", null, true);
            echo "</span></td>
                    <td class=\"message\">";
            // line 56
            echo twig_escape_filter($this->env, $this->getAttribute($context["log"], "message", []), "html", null, true);
            echo "</td>
                    ";
            // line 57
            if (($context["verbose"] ?? null)) {
                // line 58
                echo "                </tr>
                <tr class=\"trace\">
                    <td colspan=\"2\">&nbsp;</td>
                    <td>
                        <div class=\"overflow\">
                            <ol>
                            ";
                // line 64
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["log"], "trace", []));
                foreach ($context['_seq'] as $context["_key"] => $context["tracerow"]) {
                    // line 65
                    echo "                                <li><code>";
                    echo twig_escape_filter($this->env, $context["tracerow"], "html", null, true);
                    echo "</code></li>
                            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tracerow'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 67
                echo "                            </ol>
                        </div>
                    </td>
                    ";
            }
            // line 71
            echo "                </tr>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['log'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 73
        echo "            </tbody>
        </table>
    </div>


</div>
";
    }

    // line 3
    public function getrender_select($__name__ = null, $__options__ = null, $__selected__ = null, $__autokey__ = false, ...$__varargs__)
    {
        $context = $this->env->mergeGlobals([
            "name" => $__name__,
            "options" => $__options__,
            "selected" => $__selected__,
            "autokey" => $__autokey__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        ob_start();
        try {
            // line 4
            echo "        <div class=\"forms-select-wrapper\">
            <select class=\"form-select\" name=\"";
            // line 5
            echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
            echo "\"  data-grav-selectize>
                ";
            // line 6
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["options"] ?? null));
            foreach ($context['_seq'] as $context["key"] => $context["option"]) {
                // line 7
                echo "                    ";
                if (($context["autokey"] ?? null)) {
                    // line 8
                    echo "                        ";
                    $context["key"] = (($this->env->getExtension('Grav\Common\Twig\TwigExtension')->ofTypeFunc($context["key"], "int")) ? (twig_lower_filter($this->env, $context["option"])) : ($context["key"]));
                    // line 9
                    echo "                    ";
                }
                // line 10
                echo "                    <option value=\"";
                echo twig_escape_filter($this->env, $context["key"], "html", null, true);
                echo "\" ";
                echo ((($context["key"] == ($context["selected"] ?? null))) ? (" selected") : (""));
                echo ">";
                echo twig_escape_filter($this->env, $this->env->getExtension('Grav\Common\Twig\TwigExtension')->inflectorFilter("title", $context["option"]), "html", null, true);
                echo "</option>
                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['option'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 12
            echo "            </select>
        </div>
    ";
        } catch (\Exception $e) {
            ob_end_clean();

            throw $e;
        } catch (\Throwable $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "partials/tools-logs.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  222 => 12,  209 => 10,  206 => 9,  203 => 8,  200 => 7,  196 => 6,  192 => 5,  189 => 4,  174 => 3,  164 => 73,  157 => 71,  151 => 67,  142 => 65,  138 => 64,  130 => 58,  128 => 57,  124 => 56,  118 => 55,  114 => 54,  111 => 53,  107 => 52,  94 => 42,  90 => 41,  81 => 35,  77 => 34,  73 => 33,  70 => 32,  67 => 31,  65 => 30,  56 => 23,  53 => 22,  50 => 21,  47 => 20,  44 => 19,  42 => 18,  39 => 17,  37 => 16,  34 => 15,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"logs-content\">

    {% macro render_select(name, options, selected, autokey=false) %}
        <div class=\"forms-select-wrapper\">
            <select class=\"form-select\" name=\"{{ name }}\"  data-grav-selectize>
                {% for key,option in options %}
                    {% if autokey %}
                        {% set key = key|of_type('int') ? option|lower : key %}
                    {% endif %}
                    <option value=\"{{ key }}\" {{ key == selected ? ' selected' : '' }}>{{ option|titleize }}</option>
                {% endfor %}
            </select>
        </div>
    {% endmacro %}

    {% import _self as macro %}

        {% set file = grav.uri.query('log') ?: 'grav' %}
        {% set verbose = grav.uri.query('verbose') == 'true' ? true : false %}
        {% set lines = grav.uri.query('lines') ?: 20 %}
        {% set logfile = grav.locator.findResource(\"log://\" ~ file ~ '.log') %}
        {% set logs = logviewer.objectTail(logfile, lines|int, false) %}

        <div class=\"logs-output\">

        <form id=\"logs-form\">
            <div class=\"block block-select\">
                <div class=\"form-field\">
                    <div class=\"form-data\">
                        {% set log_files = config.plugins.admin.log_viewer_files|default(['grav','email']) %}
                        {% set lines_list = {10:'10 entries', 25:'25 entries', 50:'50 entries', 100:'100 entries', 200:'200 entries', 500:'500 entries'} %}

                        {{ macro.render_select('log', log_files, file, true) }}
                        {{ macro.render_select('verbose', {'false':'Essential Output', 'true':'Verbose Output'}, verbose) }}
                        {{ macro.render_select('lines', lines_list, lines) }}
                    </div>
                </div>
            </div>
        </form>

        <h1>{{ file|titleize }} Log File</h1>
        <h3>Display the {{ lines }} most recent entries...</h3>
        <table class=\"noflex\">
            <thead>
                <tr>
                    <th class=\"date\">Date</th>
                    <th class=\"level\">Level</th>
                    <th class=\"message\">Message</th>
                </tr>
            </thead>
            <tbody>
                {% for log in logs %}
                <tr>
                    <td class=\"date\">{{ log.date|date }}</td>
                    <td class=\"level\"><span class=\"badge {{ log.level|lower }}\">{{ log.level }}</span></td>
                    <td class=\"message\">{{ log.message }}</td>
                    {% if verbose %}
                </tr>
                <tr class=\"trace\">
                    <td colspan=\"2\">&nbsp;</td>
                    <td>
                        <div class=\"overflow\">
                            <ol>
                            {% for tracerow in log.trace %}
                                <li><code>{{ tracerow }}</code></li>
                            {% endfor %}
                            </ol>
                        </div>
                    </td>
                    {% endif %}
                </tr>
                {% endfor %}
            </tbody>
        </table>
    </div>


</div>
", "partials/tools-logs.html.twig", "/var/www/html/grav/user/plugins/admin/themes/grav/templates/partials/tools-logs.html.twig");
    }
}
