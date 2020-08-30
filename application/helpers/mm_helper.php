<?php

class MM
{
    private static $u;
    private static $p;

    static function deposit($number, $amount)
    {
        // LOGIC FOR DEPOSITING
        return ["external" => 12234567];
        // TODO :: 
        /**
         * Have to return an array with the references
         * OR Throw an error on FAILURE
         * more so external
         * 
         *  */
    }

    static function status($mm_id)
    {
        // LOGIC FOR CHECKING MM TRANSACTION STATUS
        // TODO :: 
        return ["status" => 'PENDING'];
        /**
         * Have to return an array with the status
         * OR Throw an error on FAILURE
         * 
         *  */
    }
}
