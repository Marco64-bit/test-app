<?php

use App\Mail\VerificationCodeMail;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('send-verification-code-mail', function () {
    Mail::to('marcoosamehh10@gmail.com')->send(new VerificationCodeMail("Marco"));

    // Also, you can use specific mailer if your default mailer is not "mailtrap-sdk" but you want to use it for welcome mails
    // Mail::mailer('mailtrap-sdk')->to('testreceiver@gmail.com')->send(new WelcomeMail("Jon"));
})->purpose('Send verification code mail');
