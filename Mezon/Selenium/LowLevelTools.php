<?php
namespace Mezon\Selenium;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;

/**
 * Selenium low level utilities wich are using only selenium bindings
 *
 * @author Dodonov A.A.
 * @psalm-suppress PropertyNotSetInConstructor
 */
class LowLevelTools extends BaseTools
{

    /**
     * Setting up test vars
     */
    public function setUp(): void
    {
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

        $options->addArguments([
            '--user-agent=Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0'
        ]);

        $prefs = [
            'download.default_directory' => $this->downloadsDirectory
        ];
        $options->setExperimentalOption('prefs', $prefs);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        self::$driver = RemoteWebDriver::create($host, $capabilities, 5000);
    }

    /**
     * Method destroys test variables
     */
    public function tearDown(): void
    {
        /** @var string[] $handles */
        $handles = self::getDriver()->getWindowHandles();

        foreach ($handles as $handle) {
            self::getDriver()->switchTo()->window($handle);
            self::getDriver()->close();
        }
    }
}
