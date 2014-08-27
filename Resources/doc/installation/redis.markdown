Redis Driver
==========

At this moment, this driver works with [SncRedisBundle](https://github.com/snc/SncRedisBundle), please install it if you want to use this driver.

/!\ WARNING /!\

Redis driver have to work with immediate delivery.

# 1) Define driver section on configuration

```yml
#config.yml
spy_timeline:
    drivers:
        redis:
            client:           ~ # snc_redis.default
            pipeline:         true
            prefix:           vlr_timeline
            classes:
                action:           'Spy\Timeline\Model\Action'
                component:        'Spy\Timeline\Model\Component'
                action_component: 'Spy\Timeline\Model\ActionComponent'
```

That's all

[index](https://github.com/stephpy/timeline-bundle/blob/master/Resources/doc/index.markdown)
