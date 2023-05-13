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

namespace de\codenamephp\deploymentchecks\async\test\Check\Factory\FromCheck;

use de\codenamephp\deploymentchecks\async\Check\Factory\FromCheck\WithErrorHandlerFactory;
use de\codenamephp\deploymentchecks\async\Check\ParallelCheckWithErrorHandler;
use de\codenamephp\deploymentchecks\async\ErrorHandler\ErrorHandlerInterface;
use de\codenamephp\deploymentchecks\async\SuccessHandler\SuccessHandlerInterface;
use de\codenamephp\deploymentchecks\base\Check\CheckInterface;
use PHPUnit\Framework\TestCase;

final class WithErrorHandlerFactoryTest extends TestCase {

  public function testBuild() : void {
    $successHandler = $this->createMock(SuccessHandlerInterface::class);
    $errorHandler = $this->createMock(ErrorHandlerInterface::class);
    $check = $this->createMock(CheckInterface::class);

    $sut = new WithErrorHandlerFactory($successHandler, $errorHandler);

    $wrappedCheck = $sut->build($check);

    self::assertInstanceOf(ParallelCheckWithErrorHandler::class, $wrappedCheck);
    self::assertSame($check, $wrappedCheck->check);
    self::assertSame($successHandler, $wrappedCheck->successHandler);
    self::assertSame($errorHandler, $wrappedCheck->errorHandler);
  }

  public function test__construct() : void {
    $successHandler = $this->createMock(SuccessHandlerInterface::class);
    $errorHandler = $this->createMock(ErrorHandlerInterface::class);

    $sut = new WithErrorHandlerFactory($successHandler, $errorHandler);

    $this->assertSame($successHandler, $sut->successHandler);
    $this->assertSame($errorHandler, $sut->errorHandler);
  }
}
