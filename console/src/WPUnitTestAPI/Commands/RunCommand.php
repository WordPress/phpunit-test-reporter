<?php

namespace WPUnitTestAPI\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class RunCommand extends Command {

    protected function configure()
    {
        $this->setName("run")
             ->setDescription("Runs the WP Unit Tests and submits the results.")
             ->addArgument('directory', InputArgument::REQUIRED, 'The directory containing WordPress Core.')
             ->setHelp(<<<EOT
TODO: Write help.
EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $header_style = new OutputFormatterStyle('white', 'default', array('bold'));
        $output->getFormatter()->setStyle('header', $header_style);

        $directory = $input->getArgument('directory');

        $process = new Process("ls -lsa $directory");
        $process->run();

        if (!$process->isSuccessful()) {
          throw new ProcessFailedException($process);
        }

        $output->writeln('<header>Running Unit Tests</header>');
        $output->writeln($process->getOutput());
        $output->writeln('<header>Done</header>');
    }
}
