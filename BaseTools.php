<?php
namespace Enterprize\Selenium;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use PHPUnit\Framework\TestCase;

/**
 * Selenium low level utilities wich are using only selenium bindings
 *
 * @author Dodonov A.A.
 */
class BaseTools extends TestCase
{

    /**
     * Selenium driver
     * 
     * @var RemoteWebDriver
     */
    var $driver = null;

    /**
     * Method waits when element with class $className become visible
     *
     * @param string $selector
     *            Selector
     * @param string $errorMessage
     *            Error message
     */
    protected function waitForVisibilityBySelector(string $selector, string $errorMessage = ''): void
    {
        try {
            $element = \Facebook\WebDriver\WebDriverBy::cssSelector($selector);
            $condition = \Facebook\WebDriver\WebDriverExpectedCondition::visibilityOfElementLocated($element);
            $this->driver->wait()->until($condition);

            $this->addToAssertionCount(1);
        } catch (\Facebook\WebDriver\Exception\NoSuchElementException $e) {
            $this->fail($errorMessage);
        }
    }

    /**
     * Method waits when element with class $className become invisible
     *
     * @param string $selector
     *            Selector
     * @param string $errorMessage
     *            Error message
     */
    protected function wait_for_invisibility_by_selector(string $selector, string $errorMessage = ''): void
    {
        try {
            $element = \Facebook\WebDriver\WebDriverBy::cssSelector($selector);
            $condition = \Facebook\WebDriver\WebDriverExpectedCondition::invisibilityOfElementLocated($element);
            $this->driver->wait()->until($condition);

            $this->addToAssertionCount(1);
        } catch (\Facebook\WebDriver\Exception\NoSuchElementException $e) {
            $this->fail($errorMessage);
        }
    }

    /**
     * Method clicks on element
     *
     * @param string $selector
     *            Selector of the clicking element
     */
    protected function clickElement(string $selector): void
    {
        $this->waitForVisibilityBySelector($selector, 'Element was not shown');

        $element = $this->driver->findElement(\Facebook\WebDriver\WebDriverBy::cssSelector($selector));
        $element->click();
    }

    /**
     * Method returns true if the test was lanched for mobile device
     *
     * @return bool true if the test was lanched for mobile device, false otherwise
     */
    protected function phone(): bool
    {
        global $argv;

        return in_array('iphonex', $argv);
    }

    /**
     * Method returns true if the test was lanched for tablet device
     *
     * @return bool true if the test was lanched for tablet device, false otherwise
     */
    protected function tablet(): bool
    {
        global $argv;

        return in_array('ipad', $argv);
    }

    /**
     * Method returns true if the test was launched for desktop
     *
     * @return bool true if the test was launched for desktop, false otherwise
     */
    protected function desktop(): bool
    {
        return $this->phone() === false && $this->tablet() === false;
    }

    /**
     * Method send data to the element with the specified selector
     *
     * @param string $selector
     *            Element's selector
     * @param string $Value
     *            Value to be inputed
     */
    protected function input_in(string $selector, string $Value): void
    {
        $element = $this->driver->findElement(\Facebook\WebDriver\WebDriverBy::cssSelector($selector));

        $element->click();
        $element->sendKeys($Value);
    }

    /**
     * Waiting for page will be reloaded
     *
     * @param callable $Reloader
     */
    protected function wait_for_page_reload(callable $Reloader): void
    {
        $id = $this->driver->findElement(\Facebook\WebDriver\WebDriverBy::cssSelector('html'))->getID();

        call_user_func($Reloader);

        $this->driver->wait()->until(
            function () use ($id) {
                if ($id != $this->driver->findElement(\Facebook\WebDriver\WebDriverBy::cssSelector('html'))
                    ->getID()) {
                    return true;
                }
            });
    }

    /**
     * Method clears input field
     *
     * @param string $selector
     *            Selector of the input field
     */
    protected function clear_input(string $selector): void
    {
        $Input = $this->driver->findElement(\Facebook\WebDriver\WebDriverBy::cssSelector($selector));
        $Input->clear();
    }

    /**
     * Method asserts class name for the element
     *
     * @param string $selector
     *            Selector of the element
     * @param string $className
     *            Class name
     */
    protected function assertHasClass(string $selector, string $className): void
    {
        $this->waitForVisibilityBySelector($selector . '.' . $className);
    }

    /**
     * Scrolling to element
     *
     * @param string $selector
     */
    protected function scrollToElement(string $selector): void
    {
        $element = $this->driver->findElement(\Facebook\WebDriver\WebDriverBy::cssSelector($selector));
        $action = new \Facebook\WebDriver\Interactions\WebDriverActions($this->driver);
        $action->moveToElement($element);
        $action->perform();
    }

    /**
     * Method returns true if the parameter 'prod' was passed through command line
     *
     * @return bool
     */
    protected function isProd(): bool
    {
        global $argv;

        return in_array('prod', $argv);
    }

    /**
     * Method returns true if the element is visible
     *
     * @param string $selector
     *            element's selector
     * @return bool true if visible, false otherwise
     */
    protected function isVisible(string $selector): bool
    {
        $element = $this->driver->findElement(\Facebook\WebDriver\WebDriverBy::cssSelector($selector));

        return $element->isDisplayed();
    }

    /**
     * Method returns true if the element exists
     *
     * @param string $selector
     *            selector of the testing element
     * @return bool true if the element exists, false otherwise
     */
    protected function elementExists(string $selector): bool
    {
        try {
            $this->driver->findElement(\Facebook\WebDriver\WebDriverBy::cssSelector($selector));

            return true;
        } catch (\Facebook\WebDriver\Exception\NoSuchElementException $e) {
            return false;
        }
    }
}
