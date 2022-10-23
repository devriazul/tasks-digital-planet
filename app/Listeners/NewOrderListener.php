<?php

namespace App\Listeners;

use App\Events\NewOrderEvent;
use App\Notifications\NewOrder;
use Illuminate\Support\Facades\Notification;

class NewOrderListener
{

    /**
     * Handle the event.
     *
     * @param  NewOrderEvent  $event
     * @return void
     */

    public function handle(NewOrderEvent $event)
    {
        Notification::send($event->notifyUser, new NewOrder($event->order));
    }

}
