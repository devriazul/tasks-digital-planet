<?php

namespace App\Observers;

use App\Events\NewUserEvent;
use App\Events\NewUserRegistrationViaInviteEvent;
use App\Models\User;

class UserObserver
{

    public function created(User $user)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $sendMail = true;

            if (request()->has('sendMail') && request()->sendMail == 'no') {
                $sendMail = false;
            }

            if ($sendMail && request()->password != '' && auth()->check() && request()->email != '') {
                event(new NewUserEvent($user, request()->password));
            }

            // request()->has('send_mail_to_admin') && request()->send_mail_to_admin == 'yes' &&
            if (request()->password != '' && request()->email != '') {

                $admins = User::allAdmins();

                if ($admins) {
                    foreach ($admins as $admin) {
                        event(new NewUserRegistrationViaInviteEvent($admin, $user));
                    }
                }
            }

        }

    }

}
