<?php
namespace Mezon\Selenium;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverExpectedCondition;

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
    static $driver = null;

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
            $element = WebDriverBy::cssSelector($selector);
            $condition = WebDriverExpectedCondition::visibilityOfElementLocated($element);
            self::$driver->wait()->until($condition);

            $this->addToAssertionCount(1);
        } catch (NoSuchElementException $e) {
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
    protected function waitForInvisibilityBySelector(string $selector, string $errorMessage = ''): void
    {
        try {
            $element = WebDriverBy::cssSelector($selector);
            $condition = WebDriverExpectedCondition::invisibilityOfElementLocated($element);
            self::$driver->wait()->until($condition);

            $this->addToAssertionCount(1);
        } catch (NoSuchElementException $e) {
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

        $element = self::$driver->findElement(WebDriverBy::cssSelector($selector));
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
     * @param string $value
     *            Value to be inputed
     */
    protected function inputIn(string $selector, string $value): void
    {
        $element = self::$driver->findElement(WebDriverBy::cssSelector($selector));

        $element->click();
        $element->sendKeys($value);
    }

    /**
     * Waiting for page will be reloaded
     *
     * @param callable $reloader
     */
    protected function waitForPageReload(callable $reloader): void
    {
        $id = self::$driver->findElement(WebDriverBy::cssSelector('html'))->getID();

        call_user_func($reloader);

        self::$driver->wait()->until(
            function () use ($id) {
                if ($id != self::$driver->findElement(WebDriverBy::cssSelector('html'))
                    ->getID()) {
                    return true;
                }
            });
    }
    
    /**
     * Waiting for page load
     *
     * @param string $url
     *            page url
     */
    protected function waitForPageLoad(string $url): void
    {
        self::$driver->get($url);
        
        self::$driver->wait(10, 1000)->until(
            function () {
                $elements = self::$driver->findElements(WebDriverBy::cssSelector('html'));
                return ! empty($elements);
            },
            'Error element');
    }

    /**
     * Method clears input field
     *
     * @param string $selector
     *            Selector of the input field
     */
    protected function clearInput(string $selector): void
    {
        $Input = self::$driver->findElement(WebDriverBy::cssSelector($selector));
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
        $element = self::$driver->findElement(WebDriverBy::cssSelector($selector));
        $action = new WebDriverActions(self::$driver);
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
        $element = self::$driver->findElement(WebDriverBy::cssSelector($selector));

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
            self::$driver->findElement(WebDriverBy::cssSelector($selector));

            return true;
        } catch (NoSuchElementException $e) {
            return false;
        }
    }

    /**
     * Method validates tag content
     *
     * @param string $selector
     *            tag selector
     * @param string $expectedValue
     *            expected value
     */
    protected function checkTagContent(string $selector, string $expectedValue): void
    {
        $element = WebDriverBy::cssSelector($selector);
        $element = self::$driver->findElement($element);
        $this->assertEquals($expectedValue, $element->getText());
    }
}
