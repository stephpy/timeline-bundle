<?php

namespace Highco\TimelineBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Highco\TimelineBundle\Model\TimelineAction;

/**
 * DeployTimelineActionCommand
 *
 * @uses ContainerAwareCommand
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class DeployTimelineActionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('highco:timeline-deploy')
            ->setDescription('Deploy on spreads for waiting timeline action')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'How many actions will be deployed', 200)
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = (int) $input->getOption('limit');

        if($limit < 1)
        {
            throw new \InvalidArgumentException('Limit defined should be biggest than 0 ...');
        }

        $container = $this->getContainer();

        $qb = $container->get('highco.timeline.entity_manager')
            ->getRepository('HighcoTimelineBundle:TimelineAction')
            ->createQueryBuilder('ta')
            ->where('ta.status_wanted = :status_wanted')
            ->setMaxResults($limit)
            ->setParameter('status_wanted', TimelineAction::STATUS_PUBLISHED)
            ;

        $results = $qb->getQuery()
            ->getResult();

        $output->writeln(sprintf('<info>There is %s timeline action(s) to deploy</info>', count($results)));

        $deployer = $container->get('highco.timeline.spread.deployer');

        foreach($results as $timelineAction)
        {
            try {
                $deployer->deploy($timelineAction);
                $output->writeln(sprintf('<comment>Deploy timeline action %s</comment>', $timelineAction->getId()));
            } catch(\Exception $e){
                $message = sprintf('[TIMELINE] Error during deploy timeline_action %s', $timelineAction->getId());

                $container->get('logger')->crit($message);
                $output->writeln(sprintf('<error>%s</error>', $message));
            }
        }

        $output->writeln('<info>Done</info>');
    }
}
