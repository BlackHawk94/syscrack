<?php
    namespace Framework\Syscrack\Game\Operations;

    /**
     * Lewis Lancaster 2017
     *
     * Class Execute
     *
     * @package Framework\Syscrack\Game\Operations
     */

    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
    use Framework\Syscrack\Game\Structures\Operation as Structure;
    use Framework\Syscrack\Game\Structures\Software;

    class Execute extends BaseClass implements Structure
    {

        /**
         * Execute constructor.
         */

        public function __construct()
        {

            parent::__construct(true);
        }

        /**
         * @return array
         */

        public function configuration()
        {

            return parent::configuration(); // TODO: Change the autogenerated stub
        }

        /**
         * Called when the software is executed
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

            if( $this->checkData( $data, ['ipaddress', 'softwareid' ] ) == false )
            {

                return false;
            }

            if( $this->softwares->canExecute( $data['softwareid'] ) == false )
            {

                $this->redirectError('Sorry, this software cannot be executed', $this->getRedirect( $data['ipaddress'] ) );
            }

            if( $this->softwares->isInstalled( $data['softwareid'], $this->getComputerId( $data['ipaddress'] ) ) == false )
            {

                return false;
            }

            if( $this->softwares->localExecuteOnly( $data['softwareid'] ) )
            {

                if( $this->computers->getComputer( $computerid )->ipaddress !== $data['ipaddress'] )
                {

                    $this->redirectError('This action can only be executed on your local computer', $this->getRedirect( $data['ipaddress'] ) );
                }
            }

            return true;
        }

        /**
         * On completion
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

            if( $this->checkData( $data, ['ipaddress', 'softwareid' ] ) == false )
            {

                throw new SyscrackException();
            }

            if( $this->internet->ipExists( $data['ipaddress'] ) == false )
            {

                $this->redirectError('Sorry, this ip address does not exist anymore', $this->getRedirect() );
            }

            if( $this->softwares->softwareExists( $data['softwareid'] ) == false )
            {

                $this->redirectError('Sorry, it looks like this software might have been deleted', $this->getRedirect( $data['ipaddress'] ) );
            }

            $class = $this->softwares->getSoftwareClassFromID( $data['softwareid'] );

            if( $class instanceof Software == false )
            {

                throw new SyscrackException();
            }

            $result = $class->onExecuted( $data['softwareid'], $userid, $this->getComputerId( $data['ipaddress'] ) );

            if( $result == false )
            {

                $this->redirectError('Failed to execute software', $this->getRedirect( $data['ipaddress'] ) );
            }

            $this->redirectSuccess( $this->getRedirect( $data['ipaddress'] ) );
        }

        /**
         * Gets the completion speed
         *
         * @param $computerid
         *
         * @param $ipaddress
         *
         * @param null $softwareid
         *
         * @return mixed|null
         */

        public function getCompletionSpeed($computerid, $ipaddress, $softwareid = null)
        {

            if( $softwareid == null )
            {

                throw new SyscrackException();
            }

            if( $this->softwares->softwareExists( $softwareid ) == false )
            {

                throw new SyscrackException();
            }

            $class = $this->softwares->getSoftwareClassFromID( $softwareid );

            if( $class instanceof Software == false )
            {

                throw new SyscrackException();
            }

            return $class->getExecuteCompletionTime( $softwareid, $computerid );
        }

        public function getCustomData($ipaddress, $userid)
        {
            // TODO: Implement getCustomData() method.
        }

        public function onPost($data, $ipaddress, $userid)
        {
            // TODO: Implement onPost() method.
        }
    }