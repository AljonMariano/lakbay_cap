<?php
class Miryenda{
    public $bread_list = ['Pandesal' => 3, 'Mamon' => 10, 'Cheese Bread' => 16];
    public $drinks_list = array('Water' => 12, 'Softdrinks' => 18, 'Coffee' => 20);

    public function calculateCost($price, $quantity){
        return $price * $quantity;
    }

    public function calculateTotal($totals){
        $total = 0;
        foreach($totals as $item){
            $total += $item;
        }
        return $total;
    }

    function getPrice($field, $array){
        return $this->{$array}[$field];
    }
}
?>