<?php
namespace TijmenWierenga\Project\Timesheets\Domain\Model\User;

use TijmenWierenga\Project\Timesheets\Domain\Model\Aggregate\AggregateRoot;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
class User extends AggregateRoot
{
    /**
     * @var UserId
     */
    private $userId;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * User constructor.
     * @param UserId $userId
     */
    private function __construct(UserId $userId)
    {
        $this->userId = $userId;
    }

    public static function new(UserId $userId, string $firstName, string $lastName)
    {
        $user = new self($userId);

        $user->apply(new UserWasCreated($userId, $firstName, $lastName));

        return $user;
    }

    /**
     * @return UserId
     */
    public function getUserId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    protected function applyUserWasCreated(UserWasCreated $event): void
    {
        $this->firstName = $event->getFirstName();
        $this->lastName = $event->getLastName();
    }
}
