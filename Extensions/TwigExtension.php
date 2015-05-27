<?php

namespace Unifik\SystemBundle\Extensions;

use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

use Unifik\DoctrineBehaviorsBundle\ORM\Metadatable\MetadatableGetter;
use Unifik\SystemBundle\Lib\Core;

/**
 * Library of helper functions
 */
class TwigExtension extends \Twig_Extension
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var String
     */
    private $locale;

    /**
     * @var Core
     */
    protected $systemCore;

    /**
     * @var ControllerNameParser
     */
    protected $controllerNameParser;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param mixed $systemCore
     */
    public function setSystemCore($systemCore)
    {
        $this->systemCore = $systemCore;
    }

    /**
     * Set the locale
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * Set Controller Name Parser
     *
     * @param ControllerNameParser $controllerNameParser
     */
    public function setControllerNameConverter($controllerNameParser)
    {
        $this->controllerNameParser = $controllerNameParser;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * List of available functions
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'is_external_url' => new \Twig_Function_Method($this, 'isExternalUrl'),
            'date_range' => new \Twig_Function_Method($this, 'dateRange'),
            'tree_indentation' => new \Twig_Function_Method($this, 'treeIndentation'),
            'bundle_name' => new \Twig_Function_Method($this, 'getBundleName'),
            'controller_name' => new \Twig_Function_Method($this, 'getControllerName'),
            'action_name' => new \Twig_Function_Method($this, 'getActionName'),
        );
    }

    /**
     * List of available filters
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'trim' => new \Twig_Filter_Method($this, 'trim'),
            'strip_line_breaks' => new \Twig_Filter_Method($this, 'stripLineBreaks'),
            'format_currency' => new \Twig_Filter_Method($this, 'formatCurrency'),
            'ceil' => new \Twig_Filter_Method($this, 'ceil'),
            'titleCase' => new \Twig_Filter_Method($this, 'titleCase'),
        );
    }

    /**
     * Get Globals
     *
     * @return array
     */
    public function getGlobals()
    {
        $section = null;

        if ($this->systemCore->isLoaded()) {
            $section = $this->systemCore->getApplicationCore()->getSection();
        }

        return array(
            'section' => $section,
            'project_title' => $this->container->getParameter('unifik_system.metadata.title'),
            'project_description' => $this->container->getParameter('unifik_system.metadata.description'),
            'project_keywords' => $this->container->getParameter('unifik_system.metadata.keywords')
        );
    }

    /**
     * Get current bundle name
     *
     * @return string|null
     */
    public function getBundleName()
    {
        try {
            $controller = $this->controllerNameParser->parse($this->request->get('_controller'));
        } catch (\InvalidArgumentException $e) {
            $controller = $this->request->get('_controller');
        }

        $pattern = "#\\\([a-zA-Z]*)Bundle#";
        $matches = array();

        if (preg_match($pattern, $controller, $matches)) {
            return strtolower($matches[1]);
        }
    }

    /**
     * Get current controller name
     *
     * @return string|null
     */
    public function getControllerName()
    {
        try {
            $controller = $this->controllerNameParser->parse($this->request->get('_controller'));
        } catch (\InvalidArgumentException $e) {
            $controller = $this->request->get('_controller');
        }

        $pattern = "#Controller\\\([a-zA-Z]*)Controller#";
        $matches = array();

        if (preg_match($pattern, $controller, $matches)) {
            return strtolower($matches[1]);
        }

        // If controllerNameParser couldn't parse the Controller name
        $pattern = "#(.*)\\\([a-zA-Z]*)Controller::([a-zA-Z]*)Action#";
        $matches = array();

        if (preg_match($pattern, $controller, $matches)) {
            return strtolower($matches[2]);
        }
    }

    /**
     * Get current action name
     *
     * @return string|null
     */
    public function getActionName()
    {
        try {
            $controller = $this->controllerNameParser->parse($this->request->get('_controller'));
        } catch (\InvalidArgumentException $e) {
            $controller = $this->request->get('_controller');
        }

        $pattern = "#::([a-zA-Z]*)Action#";
        $matches = array();

        if (preg_match($pattern, $controller, $matches)) {
            return $matches[1];
        }
    }

    /**
     * Determine if an url is external
     *
     * @param string $url
     *
     * @return bool
     */
    public function isExternalUrl($url)
    {
        $trustedHostPatterns = $this->request->getTrustedHosts();

        if (count($trustedHostPatterns) > 0) {
            $parse = parse_url($url);

            foreach ($trustedHostPatterns as $pattern) {
                if (preg_match($pattern, $parse['host'])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Returns a textual representation of a date range
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param string    $locale
     *
     * @return string
     */
    public function dateRange($startDate, $endDate, $locale = null)
    {
        if (!$locale) {
            $locale = $this->locale;
        }

        $defaultDateFormatter = \IntlDateFormatter::create($locale, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);

        if ($startDate == $endDate) {
            return $defaultDateFormatter->format($startDate);
        }

        $startDateInfos = date_parse($startDate->format('Y-m-d'));
        $endDateInfos = date_parse($endDate->format('Y-m-d'));

        if ($startDateInfos['month'] == $endDateInfos['month'] && $startDateInfos['year'] == $endDateInfos['year']) {

            // ex.: 2 au 5 février 2012
            if ($locale == 'fr') {
                $range = $startDateInfos['day'] . ' au ' . $defaultDateFormatter->format($endDate);

            // ex.: February 2 to 5, 2012
            } else {
                $dateFormatterStart = \IntlDateFormatter::create($locale, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE, null, null, 'MMMM d');
                $dateFormatterEnd = \IntlDateFormatter::create($locale, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE, null, null, 'd, Y');
                $range = $dateFormatterStart->format($startDate) . ' to ' . $dateFormatterEnd->format($endDate);
            }

        } elseif ($startDateInfos['year'] == $endDateInfos['year']) {

            // ex.: 2 février au 5 mai 2012
            if ($locale == 'fr') {
                $dateFormatterStart = \IntlDateFormatter::create($locale, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE, null, null, 'd MMMM');
                $range = $dateFormatterStart->format($startDate) . ' au ' . $defaultDateFormatter->format($endDate);

            // ex.: February 2 to May 5, 2012
            } else {
                $dateFormatter = \IntlDateFormatter::create($locale, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE, null, null, 'MMMM d');
                $range = $dateFormatter->format($startDate) . ' to ' . $dateFormatter->format($endDate) . ', ' . $endDateInfos['year'];
            }

        } else {
            $range = $defaultDateFormatter->format($startDate);
            $range .= $locale == 'fr' ? ' au ' : ' to ';
            $range .= $defaultDateFormatter->format($endDate);
        }

        if ($locale == 'fr') {
            $range = preg_replace(array('/^1 /', '/au 1 /'), array('1er ', 'au 1er '), $range);
        }

        return $range;
    }

    /**
     * Strip whitespace
     *
     * @param string $string
     *
     * @return string
     */
    public function trim($string)
    {
        return trim($string);
    }

    /**
     * Strip line breaks
     *
     * @param string $string
     *
     * @return string
     */
    public function stripLineBreaks($string)
    {
        return str_replace(array("\r\n", "\r", "\n"), "", $string);
    }

    /**
     * Round fractions up
     *
     * @param float $number
     *
     * @return integer
     */
    public function ceil($number)
    {
        return ceil($number);
    }

    /**
     * Format currency
     *
     * @param float  $price
     * @param string $locale
     * @param string $currency
     * @param bool   $showSymbol           Show or hide the dollard sign (works only for CAD and USD currencies for now)
     * @param bool   $showDecimalsWhenZero Show or hide decimals if they are equal to 0 (works only for CAD and USD currencies for now)
     *
     * @return string
     */
    public function formatCurrency($price, $locale = null, $currency = 'CAD', $showSymbol = true, $showDecimalsWhenZero = true)
    {
        if (!$locale) {
            $locale = $this->locale;
        }

        $format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        $formatedPrice = $format->formatCurrency($price, $currency);

        if ($currency == 'CAD' or $currency == 'USD') {

            $formatedPrice = str_replace(array('CA', 'US'), '', $formatedPrice);

            if (!$showSymbol) {
                $formatedPrice = str_replace('$', '', $formatedPrice);
            }

            if (!$showDecimalsWhenZero && $price - floor($price) == 0) {
                $formatedPrice = str_replace(array('.00', ',00'), '', $formatedPrice);
            }
        }

        return $formatedPrice;
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getName()
    {
        return 'unifik_system_extension';
    }

    /**
     * Tree Indentation
     *
     * Indent label or widget to render as a tree
     *
     * @param $level
     *
     * @return string
     */
    public function treeIndentation($level)
    {
        $indent = '';

        for ($i = 2; $i <= $level; $i++) {
            $indent .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        return $indent;
    }

    /**
     * Convert "aCamelCaseString" to "A camel case string"
     *
     * @param $string
     * @return mixed|string
     */
    public function titleCase($string)
    {
        $string = preg_replace('/(?<=\\w)(?=[A-Z])/'," $1", $string);
        $string = trim($string);
        $string = strtolower($string);
        $string = ucfirst($string);

        return $string;
    }
}
