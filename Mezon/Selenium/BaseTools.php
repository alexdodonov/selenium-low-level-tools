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
use Mezon\Pop3\Client;

/**
 * Selenium low level utilities wich are using only selenium bindings
 *
 * @author Dodonov A.A.
 * @psalm-suppress PropertyNotSetInConstructor
 */
class BaseTools extends TestCase
{

    /**
     * Directory for downloads
     *
     * @var string
     */
    public $downloadsDirectory = 'c:\\tmp\\selenium\\';

    /**
     * Selenium driver
     *
     * @var ?RemoteWebDriver
     */
    static $driver = null;

    /**
     * Method fetches web driver object
     *
     * @return RemoteWebDriver web driver object
     */
    protected static function getDriver(): RemoteWebDriver
    {
        if (self::$driver === null) {
            throw (new \Exception('WebDirever was not setup', - 1));
        }

        return self::$driver;
    }

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
            self::getDriver()->wait()->until($condition);

            $this->assertTrue(true);
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
            self::getDriver()->wait()->until($condition);

            $this->assertTrue(true);
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

        $element = self::getDriver()->findElement(WebDriverBy::cssSelector($selector));
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
        $element = self::getDriver()->findElement(WebDriverBy::cssSelector($selector));

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
        $id = self::getDriver()->findElement(WebDriverBy::cssSelector('html'))->getID();

        call_user_func($reloader);

        self::getDriver()->wait()->until(
            function () use ($id) {
                if ($id != self::getDriver()->findElement(WebDriverBy::cssSelector('html'))
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
        self::getDriver()->get($url);

        self::getDriver()->wait(10, 1000)->until(
            function () {
                $elements = self::getDriver()->findElements(WebDriverBy::cssSelector('html'));
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
        $Input = self::getDriver()->findElement(WebDriverBy::cssSelector($selector));
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
        $element = self::getDriver()->findElement(WebDriverBy::cssSelector($selector));
        $action = new WebDriverActions(self::getDriver());
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
        $element = self::getDriver()->findElement(WebDriverBy::cssSelector($selector));

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
            self::getDriver()->findElement(WebDriverBy::cssSelector($selector));

            return true;
        } catch (NoSuchElementException $e) {
            return false;
        }
    }

    /**
     * Method fetches tag's text
     *
     * @param string $selector
     *            tag's selector
     * @return string tag's text
     */
    protected function getTagContent(string $selector): string
    {
        $element = WebDriverBy::cssSelector($selector);
        $element = self::getDriver()->findElement($element);
        return $element->getText();
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
        $this->assertEquals($expectedValue, $this->getTagContent($selector));
    }

    /**
     * Email server
     *
     * @var string
     */
    protected $server = '';

    /**
     * Email login
     *
     * @var string
     */
    protected $login = '';

    /**
     * Email password
     *
     * @var string
     */
    protected $password = '';

    /**
     * Method clears all emails
     */
    protected function clearAllEmails(): void
    {
        $client = new Client($this->server, $this->login, $this->password, 10, 995);

        $count = $client->getCount();
        for ($i = 1; $i <= $count; $i ++) {
            $client->deleteMessage($i);
        }

        $client->quit();
    }

    /**
     * Method asserts that email with subject exists
     *
     * @param string $subject
     *            required subject
     * @param int $timeout
     *            timeout in seconds
     */
    protected function assertEmailWithSubject(string $subject, int $timeout = 10): void
    {
        $client = new Client($this->server, $this->login, $this->password, $timeout, 995);

        for ($t = 0; $t < $timeout; $t ++) {
            for ($i = 1; $i <= $client->getCount(); $i ++) {
                if ($client->getMessageSubject($i) === $subject) {
                    $this->addToAssertionCount(1);
                    return;
                }
            }

            sleep(1);
        }

        $this->fail('Email with subject "' . $subject . '" was not found');
    }
}
