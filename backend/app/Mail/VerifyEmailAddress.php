<?php

namespace App\Mail;
use App\Models\System\ConfigurationModel;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmailAddress extends Mailable
{
    use Queueable, SerializesModels;

    private $code = '';
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code)
    {
        $this->code=$code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $config = ConfigurationModel::find(101);        
        return $this->view('emails.VerifyEmailPassword')->with([
                                                            'nama_pt'=>$config->config_value,
                                                            'code'=>$this->code
                                                        ]);
    }
}
