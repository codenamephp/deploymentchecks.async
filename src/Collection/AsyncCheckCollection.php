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
