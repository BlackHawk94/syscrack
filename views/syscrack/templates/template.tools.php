<?php

    use Framework\Syscrack\Game\Computer;
    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\Softwares;
    use Framework\Syscrack\Game\Utilities\PageHelper;

    if( empty( $internet ) )
    {

        $internet = new Internet();
    }

    if( empty( $pagehelper ) )
    {

        $pagehelper = new PageHelper();
    }

    if( empty( $computer ) )
    {

        $computer = new Computer();
    }

    if( empty( $softwares ) )
    {

        $softwares = new Softwares();
    }
?>
<div class="col-md-4">

    <?php

        if( $computer->getComputer( $computer->getCurrentUserComputer() )->ipaddress == $ipaddress )
        {

            ?>

            <div class="panel panel-success">
                <div class="panel-body text-center">
                    You are currently viewing yourself.
                </div>
            </div>
            <?php
        }
        elseif( $pagehelper->alreadyHacked( $ipaddress ) )
        {

            if( $pagehelper->isCurrentlyConnected( $ipaddress ) == false )
            {

                ?>

                    <div class="panel panel-success">
                        <div class="panel-body text-center">
                            This computer is already in your hacked database.
                        </div>
                    </div>
                    <form action="/game/internet/<?=$ipaddress?>/login" method="get">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <button style="width: 100%;" class="btn btn-success" type="submit">
                                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Login
                                </button>
                            </div>
                        </div>
                    </form>
                <?php
            }
            else
            {

                ?>
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            Computer Information
                        </div>
                        <div class="panel-body">
                            <small style="color:lightslategray;" class="text-uppercase">Hardware</small>
                            <div class="well">
                                <?=$internet->getComputer($ipaddress)->hardwares?>
                            </div>
                        </div>
                    </div>
                    <form action="/game/internet/<?=$ipaddress?>/upload" method="post">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <select name="softwareid" class="combobox input-sm form-control">
                                    <option></option>

                                    <?php

                                        $computersoftwares = $computer->getComputerSoftware( $computer->getCurrentUserComputer() );

                                        if( empty( $computersoftwares ) == false )
                                        {

                                            foreach( $computersoftwares as $key=>$value )
                                            {

                                                $software = $softwares->getSoftware( $value['softwareid'] );

                                                $extension = $softwares->getSoftwareExtension( $softwares->getSoftwareNameFromSoftwareID( $value['softwareid'] ) );

                                                echo('<option value="' . $software->softwareid . '">' . $software->softwarename . $extension . ' ' . $software->size . 'mb (' . $software->level . ')' . '</option>');
                                            }

                                        }
                                    ?>
                                </select>
                                <button style="width: 100%; margin-top: 2.5%;" class="btn btn-primary" type="submit">
                                    <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span> Upload
                                </button>
                            </div>
                        </div>
                    </form>
                    <form action="/game/internet/<?=$ipaddress?>/logout">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <button style="width: 100%;" class="btn btn-danger" type="submit">
                                    <span class="glyphicon glyphicon-alert" aria-hidden="true"></span> Logout
                                </button>
                            </div>
                        </div>
                    </form>
                <?php
            }
        }
        else
        {

            if( $pagehelper->getInstalledCracker() !== null )
            {

                ?>

                    <form action="/game/internet/<?=$ipaddress?>/hack">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <button style="width: 100%;" class="btn btn-primary" type="submit"
                                    <?php if( $pagehelper->getInstalledCracker() == null ){ echo 'disabled'; }?> >
                                    <span class="glyphicon glyphicon-lock" aria-hidden="true"></span> Hack <span class="badge"><?=$pagehelper->getSoftwareLevel( $pagehelper->getInstalledCracker()['softwareid'] )?></span>
                                </button>
                            </div>
                        </div>
                    </form>
                <?php
            }
            else
            {

                ?>

                    <div class="panel panel-danger">
                        <div class="panel-body">
                            You currently don't have a cracker, you should probably get one...
                        </div>
                    </div>
                <?php
            }
        }
    ?>
</div>