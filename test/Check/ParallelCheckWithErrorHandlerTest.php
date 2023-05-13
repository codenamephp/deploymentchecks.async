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

namespace de\codenamephp\deploymentchecks\async\test\Check;

use de\codenamephp\deploymentchecks\async\Check\ParallelCheckWithErrorHandler;
use de\codenamephp\deploymentchecks\async\ErrorHandler\ErrorHandlerInterface;
use de\codenamephp\deploymentchecks\async\SuccessHandler\SuccessHandlerInterface;
use de\codenamephp\deploymentchecks\base\Check\CheckInterface;
use PHPUnit\Framework\TestCase;

final class ParallelCheckWithErrorHandlerTest extends TestCase {

  public function testErrorHandler() : void {
    $errorHandler = $this->createMock(ErrorHandlerInterface::class);

    $check = new ParallelCheckWithErrorHandler($this->createMock(CheckInterface::class), $this->createMock(SuccessHandlerInterface::class), $errorHandler);

    self::assertSame($errorHandler, $check->errorHandler());
  }

  public function test__invoke() : void {
    $check = $this->createMock(CheckInterface::class);
    $check->expects(self::once())->method('run');

    $check = new ParallelCheckWithErrorHandler($check, $this->createMock(SuccessHandlerInterface::class), $this->createMock(ErrorHandlerInterface::class));

    $check();
  }

  public function test__construct() : void {
    $check = $this->createMock(CheckInterface::class);
    $successHandler = $this->createMock(SuccessHandlerInterface::class);
    $errorHandler = $this->createMock(ErrorHandlerInterface::class);

    $sut = new ParallelCheckWithErrorHandler($check, $successHandler, $errorHandler);

    self::assertSame($check, $sut->check);
    self::assertSame($successHandler, $sut->successHandler);
    self::assertSame($errorHandler, $sut->errorHandler);
  }

  public function testSuccessHandler() : void {
    $successHandler = $this->createMock(SuccessHandlerInterface::class);

    $check = new ParallelCheckWithErrorHandler($this->createMock(CheckInterface::class), $successHandler, $this->createMock(ErrorHandlerInterface::class));

    self::assertSame($successHandler, $check->successHandler());
  }
}
