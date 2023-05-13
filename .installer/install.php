<?php declare(strict_types=1);

use de\codenamephp\installer\StepExecutor;
use de\codenamephp\installer\steps\CopyTemplateFolder;
use de\codenamephp\installer\steps\DeleteFilesAndFolders;
use de\codenamephp\installer\steps\SequentialCollection;
use de\codenamephp\installer\templateCopy\directoryHandler\CreateDirectoryWithSymfonyFilesystem;
use de\codenamephp\installer\templateCopy\fileHandler\RenderWithTwigAndDumpWithSymfonyFilesystem;
use de\codenamephp\installer\templateCopy\variableReplacer\FramedStringReplace;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

return call_user_func(static function() {
  require_once __DIR__ . '/../vendor/autoload.php';

  $filesystem = new Filesystem();
  $variableReplacer = new FramedStringReplace();
  $componentName = basename(trim(shell_exec("git config --get remote.origin.url")), '.git');
  $variables = [
    'vendor' => 'codenamephp',
    'componentName' => $componentName,
    'namespace' => implode('\\', array_merge(['de', 'codenamephp'], explode('.', $componentName))),
    'phpVersion' => '8.1',
  ];

  (new StepExecutor(
    new SequentialCollection(
      new CopyTemplateFolder(
        new \de\codenamephp\installer\templateCopy\RecursiveIterator(
          new CreateDirectoryWithSymfonyFilesystem($filesystem, $variableReplacer),
          new RenderWithTwigAndDumpWithSymfonyFilesystem($filesystem, $variableReplacer, new Environment(new FilesystemLoader('/', '/')))
        ),
        __DIR__ . '/templates',
        dirname(__DIR__),
        $variables
      ),
      new DeleteFilesAndFolders($variableReplacer, $filesystem, [dirname(__DIR__) . '/src/.gitkeep', __DIR__], $variables),
    )
  ))->run();
});