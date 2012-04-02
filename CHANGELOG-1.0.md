CHANGELOG for 1.0.X
===================

# 1.0.0:

- fix typo Dupplicate by Duplicate (ee557740725c928ccf18124e9a2ae3ed1dfa3f61)
- Replace InterfaceXXX by XXXInterface (80eacba0f9e4f7ece28020836e4cfd3d0027240b)
- camelCase on arguments/properties (fb5a2540d5482e6733a6ae40cbde6eda4656f2e5)
- Be able to override the Datahydrator query for each models, use getTimelineResultsForOIds
- Pipelining is available for RedisProvider (ed9da0866a7e55ba3dc3b78f64cac1b0dd9daae7)

# 1.0.1:

- You can define a db_driver on your configuration, or override TimelineActionManager (314af222148e760c73cac75f49ea64279ef5b00d)
- Deleting Entity retriever, TimelineActionManager has to do this ... (8794ca52c7442e983a9ee2e9686b1b20e8d237c2)
- Option pipeline is overrideable, %highco.timeline.provider.redis.pipeline%, then, spreadtime can be change TimelineAction::getSpreadTime(); (5aeca75ab5f8bf0a5b6c9a961d2541b07bd48de6)


# 1.1.0:

- Notifiers are available ! ( UnreadNotification actually available)
