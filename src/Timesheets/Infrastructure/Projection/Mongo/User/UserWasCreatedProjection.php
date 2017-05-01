<?php
namespace TijmenWierenga\Project\Timesheets\Infrastructure\Projection\Mongo\User;

use MongoDB\Client;
use TijmenWierenga\Project\Timesheets\Domain\Event\DomainEvent;
use TijmenWierenga\Project\Timesheets\Domain\Model\User\UserWasCreated;
use TijmenWierenga\Project\Timesheets\Domain\Projection\Projection;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
class UserWasCreatedProjection implements Projection
{
    /**
     * @var Client
     */
    private $client;

    /**
     * UserWasCreatedProjection constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Returns the event the projection is listening for
     *
     * @return string
     */
    public function listensTo(): string
    {
        return UserWasCreated::class;
    }

    /**
     * Projects a given event to the read model
     *
     * @param DomainEvent $event
     */
    public function project(DomainEvent $event): void
    {
        // TODO: Implement project() method.
    }
}
