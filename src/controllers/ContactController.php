<?php
namespace Controllers;

use Services\ContactService;

class ContactController {
    private $contactService;

    public function __construct(ContactService $contactService) {
        $this->contactService = $contactService;
    }

    public function handleContactForm($name, $email, $subject, $message) {
        return $this->contactService->sendContactEmail($name, $email, $subject, $message);
    }
}
