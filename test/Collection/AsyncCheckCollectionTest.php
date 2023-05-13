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

namespace de\codenamephp\deploymentchecks\async\test\Collection;

use Closure;
use de\codenamephp\deploymentchecks\async\Check\Factory\FromCheck\FromCheckFactoryInterface;
use de\codenamephp\deploymentchecks\async\Check\ParallelCheckInterface;
use de\codenamephp\deploymentchecks\async\Check\WithErrorHandlerInterface;
use de\codenamephp\deploymentchecks\async\Collection\AsyncCheckCollection;
use de\codenamephp\deploymentchecks\async\test\MockeryTrait;
use de\codenamephp\deploymentchecks\base\Check\CheckInterface;
use de\codenamephp\deploymentchecks\base\Check\Result\Collection\ResultCollectionInterface;
use de\codenamephp\deploymentchecks\base\Check\Result\ResultInterface;
use Exception;
use Hamcrest\Matchers;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Spatie\Async\Pool;
use Spatie\Async\Process\Runnable;

final class AsyncCheckCollectionTest extends TestCase {

  use MockeryPHPUnitIntegration, MockeryTrait;

  public function testRun() : void {
    $resultCollection = $this->createMock(ResultCollectionInterface::class);

    $check1 = $this->createMock(CheckInterface::class);
    $check2 = $this->createMock(CheckInterface::class);
    $check3 = $this->createMock(CheckInterface::class);

    $output1 = $this->createMock(ResultInterface::class);
    $output2 = $this->createMock(ResultInterface::class);
    $output3 = $this->createMock(ResultInterface::class);

    $exception = new Exception();

    $parallelCheck1 = $this->mock(ParallelCheckInterface::class, fn(Mockery\MockInterface $parallelCheck) => $parallelCheck->expects('successHandler->handle')->once()->with($resultCollection, $output1));
    $parallelCheck2 = $this->mock(ParallelCheckInterface::class, WithErrorHandlerInterface::class, function(Mockery\MockInterface $parallelCheck) use ($resultCollection, $exception, $output2) {
      $parallelCheck->expects('successHandler->handle')->once()->with($resultCollection, $output2);
      $parallelCheck->expects('errorHandler->handle')->once()->with($resultCollection, $exception);
    });
    $parallelCheck3 = $this->mock(ParallelCheckInterface::class, fn(Mockery\MockInterface $parallelCheck) => $parallelCheck->expects('successHandler->handle')->once()->with($resultCollection, $output3));

    $fromCheckFactory = $this->mock(FromCheckFactoryInterface::class, function(Mockery\MockInterface $fromCheckFactory) use ($check1, $check2, $check3, $parallelCheck1, $parallelCheck2, $parallelCheck3) {
      $fromCheckFactory->expects('build')->once()->ordered()->with($check1)->andReturn($parallelCheck1);
      $fromCheckFactory->expects('build')->once()->ordered()->with($check2)->andReturn($parallelCheck2);
      $fromCheckFactory->expects('build')->once()->ordered()->with($check3)->andReturn($parallelCheck3);
    });

    $pool = $this->mock(Pool::class, function(Mockery\MockInterface $pool) use ($exception, $output1, $output2, $output3, $parallelCheck1, $parallelCheck2, $parallelCheck3) {
      $pool->expects('add')->once()->ordered()->with($parallelCheck1)->andReturn($this->mock(Runnable::class, function(Mockery\MockInterface $runnable) use ($output1) {
        $runnable->expects('then')->once()->ordered()->andReturnUsing(static fn(Closure $closure) => $closure($output1));
      }));

      $pool->expects('add')->once()->ordered()->with($parallelCheck2)->andReturn($this->mock(Runnable::class, function(Mockery\MockInterface $runnable) use ($output2, $exception) {
        $runnable->expects('then')->once()->ordered()->andReturnUsing(static function(Closure $closure) use ($runnable, $output2) {
          $closure($output2);
          return $runnable;
        });
        $runnable->expects('catch')->once()->ordered()->with(Matchers::anInstanceOf(Closure::class))->andReturnUsing(static fn(Closure $closure) => $closure($exception));
      }));

      $pool->expects('add')->once()->ordered()->with($parallelCheck3)->andReturn($this->mock(Runnable::class, function(Mockery\MockInterface $runnable) use ($output3) {
        $runnable->expects('then')->once()->ordered()->andReturnUsing(static fn(Closure $closure) => $closure($output3));
      }));

      $pool->expects('wait')->ordered()->once();
    });

    self::assertSame($resultCollection, (new AsyncCheckCollection($pool, $resultCollection, $fromCheckFactory, $check1, $check2, $check3))->run());
  }

  public function test__construct() : void {
    $pool = $this->createMock(Pool::class);
    $resultCollection = $this->createMock(ResultCollectionInterface::class);
    $fromCheckFactory = $this->createMock(FromCheckFactoryInterface::class);
    $check1 = $this->createMock(CheckInterface::class);
    $check2 = $this->createMock(CheckInterface::class);
    $check3 = $this->createMock(CheckInterface::class);

    $sut = new AsyncCheckCollection($pool, $resultCollection, $fromCheckFactory, $check1, $check2, $check3);

    self::assertSame($pool, $sut->pool);
    self::assertSame($resultCollection, $sut->resultCollection);
    self::assertSame([$check1, $check2, $check3], $sut->checks);
    self::assertSame($fromCheckFactory, $sut->fromCheckFactory);
  }

}
