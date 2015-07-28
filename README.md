# turksoap-laravel
This package is for accessing amazon turk from laravel

This package is to overcome the problem of laraturk( https://github.com/pauly4it/laraturk.git) which is not designed for external experiment design.

This package used the library file that is not official to amazon but they have provided it to their site for use. 
Not all operations are implemented now, but I will provided a clear package which will be able to do all sorts of work for amazon mechanical turk research.

Installation

Install by adding turksoap to your composer.json file:

require : {
    "mahbubcsedu/laraturksoap": "dev-master"
}
or with a composer command:

composer require "mahbubcsedu/laraturksoap": "dev-master"
After installation, add the provider to your config/app.php providers:

'mahbubcsedu\TurkSoap\TurkSoapServiceProvider',
and the facade to config/app.php aliases:

'TurkSoap' => 'mahbubcsedu\turksoap\Facades\TurkSoap',
Configuring TurkSoap

First publish the config file:

php artisan vendor:publish
This will create a turksoap.php config file. There you can define default values of parameters used in all functions.

If you will only be creating one type of HIT, you should specify all the default values in the config file.

You will also need to set two environment variables which the laraturk.php config file uses: AWS_ROOT_ACCESS_KEY_ID and AWS_ROOT_SECRET_ACCESS_KEY. If these are not set and you try to use LaraTurk, LaraTurk will throw a LaraTurkException.
