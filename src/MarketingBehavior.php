<?php

namespace halumein\marketing;

use yii;
use yii\base\Behavior;

class MarketingBehavior extends Behavior
{
    public function events()
    {
        return [
            'create' => 'promo',
        ];
    }

    public function init()
    {
        parent::init();

    }

    public function promo($event)
    {
        // $cartElements = yii::$app->cart->getElements();
        // var_dump($cartElements);
    }


}
