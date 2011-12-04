<?php

namespace Highco\TimelineBundle\Timeline\Spread\Entry;

class Entry
{
	public $subject_model;
	public $subject_id;

	public function getIdent()
	{
		return sprintf('%s:%s', $this->subject_model, $this->subject_id);
	}
}
