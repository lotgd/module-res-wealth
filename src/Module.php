<?php
declare(strict_types=1);

namespace LotGD\Module\Res\Wealth;

use Exception;
use LotGD\Core\Events\EventContext;
use LotGD\Core\Exceptions\CharacterStatGroupExistsException;
use LotGD\Core\Game;
use LotGD\Core\Models\Character;
use LotGD\Core\Models\CharacterStatGroup;
use LotGD\Core\Models\CharacterStats;
use LotGD\Core\Models\CharacterStats\BaseCharacterStat;
use LotGD\Core\Module as ModuleInterface;
use LotGD\Core\Models\Module as ModuleModel;
use Symfony\Component\Console\Command\Command;

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
            "h/lotgd/core/cli/character-config-list" => self::handleCharacterConfigListEvent($g, $context),
            "h/lotgd/core/cli/character-config-set" => self::handleCharacterConfigSetEvent($g, $context),
            "h/lotgd/core/cli/character-config-reset" => self::handleCharacterConfigResetEvent($g, $context),
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

    protected static function handleCharacterConfigListEvent(Game $g, EventContext $context): EventContext
    {
        /** @var Character $character */
        $character = $context->getDataField("character");

        # Get existing settings
        $settings = $context->getDataField("settings");

        $settings = [
            ...$settings, [
                Module::CharacterPropertyGold,
                $character->getProperty(Module::CharacterPropertyGold, 0),
                "Current amount of gold"
            ], [
                Module::CharacterPropertyGems,
                $character->getProperty(Module::CharacterPropertyGems, 0),
                "Current amount of gems"
            ]
        ];

        # Set settings
        $context->setDataField("settings", $settings);

        # Return
        return $context;
    }

    protected static function handleCharacterConfigSetEvent(Game $g, EventContext $context): EventContext
    {
        $setting = $context->getDataField("setting");

        if ($setting === self::CharacterPropertyGold) {
            try {
                $value = intval($context->getDataField("value"));
                $context->getDataField("character")->setGold($value);
                $context->setDataField("return", Command::SUCCESS);
                $context->getDataField("io")->success("Character gold set to {$value}.");
                $g->getLogger()->info("CLI event sets {$setting} to {$value}");
            } catch (Exception $e) {
                $context->setDataField("reason", $e->getMessage());
            }
        } elseif ($setting === self::CharacterPropertyGems) {
            try {
                $value = intval($context->getDataField("value"));
                $context->getDataField("character")->setGems($value);
                $context->setDataField("return", Command::SUCCESS);
                $context->getDataField("io")->success("Character gems set to {$value}.");
                $g->getLogger()->info("CLI event sets {$setting} to {$value}");
            } catch (Exception $e) {
                $context->setDataField("reason", $e->getMessage());
            }
        }

        # Return
        return $context;
    }

    protected static function handleCharacterConfigResetEvent(Game $g, EventContext $context): EventContext
    {
        $setting = $context->getDataField("setting");

        if ($setting === self::CharacterPropertyGold) {
            try {
                $context->getDataField("character")->setGold(0);
                $context->setDataField("return", Command::SUCCESS);
                $context->getDataField("io")->success("Character gold set back to 0.");
                $g->getLogger()->info("CLI event sets {$setting} to 0.");
            } catch (Exception $e) {
                $context->setDataField("reason", $e->getMessage());
            }
        } elseif ($setting === self::CharacterPropertyGems) {
            try {
                $context->getDataField("character")->setGems(0);
                $context->setDataField("return", Command::SUCCESS);
                $context->getDataField("io")->success("Character gems set back to 0.");
                $g->getLogger()->info("CLI event sets {$setting} to 0.");
            } catch (Exception $e) {
                $context->setDataField("reason", $e->getMessage());
            }
        }

        # Return
        return $context;
    }

    public static function onRegister(Game $g, ModuleModel $module) { }
    public static function onUnregister(Game $g, ModuleModel $module) { }
}
