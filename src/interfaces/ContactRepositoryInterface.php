<?php
namespace Interfaces;

interface ContactRepositoryInterface {
    public function saveMessage($name, $email, $message);
}
