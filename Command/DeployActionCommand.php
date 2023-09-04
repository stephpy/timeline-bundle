<?php

namespace Spy\TimelineBundle\Command;

use Exception;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This command will deploy each actions (see limit option) which
 * has PUBLISHED on status_wanted.
 */
class DeployActionCommand extends Command implements ContainerAwareInterface
{
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('spy_timeline:deploy')
            ->setDescription('Deploy on spreads for waiting action')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'How many actions will be deployed', 200)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output):int
    {
        $limit = (int) $input->getOption('limit');

        if ($limit < 1) {
            throw new InvalidArgumentException('Limit defined should be biggest than 0 ...');
        }

        $actionManager = $this->container->get('spy_timeline.action_manager');
        $results       = $actionManager->findActionsWithStatusWantedPublished($limit);

        $output->writeln(sprintf('<info>There is %s action(s) to deploy</info>', count($results)));

        $deployer = $this->container->get('spy_timeline.spread.deployer');

        foreach ($results as $action) {
            try {
                $deployer->deploy($action, $actionManager);
                $output->writeln(sprintf('<comment>Deploy action %s</comment>', $action->getId()));
            } catch (Exception $e) {
                $message = sprintf('[TIMELINE] Error during deploy action %s', $action->getId());
                if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                    $message .= sprintf('%s: %s', $message, $e->getMessage());
                }

                $this->container->get('logger')->crit($message);
                $output->writeln(sprintf('<error>%s</error>', $message));
            }
        }

        $output->writeln('<info>Done</info>');

        return 0;
    }

    /**
     * @param ContainerInterface|null $container
     *
     * @return void
     */
    public function setContainer(?ContainerInterface $container): void
    {
        $this->container = $container;
    }
}
