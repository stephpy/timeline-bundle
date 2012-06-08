CHANGELOG for 1.X
===================

[BC Break] prefix the message if it has to ...

# 1.0.0:

- [BC Break] fix typo Dupplicate by Duplicate (ee557740725c928ccf18124e9a2ae3ed1dfa3f61)
- [BC Break] Replace InterfaceXXX by XXXInterface (80eacba0f9e4f7ece28020836e4cfd3d0027240b)
- [BC Break] camelCase on arguments/properties (fb5a2540d5482e6733a6ae40cbde6eda4656f2e5)
- Be able to override the Datahydrator query for each models, use getTimelineResultsForOIds
- Pipelining is available for RedisProvider (ed9da0866a7e55ba3dc3b78f64cac1b0dd9daae7)

# 1.0.1:

- You can define a db_driver on your configuration, or override TimelineActionManager (314af222148e760c73cac75f49ea64279ef5b00d)
- Deleting Entity retriever, TimelineActionManager has to do this ... (8794ca52c7442e983a9ee2e9686b1b20e8d237c2)
- Option pipeline is overrideable, %highco.timeline.provider.redis.pipeline%, then, spreadtime can be change TimelineAction::getSpreadTime(); (5aeca75ab5f8bf0a5b6c9a961d2541b07bd48de6)


# 1.1.0:

- Notifiers are available ! ( UnreadNotification actually available)
- [BC Break] [UnreadNotification]Change "removeUnreadNotification" and "removeUnreadNotifications" by "markAsReadTimelineAction" and "markAsReadTimelineActions"
- [UnreadNotification] Can mark all as read
- [Provider] Can remove all data from a key

# 1.2.0:
- [BC BREAK] Deleting puller and pusher, because they are not useful, only use Manager now, **Manager migrate from Timeline\Manager\Manager to Timeline\Manager** !
- DataHydrator filter use db_driver to get TimelineResults, `getTimelineResultsForModelAndOids` was deleted from TimelineActionManagerInterface
- Collection of TimelineAction moved to Model dir
- [BC BREAK] Move all dirs from Timeline to /
- Create an interface for TimelineAction.
- Thanks to snc works, TimelineBundle accepts now phpredis as provider. You just have to define your connection with phpredis on snc_redis.
- [BC] $timelineAction = new TimelineAction(); $timelineAction->create('...'); become $timelineAction = TimelineAction::create('....');
- `redis` db_driver added

# 1.3.0:

- [BC] Define entity as superclass, user has now to create override entity to be able to use `orm` db_driver
- [BC] Be able to pass options to filters, definition of configuration changed.
- Move Compilers to DependencyInjection dir
- Define filters service on a compiler pass.
