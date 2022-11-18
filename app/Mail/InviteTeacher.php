<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InviteTeacher extends Mailable
{
    use Queueable, SerializesModels;

    private $school;
    private $email;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($school,$email)
    {
        $this->school = $school;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = "https://test-comiru-vue.herokuapp.com/register?sid={$this->school->id}&email={$this->email}";
        return $this->subject($this->school->name.'邀请你加入')
            ->to($this->email)
            ->view('invite',[
                'school' => $this->school,
                'url'   => $url
            ]);
    }
}
