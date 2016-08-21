<?php
declare(strict_types=1);

namespace LotGD\Modules\SimpleWealth;

use LotGD\Core\Game;
use LotGD\Core\Module as ModuleInterface;
use LotGD\Core\Models\Module as ModuleModel;

class Module implements ModuleInterface {
    const GoldProperty = 'lotgd/module-simple-wealth/gold';
    const GemsProperty = 'lotgd/module-simple-wealth/gems';

    private $g;

    public static function handleEvent(Game $g, string $event, array $context) { }
    public static function onRegister(Game $g, ModuleModel $module) { }
    public static function onUnregister(Game $g, ModuleModel $module) { }

    public function __construct(Game $g)
    {
        $this->g = $g;
    }

    public function getGoldForUser(User $user)
    {
        return $user->getProperty(self::GoldProperty, 0);
    }

    public function setGoldForUser(User $user, int $gold)
    {
        return $user->setProperty(self::GoldProperty, $gold);
    }

    public function getGemsForUser(User $user)
    {
        return $user->getProperty(self::GemsProperty, 0);
    }

    public function setGemsForUser(User $user, int $gems)
    {
        return $user->setProperty(self::GemsProperty, $gems);
    }
}
