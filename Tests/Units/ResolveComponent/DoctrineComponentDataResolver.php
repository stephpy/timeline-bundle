<?php

namespace Spy\TimelineBundle\Tests\Units\ResolveComponent;

use mageekguy\atoum;
use Spy\TimelineBundle\ResolveComponent\DoctrineComponentDataResolver as TestedModel;
use Spy\Timeline\ResolveComponent\TestHelper\User;
use Spy\Timeline\ResolveComponent\ValueObject\ResolveComponentModelIdentifier;

class DoctrineComponentDataResolver extends atoum\test
{
    public function testObjectManagedByDoctrine()
    {
        $object = new User(5);
        $resolve = new ResolveComponentModelIdentifier($object);

        $this->if($classMetadata = new \mock\Doctrine\Common\Persistence\Mapping\ClassMetadata())
            ->and($managerRegistry = new \mock\Doctrine\Common\Persistence\ManagerRegistry())
            ->and($this->mockGenerator->orphanize('__construct'))
            ->and($this->mockGenerator->shuntParentClassCalls())
            ->and($objectManager = new \mock\Doctrine\Common\Persistence\ObjectManager())
            ->and($this->calling($managerRegistry)->getManagerForClass = function () use ($objectManager) {
                return $objectManager;
            })
            ->and($this->calling($objectManager)->getClassMetadata = function () use ($classMetadata) {
                return $classMetadata;
            })
            ->and($this->calling($classMetadata)->getIdentifier = function () { return array('id');

            })
            ->and($this->calling($classMetadata)->getName = function () {
                return 'Spy\Timeline\ResolveComponent\TestHelper\User';

            })
            ->and($resolver = new TestedModel())
            ->and($resolver->addRegistry($managerRegistry))
            ->when($result = $resolver->resolveComponentData($resolve))
            ->then(
                $this->mock($managerRegistry)->call('getManagerForClass')->withArguments('Spy\Timeline\ResolveComponent\TestHelper\User')->exactly(1)
                ->and($this->mock($objectManager)->call('getClassMetadata')->withArguments('Spy\Timeline\ResolveComponent\TestHelper\User')->exactly(1))
                ->and($this->mock($classMetadata)->call('getIdentifier')->exactly(1))
                ->and($this->string($result->getIdentifier())->isEqualTo(5))
                ->and($this->string($result->getModel())->isEqualTo('Spy\Timeline\ResolveComponent\TestHelper\User'))
            )
            ;
    }

    public function testObjectNotManagedByDoctrine()
    {
        $object = new User(5);
        $resolve = new ResolveComponentModelIdentifier($object);

        $this->if($managerRegistry = new \mock\Doctrine\Common\Persistence\ManagerRegistry())
            ->and($resolver = new TestedModel())
            ->and($resolver->addRegistry($managerRegistry))
            ->when($result = $resolver->resolveComponentData($resolve))
            ->then(
                $this->mock($managerRegistry)->call('getManagerForClass')->withArguments('Spy\Timeline\ResolveComponent\TestHelper\User')->exactly(1)
                ->and($this->string($result->getIdentifier())->isEqualTo(5))
                ->and($this->string($result->getModel())->isEqualTo('Spy\Timeline\ResolveComponent\TestHelper\User'))
                )
        ;
    }

    public function testObjectNotManagedByDoctrineWithoutGetIdMethod()
    {
        $object = new \stdClass();
        $resolve = new ResolveComponentModelIdentifier($object);

        $this->if($managerRegistry = new \mock\Doctrine\Common\Persistence\ManagerRegistry())
            ->and($resolver = new TestedModel())
            ->and($resolver->addRegistry($managerRegistry))
            ->exception(function () use ($resolver, $resolve) {
                $resolver->resolveComponentData($resolve);
            })
            ->isInstanceOf('Spy\Timeline\Exception\ResolveComponentDataException')
            ->hasMessage('Model must have a getId method.')
            ;
    }

    public function testStringModelAndIdentifierGiven()
    {
        $model = 'foo';
        $identifier = array('foo' => 'bar');
        $resolve = new ResolveComponentModelIdentifier($model, $identifier);

        $this->if($managerRegistry = new \mock\Doctrine\Common\Persistence\ManagerRegistry())
            ->and($resolver = new TestedModel())
            ->and($resolver->addRegistry($managerRegistry))
            ->when($result = $resolver->resolveComponentData($resolve))
            ->then(
                $this->mock($managerRegistry)->call('getManagerForClass')->withArguments('Spy\Timeline\ResolveComponent\TestHelper\User')->exactly(0)
                ->and($this->array($result->getIdentifier())->isEqualTo($identifier))
                ->and($this->string($result->getModel())->isEqualTo($model))
            )
        ;
    }
}
