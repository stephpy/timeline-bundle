QueryBuilder
============

*This feature is at this moment only available for `ORM` driver.*

You can create a query_builder to fetch timeline actions like you can do with Doctrine ORM QueryBuilder.

## Api of query Builder

```php
$qb = $this->get('spy_timeline.query_builder');

// filter on timeline subject(s)
$qb->addSubject($subject); // accept a ComponentInterface
$qb->setPage(1);
$qb->setMaxPerPage(10);
$qb->orderBy($fieldName, 'ASC'); // or DESC
$qb->groupByAction(); // true or false on first argument. default: true
$qb->toArray();
$qb->fromArray($data);

// add filters
$qb->setCriterias($criterias); // see explanation below

```

## Transform to array

You want to store the query **or** give it to a webservice (QueryBuilder is provided on `stephpy/timeline` standalone library) ?

```php
$qb = $this->get('spy_timeline.query_builder');
//...

$data = $qb->toArray();
$qb   = $qb->fromArray($data);
```

## Criterias

Criterias will allow you to filter actions on theses fields:

- context
- createdAt: Date of timeline propagation
- verb
- type: Type of component (on ActionComponent table), see examples below
- text: Text on ActionComponent
- model
- identifier


### Create criterias

*Example 1) Fetch actions where something kick something.*

```php
$criterias = $qb->field('verb')->equals('kick');
```

*Example 2) Fetch actions where Chuck Norris kicks something.*

```php
$criterias = $qb->logicalAnd(
	$qb->field('model')->equals('User'),
	$qb->field('identifier')->equals('ChuckNorris'),
	$qb->field('verb')->equals('kick')
)
// (component.model = User AND component.identifier = ChuckNorris and actionComponent.verb = kick)
```

*Example 3) Fetch actions where Chuck Norris is with Bruce Lee.*

```php
$criterias = $qb->logicalAnd(
	$qb->field('model')->equals('User'),
	$qb->field('identifier')->equals('ChuckNorris'),
	$qb->field('model')->equals('User'),
	$qb->field('identifier')->equals('BruceLee'),
)
// but prefer (for lisibility)
$criterias = $qb->logicalAnd(
	$qb->logicalAnd(
		$qb->field('model')->equals('User'),
		$qb->field('identifier')->equals('ChuckNorris')
	),
	$qb->logicalAnd(
		$qb->field('model')->equals('User'),
		$qb->field('identifier')->equals('BruceLee')
	)
)
```

*Example 4) Actions where Chuck Norris or Bruce Lee kick something.*

```php
$criterias = $qb->logicalAnd(
	$qb->logicalOr(
		$qb->logicalAnd(
			$qb->field('model')->equals('User'),
			$qb->field('identifier')->equals('ChuckNorris')
		),
		$qb->logicalAnd(
			$qb->field('model')->equals('User'),
			$qb->field('identifier')->equals('BruceLee')
		)
	),
	$qb->field('verb', 'kick')
)
```

You can asking for each field listed above.

Fields methods:

```php
$value = 'foo'; // you can provide a DateTime,
// for identifier, do not send a serialized data

$qb->field('createdAt')->equals($value);
$qb->field('createdAt')->notEquals($value);
$qb->field('createdAt')->in(array($value));
$qb->field('createdAt')->notIn(array($value));
$qb->field('createdAt')->like('%'.$value);
$qb->field('createdAt')->notLike($value.'%');
$qb->field('createdAt')->lt($value); // lower than
$qb->field('createdAt')->lte($value); // lower than equals
$qb->field('createdAt')->gt($value); // greater than
$qb->field('createdAt')->gte($value); // greater than equals
```

## Fetch results

```php

$qb = $this->get('spy_timeline.query_builder');
$qb->setCriterias('....');

$results = $qb->execute(array('paginate' => true, 'filter' => true));
```
