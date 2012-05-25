<?php

namespace Highco\TimelineBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command will deploy each timeline actions (see limit option) which
 * has PUBLISHED on status_wanted.
 *
 * @uses ContainerAwareCommand
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class DeployTimelineActionCommand extends ContainerAwareCommand
{
    /**
     * configure command
     */
    protected function configure()
    {
        $this
            ->setName('highco:timeline-deploy')
            ->setDescription('Deploy on spreads for waiting timeline action')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'How many actions will be deployed', 200);
    }

    /**
     * @param InputInterface  $input  input variable
     * @param OutputInterface $output output variable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = (int) $input->getOption('limit');

        if ($limit < 1) {
            throw new \InvalidArgumentException('Limit defined should be biggest than 0 ...');
        }

        $container = $this->getContainer();

        $results = $container->get('highco.timeline_action_manager')
            ->getTimelineWithStatusPublished($limit);

        $output->writeln(sprintf('<info>There is %s timeline action(s) to deploy</info>', count($results)));

        $deployer = $container->get('highco.timeline.spread.deployer');

        foreach ($results as $timelineAction) {
            try {
                $deployer->deploy($timelineAction);
                $output->writeln(sprintf('<comment>Deploy timeline action %s</comment>', $timelineAction->getId()));
            } catch (\Exception $e) {
                $message = sprintf('[TIMELINE] Error during deploy timeline_action %s', $timelineAction->getId());

                $container->get('logger')->crit($message);
                $output->writeln(sprintf('<error>%s</error>', $message));
            }
        }

        $output->writeln('<info>Done</info>');
    }
}
