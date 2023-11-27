<?php

abstract class Event implements SplSubject
{
    protected $storage;

    public function __construct()
    {
        $this->storage = new SplObjectStorage();
    }

    public function attach(SplObserver $observer)
    {
        $this->storage->attach($observer);
    }

    public function detach(SplObserver $observer)
    {
        if (!$this->storage->contains($observer)) {
            return;
        }

        $this->storage->detach($observer);
    }

    public function notify()
    {
        if (!count($this->storage)) {
            return;
        }

        foreach ($this->storage as $observer) {
            $observer->update($this);
        }
    }
}

class MailingListSignup extends Event
{
    public $user;

    public function __construct($user)
    {
        parent::__construct();
        $this->user = $user;
    }
}

class UpdateMailingStatusInDatabase implements SplObserver
{
    public function update(SplSubject $event)
    {
        var_dump('Update user in db: ' . $event->user->id);
    }
}

class SubscribeUserToService implements SplObserver
{
    public function update(SplSubject $event)
    {
        var_dump('Subscribe user to Mailchimp: ' . $event->user->email);
    }
}

class User
{
    public $id = 1;
    public $email = 'alex@codecourse.com';
}

$user = new User;

$event = new MailingListSignup($user);
$event->attach(new UpdateMailingStatusInDatabase);
$event->attach(new SubscribeUserToService);

$event->notify();