<?php

interface Observer
{
    public function handle($event);
}

interface Subject
{
    public function attach(Observer $observer);
    public function detach(Observer $observer);
    public function notify();
}

trait Subjectable
{
    protected $observers;

    public function attach(Observer $observer)
    {
        $this->observers[] = $observer;
    }

    public function detach(Observer $observer)
    {
        for ($i = 0; $i < count($this->observers); $i++) {
            if ($this->observers[$i] == $observer) {
                unset($this->observers[$i]);
            }

            $this->observers = array_values($this->observers);
        }
    }

    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->handle($this);
        }
    }
}

class User
{
    public $id = 1;
    public $email = 'alex@codecourse.com';
}

class MailingListSignup implements Subject
{
    public function __construct($user)
    {
        $this->user = $user;
    }

    use Subjectable;
}

class UpdateMailingStatusInDatabase implements Observer
{
    public function handle($event)
    {
        var_dump('Update user in database: ' . $event->user->email);
    }
}

class SubscribeUserToService implements Observer
{
    public function handle($event)
    {
        var_dump('Subscribe user to Mailchimp: ' . $event->user->id);
    }
}

$user = new User;

$event = new MailingListSignup($user);
$event->attach(new SubscribeUserToService);
$event->attach(new UpdateMailingStatusInDatabase);

$event->notify();
