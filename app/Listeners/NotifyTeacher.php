<?php

namespace App\Listeners;

use App\Events\StudentAssigned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyTeacher
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\StudentAssigned  $event
     * @return void
     */
    public function handle(StudentAssigned $event)
    {
        //
    }
}
