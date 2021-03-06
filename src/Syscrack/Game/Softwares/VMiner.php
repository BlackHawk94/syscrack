<?php
    namespace Framework\Syscrack\Game\Softwares;

/**
 * Lewis Lancaster 2017
 *
 * Class VMiner
 *
 * @package Framework\Syscrack\Game\Softwares
 */

use Framework\Application\Settings;
use Framework\Syscrack\Game\BaseClasses\Software as BaseClass;
use Framework\Syscrack\Game\Structures\Software as Structure;

class VMiner extends BaseClass implements Structure
{

    /**
     * The configuration of this Structure
     *
     * @return array
     */

    public function configuration()
    {

        return array(
            'uniquename'    => 'vminer',
            'extension'     => '.vminer',
            'type'          => 'virus',
            'installable'   => true,
            'uninstallable' => true,
            'executable'    => false,
            'removable'    => false
        );
    }

    public function onExecuted( $softwareid, $userid, $computerid )
    {


    }

    public function onInstalled( $softwareid, $userid, $computerid )
    {


    }

    public function onUninstalled($softwareid, $userid, $computerid)
    {
        // TODO: Implement onUninstalled() method.
    }

    public function onCollect( $softwareid, $userid, $computerid, $timeran )
    {

        if( $this->hardware->hasHardwareType( $computerid, Settings::getSetting('syscrack_hardware_cpu_type') ) == false )
            {

                return Settings::getSetting('syscrack_collector_vspam_yield') * $timeran;
            }

        return ( Settings::getSetting('syscrack_collector_vspam_yield') * ( $this->hardware->getCPUSpeed( $computerid ) * $timeran ) ) / Settings::getSetting('syscrack_collector_global_yield');
    }

    public function getExecuteCompletionTime($softwareid, $computerid)
    {

        return null;
    }

    /**
     * Default size of 10.0
     *
     * @return float
     */

    public function getDefaultSize()
    {

        return 12.0;
    }

    /**
     * Default level of 1.0
     *
     * @return float
     */

    public function getDefaultLevel()
    {

        return 1.5;
    }
}