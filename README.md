HighcoTimelineBundle
====================

**/!\ WARNING /!\ This version is not yet finished, wait to use it**

Build timeline easily.

# How it works ?

To have a timeline you have:

Subjet (subject_model, subject_id)
Verb (verb)
DirectComplement (direct_complement_model, direct_complement_id)
IndirectComplement (indirect_complement_model, indirect_complement_id)

    Chuck Norris Own the World with Vic Mc Key

Chuck Norris is SUBJECT
Own is the VERB
the World is the DIRECT COMPLEMENT
Vic Mc Key is the INDIRECT COMPLEMENT

Now, we have:

## Timelines

Timeline of a subject is all his actions

## Walls

Wall of a subject is all his actions + all actions of his **spreads**

## Context

If you have only one context, you'll get each actions without can easily filter them to return only "OWN" actions or have only actions of friends of ChuckNorris

That's why we have a "Global" context, and you can easily add other contexts.

# Spread System

Exemple, we add action

    Chuck Norris Own the World with Vic Mc Key

We want to publish it on Chuck Norris wall/timeline, but even in all users of the world. To do this, add a Spread and add this action on each Subject you want and on each **Context** you want.

Todo
----

- Make documentation of adding a spread
- Explain better the system of actions/walls/timelines/context/spread
- Add GLOBAL context, each actions of spreads are dupplicated on their GLOBAL context
- spread_to_me on **SpreadManager** should be configurable
- Can use Doctrine ODM, Propel, etc ...
- Can use an other one entity manager than default

Withlist
--------

- ** Separate in HighcoTimelineClientBundle and HighcoTimelineServerBundle, because you may want to use only client part (get timeline/wall) and set server part in an other one app **
