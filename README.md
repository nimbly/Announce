# Announce

A simple framework agnostic PSR-14 event dispatcher for your event-driven application.

## Installation

```bash
composer require nimbly/announce
```

## Quick start

### Create an event class

```php
class UserRegisteredEvent extends Announce\Event
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
```

### Add a listener and bind dispatcher to Container.
```php
$dispatcher = new Announce\Dispatcher;

$dispatcher->listen(
    UserRegisteredEvent::class,
    function(UserRegisteredEvent $event) {

        Log::debug("Handling UserRegistered event.");

    }
);

Container::set(Announce\Dispatcher::class, $dispatcher);
```


### Dispatch event from your application code
```php
Container::get(Announce\Dispatcher::class)->dispatch(new UserRegisteredEvent($user));
```

## Events
Events are classes that represent some important or significant "event" that has taken place within your application code. This event can be anything you like: a new user registering an account, a user updating their address, a session being destroyed, etc - but usually represent some sort of state-change.

Usually (but not always), you'll want to pass along something *in to* the event that your event handlers will need to do their job. You can use the ```Event``` instance to capture that data.

```php
class UserRegisteredEvent extends Announce\Event
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
```

## Listening for events

Call the dispatcher's ```listen``` method to bind an Event (or set of Events) to a handler.

```php
// Register a listener to the current instance's sendRegistrationEmail method.

$dispatcher->listen(
    UserRegisteredEvent::class,
    [$this, 'sendRegistrationEmail']
);
```

```php
// Register a listener to a closure.

$dispatcher->listen(
    UserRegisteredEvent::class,
    function(UserRegisteredEvent $event) {

        // Send email...

    }
);

```

```php
// Register a listener to a function.

function sendRegistrationEmail(UserRegisteredEvent $event)
{
    // Send email....
}

$dispatcher->listen(
    UserRegisteredEvent::class,
    'sendRegistrationEmail'
);
```

## Accessing the dispatcher

It is usually best practice to attach your instance of the ```Dispatcher``` to your dependency injection container for later use in your application code (see **Dispatching events** section).

```php
$dispatcher = new Announce\Dispatcher;

$dispatcher->listen(
    UserRegisteredEvent::class,
    function(UserRegisteredEvent $event) {

        // Send email...

    }
);

Container::set(Announce\Dispatcher::class, $dispatcher);
```

### Handlers

Handlers are the methods or functions that handle a dispatched ```Event```. A handler can be a method on a class, a ```closure```, or any other thing of type ```callable```.

The ```Dispatcher``` will always pass the ```Event``` instance into the handler as the only parameter.

```php
function sendEmail(UserRegisteredEvent $userRegisteredEvent)
{
    Email::send('welcome')->to($userRegisteredEvent->user->email);
}
```

### Subscribers

Subscribers are classes that register one or more events to a handler. Subscribers are a great way to organize all related handlers into a single class: i.e. manage a single area of concern.

Subscribers must extend from ```Announce\Subscriber``` and implement the ```register``` method. The ```register``` method accepts the ```Dispatcher``` instance as its only parameter.

The ```register``` method can then use the ```Dispatcher``` instance to listen to any number of events.

```php
class NotificationSubscriber extends Announce\Subscriber
{
    public function userRegistered(UserRegisteredEvent $userRegisteredEvent)
    {
        // Send the "Welcome" email to new user.
        Email::send("welcome")->to($userRegisteredEvent->user->email);
    }

    public function userInvited(UserInvitedEvent $userInvitedEvent)
    {
        // Send the "Invitation" email to invited user.
        Email::send("invitation")->to($userInvitedEvent->user->email);
    }

    public function subscribe(Announce\Dispatcher $dispatcher)
    {
        $dispatcher->listen(
            UserRegisteredEvent::class,
            [$this, "userRegistered"]
        );

        $dispatcher->listen(
            UserInvitedEvent::class,
            [$this, "userInvited"]
        );
    }
}
```

You can also listen to more than one event for a single handler.

```php
    $dispatcher->listen(
        [UserRegisteredEvent::class, UserInvitedEvent::class],
        [$this, "userRegistered"]
    );
```

### Registering subscribers

Before your subscribers' handlers can be called, they must be registered with the event ```Dispatcher```.

```php
$dispatcher = new Announce\Dispatcher;

$dispatcher->register([
    NotificationSubscriber::class,
    MyOtherSubscriber::class,
]);
```

### Registering listeners

You may opt to bypass subscribers altogther and simply attach your own handlers ad-hoc. Handlers can be anything of type ```callable```.

```php
$dispatcher = new Announce\Dispatcher;

/**
 * Closure based handler.
 */
$dispatcher->listen(MyEvent::class, function(MyEvent $myEvent){

    // Do foo...

});

/**
 * Callable based handler.
 */
function myEventHandler(MyEvent $myEvent)
{
    // Do foo...
}

$dispatcher->listen(MyEvent::class, "myEventHandler");

/**
 * Class@Method string based handler.
 * 
 * The App\\Subscribers\\FooSubscriber class will be instantiated and the "barHandler" method on the instance will be called.
 */
$dispatcher->listen(MyOtherEvent::class, "App\\Subscribers\\FooSubscriber@barHandler");
```

### Dispatching events

To dispatch (trigger) an event in your code, simply call the ```dispatch``` method on the ```Dispatcher``` with your ```Event``` instance.

```php
$dispatcher = Container::get(Announce\Dispatcher::class);
$dispatcher->dispatch(new UserRegisteredEvent($user));
```

### Stopping event propagation
If you need to stop event propagation during its lifetime, just call the ```stop()``` method on the event instance. The event will no longer be propagated to any subscribed listeners.

```php
function eventHandler(UserRegisteredEvent $event)
{
    Email::send("welcome")->to($event->user->email);

    $event->stop();
}
```

### Broadcasting
You may hook-in any custom code required to broadcast your event to any resource you wish by implementing the ```BroadcastableEvent``` interface. This interface defines a single method ```broadcast()``` with no parameters and a ```void``` return type.

The dispatcher will automatically call the ```broadcast()``` method after all registered handlers have finished processing the event.

```php
class WidgetUpdatedEvent extends Annouce\Event implements Announce\BroadcastableEvent
{
    protected $widget;

    public function __construct(Widget $widget)
    {
        $this->widget = $widget;
    }

    public function broadcast(): void
    {
        Container::get(SnsClient::class)->publish([
            'Message' => [
                'event' => $this->getName(),
                'data' => $this->widget->serialize(),
            ],
            'TopicArn' => 'arn:aws:sns:us-west-2:038318100391:WidgetUpdated'
        ]);
    }
}
```