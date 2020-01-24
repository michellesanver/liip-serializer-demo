<?php

declare(strict_types=1);

namespace App\Serializer\Command;

use Liip\Serializer\Compiler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command is only set up in the dev environment.
 *
 * Other environments should only use the cache:warmup process, which also triggers the compiler.
 */
class SerializerGeneratorCommand extends Command
{
    protected static $defaultName = 'liip:serializer:generate';

    /**
     * @var Compiler
     */
    private $compiler;

    public function __construct(Compiler $compiler)
    {
        $this->compiler = $compiler;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Command to re-build the generated serializer files. Only available in dev as a shortcut to not need to do a full cache:clear too often when working on models or the serializer.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->compiler->compile();

        return 0;
    }
}
