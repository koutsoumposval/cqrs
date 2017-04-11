<?php
namespace TijmenWierenga\Project\Tests\Timesheets\Infrastructure\Event;

use DateTimeImmutable;
use NilPortugues\Serializer\Serializer;
use NilPortugues\Serializer\Strategy\JsonStrategy;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use TijmenWierenga\Project\Timesheets\Infrastructure\Event\RedisEventStore;
use TijmenWierenga\Project\Timesheets\Domain\Event\EventStore;
use TijmenWierenga\Project\Timesheets\Domain\Event\EventStream;
use TijmenWierenga\Project\Timesheets\Domain\Model\ValueObject\TimeFrame;
use TijmenWierenga\Project\Timesheets\Domain\Model\WorkLog\WorkLog;
use TijmenWierenga\Project\Timesheets\Domain\Model\WorkLog\WorkLogId;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
class RedisEventStoreTest extends TestCase
{
    /**
     * @var EventStore
     */
    private $eventStore;

    public function setUp()
    {
        $client = new Client([
            'scheme' => 'tcp',
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT')
        ]);
        $serializer = new Serializer(new JsonStrategy());

        $this->eventStore = new RedisEventStore($client, $serializer);
    }

    /**
     * @test
     */
    public function it_appends_events_to_the_store()
    {
        $workLog = WorkLog::new(WorkLogId::new(), TimeFrame::new(
            new DateTimeImmutable("2017-04-10T08:00:00"),
            new DateTimeImmutable("2017-04-10T14:00:00")
        ));

        $eventStream = new EventStream($workLog->getWorkLogId(), $workLog->recordedEvents());

        $this->eventStore->append($eventStream);
        $retrievedEventStream = $this->eventStore->getEventsFor($workLog->getWorkLogId());
        $reconstitutedWorkLog = WorkLog::reconstitute($retrievedEventStream);

        $this->assertEquals($eventStream->getEvents(), $retrievedEventStream->getEvents());
        $this->assertEquals($workLog, $reconstitutedWorkLog);
    }
}
