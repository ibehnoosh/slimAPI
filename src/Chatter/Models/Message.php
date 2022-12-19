<?php

namespace Chatter\Models;

class Message extends \Illuminate\Database\Eloquent\Model
{
    public function output()
    {
        $output = [];
        $output['body']         = $this->body;
        $output['user_id']      = $this->user_id;
        $output['user_uri']     = '/user/' .$this->user_id;
        $output['created_at']   = $this->created_at;
        $output['image_url']    = $this->image_url;
        $output['message_id']   = $this->id;
        $output['message_uri']  = '/messages/' .$this->id;

        return $output;
    }
}