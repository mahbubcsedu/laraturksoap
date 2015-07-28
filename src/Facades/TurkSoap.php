<?php mahbubcsedu\laraturksoap\Facades;
/**
 * Created by PhpStorm.
 * User: mahbub
 * Date: 7/28/15
 * Time: 2:36 AM
 */

use \Illuminate\Support\Facades\Facade;

class TurkSoap extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'turksoap';
    }

}