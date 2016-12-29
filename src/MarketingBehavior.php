<?php

namespace halumein\marketing;

use yii;
use yii\base\Behavior;

class MarketingBehavior extends Behavior
{
    public $targetModel = null; // на что распростарняется поведение
    public $freeProduct = null; // подарочный товар (['model' => '...', 'id' => '...'])
    public $giftCostNeed = null; // сумма при покупке на которую выдаётся подарок
    public $promoAction = null;
    public $conditionProducts = null; // массив товаров для условия ([ ['model' => '...', 'id' => ...], ... ])

    public function events()
    {
        return [
            'cart_update' => $this->promoAction,
        ];
    }

    public function init()
    {
        parent::init();
    }

    /*
    *   Акция "при определённой стоимости корзины что-то в подарок"
    */

    public function onNCostFreeGift($event)
    {

        $cartCount = $event->count;
        $cartCost = $event->cost;

        $freeElement = $this->checkProductInCart($event->cart, $this->freeProduct['model'], $this->freeProduct['id']);

        if ($this->giftCostNeed != null && $cartCost > $this->giftCostNeed && $freeElement === false) {
            // добавляем в корзину акциооный товар при выполнении условий акции
            $model = new $this->freeProduct['model'];
            yii::$app->cart->put($model::findOne($this->freeProduct['id']));
        } elseif ($this->giftCostNeed != null && $cartCost < $this->giftCostNeed && $freeElement !== false) {
            // если в корзине акционный товар есть - удаляем из корзины если условия акции не выполнены
            yii::$app->cart->deleteElement($freeElement);
        }

    }

    /*
    *   Акция "если в корзине есть перечисленные товары - что-то в подарок"
    */

    public function onNPlusNFreeGift($event)
    {
        $cartCount = $event->count;
        $cartCost = $event->cost;

        $freeElement = $this->checkProductInCart($event->cart, $this->freeProduct['model'], $this->freeProduct['id']);

        $condition = 0; // условие - количество определённых позиций в корзине должно быть равно условию. изначально не равно
        if ($this->conditionProducts) {
            foreach ($this->conditionProducts as $key => $conditionProduct) {
                if ($this->checkProductInCart($event->cart, $conditionProduct['model'], $conditionProduct['id'])) {
                    $condition++;
                }
            }
            if ($condition === count($this->conditionProducts) && $freeElement === false) {
                // добавляем в корзину акциооный товар при выполнении условий акции
                $model = new $this->freeProduct['model'];
                yii::$app->cart->put($model::findOne($this->freeProduct['id']));
            } elseif ($condition !== count($this->conditionProducts) && $freeElement !== false) {
                // если в корзине акционный товар есть - удаляем из корзины если условия акции не выполнены
                yii::$app->cart->deleteElement($freeElement);
            }
        }
    }

    /*
    *   Акция "если в корзине есть определённое количество указанного товара - что-то в подарок"
    */

    public function onAmountNFreeGift($event)
    {
        $cartCount = $event->count;
        $cartCost = $event->cost;

        $freeElement = $this->checkProductInCart($event->cart, $this->freeProduct['model'], $this->freeProduct['id']);

        if ($this->conditionProducts) {
            foreach ($this->conditionProducts as $key => $conditionProduct) {
                if ($element = $this->checkProductInCart($event->cart, $conditionProduct['model'], $conditionProduct['id'])) {
                    if ($element->count >= $conditionProduct['count'] && $freeElement === false) {
                        // добавляем в корзину акциооный товар при выполнении условий акции
                        $model = new $this->freeProduct['model'];
                        yii::$app->cart->put($model::findOne($this->freeProduct['id']));
                    } elseif ($element->count < $conditionProduct['count'] && $freeElement !== false) {
                        // если в корзине акционный товар есть - удаляем из корзины если условия акции не выполнены
                        yii::$app->cart->deleteElement($freeElement);
                    } elseif ($element->count >= $conditionProduct['count'] && $freeElement !== false) {
                        // если
                        if ((integer)($element->count/$conditionProduct['count'] !== $element->count)) {
                            //
                            $count = (integer)($element->count/$conditionProduct['count']);
                            $freeElement->count = $count;
                            $freeElement->update();

                        }
                    }
                }
            }

        }
    }


    /*
    *   Проверяет есть ли продукт в корзине
    *   @property array $cartElements - корзина
    *   @property string $productModel - модель
    *   @property int $productId - ид
    *   @return - bool/object
    */

    private function checkProductInCart($cartElements, $productModel, $productId)
    {
        $freeElement = false; // акционного товара в корзине нет

        // TODO привязаться на $this->targetModel
        foreach ($cartElements as $key => $element) {
            if ($element->model == $productModel && $element->item_id == (int)$productId) {
                $freeElement = $element; // акционный товар в корзине есть
                break;
            }
        }

        return $freeElement;
    }



}
