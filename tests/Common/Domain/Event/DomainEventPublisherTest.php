<?php
namespace TijmenWierenga\Project\Tests\Common\Infrastructure\Event;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use TijmenWierenga\Project\Common\Domain\Event\DomainEvent;
use TijmenWierenga\Project\Common\Domain\Event\DomainEventPublisher;
use TijmenWierenga\Project\Common\Domain\Event\DomainEventSubscriber;
use TijmenWierenga\Project\Common\Domain\Event\PersistingDomainEvent;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
class DomainEventPublisherTest extends TestCase
{
    /**
     * @test
     */
    public function it_subscribes_domain_event_subscribers()
    {
    	$publisher = DomainEventPublisher::instance();
    	/** @var DomainEventSubscriber|PHPUnit_Framework_MockObject_MockObject $subscriber */
    	$subscriber = $this->getMockBuilder(DomainEventSubscriber::class)->getMock();

    	$publisher->subscribe($subscriber);
        $property = new \ReflectionProperty($publisher, 'subscribers');
        $property->setAccessible(true);
        $subscribers = $property->getValue($publisher);
        $property->setAccessible(false);

        $this->assertContains($subscriber, $subscribers);

        /** @var DomainEvent $event */
        $event = new class extends PersistingDomainEvent implements DomainEvent {
        };

        $subscriber->expects($this->once())
            ->method('isSubscribedTo')
            ->with($event)
            ->willReturn(true);

        $subscriber->expects($this->once())
            ->method('handle')
            ->with($event);

        $publisher->publish($event);
    }

    /**
     * @test
     */
    public function it_always_returns_the_same_instance()
    {
    	$publisher = DomainEventPublisher::instance();
    	$publisherAgain = DomainEventPublisher::instance();

    	$this->assertSame($publisher, $publisherAgain);
    }
}
