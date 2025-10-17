<?php
namespace App\Jobs;

use App\Mail\NewPasswordMail;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewPasswordEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $email;
    protected string $plainPassword;
    protected string $user;

    public function __construct(string $email, string $plainPassword,string $user)
    {
        $this->email = $email;
        $this->plainPassword = $plainPassword;
        $this->user =$user;
    }

    public function handle(): void
    {
        Mail::to($this->email)->send(new NewPasswordMail($this->plainPassword,$this->user));
    }
}
