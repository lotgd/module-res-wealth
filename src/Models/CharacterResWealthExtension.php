<?php
declare(strict_types=1);

namespace LotGD\Modules\Res\Wealth\Models;

use LotGD\Core\Doctrine\Annotations\Extension;
use LotGD\Core\Doctrine\Annotations\ExtensionMethod;
use LotGD\Core\Events\CharacterEventData;
use LotGD\Core\Events\EventContextData;
use LotGD\Core\Models\Character;
use LotGD\Modules\Res\Wealth\Module;

/**
 * API extension helpers for the character model
 * @Extension(of="LotGD\Core\Models\Character")
 */
class CharacterResWealthExtension
{
    /**
     * Returns the amount of gold a character owns.
     * @param Character $character
     * @return int
     * @ExtensionMethod(as="getGold")
     */
    public static function getGoldOfCharacter(Character $character): int
    {
        return (int)$character->getProperty(Module::CharacterPropertyGold, 0);
    }

    /**
     * Sets the amount of gold a character owns.
     * @param Character $character
     * @param int $amount
     * @ExtensionMethod(as="setGold")
     */
    public static function setGoldForCharacter(Character $character, int $amount): void
    {
        $character->setProperty(Module::CharacterPropertyGold, $amount);

        // Log
        $character->getGame()->getLogger()->info("Set gold of {$character} to {$amount}");
    }

    /**
     * Increases the amount of gold a character owns.
     *
     * This method offers a hook to globally or on a character basis change the amount of gold that is earned. This
     * could be used to offer difficulty levels. Only increment is affected. Setting a set amount should not have any
     * side effects.
     *
     * The module also returns the actual amount after modification. This can help to adjust text reporting on the
     * amount of gold earned.
     * @param Character $character
     * @param int $amount
     * @return int
     * @ExtensionMethod(as="addGold")
     */
    public static function incrementGoldForCharacter(Character $character, int $amount): int
    {
        // Add in a hook to globally punish (or artificially increase/decrease difficulty) the gold gain.
        // Only increment is affected. Setting an amount should not have any side effects.
        $currentAmount = self::getGoldOfCharacter($character);

        // Call event
        $contextData = $character->getGame()->getEventManager()->publish(
            Module::HookIncrementGold,
            EventContextData::create(["character" => $character, "current" => $currentAmount, "amount" => $amount])
        );

        // Overwrite the amount
        $amount = $contextData->get("amount");

        // Log
        $character->getGame()->getLogger()->info("Increase gold of {$character} by {$amount}");

        // Save
        $character->setProperty(Module::CharacterPropertyGold, $currentAmount + $amount);

        // Return the actual amount
        return $amount;
    }

    /**
     * Returns the amount of gems a character owns.
     * @param Character $character
     * @return int
     * @ExtensionMethod(as="getGems")
     */
    public static function getGemsOfCharacter(Character $character): int
    {
        return (int)$character->getProperty(Module::CharacterPropertyGems, 0);
    }

    /**
     * Sets the amount of gems a character owns.
     * @param Character $character
     * @param int $amount
     * @ExtensionMethod(as="setGems")
     */
    public static function setGemsForCharacter(Character $character, int $amount): void
    {
        $character->setProperty(Module::CharacterPropertyGems, $amount);

        // Log
        $character->getGame()->getLogger()->info("Set gems of {$character} to {$amount}");
    }

    /**
     * Increases the amount of gems a character owns.
     *
     * @see self::incrementGoldForCharacter
     * @param Character $character
     * @param int $amount, use a negative number for decreasing.
     * @return int
     * @ExtensionMethod(as="addGems")
     */
    public static function incrementGemsForCharacter(Character $character, int $amount = 1): int
    {
        // Add in a hook to globally punish (or artificially increase/decrease difficulty) the gold gain.
        // Only increment is affected. Setting an amount should not have any side effects.
        $currentAmount = self::getGemsOfCharacter($character);

        // Call event
        $contextData = $character->getGame()->getEventManager()->publish(
            Module::HookIncrementGems,
            EventContextData::create(["character" => $character, "current" => $currentAmount, "amount" => $amount])
        );

        // Overwrite the amount
        $amount = $contextData->get("amount");

        // Log
        $character->getGame()->getLogger()->info("Increase gems of {$character} by {$amount}");

        // Save
        self::setGemsForCharacter($character, $currentAmount + $amount);

        // Return the actual amount
        return $amount;
    }
}