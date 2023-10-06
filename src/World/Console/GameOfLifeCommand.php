<?php

declare(strict_types=1);

namespace App\World\Console;

use App\World\Service\Input\InputNormalizerInterface;
use App\World\Service\Population\Evolutionizer;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @psalm-suppress UnusedClass
 */
class GameOfLifeCommand extends Command
{
    public function __construct(
        private readonly InputNormalizerInterface $inputNormalizer,
        private readonly Evolutionizer $evolutionizer,
        private readonly string $defaultFileName = 'world.xml'
    ) {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this->setName('app:play')
            ->addOption(
                'file-path',
                null,
                InputOption::VALUE_REQUIRED,
                'Path to file with world definition (.xml)',
            );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Welcome to Game of Life!');
        /** @psalm-suppress RedundantCast */
        $inputFilePath = (string) ($input->getOption('file-path') ?? $this->defaultFileName);
        try {
            $world = $this->inputNormalizer->normalize($inputFilePath);
            for ($i = 1; $i <= $world->getIterations(); $i++) {
                $worldChanged = $this->evolutionizer->evolve($world);
                //$renderer->render($world);
                if ($worldChanged === false) {
                    break;
                }
            }
            //$outputDenormalizer->denormalize($world);
        } catch (Exception $e) {
            $output->writeln(sprintf(
                '<error>%s</error>',
                $e->getMessage(),
            ));
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
