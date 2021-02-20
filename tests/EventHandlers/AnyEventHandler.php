<?php
declare(strict_types=1);

namespace LotGD\Module\Res\Wealth\Tests\EventHandlers;

use LotGD\Core\EventHandler;
use LotGD\Core\Events\EventContext;
use LotGD\Core\Game;

class AnyEventHandler implements EventHandler
{
    public static $events = [];
    public static $callbacks = [];

    public static function initCounter(): void
    {
        self::$events = [];
        self::$callbacks = [];
    }

    public static function addCallback($event, $callback)
    {
        self::$callbacks[$event] = $callback;
    }

    public static function handleEvent(Game $g, EventContext $context): EventContext
    {
        $event = $context->getEvent();

        if (!isset(self::$events[$event])) {
            self::$events[$event] = 0;
        }

        self::$events[$event]++;

        if (isset(self::$callbacks[$event])) {
            $context = self::$callbacks[$event]($context);
        }

        return $context;
    }
}