<?php
namespace Framework\Syscrack\Game\Operations;

/**
 * Lewis Lancaster 2017
 *
 * Class Logout
 *
 * @package Framework\Syscrack\Game\Operations
 */

use Framework\Application\Settings;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
use Framework\Syscrack\Game\Structures\Operation as Structure;

class Logout extends BaseClass implements Structure
{

    /**
     * Logout constructor.
     */

    public function __construct()
    {

        parent::__construct();
    }

    /**
     * The configuration of this operation
     */

    public function configuration()
    {

        return array(
            'allowsoftwares'    => false,
            'allowlocal'        => false,
            'requireloggedin'   => true
        );
    }

    /**
     * Called when this process request is created
     *
     * @param $timecompleted
     *
     * @param $computerid
     *
     * @param $userid
     *
     * @param $process
     *
     * @param array $data
     *
     * @return mixed
     */

    public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
    {

        if( $this->checkData( $data, ['ipaddress'] ) == false )
        {

            return false;
        }

        if( $this->internet->hasCurrentConnection() == false )
        {

            return false;
        }

        if( $this->internet->getCurrentConnectedAddress() !== $data['ipaddress'] )
        {

            return false;
        }

        return true;
    }

    /**
     * Called when this process request is created
     *
     * @param $timecompleted
     *
     * @param $computerid
     *
     * @param $userid
     *
     * @param $process
     *
     * @param array $data
     */

    public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
    {

        if( $this->checkData( $data, ['ipaddress'] ) == false )
        {

            throw new SyscrackException();
        }

        $this->internet->setCurrentConnectedAddress( null );

        if( Settings::hasSetting('syscrack_operations_safeunset') )
        {

            $this->safeUnset();
        }

        $this->redirectSuccess( $this->getRedirect() . '/internet' );
    }

    /**
     * Gets the custom data for this operation
     *
     * @param $ipaddress
     *
     * @param $userid
     *
     * @return array
     */

    public function getCustomData($ipaddress, $userid)
    {

        return array();
    }

    /**
     * Called upon a post request to this operation
     *
     * @param $data
     *
     * @param $ipaddress
     *
     * @param $userid
     *
     * @return bool
     */

    public function onPost($data, $ipaddress, $userid)
    {

        return true;
    }

    /**
     * Gets the completion time
     *
     * @param $computerid
     *
     * @param $ipaddress
     *
     * @param null $softwareid
     *
     * @return null
     */

    public function getCompletionSpeed($computerid, $ipaddress, $softwareid=null )
    {

        return null;
    }
}