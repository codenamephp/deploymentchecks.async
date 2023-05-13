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

namespace de\codenamephp\deploymentchecks\async\test\SuccessHandler;

use de\codenamephp\deploymentchecks\async\SuccessHandler\AddToResultCollection;
use de\codenamephp\deploymentchecks\base\Check\Result\Collection\ResultCollectionInterface;
use de\codenamephp\deploymentchecks\base\Check\Result\ResultInterface;
use PHPUnit\Framework\TestCase;

final class AddToResultCollectionTest extends TestCase {

  public function testHandle() : void {
    $resultCollection = $this->createMock(ResultCollectionInterface::class);
    $result = $this->createMock(ResultInterface::class);

    $resultCollection->expects($this->once())->method('add')->with($result);

    $this->assertSame($result, (new AddToResultCollection())->handle($resultCollection, $result));
  }
}
