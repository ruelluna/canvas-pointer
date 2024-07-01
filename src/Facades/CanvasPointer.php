<?php

namespace RuelLuna\CanvasPointer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RuelLuna\CanvasPointer\CanvasPointer
 */
class CanvasPointer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \RuelLuna\CanvasPointer\CanvasPointer::class;
    }
}
