<?php

namespace asssu\forms;

include_once __DIR__.'/../../lib/Bjork/bjork/forms/__init.php';

use bjork\core\exceptions\ValidationError,
    bjork\forms;

class ConfigForm extends forms\Form {
    
    function __construct($asssu, $data=null, $files=null, array $options=null) {
        $this->asssu = $asssu;
        if (!empty($this->asssu->config)) {
            if (null === $options)
                $options = array();
            $options['initial'] = $this->asssu->config;
        }
        parent::__construct($data, $files, $options);
    }

    function clean() {
        $data = $this->getCleanedData();
        $asssu = $this->asssu;

        $data['id'] = 1;
        $data['site_url'] = $asssu->site_url;
        $data['version'] = $asssu::$version;
        
        $original_is_active = isset($data['is_active']) ? $data['is_active'] : false;
        $data['is_active'] = false;
        
        if (!isset($data['access_key']) || !isset($data['secret_key']) || !isset($data['bucket_name']))
            return $data;
        
        require_once __DIR__.'/../../lib/aws-sdk-php/aws-autoloader.php';
        
        // for some reason this does not throw exception 
        // when you try to connect with wrong credentials
        $client = \Aws\S3\S3Client::factory(array(
            'key' => $data['access_key'],
            'secret' => $data['secret_key']
        ));
            
        // checking access_key, secret_key and bucket_name
        try {
            $result = $client->getCommand('getBucketLocation')->set('Bucket', $data['bucket_name'])->getResult();
        } catch (\Aws\S3\Exception\InvalidAccessKeyIdException $e) {
            throw new ValidationError('Your credentials are invalid.');
        } catch (\Aws\S3\Exception\SignatureDoesNotMatchException $e) {
            throw new ValidationError('Your credentials are invalid.');
        } catch (\Aws\S3\Exception\NoSuchBucketException $e) {
            throw new ValidationError('Bucket with name "'.$data['bucket_name'].'" does not exist.');
        }
        
        // finding the endpoint
        $data['bucket_location'] = $result['Location'];
        try {
            $this->fields['bucket_location']->validate($data['bucket_location']);
        } catch (ValidationError $e) {
            // sending email to inform the admin
            $subject = 'Amazon S3 Uploads - Unknown location';
            $recipient = 'atvdev@gmail.com';
            $message = 'The plugin Amazon S3 Uploads encountered an uknown bucket location "'.$data['bucket_location'].'".';
            @ mail($recipient, $subject, $message);
            throw new ValidationError('Unknown bucket location "'.$data['bucket_location'].'". The plugin developer have been informed about this. Stay tuned for the fix.', 1);
        }
        
        // trying the connection with endpoint
        $data['region'] = $this->fields['bucket_location']->choices[$data['bucket_location']];
        if ($data['region'] !== '') {
            try {
                $client = \Aws\S3\S3Client::factory(array(
                    'key' => $data['access_key'],
                    'secret' => $data['secret_key'],
                    'region' => $data['region']
                ));
            } catch (\Aws\Common\Exception\InvalidArgumentException $e) {
                // sending email to inform the admin
                $subject = 'Amazon S3 Uploads - Unknown region';
                $recipient = 'atvdev@gmail.com';
                $message = 'The plugin Amazon S3 Uploads encountered an uknown region "'.$data['region'].'".';
                @ mail($recipient, $subject, $message);
                throw new ValidationError('Unknow server region "'.$data['region'].'". The plugin developer have been informed about this. Stay tuned for the fix.');
            }
        }
        
        $result = $client->getCommand('getBucketVersioning')->set('Bucket', $data['bucket_name'])->getResult();
        if ($result['Status'] !== 'Enabled')
            throw new ValidationError('The specified bucket does not have versioning enabled. Please enable it before continuing.');

        $data['is_active'] = $original_is_active;

        return $data;
    }
}

ConfigForm::fields(array(
    'id' => new forms\IntegerField(array('required' => false)),
    'site_url' => new forms\CharField(array('required' => false)),
    'version' => new forms\CharField(array('required' => false)),
    'is_active' => new forms\BooleanField(array('required' => false)),

    'access_key' => new forms\CharField(),
    'secret_key' => new forms\CharField(),
    // 'secret_key' => new forms\CharField(array('widget' => 'bjork\forms\PasswordInput')),
    'bucket_name' => new forms\CharField(),
    'bucket_subdir' => new forms\CharField(array('required' => false)),
    'terms_of_use' => new forms\BooleanField(array('label' => 'I agree to the <a href="http://wordpress.org/plugins/amazon-s3-uploads/" target="_blank">Terms of Use</a>')),

    'bucket_location' => new forms\ChoiceField(array(
        ''                  => '',
        'US'                => '',
        'us-west-2'         => 'us-west-2',
        'us-west-1'         => 'us-west-1',
        'EU'                => 'eu-west-1',
        'eu-west-1'         => 'eu-west-1',
        'ap-southeast-1'    => 'ap-southeast-1',
        'ap-southeast-2'    => 'ap-southeast-2',
        'ap-northeast-1'    => 'ap-northeast-1',
        'sa-east-1'         => 'sa-east-1'
    ), array('required' => false)),
    'region' => new forms\ChoiceField(array(
        ''                  => '',
        'us-west-2'         => 'us-west-2',
        'us-west-1'         => 'us-west-1',
        'eu-west-1'         => 'eu-west-1',
        'ap-southeast-1'    => 'ap-southeast-1',
        'ap-southeast-2'    => 'ap-southeast-2',
        'ap-northeast-1'    => 'ap-northeast-1',
        'sa-east-1'         => 'sa-east-1'
    ), array('required' => false))
));
