<?php
declare(strict_types=1);


namespace LotGD\Module\Res\Wealth\Tests;

use LotGD\Core\Console\Command\Character\CharacterConfigListCommand;
use LotGD\Core\Console\Command\Character\CharacterConfigResetCommand;
use LotGD\Core\Console\Command\Character\CharacterConfigSetCommand;
use LotGD\Core\Models\Character;
use LotGD\Core\Models\CharacterProperty;
use LotGD\Module\Res\Wealth\Module;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class CommandEventTest extends ModuleTest
{
    public function testIfCharacterSettingGetsAddedToListSettingCommand()
    {
        /** @var Character $character */
        $character = $this->g->getEntityManager()->getRepository(Character::class)
            ->find("10000000-0000-0000-0000-000000000001");
        $this->g->setCharacter($character);
        $character->setGold(1024);
        $character->setGems(1337);

        $command = new CommandTester(new CharacterConfigListCommand($this->g));
        $command->execute(["id" => $character->getId()->toString()]);
        $output = $command->getDisplay();

        $this->assertStringContainsString(Module::CharacterPropertyGold, $output);
        $this->assertStringContainsString("1024", $output);
        $this->assertStringContainsString(Module::CharacterPropertyGems, $output);
        $this->assertStringContainsString("1337", $output);
    }

    public function testIfCharacterConfigSetCommandSetsGoldAmountCorrectly()
    {
        /** @var Character $character */
        $character = $this->g->getEntityManager()->getRepository(Character::class)
            ->find("10000000-0000-0000-0000-000000000001");
        $this->g->setCharacter($character);

        $command = new CommandTester(new CharacterConfigSetCommand($this->g));
        $command->execute([
            "id" => $character->getId()->toString(),
            "setting" => Module::CharacterPropertyGold,
            "value" => 1231,
        ]);
        $output = $command->getDisplay();

        $this->assertSame(1231, $character->getGold());
        $this->assertStringContainsString("Character gold set to 1231.", $output);
        $this->assertSame(Command::SUCCESS, $command->getStatusCode());
    }

    public function testIfCharacterConfigSetCommandSetsGemsAmountCorrectly()
    {
        /** @var Character $character */
        $character = $this->g->getEntityManager()->getRepository(Character::class)
            ->find("10000000-0000-0000-0000-000000000001");
        $this->g->setCharacter($character);

        $command = new CommandTester(new CharacterConfigSetCommand($this->g));
        $command->execute([
            "id" => $character->getId()->toString(),
            "setting" => Module::CharacterPropertyGems,
            "value" => 13,
        ]);
        $output = $command->getDisplay();

        $this->assertSame(13, $character->getGems());
        $this->assertStringContainsString("Character gems set to 13.", $output);
        $this->assertSame(Command::SUCCESS, $command->getStatusCode());
    }

    public function testIfCharacterConfigResetCommandResetsGoldAmountCorrectly()
    {
        /** @var Character $character */
        $character = $this->g->getEntityManager()->getRepository(Character::class)
            ->find("10000000-0000-0000-0000-000000000001");
        $this->g->setCharacter($character);
        $character->setGold(1024);

        $command = new CommandTester(new CharacterConfigResetCommand($this->g));
        $command->execute([
            "id" => $character->getId()->toString(),
            "setting" => Module::CharacterPropertyGold,
        ]);
        $output = $command->getDisplay();

        $this->assertSame(0, $character->getGold());
        $this->assertStringContainsString("Character gold set back to 0.", $output);
        $this->assertSame(Command::SUCCESS, $command->getStatusCode());
    }

    public function testIfCharacterConfigResetCommandResetsGemsAmountCorrectly()
    {
        /** @var Character $character */
        $character = $this->g->getEntityManager()->getRepository(Character::class)
            ->find("10000000-0000-0000-0000-000000000001");
        $this->g->setCharacter($character);
        $character->setGems(1312312);

        $command = new CommandTester(new CharacterConfigResetCommand($this->g));
        $command->execute([
            "id" => $character->getId()->toString(),
            "setting" => Module::CharacterPropertyGems,
        ]);
        $output = $command->getDisplay();

        $this->assertStringContainsString("Character gems set back to 0.", $output);
        $this->assertSame(0, $character->getGems());
        $this->assertSame(Command::SUCCESS, $command->getStatusCode());
    }
}