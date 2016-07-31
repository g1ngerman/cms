<?php

abstract class IniSettings
{
    // Force Extending class to define this method
    abstract protected function setNewIniParam($current,$candidate,$iniPath);

    abstract protected function updateIni($search,$replace,$iniPath);


}