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

namespace de\codenamephp\deploymentchecks\async\test\ErrorHandler;

use de\codenamephp\deploymentchecks\async\ErrorHandler\RethrowException;
use de\codenamephp\deploymentchecks\base\Check\Result\Collection\ResultCollectionInterface;
use Exception;
use PHPUnit\Framework\TestCase;

final class RethrowExceptionTest extends TestCase {

  public function testHandle() : void {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('test');

    (new RethrowException())->handle($this->createMock(ResultCollectionInterface::class), new Exception('test'));
  }
}
