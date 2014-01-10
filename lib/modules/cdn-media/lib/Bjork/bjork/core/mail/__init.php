<?php

namespace bjork\core;

use bjork\conf\settings;

use bjork\core\mail\message\EmailMessage,
    bjork\core\mail\message\EmailMultiAlternatives;

final class mail {
    
    static $outbox = null; // Used by the locmem backend.
    
    /**
    * Load an email backend and return an instance of it.
    * 
    * If backend is None (default) settings.EMAIL_BACKEND is used.
    * 
    * Both fail_silently and options are used in the constructor of the
    * backend.
    */
    public static function get_connection($fail_silently=false, $backend=null,
            array $options=null)
    {
        $cls = $backend ? $backend : settings::get('EMAIL_BACKEND');
        if (is_array($cls)) {
            list($cls, $opts) = $cls;
            if (!$options)
                $options = array();
            $options = array_merge($opts, $options);
        }
        return new $cls($options, $fail_silently);
    }
    
    /**
    * Easy wrapper for sending a single message to a recipient list. All members
    * of the recipient list will see the other recipients in the 'To' field.
    * 
    * If auth_user is None, the EMAIL_HOST_USER setting is used.
    * If auth_password is None, the EMAIL_HOST_PASSWORD setting is used.
    */
    public static function send_mail($subject, $message, $from_email,
            array $recipient_list, $fail_silently=false, $connection=null,
            $auth_user=null, $auth_password=null)
    {
        if (null === $connection)
            $connection = self::get_connection($fail_silently, null, array(
                'username' => $auth_user,
                'password' => $auth_password));
        
        $msg = new EmailMessage($subject, $message, $from_email, $recipient_list,
            null, null, $connection);
        
        return $msg->send();
    }
    
    /**
    * Given a datatuple of (subject, message, from_email, recipient_list), sends
    * each message to each recipient list. Returns the number of emails sent.
    * 
    * If from_email is None, the DEFAULT_FROM_EMAIL setting is used.
    * If auth_user and auth_password are set, they're used to log in.
    * If auth_user is None, the EMAIL_HOST_USER setting is used.
    * If auth_password is None, the EMAIL_HOST_PASSWORD setting is used.
    */
    public static function send_mass_mail(array $data, $fail_silently=false,
            $connection=null, $auth_user=null, $auth_password=null)
    {
        if (null === $connection)
            $connection = self::get_connection($fail_silently, null, array(
                'username' => $auth_user,
                'password' => $auth_password));
        
        $messages = array_map(function($a) {
            return new EmailMessage($a[0], $a[1], $a[2], $a[3]);
        }, $data);
        
        return $connection->sendMessages($messages);
    }
    
    /**
    * Sends a message to the admins, as defined by the ADMINS setting.
    */
    public static function mail_admins($subject, $message,
            $fail_silently=false, $connection=null, $html_message=null)
    {
        return self::mail_staff(array_keys(settings::get('ADMINS')),
            $subject, $message, $fail_silently, $connection, $html_message);
    }
    
    /**
    * Sends a message to the managers, as defined by the MANAGERS setting.
    */
    public static function mail_managers($subject, $message,
            $fail_silently=false, $connection=null, $html_message=null)
    {
        return self::mail_staff(array_keys(settings::get('MANAGERS')),
            $subject, $message, $fail_silently, $connection, $html_message);
    }
    
    /* internal helpers */
    
    static function mail_staff(array $to, $subject, $message,
            $fail_silently=false, $connection=null, $html_message=null)
    {
        if (!$to)
            return;
        
        $mail = new EmailMultiAlternatives(
            settings::get('EMAIL_SUBJECT_PREFIX') . $subject,
            $message,
            settings::get('SERVER_EMAIL'),
            $to,
            null,
            null,
            $connection);
        
        if ($html_message)
            $mail->attachAlternative($html_message, 'text/html');
        
        $mail->send($fail_silently);
    }
}
