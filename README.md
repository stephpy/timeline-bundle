HighcoTimelineBundle
====================

**/!\ WARNING /!\ This version is not yet finished, wait to use it**

Build timeline easily.

# How it works ?

To have a timeline you have:

* Subjet (subject_model, subject_id)
* Verb (verb)
* DirectComplement (direct_complement_model, direct_complement_id)
* IndirectComplement (indirect_complement_model, indirect_complement_id)

    Chuck Norris Own the World with Vic Mc Key

* Chuck Norris is **SUBJECT**
* Own is the **VERB**
* the World is the **DIRECT COMPLEMENT**
* Vic Mc Key is the **INDIRECT COMPLEMENT**

Some definitions:

## Timelines

Timeline of a subject is all his actions

## Walls

Wall of a subject is all his actions + all actions of his **spreads**

## Context

Exemple:

ChuckNorris has 233 friends, and follow 20 companies

I we have one context, like facebook, his wall will return each actions of his friends and companies.

You can too use **Contexts** to filter timelines, for this exemple, we can have 3 contexts:

* GLOBAL: actions of his friends and companies
* FRIEND: actions of his friends
* COMPANIES: actions of his companies

You can define as many context that you want.
If you have only one context, you'll get each actions without can easily filter them to return only "OWN" actions or have only actions of friends of ChuckNorris

That's why we have a "Global" context, and you can easily add other contexts.

# Adding a timeline action

    $manager = $this->get('highco.timeline.manager');

    $entry = new TimelineAction();
    $entry->setSubjectModel('\Chuck');
    $entry->setSubjectId(1);
    $entry->setVerb('Own');
    $entry->setDirectComplementModel('\World');
    $entry->setDirectComplementId(1);
    $entry->setIndirectComplementModel('\VicMcKey');
    $entry->setIndirectComplementId(1);

    $manager = $this->get('highco.timeline.manager');
    $manager->push($entry);

# Pull Wall of Subject

    $manager = $this->get('highco.timeline.manager');
    $results = $manager->getWall('\Chuck', 1, 'GLOBAL'); //GLOBAL is the context wanted

# Pull Timeline of Subject

    $manager = $this->get('highco.timeline.manager');
    $results = $manager->getTimeline('\Chuck', 1);

# Spread System

Exemple, we add action

    Chuck Norris Own the World with Vic Mc Key

We want to publish it on:

* Chuck Norris wall
* Tom wall
* Bazinga Wall
* Francky Vincent Wall

When you publish a timeline action, you can choose spreads by defining Subject Model and Subject Id.

## Defining a Spread class

**@todo**

## Exemple of Spread class

**@todo**

# Filters

## Adding a filter

**@todo**

## Filter "Dupplicate Key"

**@todo**

## Filter "Data hydrator"

**@todo**

# Providers

## Adding a provider

**@todo**

## Provider "REDIS"

**@todo**

# Renderer

**@todo**



Todo
----

- Finish :documentation
- Add GLOBAL context, each actions of spreads are dupplicated on their GLOBAL context
- spread_to_me on **SpreadManager** should be configurable
- Can use an other one entity manager than default
- Filters should no be mandatory
- Add renderer

Withlist
--------

- Can use Doctrine ODM, Propel, etc ...
- ** Separate in HighcoTimelineClientBundle and HighcoTimelineServerBundle, because you may want to use only client part (get timeline/wall) and set server part in an other one app **
