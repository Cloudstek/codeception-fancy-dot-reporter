# Fancy Dot Reporter

This is a fork from the [DotReporter of Codeception](https://github.com/Codeception/Codeception/tree/2.5/ext#dotreporter) and adds some fancy enhancements that make it behave more like PHPUnit's reporter.

#### Requirements

* Codeception 2.3+
* PHP 5.4+

#### Example

 ```sh
 .......... 10 / 75 ( 13%)
 .......... 20 / 75 ( 28%)
 .......... 30 / 75 ( 40%)
 .......... 40 / 75 ( 54%)
 .......... 50 / 75 ( 67%)
 .......... 60 / 75 ( 80%)
 .......... 70 / 75 ( 94%)
 .....      75 / 75 (100%)
 
 Time: 2.07 seconds, Memory: 20.00MB
 
 OK (75 tests, 124 assertions)
 ```

`.` when the test succeeds.
`F` when an assertion fails while running the test method.
`E` when an error occurs while running the test method.
`S` when the test has been skipped.
`I` when the test is marked as being incomplete or not yet implemented.

## Installation

### Using composer

1. Install using composer
```sh
composer require --dev cloudstek/codeception-fancy-dot-reporter
```
2. Enable the extension (see [usage](#usage)).

### Manually

1. Clone the repository or download and extract the [latest release](https://github.com/Cloudstek/codeception-fancy-dot-reporter/releases/latest). 
2. Require the `src/DotReporter.php` file in `tests/_bootstrap.php`
3. Enable the extension (see [usage](#usage)).

## Usage

To enable the extension specify it with `—ext` or add it to the list of enabled extensions in your `codeception.yml`.

With `—ext` option:

```sh
vendor/bin/codeception run --ext "Cloudstek\Codeception\Extension\DotReporter"
```

With `codeception.yml`:

```yaml
extensions:
  enabled:
    - Cloudstek\Codeception\Extension\DotReporter
```

### Configuration options

See the [official documentation](https://codeception.com/docs/08-Customization#Configuring-Extension) on how to configure Codeception extensions. See the list below for available options.

#### columns

*Type: integer*

Number of columns to use for progress output. Defaults to the width of the output console, which is always the max.

## License

See [LICENSE](./LICENSE)

