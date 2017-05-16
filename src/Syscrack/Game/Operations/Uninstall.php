<?php
    namespace Framework\Syscrack\Game\Operations;

    /**
     * Lewis Lancaster 2017
     *
     * Class Uninstall
     *
     * @package Framework\Syscrack\Game\Operations
     */

    use Framework\Application\Settings;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
    use Framework\Syscrack\Game\Structures\Operation as Structure;

    class Uninstall extends BaseClass implements Structure
    {

        /**
         * Uninstall constructor.
         */

        public function __construct()
        {

            parent::__construct();
        }

        /**
         * The configuration for this operation
         *
         * @return array
         */

        public function configuration()
        {

            return array(
                'allowsoftwares'    => true,
                'allowlocal'        => true,
                'requiresoftwares'  => true,
                'requireloggedin'   => true
            );
        }

        /**
         * Called when the operation is created
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
         * @return bool
         */

        public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
        {

            if( $this->checkData( $data ) == false )
            {

                return false;
            }

            if( $this->softwares->softwareExists( $data['softwareid'] ) == false )
            {

                return false;
            }
            else
            {

                if( $this->computer->hasSoftware( $this->getComputerId( $data['ipaddress'] ), $data['softwareid'] ) == false )
                {

                    return false;
                }
                else
                {

                    if( $this->softwares->canUninstall( $data['softwareid'] ) == false )
                    {

                        return false;
                    }
                }
            }

            return true;
        }

        /**
         * Called when the operation is completed
         *
         * @param $timecompleted
         *
         * @param $timestarted
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

            if( $this->checkData( $data ) == false )
            {

                throw new SyscrackException();
            }

            if( $this->softwares->softwareExists( $data['softwareid'] ) == false )
            {

                $this->redirectError('Sorry, it looks like this software might have been deleted', $this->getRedirect( $data['ipaddress'] ) );
            }

            if( $this->softwares->isInstalled( $data['softwareid'], $this->getComputerId( $data['ipaddress'] ) ) == false )
            {

                $this->redirectError('Sorry, it looks like this software got uninstalled already', $this->getRedirect( $data['ipaddress'] ) );
            }

            $this->softwares->uninstallSoftware( $data['softwareid'] );

            $this->computer->uninstallSoftware( $this->getComputerId( $data['ipaddress'] ), $data['softwareid'] );

            $this->logUninstall( $this->getSoftwareName( $data['softwareid' ] ),
                $this->getComputerId( $data['ipaddress'] ),$this->getCurrentComputerAddress() );

            $this->logLocal( $this->getSoftwareName( $data['softwareid' ] ),
                $this->computer->getCurrentUserComputer(), $data['ipaddress']);

            $this->softwares->executeSoftwareMethod( $this->softwares->getSoftwareNameFromSoftwareID( $data['softwareid'] ), 'onUninstalled', array(
                'softwareid'    => $data['softwareid'],
                'userid'        => $userid,
                'computerid'    => $this->getComputerId( $data['ipaddress'] )
            ));

            if( isset( $data['redirect'] ) )
            {

                $this->redirectSuccess( $data['redirect'] );
            }
            else
            {

                $this->redirectSuccess( $this->getRedirect( $data['ipaddress'] ) );
            }
        }

        /**
         * Gets the completion speed of this operation
         *
         * @param $computerid
         *
         * @param $ipaddress
         *
         * @param null $softwareid
         *
         * @return int
         */

        public function getCompletionSpeed($computerid, $ipaddress, $softwareid=null)
        {

            return $this->calculateProcessingTime( $computerid, Settings::getSetting('syscrack_hardware_cpu_type'), 20, $softwareid );
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
         * Logs a login action to the computers log
         *
         * @param $computerid
         *
         * @param $ipaddress
         */

        private function logUninstall( $softwarename, $computerid, $ipaddress )
        {

            if( $this->computer->getCurrentUserComputer() == $computerid )
            {

                return;
            }

            $this->logToComputer('Uninstalled file (' . $softwarename . ') on root', $computerid, $ipaddress );
        }

        /**
         * Logs to the computer
         *
         * @param $computerid
         *
         * @param $ipaddress
         */

        private function logLocal( $softwarename, $computerid, $ipaddress )
        {

            $this->logToComputer('Uninstalled file (' . $softwarename . ') on <' . $ipaddress . '>', $computerid, 'localhost' );
        }
    }