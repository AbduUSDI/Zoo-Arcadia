<?php
namespace Interfaces;

interface ContactServiceInterface {
    public function saveMessage($name, $email, $message);
}
