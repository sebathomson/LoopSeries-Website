<?php
namespace LoopAnime\AppBundle\ConsoleCommand;


use Symfony\Component\Console\Output\OutputInterface;

interface ConsoleOutputAware {

    public function setOutputInterface(OutputInterface $outputInterface);
    public function OutputLog($message, $level);

}
