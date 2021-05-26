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
    private $name = '';
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code,$name)
    {
        $this->code=$code;
        $this->name=$name;
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
                                                            'name'=>$this->name,
                                                            'code'=>$this->code,
                                                            'ta'=>'2021',
                                                        ]);
    }
}
