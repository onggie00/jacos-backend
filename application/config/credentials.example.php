<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| TEMPLATE kredensial Jacos (tracked di git).
| Copy file ini ke `credentials.php` lalu isi nilai aslinya.
| `credentials.php` gitignored — jangan pernah di-commit.
|--------------------------------------------------------------------------
*/

// Path service account JSON Firebase (FCM v1), relatif ke FCPATH.
// Buat project Firebase Jacos -> Project settings -> Service accounts -> download JSON.
defined('FIREBASE_SERVICE_ACCOUNT_PATH') OR define('FIREBASE_SERVICE_ACCOUNT_PATH', 'jacos-firebase-service-account.json');

// Firebase project ID (URL FCM v1: /v1/projects/<ID>/messages:send)
defined('FIREBASE_PROJECT_ID') OR define('FIREBASE_PROJECT_ID', 'jacosverse-app');
