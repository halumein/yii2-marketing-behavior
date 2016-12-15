<?php

namespace halumein\marketing;

use Yii;
use yii\base\Behavior;

/**
 * Class ManyToManyBehavior
 * @package voskobovich\manytomany
 *
 * See README.md for examples
 */
class TestBehavior extends Behavior
{
    /**
     * Events list
     * @return array
     */
    public function events()
    {
        return [
            // ActiveRecord::EVENT_AFTER_INSERT => 'saveRelations',
            // ActiveRecord::EVENT_AFTER_UPDATE => 'saveRelations',
        ];
    }

    public function init()
    {
        parent::init();

    }

    public function test()
    {

        return "ok";


    }


}
