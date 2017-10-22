<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailAccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $client_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($client_name)
    {
        $this->client_name = $client_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('minibank@minibank.com')->subject('Account Created')->view('email');
    }
}
