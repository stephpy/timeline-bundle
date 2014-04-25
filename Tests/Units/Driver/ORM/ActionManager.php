<?php

namespace Spy\TimelineBundle\Tests\Units\Driver\ORM;

require_once __DIR__ . "/../../../../vendor/autoload.php";

use atoum\AtoumBundle\Test\Units\Test;
use Spy\TimelineBundle\Driver\ORM\ActionManager as TestedModel;
use Spy\Timeline\ResolveComponent\ValueObject\ResolvedComponentData;

/**
 * Test file for Spy\TimelineBundle\Driver\ORM\ActionManager
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class ActionManager extends Test
{
    public function testCreateComponent()
    {
        $this
            ->if($this->mockClass('Spy\Timeline\ResultBuilder\ResultBuilderInterface', '\Mock'))
            ->and($this->mockClass('Spy\Timeline\ResolveComponent\ComponentDataResolverInterface', '\Mock'))
            ->and($this->mockClass('Doctrine\Common\Persistence\ObjectManager', '\Mock'))
            ->and($objectManager = new \mock\ObjectManager())
            ->and($resultBuilder = new \mock\ResultBuilderInterface())
            ->and($componentDataResolver = new \mock\ComponentDataResolverInterface())
            ->and($this->calling($componentDataResolver)->resolveComponentData = function () {
                return new ResolvedComponentData('user', '1');
            })
            ->and($actionClass = 'Spy\Timeline\Model\Action')
            ->and($componentClass = 'Spy\Timeline\Model\Component')
            ->and($actionComponentClass = 'Spy\Timeline\Model\ActionComponent')
            ->and($actionManager = new TestedModel($objectManager, $resultBuilder, $actionClass, $componentClass, $actionComponentClass))
                ->exception(function () use ($actionManager) {
                    $actionManager->getComponentDataResolver();
                }
             )->hasMessage('Component data resolver not set')
            ->and($actionManager->setComponentDataResolver($componentDataResolver))
            ->when($result = $actionManager->createComponent('user', 1))
            ->mock($objectManager)->call('persist')->withArguments($result)->exactly(1)
            ->mock($objectManager)->call('flush')->exactly(1)
            ->mock($componentDataResolver)->call('resolveComponentData')->withArguments('user', 1)->exactly(1)
            ->string($result->getIdentifier())->isEqualTo(1)
            ->string($result->getModel())->isEqualto('user')
            ->variable($result->getData())->isNull()
            ;
    }

    public function testfindOrCreateComponentWithExistingComponent()
    {
        $resolvedComponentData = new ResolvedComponentData('user', 1);
        $this
            ->if($this->mockClass('Spy\Timeline\ResultBuilder\ResultBuilderInterface', '\Mock'))
            ->and($this->mockGenerator->orphanize('__construct'))
            ->and($this->mockGenerator->shuntParentClassCalls())
            ->and($this->mockClass('Doctrine\ORM\EntityRepository', '\Mock'))
            ->and($this->mockClass('Doctrine\Orm\QueryBuilder', '\Mock'))
            ->and($this->mockClass('Spy\Timeline\ResolveComponent\ComponentDataResolverInterface', '\Mock'))
            ->and($this->mockClass('Doctrine\Common\Persistence\ObjectManager', '\Mock'))
            ->and($this->mockGenerator->orphanize('__construct'))
            ->and($this->mockGenerator->shuntParentClassCalls())
            ->and($this->mockClass('Doctrine\ORM\AbstractQuery', '\Mock'))
            ->and($this->mockClass('Spy\Timeline\Model\Component'))
            ->and($entityRepository = new \mock\EntityRepository())
            ->and($objectManager = new \mock\ObjectManager())
            ->and($resultBuilder = new \mock\ResultBuilderInterface())
            ->and($componentDataResolver = new \mock\ComponentDataResolverInterface())
            ->and($queryBuilder = new \mock\QueryBuilder())
            ->and($query = new \mock\AbstractQuery())
            ->and($component = new \mock\Component())
            ->and($this->calling($componentDataResolver)->resolveComponentData = function () use ($resolvedComponentData) {
                return $resolvedComponentData;
            })
            ->and($this->calling($objectManager)->getRepository = function() use($entityRepository) {
                return $entityRepository;
            })
            ->and($this->calling($entityRepository)->createQueryBuilder = function() use ($queryBuilder) {
                return $queryBuilder;
            })
            //here we return the component as result of the query
            ->and($this->calling($query)->getOneOrNullResult = function() use ($component) { return $component;})
            //grouping those did not work the method was __call
            ->and($this->calling($queryBuilder)->where = function() use($queryBuilder) { return $queryBuilder;})
            ->and($this->calling($queryBuilder)->andWhere = function () use ($queryBuilder) {
                return $queryBuilder;
            })
            ->and($this->calling($queryBuilder)->setParameter = function () use ($queryBuilder) {
                return $queryBuilder;
            })
            ->and($this->calling($queryBuilder)->getQuery = function() use($query) { return $query;})
            ->and($actionClass = 'Spy\Timeline\Model\Action')
            ->and($componentClass = 'Spy\Timeline\Model\Component')
            ->and($actionComponentClass = 'Spy\Timeline\Model\ActionComponent')
            ->and($actionManager = new TestedModel($objectManager, $resultBuilder, $actionClass, $componentClass, $actionComponentClass))
            ->and($actionManager->setComponentDataResolver($componentDataResolver))
            ->and($this->calling($componentDataResolver)->resolveComponentData = function () {
                return new ResolvedComponentData('user', '1');
            })
            ->when($result = $actionManager->findOrCreateComponent('user', 1))
            ->mock($componentDataResolver)->call('resolveComponentData')->withArguments('user', 1)->exactly(1)
            ->mock($queryBuilder)->call('where')->withArguments('c.model = :model')->exactly(1)
            ->mock($queryBuilder)->call('andWhere')->withArguments('c.identifier = :identifier')->exactly(1)
            ->mock($queryBuilder)->call('setParameter')->withArguments('model', $resolvedComponentData->getModel())->exactly(1)
            ->mock($queryBuilder)->call('setParameter')->withArguments('identifier', serialize($resolvedComponentData->getIdentifier()))->exactly(1)
            ->object($result)->isEqualTo($component)
        ;
    }
}
