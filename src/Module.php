<?php
declare(strict_types=1);

namespace LotGD\Module\Res\Wealth;

use LotGD\Core\Events\EventContext;
use LotGD\Core\Exceptions\CharacterStatGroupExistsException;
use LotGD\Core\Game;
use LotGD\Core\Models\Character;
use LotGD\Core\Models\CharacterStatGroup;
use LotGD\Core\Models\CharacterStats;
use LotGD\Core\Models\CharacterStats\BaseCharacterStat;
use LotGD\Core\Module as ModuleInterface;
use LotGD\Core\Models\Module as ModuleModel;

class Module implements ModuleInterface {
    const CharacterPropertyGold = 'lotgd/module-res-wealth/gold';
    const CharacterPropertyGems = 'lotgd/module-res-wealth/gems';
    const HookIncrementGold = "h/lotgd/module-res-wealth/gold/increment";
    const HookIncrementGems = "h/lotgd/module-res-wealth/gems/increment";
    const CharacterStatGroup = "lotgd/res/wealth";
    const CharacterStatGold = "lotgd/res/wealth/gold";
    const CharacterStatGems = "lotgd/res/wealth/gems";

    public static function handleEvent(Game $g, EventContext $context): EventContext
    {
        $event = $context->getEvent();

        $context = match ($event) {
            "h/lotgd/core/characterStats/populate" => self::handleCharacterStatsPopulateEvent($g, $context),
            default => $context,
        };

        return $context;
    }

    protected static function handleCharacterStatsPopulateEvent(Game $g, EventContext $context): EventContext
    {
        /** @var CharacterStats $stats */
        $stats = $context->getDataField("stats");
        /** @var Character $character */
        $character = $context->getDataField("character");

        try {
            $group = new CharacterStatGroup(
                id: self::CharacterStatGroup,
                name: "Wealth", weight:
                20
            );

            $group->addCharacterStat(new BaseCharacterStat(
                id: self::CharacterStatGold,
                name: "Gold",
                value: $character->getGold(),
                weight: 0
            ));

            $group->addCharacterStat(new BaseCharacterStat(
                id: self::CharacterStatGems,
                name: "Gems",
                value: $character->getGems(),
                weight: 20
            ));

            $stats->addCharacterStatGroup($group);
        } catch (CharacterStatGroupExistsException) {
            $g->getLogger()->error("Character stat group already exists. Maybe there is a module conflict?");
        }

        return $context;
    }

    public static function onRegister(Game $g, ModuleModel $module) { }
    public static function onUnregister(Game $g, ModuleModel $module) { }
}
