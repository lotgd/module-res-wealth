<?php
declare(strict_types=1);

namespace LotGD\Module\Res\Wealth\Tests;

use LotGD\Core\Events\EventContext;
use LotGD\Core\Events\EventContextData;
use LotGD\Module\Res\Wealth\Module;
use LotGD\Module\Res\Wealth\Tests\EventHandlers\AnyEventHandler;

use LotGD\Core\Game;
use LotGD\Core\Models\Module as ModuleModel;
use LotGD\Core\Tests\ModelTestCase;
use Symfony\Component\Yaml\Yaml;

class ModuleTest extends ModelTestCase
{
    const Library = 'lotgd/module-res-wealth';
    const RootNamespace = "LotGD\\Module\\Res\\Wealth\\";

    /** @var Game */
    public $g;
    protected $moduleModel;

    public function getDataSet(): array
    {
        return Yaml::parseFile(implode(DIRECTORY_SEPARATOR, [__DIR__, 'datasets', 'module.yml']));
    }

    public function getCwd(): string
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, '..']);
    }

    public function setUp(): void
    {
        parent::setUp();

        // Register and unregister before/after each test, since
        // handleEvent() calls may expect the module be registered (for example,
        // if they read properties from the model).
        $this->moduleModel = new ModuleModel(self::Library);
        $this->moduleModel->save($this->getEntityManager());
        Module::onRegister($this->g, $this->moduleModel);

        // Event listener
        $this->g->getEventManager()->subscribe("/(.*)/", AnyEventHandler::class, "lotgd/module-res-wealth");

        $this->g->getEntityManager()->flush();
        $this->g->getEntityManager()->clear();
    }

    public function tearDown(): void
    {
        Module::onUnregister($this->g, $this->moduleModel);
        $m = $this->getEntityManager()->getRepository(ModuleModel::class)->find(self::Library);
        if ($m) {
            $m->delete($this->getEntityManager());
        }

        $this->g->getEventManager()->unsubscribe("/(.*)/", AnyEventHandler::class, "lotgd/module-res-wealth");

        $this->g->getEntityManager()->flush();
        $this->g->getEntityManager()->clear();

        parent::tearDown();
    }

    public function testHandleUnknownEvent()
    {
        // Always good to test a non-existing event just to make sure nothing happens :).
        $context = new EventContext(
            "e/lotgd/tests/unknown-event",
            "none",
            EventContextData::create([])
        );

        $newContext = Module::handleEvent($this->g, $context);

        $this->assertSame($context, $newContext);
    }
}
