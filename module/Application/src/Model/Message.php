<?php

namespace Application\Model;

class Message
{
  public $id;
  public $created;
  public $text;

  public function exchangeArray(array $data) {
    $this->id       = !empty($data['id']) ? $data['id']:null;
    $this->created  = !empty($data['created']) ? $data['created']: date('Y-m-d H:i:s');
    $this->text     = !empty($data['text']) ? $data['text']: '';
  }
}