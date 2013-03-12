<?php

namespace MS\Bundle\FrontControllerSecurityBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FrontControllerSecurityIPListCommand extends FrontControllerSecurityBase
{
    protected function configure()
    {
        $this
            ->setName('front-controller:security:ip:list')
            ->setDescription('List entries in the security .json file')
            ->addOption(
                'file',
                null,
                InputOption::VALUE_OPTIONAL,
                'If not set, the task will guess the security file'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $this->getSecurityFilename($input->getOption('file'));
        $ranges = $this->getCurrentSecurity($filename);

        $output->writeln(sprintf('Reading data from: "%s"', $filename));

        $formatter = $this->getHelperSet()->get('formatter');
        foreach($ranges as $id => $range){
            $output->writeln($formatter->formatSection(
                $id + 1,
                sprintf("%s - %s expires: %s (%s)",
                    $range->begin,
                    $range->end,
                    ($range->expire && strtotime($range->expire) < time()) ? '<error>'.$range->expire.'</error>' : $range->expire,
                    $range->note
                )
            ));
        }
    }

}
