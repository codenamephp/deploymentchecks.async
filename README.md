# deploymentchecks.async

![Packagist Version](https://img.shields.io/packagist/v/codenamephp/deploymentchecks.async)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/codenamephp/deploymentchecks.async)
![Lines of code](https://img.shields.io/tokei/lines/github/codenamephp/deploymentchecks.async)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/codenamephp/deploymentchecks.async)
![CI](https://github.com/codenamephp/deploymentchecks.async/workflows/CI/badge.svg)
![Packagist Downloads](https://img.shields.io/packagist/dt/codenamephp/deploymentchecks.async)
![GitHub](https://img.shields.io/github/license/codenamephp/deploymentchecks.async)

Package that let's you run the deployment checks in parallel.

## Installation

Easiest way is via composer. Just run `composer require codenamephp/deploymentchecks.async` in your cli which should install the latest version for you.

You should also explicitly install the codenamephp/deploymentchecks.base package since you will end up using it directly in almost all cases.

## Important note

Because spatie/async (the package that provides the underlying async functionality) passes data between the processes/threads with serialize/unserialize
the packages that want to be compatible with this MUST make sure the checks are serializable. This means that the checks MUST NOT contain any resources,
closures or other non-serializable data.

## Usage

Just use it like any other deployment check collection. Only difference is that you need to provide a pool and some factories. The number of threads
can also be configured with the pool.

```php
use de\codenamephp\deploymentchecks\async\Check\Factory\FromCheck\WithErrorHandlerFactory;
use de\codenamephp\deploymentchecks\async\Collection\AsyncCheckCollection;
use de\codenamephp\deploymentchecks\base\Check\Result\Collection\ResultCollection;
use de\codenamephp\deploymentchecks\base\Check\Result\WithExitCodeInterface;
use de\codenamephp\deploymentchecks\base\ExitCode\DefaultExitCodes;
use de\codenamephp\deploymentchecks\http\RunTestsOnHttpResponse;
use de\codenamephp\deploymentchecks\http\Test\StatusCode;
use GuzzleHttp\Psr7\Request;

require_once __DIR__ . '/../vendor/autoload.php';

$result = (new AsyncCheckCollection( // This is the collection that will run the checks instead of the default sequential collection
new \Spatie\Async\Pool(), // This is the pool that will run the checks in parallel
  new ResultCollection(), // This is the result collection that will be filled with the results of the checks
  new WithErrorHandlerFactory(), // This is the factory that will create the checks from the checks that are passed to the collection
  new RunTestsOnHttpResponse( // These are the checks that will be run, in this case from the codenamephp/deploymentchecks.http package
    new Request('GET', 'https://localhost'),
    'Frontpage should be available',
    new StatusCode(200),
  ),
  new RunTestsOnHttpResponse(
    new Request('GET', 'https://localhost/admin'),
    'Admin login page should be available',
    new StatusCode(401),
  )
))->run();

exit($result instanceof WithExitCodeInterface ? $result->exitCode() : ($result->successful() ? DefaultExitCodes::SUCCESSFUL->value : DefaultExitCodes::ERROR->value));
```
