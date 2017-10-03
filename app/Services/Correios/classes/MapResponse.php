<?php

namespace App\Services\Correios\classes;


class MapResponse
{
    function toCollection()
    {
        return collect($this->toArray());
    }

    public function toArray(){
        return json_decode(json_encode($this), true);
    }

    public function __toString()
    {
        return json_encode($this, true);
    }
}