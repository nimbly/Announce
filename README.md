# Announce

[![Latest Stable Version](https://img.shields.io/packagist/v/nimbly/Announce.svg?style=flat-square)](https://packagist.org/packages/nimbly/Announce)
[![Build Status](https://img.shields.io/travis/nimbly/Announce.svg?style=flat-square)](https://travis-ci.org/nimbly/Announce)
[![Code Coverage](https://img.shields.io/coveralls/github/nimbly/Announce.svg?style=flat-square)](https://coveralls.io/github/nimbly/Announce)
[![License](https://img.shields.io/github/license/nimbly/Announce.svg?style=flat-square)](https://packagist.org/packages/nimbly/Announce)

A simple framework agnostic PSR-14 event dispatcher for your event-driven application.

## Features

* Uses PHP's `#Attribute` feature to register class methods as event handlers.
* Optional PSR-11 Container support.
* Full autowiring support for your subscribers.

## Installation

```bash
composer require nimbly/announce
```

## Quick start

### Create an event class

Your events can be standalone classes or they can extend the `Event` abstract class. By extending the `Event` abstract you gain the ability to:

* Stop event propogation if needed
* Customizable event names (defaults to fully qualified class namespace)

```php
namespace App\Events;

use App\Models\User;
use Nimbly\Announce\Event;

class UserRegisteredEvent extends Event
{
    public function __construct(public User $user)
    {
    }
}
```

### Create a subscriber
Subscribers are classes that will handle your events. You can have as many subscribers as you would like.

To register a subscriber's method to handle a particular event or set of events, use the `Nimbly\Announce\Subscribe` attribute and pass in a comma separated list of event names to listen for.

```php
namespace App\Subscribers;

use App\Events\UserRegisteredEvent;
use App\Services\EmailService;
use Nimbly\Announce\Subscribe;

class EmailSubscriber
{
    #[Subscribe(UserRegisteredEvent::class)]
    public function onUserRegistered(
        UserRegisteredEvent $event,
        EmailService $emailService): void
    {
        $emailService->send("registration_email", $event->user->email);
    }
}
```

### Initiate Dispatcher

To register your subscriber's with the event dispatcher, pass in an array of class names or instances into the `Dispatcher` constructor.

You can also pass in a PSR-11 compliant container instance to be used in autowiring your subscribers as well as for event handlers on your subscribers.

```php
$dispatcher = new Dispatcher(
    subscribers: [
        EmailSubscriber::class,
        new FooSubscriber,
    ],
    container: $container
);
```

### Dispatch event

To trigger an event, just call the `dispatch` method with the event instance.

```php
$event = new UserRegisteredEvent($user);
$dispatcher->dispatch($event);
```

### Stopping event propagation

If you need to stop event propagation during its lifetime, just call the `stop()` method on the event instance. The event will no longer be propagated to any further subscribed listeners.

```php
class EmailSubscriber
{
    #[Subscribe(UserRegisteredEvent::class)]
    public function onUserRegistered(
        UserRegisteredEvent $event,
        EmailService $emailService): void
    {
        $emailService->send("registration_email", $event->user->email);

        // Prevent any further handlers from processing this event
        $event->stop();
    }
}
```