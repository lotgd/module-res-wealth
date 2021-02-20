<?php
declare(strict_types=1);

namespace LotGD\Module\Res\Wealth\Tests;

use LotGD\Core\Events\EventContext;
use LotGD\Core\Models\Character;
use LotGD\Module\Res\Wealth\Module;
use LotGD\Module\Res\Wealth\Tests\EventHandlers\AnyEventHandler;

class CharacterModelExtensionTest extends ModuleTest
{
    public function testIfGoldAmountsAreSetAndGotten()
    {
        /** @var Character $character */
        $character = $this->g->getEntityManager()->getRepository(Character::class)
            ->find("10000000-0000-0000-0000-000000000001");

        // Assert that get returns 0 first
        $this->assertSame(0, $character->getGold());

        // Set amount of gold
        $character->setGold(100);

        // Assert of property got set
        $this->assertSame(100, $character->getProperty(Module::CharacterPropertyGold));

        // Assert that get returns the same
        $this->assertSame(100, $character->getGold());
    }

    public function testIfGemAmountsAreSetAndGotten()
    {
        /** @var Character $character */
        $character = $this->g->getEntityManager()->getRepository(Character::class)
            ->find("10000000-0000-0000-0000-000000000001");

        // Assert that get returns 0 first
        $this->assertSame(0, $character->getGems());

        // Set amount of gold
        $character->setGems(3);

        // Assert of property got set
        $this->assertSame(3, $character->getProperty(Module::CharacterPropertyGems));

        // Assert that get returns the same
        $this->assertSame(3, $character->getGems());
    }

    public function testIfGoldAmountGetsIncrementedAndHookGetsPublished()
    {
        AnyEventHandler::initCounter();

        /** @var Character $character */
        $character = $this->g->getEntityManager()->getRepository(Character::class)
            ->find("10000000-0000-0000-0000-000000000002");

        // Assert that get returns 0 first
        $this->assertSame(0, $character->getGold());

        // Set amount of gold
        $character->addGold(100);

        // Assert of property got set
        $this->assertSame(100, $character->getProperty(Module::CharacterPropertyGold));

        // Assert that get returns the same
        $this->assertSame(100, $character->getGold());

        // Assert that event got raised
        $this->assertSame(1, AnyEventHandler::$events["h/lotgd/module-res-wealth/gold/increment"]);
    }

    public function testIfGoldAmountIncreasedGetsChangedUponEvent()
    {
        AnyEventHandler::initCounter();
        AnyEventHandler::addCallback(Module::HookIncrementGold, function (EventContext $context) {
            $context->setDataField("amount", 33);
            return $context;
        });

        /** @var Character $character */
        $character = $this->g->getEntityManager()->getRepository(Character::class)
            ->find("10000000-0000-0000-0000-000000000003");

        // Assert that get returns 0 first
        $this->assertSame(0, $character->getGold());

        // Set amount of gold
        $gold = $character->addGold(100);

        // Assert that get returns the same
        $this->assertSame(33, $gold);
        $this->assertSame(33, $character->getGold());
    }

    public function testIfGemAmountGetsIncrementedAndHookGetsPublished()
    {
        AnyEventHandler::initCounter();

        /** @var Character $character */
        $character = $this->g->getEntityManager()->getRepository(Character::class)
            ->find("10000000-0000-0000-0000-000000000002");

        // Assert that get returns 0 first
        $this->assertSame(0, $character->getGems());

        // Set amount of gold
        $character->addGems(2);

        // Assert of property got set
        $this->assertSame(2, $character->getProperty(Module::CharacterPropertyGems));

        // Assert that get returns the same
        $this->assertSame(2, $character->getGems());

        // Assert that event got raised
        $this->assertSame(1, AnyEventHandler::$events["h/lotgd/module-res-wealth/gems/increment"]);
    }

    public function testIfGemAmountIncreasedGetsChangedUponEvent()
    {
        AnyEventHandler::initCounter();
        AnyEventHandler::addCallback(Module::HookIncrementGems, function (EventContext $context) {
            $context->setDataField("amount", 15);
            return $context;
        });

        /** @var Character $character */
        $character = $this->g->getEntityManager()->getRepository(Character::class)
            ->find("10000000-0000-0000-0000-000000000003");

        // Assert that get returns 0 first
        $this->assertSame(0, $character->getGems());

        // Set amount of gold
        $gems = $character->addGems(15);

        // Assert that get returns the same
        $this->assertSame(15, $gems);
        $this->assertSame(15, $character->getGems());
    }
}