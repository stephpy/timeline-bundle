<?php

namespace Spy\TimelineBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command will show all services which are defined as spread.
 */
class SpreadListCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('spy_timeline:spreads')
            ->setDescription('Show list of spreads')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $spreads = $this->getContainer()
            ->get('spy_timeline.spread.deployer')
            ->getSpreads()
        ;

        $output->writeln(sprintf('<info>There is %s timeline spread(s) defined</info>', count($spreads)));

        foreach ($spreads as $spread) {
            $output->writeln(sprintf('<comment>- %s</comment>', get_class($spread)));
        }
    }
}
