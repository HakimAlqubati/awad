<?php

namespace App\Jobs;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class FcmNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $title;
    public $body;
    public $user;
    public $device_token;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($title, $body, $user)
    {
        $this->title=$title;
        $this->body=$body;
        $this->user=$user;

        $this->queue = 'fcm-notification';

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
      if($this->user  instanceof User){
        $this->device_token=$this->user->device->token;
      }
      else if ($this->user  instanceof Branch){
        $this->device_token=$this->user->manager->device->token;
      } 
        $toToken = "cDnVLR-mQpW4cmqNU5lt00:APA91bF4GKimgE3-5HCmY3p-gOYLO9Cn7sWbC0W3mPMGcitU9nzy4XlGtb4nGL4J7ulC7zBiWtfUDzz7tYffQJHljyUG19T2EcjXfWEMxkImXmAjol3eF0a0fp-q31t8T0yaK6Darwib";
        //$serverKey = env("TOKEN_FCM");
        $response = Http::asJson()->withHeaders([
            'Authorization' => 'key=' . env("TOKEN_FCM"),
        ])->post(
            env("DOMAIN_FCM"),
            [
                'to' => $this->device_token,
                'notification' => [
                    'title' => $this->title,
                    'body' => $this->body,
                ],
                'data' => [
                    'title' => $this->title,
                    'body' => $this->body,
                ],
            ],
        );
        
        return $response->json() ;
    }
}
