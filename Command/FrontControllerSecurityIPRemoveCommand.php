<?php

namespace MS\Bundle\FrontControllerSecurityBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class FrontControllerSecurityIPRemoveCommand extends FrontControllerSecurityBase
{
    protected function configure()
    {
        $this
            ->setName('front-controller:security:ip:remove')
            ->setDescription('Remove an ip from the security .json file')
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

        $command = $this->getApplication()->find('front-controller:security:ip:list');
        $returnCode = $command->run(new ArrayInput(array('command' => 'development:security:ip:list', '--file' => $filename)), $output);

        $ranges = $this->getCurrentSecurity($filename);
        $max = count($ranges) + 1;

        $dialog = $this->getHelperSet()->get('dialog');
        $remove = $dialog->askAndValidate(
            $output,
            'Please enter the number to remove, A for all, C for cancel:  ',
            function ($answer) use ($max) {
                if(in_array($answer, array('A', 'a', 'C', 'c'))){

                    return strtoupper($answer);
                }
                if(is_numeric($answer) && $answer >= 1 && $answer <= $max) {
                    return (int) $answer;
                }

                throw new \RunTimeException('The input is not valid');
            },
            false,
            'C'
        );

        if($remove == 'A'){
            $ranges = array();
        }elseif($remove == 'C'){

            return;
        }else{
            unset($ranges[$remove - 1]);
        }

        $this->putSecurityFile($filename, $ranges);
    }

}
