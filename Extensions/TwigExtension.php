<?php

namespace Flexy\SystemBundle\Extensions;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Stopwatch\Section;
use BCC\ExtraToolsBundle\Util\DateFormatter;

use Flexy\SystemBundle\Lib\Core;

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
     * List of available functions
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'isExternalUrl' => new \Twig_Function_Method($this, 'isExternalUrl'),
            'dateRange' => new \Twig_Function_Method($this, 'dateRange'),
            'tree_indentation' => new \Twig_Function_Method($this, 'treeIndentation')
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
            'stripLineBreaks' => new \Twig_Filter_Method($this, 'stripLineBreaks'),
            'formatCurrency' => new \Twig_Filter_Method($this, 'formatCurrency'),
            'ceil' => new \Twig_Filter_Method($this, 'ceil'),
        );
    }

    public function getGlobals()
    {
        if ($this->systemCore->isLoaded()) {
            $section = $this->systemCore->getApplicationCore()->getSection();
        } else {
            $section = null;
        }

        return array(
            'section' => $section
        );
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

        $formatter = new DateFormatter();

        if ($startDate == $endDate) {
            return $formatter->format($startDate, 'long', 'none', $locale);
        }

        $startDateInfos = date_parse($startDate->format('Y-m-d'));
        $endDateInfos = date_parse($endDate->format('Y-m-d'));

        if ($startDateInfos['month'] == $endDateInfos['month'] && $startDateInfos['year'] == $endDateInfos['year']) {

            // ex.: 2 au 5 février 2012
            if ($locale == 'fr') {
                $range = $startDateInfos['day'] . ' au ' . $formatter->format($endDate, 'long', 'none', $locale);

                // ex.: February 2 to 5, 2012
            } else {
                $range = $formatter->format($startDate, 'long', 'none', $locale, 'MMMM d') . ' to ' . $formatter->format($endDate, 'long', 'none', $locale, 'd, Y');
            }

        } elseif ($startDateInfos['year'] == $endDateInfos['year']) {

            // ex.: 2 février au 5 mai 2012
            if ($locale == 'fr') {
                $range = $formatter->format($startDate, 'long', 'none', $locale, 'd MMMM') . ' au ' . $formatter->format($endDate, 'long', 'none', $locale);

                // ex.: February 2 to May 5, 2012
            } else {
                $range = $formatter->format($startDate, 'long', 'none', $locale, 'MMMM d') . ' to ' . $formatter->format($endDate, 'long', 'none', $locale, 'MMMM d') . ', ' . $endDateInfos['year'];
            }

        } else {
            $range = $formatter->format($startDate, 'long', 'none', $locale);
            $range .= $locale == 'fr' ? ' au ' : ' to ';
            $range .= $formatter->format($endDate, 'long', 'none', $locale);
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
        return 'flexy_system_extension';
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
}
