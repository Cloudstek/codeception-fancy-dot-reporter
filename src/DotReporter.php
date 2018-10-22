<?php

declare(strict_types=1);

namespace Cloudstek\Codeception\Extension;

use Codeception\Event\FailEvent;
use Codeception\Event\SuiteEvent;
use Codeception\Events;
use Codeception\Subscriber\Console;

/**
 * Fancy dot reporter.
 *
 * Enhanced version of the DotReporter found in Codeception.
 *
 * ```sh
 *  .......... 10 / 80 ( 13%)
 *  .......... 20 / 80 ( 25%)
 *  .......... 30 / 80 ( 38%)
 *  .......... 40 / 80 ( 50%)
 *  .......... 50 / 80 ( 63%)
 *  .......... 60 / 80 ( 75%)
 *  .......... 70 / 80 ( 88%)
 *  .......... 80 / 80 (100%)
 *
 * Time: 2.07 seconds, Memory: 20.00MB
 *
 * OK (80 tests, 124 assertions)
 * ```
 */
class DotReporter extends \Codeception\Extension
{
    /**
     * Standard reporter.
     *
     * @var Console
     */
    protected $standardReporter;

    /**
     * Number of tests in current suite.
     *
     * @var int
     */
    protected $tests = 0;

    /**
     * Number of tests run.
     *
     * @var int
     */
    protected $currentTest = 0;

    /**
     * Console width.
     *
     * @var int
     */
    protected $width = 80;

    /**
     * Max width to print characters.
     *
     * @var int
     */
    protected $maxWidth;

    /**
     * Current position.
     *
     * @var int
     */
    protected $currentPos = 0;

    /**
     * Initialize extension.
     */
    public function _initialize()
    {
        // Turn on printing for this extension
        $this->options['silent'] = false;

        // Turn off printing for everything else
        $this->_reconfigure(['settings' => ['silent' => true]]);

        // Standard reporter
        $this->standardReporter = new Console($this->options);

        // Console width
        $this->width = $this->standardReporter->detectWidth();

        if (!isset($this->config['columns']) || $this->config['columns'] <= 0) {
            $this->config['columns'] = $this->width;
        }
    }

    /**
     * Events we're listening for.
     *
     * @var array
     */
    public static $events = [
        Events::SUITE_BEFORE => 'beforeSuite',
        Events::SUITE_AFTER => 'afterSuite',
        Events::TEST_SUCCESS => 'success',
        Events::TEST_FAIL => 'fail',
        Events::TEST_ERROR => 'error',
        Events::TEST_SKIPPED => 'skipped',
        Events::TEST_INCOMPLETE => 'incomplete',
        Events::TEST_WARNING => 'warning',
        Events::TEST_FAIL_PRINT => 'printFailed'
    ];

    /**
     * BeforeSuite.
     *
     * @param SuiteEvent $event
     */
    public function beforeSuite(SuiteEvent $event)
    {
        // Get total number of tests in suite
        $this->tests = $event->getSuite()->count();

        // Get max number of characters to print
        $progressLength = strlen($this->getProgress($this->tests));
        $this->maxWidth = min($this->width - $progressLength, $this->config['columns']);

        // Start with a newline
        $this->writeln('');
    }

    /**
     * AfterSuite.
     *
     * @param SuiteEvent $event
     */
    public function afterSuite(SuiteEvent $event)
    {
        $progress = str_repeat(' ', $this->maxWidth - $this->currentPos).$this->getProgress($this->currentTest);
        $this->write($progress);

        $this->tests = 0;
        $this->currentPos = 0;
        $this->currentTest = 0;
    }

    /**
     * On test success.
     */
    public function success()
    {
        $this->printChar('.');
    }

    /**
     * On test failure.
     *
     * @param FailEvent $event
     */
    public function fail(FailEvent $event)
    {
        $this->printChar('<error>F</error>');
    }

    /**
     * On test error.
     *
     * @param FailEvent $event
     */
    public function error(FailEvent $event)
    {
        $this->printChar('<error>E</error>');
    }

    /**
     * On test skipped.
     */
    public function skipped()
    {
        $this->printChar('S');
    }

    /**
     * On test incomplete.
     *
     * @param FailEvent $event
     */
    public function incomplete(FailEvent $event)
    {
        $this->printChar('I');
    }

    /**
     * On test warning.
     *
     * @param FailEvent $event
     */
    public function warning(FailEvent $event)
    {
        $this->printChar('W');
    }

    /**
     * Print failed test summary.
     *
     * @param FailEvent $event
     */
    public function printFailed(FailEvent $event)
    {
        $this->standardReporter->printFail($event);
    }

    /**
     * Print character on console.
     *
     * @param string $char
     */
    protected function printChar($char)
    {
        $this->write($char);
        ++$this->currentPos;
        ++$this->currentTest;

        // Print progress and start new line
        if ($this->currentPos >= $this->maxWidth) {
            $this->write($this->getProgress($this->currentTest));
            $this->writeln('');

            $this->currentPos = 0;

            return;
        }
    }

    /**
     * Get progress indicator.
     *
     * Example output
     * ```
     *  1 / 10 (  1%)
     * 10 / 10 (100%)
     * ```
     *
     * @param int $tests Number of tests run so far
     *
     * @return string
     */
    protected function getProgress(int $tests)
    {
        if ($tests > $this->tests) {
            $tests = $this->tests;
        }

        return sprintf(
            ' %s / %d (%3d%%)',
            str_pad((string) $tests, strlen((string) $this->tests), ' ', \STR_PAD_LEFT),
            $this->tests,
            round(100 / ($this->tests / $tests))
        );
    }
}
