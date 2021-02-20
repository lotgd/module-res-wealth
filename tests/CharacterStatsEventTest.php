<?php
declare(strict_types=1);

namespace LotGD\Module\Res\Wealth\Tests;

use LotGD\Core\Models\Character;
use LotGD\Core\Models\CharacterStats;
use LotGD\Module\Res\Wealth\Module;

class CharacterStatsEventTest extends ModuleTest
{
    public function testIfCharacterStatsArePopulatedUponPublishedEvent()
    {
        /** @var Character $character */
        $character = $this->g->getEntityManager()->getRepository(Character::class)
            ->find("10000000-0000-0000-0000-000000000001");
        $this->g->setCharacter($character);
        $character->setGold(100);
        $character->setGems(10);

        // Create character stats
        $stats = new CharacterStats($this->g, $character);

        // Assert group is inside
        $this->assertTrue($stats->hasCharacterStatGroup(Module::CharacterStatGroup));

        $group = $stats->getCharacterStatGroup(Module::CharacterStatGroup);

        // Assert group contains our stats
        $this->assertTrue($group->hasCharacterStat(Module::CharacterStatGold));
        $this->assertSame(100, $group->getCharacterStat(Module::CharacterStatGold)->getValue());

        $this->assertTrue($group->hasCharacterStat(Module::CharacterStatGems));
        $this->assertSame(10, $group->getCharacterStat(Module::CharacterStatGems)->getValue());
    }
}