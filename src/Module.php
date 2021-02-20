<?php
declare(strict_types=1);

namespace LotGD\Modules\Res\Wealth;

use LotGD\Core\Events\EventContext;
use LotGD\Core\Game;
use LotGD\Core\Models\Character;
use LotGD\Core\Module as ModuleInterface;
use LotGD\Core\Models\Module as ModuleModel;

class Module implements ModuleInterface {
    const CharacterPropertyGold = 'lotgd/module-res-wealth/gold';
    const CharacterPropertyGems = 'lotgd/module-res-wealth/gems';
    const HookIncrementGold = "h/lotgd/module-res-wealth/gold/increment";
    const HookIncrementGems = "h/lotgd/module-res-wealth/gems/increment";

    private $g;

    public static function handleEvent(Game $g, EventContext $context): EventContext
    {
        return $context;
    }

    public static function onRegister(Game $g, ModuleModel $module) { }
    public static function onUnregister(Game $g, ModuleModel $module) { }
}
