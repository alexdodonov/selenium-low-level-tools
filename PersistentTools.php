<?php
namespace Mezon\Selenium;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;

/**
 * Selenium low level utilities wich are using only selenium bindings
 *
 * @author Dodonov A.A.
 */
class PersistentTools extends BaseTools
{

    /**
     * Setting up test vars
     */
    public function setUp(): void
    {
        if (self::$driver !== null) {
            return;
        }

        $host = 'http://localhost:4444/wd/hub';

        $options = new ChromeOptions();

        global $argv;

        if (in_array('iphonex', $argv)) {
            $options->addArguments([
                '--window-size=375,812'
            ]);
        } elseif (in_array('ipad', $argv)) {
            $options->addArguments([
                '--window-size=768,1024'
            ]);
        } else {
            $options->addArguments([
                '--window-size=1400,800'
            ]);
        }

        if (in_array('headless', $argv)) {
            $options->addArguments([
                '--headless'
            ]);
        }

        $options->addArguments(
            [
                '--user-agent=Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0'
            ]);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        self::$driver = RemoteWebDriver::create($host, $capabilities, 5000);
    }

    /**
     * Method destroys test variables
     */
    public function tearDown(): void
    {
        // do nothing
    }

    /**
     * Destroing driver at the end of the tests
     */
    public function __destruct()
    {
        if (self::$driver !== null) {
            self::$driver->quit();
            self::$driver = null;
        }
    }
}
