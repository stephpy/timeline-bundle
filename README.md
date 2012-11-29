SpyTimelineBundle 2.0
=====================

**[WIP] DO NOT USE THIS**

Roadmap before stable release:
==============================

- Twig
- Filters
- Notifiers
- Redis support
- Paginator #[37](https://github.com/stephpy/TimelineBundle/issues/37)
- Documentation
- Write tests with Atoum.
- DataHydrator #[33](https://github.com/stephpy/TimelineBundle/issues/33)
- MongoDB Support #[28](https://github.com/stephpy/TimelineBundle/issues/28)
- Propel support ?
- Review of filters and notifiers, could we use TreeBuilder on them to easily inject configuration.
- Any suggestion ?


Supports 2.* Symfony Framework.

[![Build Status](https://secure.travis-ci.org/stephpy/TimelineBundle.png)](http://travis-ci.org/stephpy/TimelineBundle)

Build timeline/wall for an entity easily.

There is too a notification system, you can easily know how many unread notifications you have, mark as read one/all, etc ... You can too add your notifier easily ...

[Read the Documentation](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/index.markdown)

Launch tests:

```
composer install
bin/atoum -d Tests/Units
```

---------------

# Wishlist

- Other providers ( contribute guys !)
- Propel/Doctrine ODM supports
