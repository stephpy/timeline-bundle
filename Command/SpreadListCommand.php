<?php

namespace Spy\TimelineBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This command will show all services which are defined as spread.
 */
class SpreadListCommand extends Command implements ContainerAwareInterface
{
    private $container;

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
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $spreads = $this->container
            ->get('spy_timeline.spread.deployer')
            ->getSpreads();

        $output->writeln(sprintf('<info>There is %s timeline spread(s) defined</info>', count($spreads)));

        foreach ($spreads as $spread) {
            $output->writeln(sprintf('<comment>- %s</comment>', get_class($spread)));
        }

        return 0;
    }

    public function setContainer(?ContainerInterface $container)
    {
        $this->container = $container;
    }
}
