## Announce

A simple framework agnostic Event dispatcher for your event-driven application.

### Installation

```bash
composer require nimbly/announce
```

### Dispatcher

The ```Dispatcher``` is at the core of managing subscribers, handlers, and triggering events. It is usually best practice to attach an instance of the ```Dispatcher``` to your dependency injection container for later use in your application code (see **Triggering events** section).

```php
$dispatcher = new Announce\Dispatcher;
Container::set(Announce\Dispatcher::class, $dispatcher);
```

### Events

Events are classes that represent some important or significant "event" that has taken place within your application code. This event can be anything you like: a new user registering an account, a user updating their address, a session being destroyed, etc.

Usually (but not always), you'll want to pass along something *in to* the event that your event handlers will need to do their job. You can use the ```Event``` instance to capture that data.

Extend your event classes from ```Announce\Event```.

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

### Handlers

Handlers are the methods or functions that handle a triggered ```Event```. A handler can be a method on a class, a ```closure```, or any ```callable```.

The ```Dispatcher``` will always pass the ```Event``` instance into the handler as a parameter.

```php
function sendEmail(UserRegisteredEvent $userRegisteredEvent)
{
    Email::send('welcome')->to($userRegisteredEvent->user->email);
}
```

### Subscribers

Subscribers are classes that register one or more events to a handler. Subscribers are a great way to organize all related handlers into a single class: i.e. manage a single area of concern.

Subscribers must extend from ```Announce\Subscriber``` and implement the ```subscribe``` method. The ```subscribe``` method accepts the ```Dispatcher``` instance as its only parameter.

The ```subscribe``` method can then use the ```Dispatcher``` instance to listen to any number of events.

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

You may opt to bypass subscribers altogther and simply attach your own handlers ad-hoc. Handlers can be any ```callable``` or a ```Class@Method``` style string.

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

### Triggering events

To trigger an event in your code, simply call the ```trigger``` method on the ```Dispatcher``` with your ```Event``` instance.

```php
$dispatcher = Container::get(Announce\Dispatcher::class);
$dispatcher->trigger(new UserRegisteredEvent($user));
```