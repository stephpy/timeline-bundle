<?php

namespace Highco\TimelineBundle\Timeline\Token;

use Symfony\Component\HttpFoundation\Request;

class Timeline
{
    public $subject_model;
    public $subject_id;
    public $verb;
    public $direct_complement_model;
    public $direct_complement_id;
    public $indirect_complement_model;
    public $indirect_complement_id;

    /**
     * fromRequest
     *
     * @param Request $request
     * @access public
     * @return void
     */
    static public function fromRequest(Request $request)
    {
        if(is_null($request->get('subject_model')))
        {
            throw new \InvalidArgumentException('You have to define subject model on "'.__CLASS__.'"');
        }

        if(is_null($request->get('subject_id')))
        {
            throw new \InvalidArgumentException('You have to define subject id on "'.__CLASS__.'"');
        }

        $subject                            = new Timeline();
        $subject->subject_model             = $request->get('subject_model');
        $subject->subject_id                = $request->get('subject_id');
        $subject->verb                      = $request->get('verb');
        $subject->direct_complement_model   = $request->get('direct_complement_model');
        $subject->direct_complement_id      = $request->get('direct_complement_id');
        $subject->indirect_complement_model = $request->get('indirect_complement_model');
        $subject->indirect_complement_id    = $request->get('indirect_complement_id');

        return $subject;
    }

}

