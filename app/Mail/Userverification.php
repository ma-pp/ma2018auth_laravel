<?php

namespace App\Mail; 

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Userverification extends Mailable
{
    use Queueable, SerializesModels;


    protected $verification_code;
    protected $name;


    /**
     * Create a new message instance.
     *
     * @return void
     */ 
    public function __construct($verification_code , $name)
    {
        $this->verification_code = $verification_code;
        $this->name = $name;

    }

    /** 
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.verify')->with([
            'verification_code'=>$this->verification_code,
            'name'=>$this->name

        ]);
    }
}
