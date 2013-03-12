<?php

namespace MS\Bundle\FrontControllerSecurityBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class FrontControllerSecurityIPAddCommand extends FrontControllerSecurityBase
{
    protected function configure()
    {
        $this
            ->setName('front-controller:security:ip:add')
            ->setDescription('Add ip to the security .json file')
            ->addArgument(
                'begin',
                InputArgument::REQUIRED,
                'Beginning ip of range'
            )
            ->addArgument(
                'end',
                InputArgument::OPTIONAL,
                'Ending ip of range'
            )
            ->addArgument(
                'expire',
                InputArgument::OPTIONAL,
                'Expiration date'
            )
            ->addArgument(
                'note',
                InputArgument::OPTIONAL,
                'Ending ip of range'
            )
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

        $begin = $input->getArgument('begin');
        if(!ip2long($begin)){
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid ip for begin', $begin));
        }

        $end = $input->getArgument('end') ?: $begin;
        if(!ip2long($end)){
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid ip for end', $end));
        }

        $expire = $input->getArgument('expire');
        if($expire && false === $ts = strtotime($expire)){
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid datetime for expire', $expire));
        }
        $expire = isset($ts) ? date('Y-m-d H:i:s', $ts) : null;

        $ranges[] = array(
            'begin' => $begin,
            'end' => $end,
            'expire' => $expire,
            'note' => $input->getArgument('note'),
        );

        $this->putSecurityFile($filename, $ranges);

        $command = $this->getApplication()->find('front-controller:security:ip:list');
        $returnCode = $command->run(new ArrayInput(array('command' => 'development:security:ip:list', '--file' => $filename)), $output);
    }

}
