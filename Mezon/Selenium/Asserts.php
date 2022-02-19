<?php
namespace Mezon\Selenium;

use PHPUnit\Framework\Assert;

/**
 * Class Asserts
 *
 * @package Mezon
 * @subpackage SeleniumLowLevelTools
 * @author Dodonov A.A.
 * @version v.1.0 (2022/02/19)
 * @copyright Copyright (c) 2022, http://aeon.su
 */

/**
 * Asserts trait
 */
trait Asserts
{

    /**
     * Method fetches tag's text
     *
     * @param string $selector
     *            tag's selector
     * @return string tag's text
     */
    protected abstract function getTagContent(string $selector): string;

    /**
     * Asserts that two variables are equal.
     *
     * @param string $expected
     *            expected Value
     * @param string $selector
     *            selector
     */
    public function assertTagContent(string $expected, string $selector): void
    {
        Assert::assertEquals($expected, $this->getTagContent($selector));
    }
}
