<?php

namespace halumein\marketing;

use yii;
use yii\base\Behavior;

class MarketingBehavior extends Behavior
{
    public $freeProduct = null;
    public $targetModel = null;
    public $presentCostAmount = null;

    public function events()
    {
        return [
            'cart_update' => 'onNCostFreePresent',
        ];
    }

    public function init()
    {
        parent::init();

    }

    public function onNCostFreePresent($event)
    {
        // var_dump('<=====================>');

        $cartElements = $event->cart;

        $cartCount = $event->count;
        $cartCost = $event->cost;

        $freeElement = false;

        foreach ($cartElements as $key => $element) {
            if ($element->model == $this->freeProduct['model'] && $element->item_id == $this->freeProduct['id']) {
                $freeElement = $element;
                break;
            }
        }

        if ($this->presentCostAmount != null && $cartCost > $this->presentCostAmount && $freeElement === false) {
            $model = new $this->freeProduct['model'];
            yii::$app->cart->put($model::findOne($this->freeProduct['id']));
        } elseif ($this->presentCostAmount != null && $cartCost < $this->presentCostAmount && $freeElement !== false) {
            yii::$app->cart->deleteElement($freeElement);
        }

    }


}
