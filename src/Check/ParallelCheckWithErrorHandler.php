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

namespace de\codenamephp\deploymentchecks\async\Check;

use de\codenamephp\deploymentchecks\async\ErrorHandler\ErrorHandlerInterface;
use de\codenamephp\deploymentchecks\async\ErrorHandler\RethrowException;
use de\codenamephp\deploymentchecks\async\SuccessHandler\AddToResultCollection;
use de\codenamephp\deploymentchecks\async\SuccessHandler\SuccessHandlerInterface;
use de\codenamephp\deploymentchecks\base\Check\CheckInterface;
use de\codenamephp\deploymentchecks\base\Check\Result\ResultInterface;

/**
 * Basically a wrapper around a check that implements the ParallelCheckInterface and WithErrorHandlerInterface. The spatie async pool expects a callable so this
 * class implements that interface and just calls the check. The check itself is passed in the constructor and the success and error handler are optional.
 *
 * The default AsyncCheckCollection will use this check to run the checks in parallel. It will use the success and error handler as quasi callbacks to handle the result
 * of the check.
 *
 * The default success handler will add the result to the result collection and the default error handler will rethrow the exception.
 */
final readonly class ParallelCheckWithErrorHandler implements ParallelCheckInterface, WithErrorHandlerInterface
{

    public function __construct(
        public CheckInterface $check,
        public SuccessHandlerInterface $successHandler = new AddToResultCollection(),
        public ErrorHandlerInterface $errorHandler = new RethrowException(),
    ) {}

    public function __invoke(): ResultInterface
    {
        return $this->check->run();
    }

    public function successHandler(): SuccessHandlerInterface
    {
        return $this->successHandler;
    }

    public function errorHandler(): ErrorHandlerInterface
    {
        return $this->errorHandler;
    }
}
