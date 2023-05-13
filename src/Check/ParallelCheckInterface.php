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

use de\codenamephp\deploymentchecks\async\SuccessHandler\SuccessHandlerInterface;
use de\codenamephp\deploymentchecks\base\Check\Result\ResultInterface;

/**
 * Interface for checks that can be used in parallel using the async pool
 */
interface ParallelCheckInterface
{

    /**
     * Since the async pool only accepts callables, this method is used to invoke the check and return the result
     *
     * @return ResultInterface
     */
    public function __invoke(): ResultInterface;

    /**
     * Gets the success handler that is used to handle the result if the check was successful
     *
     * @return SuccessHandlerInterface
     */
    public function successHandler(): SuccessHandlerInterface;
}
