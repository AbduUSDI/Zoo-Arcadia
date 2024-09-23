<?php

session_start();
session_destroy();
header('Location: /Zoo-Arcadia-New/login');
exit;
