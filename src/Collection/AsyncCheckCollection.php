<?php declare(strict_types=1);

/*
 *  Copyright 2023 Bastian Schwarz <bastian@codename-php.de>.
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *        http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace de\codenamephp\deploymentchecks\async\Collection;

use de\codenamephp\deploymentchecks\async\Check\Factory\FromCheck\FromCheckFactoryInterface;
use de\codenamephp\deploymentchecks\async\Check\WithErrorHandlerInterface;
use de\codenamephp\deploymentchecks\base\Check\CheckInterface;
use de\codenamephp\deploymentchecks\base\Check\Result\Collection\ResultCollectionInterface;
use de\codenamephp\deploymentchecks\base\Check\Result\ResultInterface;
use Spatie\Async\Pool;
use Throwable;

/**
 * A collection that runs all checks in parallel and returns the results in a collection. It iterates over all checks, passes them to the factory to create
 * a wrapped parallel check that can be passed to the pool. After the check is finished the result is either passed to the success handler or to the
 * error handler if the check implements the error handler interface. The result is then added to the result collection. The error handler is called
 * if the check throws an exception, not if it returns a failed result.
 *
 * When all checks are finished the result collection is returned.
 *
 * @psalm-api
 */
final readonly class AsyncCheckCollection implements CheckInterface
{

    /**
     * @var array<CheckInterface>
     */
    public array $checks;

    public function __construct(
        public Pool $pool,
        public ResultCollectionInterface $resultCollection,
        public FromCheckFactoryInterface $fromCheckFactory,
        CheckInterface ...$checks)
    {
        $this->checks = $checks;
    }

    public function run(): ResultInterface
    {
        foreach ($this->checks as $check) {
            $parallelCheck = $this->fromCheckFactory->build($check);
            $runnable = $this->pool
                ->add($parallelCheck)
                ->then(fn(ResultInterface $output) => $parallelCheck->successHandler()->handle($this->resultCollection, $output));
            if ($parallelCheck instanceof WithErrorHandlerInterface) $runnable->catch(fn(Throwable $exception): mixed => $parallelCheck->errorHandler()->handle($this->resultCollection, $exception));
        }
        $this->pool->wait();
        return $this->resultCollection;
    }
}
