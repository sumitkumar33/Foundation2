<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\AccountApproved;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Notify implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dump)
    {
        $this->data = $dump;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info(json_encode($this->data));
        $user = User::find($this->data['user_id']);
        $user->notify(new AccountApproved($this->data));
    }
}
