<?php
//
// Copyright (C) 1999-2005 MyioSoft. All rights reserved.
//
// EasyCPUInfo ia s free script under "GNU General Public License" (GPL)
//
// Usage: If you are unterested to view or show the parameters
// of the processor hosting your website as CPU type, CPU speed
// or Available Memory you can upload somewhere under your server
// the file easycpuinfo.php and point the browser to this file.
// It will show the above CPU information.
//
// Install: Just upload somewhere under your server
// this file easycpuinfo.php and point the browser to it.
// It will show the CPU information mentioned above.

// Requirements: The only requiremet for your server is to support
// PHP (version PHP5 not supported) which version is also shown.
//
// Credits: The script is based on the classes eZSysInfo and eZSys
// isolated from the free GPL version of the popular ezPublush software:
// eZ publish (tm) Open Source Content Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE included in
// the packaging of this file.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// The class eZSysInfo supports the 'attribute' system and be used directly as a template variable.
// The class eZSys figures out the OS type.
//
define( "EZ_SYS_DEBUG_INTERNALS", false );
class eZSys
{
    function eZSys()
    {
        $this->Attributes = array( "magickQuotes" => true,
                                   "hostname" => true );
        $uname = php_uname();
        if ( substr( $uname, 0, 7 ) == "Windows" )
        {
            $this->OSType = "win32";
            $this->OS = "windows";
            $this->FileSystemType = "win32";
            $this->FileSeparator = "\\";
            $this->LineSeparator= "\r\n";
            $this->EnvSeparator = ";";
            $this->ShellEscapeCharacter = '"';
            $this->BackupFilename = '.bak';
        }
        else if ( substr( $uname, 0, 3 ) == "Mac" )
        {
            $this->OSType = "mac";
            $this->OS = "mac";
            $this->FileSystemType = "unix";
            $this->FileSeparator = "/";
            $this->LineSeparator= "\r";
            $this->EnvSeparator = ":";
            $this->ShellEscapeCharacter = "'";
            $this->BackupFilename = '~';
        }
        else
        {
            $this->OSType = "unix";
            if ( strtolower( substr( $uname, 0, 5 ) ) == 'linux' )
            {
                $this->OS = 'linux';
            }
            else if ( strtolower( substr( $uname, 0, 0 ) ) == 'freebsd' )
            {
                $this->OS = 'freebsd';
            }
            else
            {
                $this->OS = false;
            }
            $this->FileSystemType = "unix";
            $this->FileSeparator = "/";
            $this->LineSeparator= "\n";
            $this->EnvSeparator = ":";
            $this->ShellEscapeCharacter = "'";
            $this->BackupFilename = '~';
        }

        $magicQuote = get_magic_quotes_gpc();

        if ( $magicQuote == 1 )
        {
            eZSys::removeMagicQuotes();
        }
    }

    function removeMagicQuotes()
    {
        $globalVariables = array( '_SERVER', '_ENV' );
        foreach ( $globalVariables as $globalVariable )
        {
            foreach ( array_keys( $GLOBALS[$globalVariable] ) as $key )
            {
                if ( !is_array( $GLOBALS[$globalVariable][$key] ) )
                {
                    $GLOBALS[$globalVariable][$key] = stripslashes( $GLOBALS[$globalVariable][$key] );
                }
            }
        }
    }

    function osType()
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        return $this->OSType;
    }

    function osName()
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        return $this->OS;
    }

    function filesystemType()
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        return $this->FileSystemType;
    }

    function fileSeparator()
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        return $this->FileSeparator;
    }

    function phpVersionText()
    {
        return phpversion();
    }

    function phpVersion()
    {
        $text = eZSys::phpVersionText();
        $elements = explode( '.', $text );
        return $elements;
    }

    function isPHPVersionSufficient( $requiredVersion )
    {
        if ( !is_array( $requiredVersion ) )
            return false;
        $phpVersion = eZSys::phpVersion();
        $len = min( count( $phpVersion ), count( $requiredVersion ) );
         for ( $i = 0; $i < $len; ++$i )
        {
            if ( $phpVersion[$i] > $requiredVersion[$i] )
                return true;
            if ( $phpVersion[$i] < $requiredVersion[$i] )
                return false;
        }
        return true;
    }

    function isShellExecution()
    {
        $sapiType = php_sapi_name();

        if ( $sapiType == 'cli' )
            return true;

        if ( substr( $sapiType, 0, 3 ) == 'cgi' )
        {
            if ( !eZSys::serverVariable( 'HTTP_HOST', true ) )
                return true;
            else
                return false;
        }
        return false;
    }

    function escapeShellArgument( $argument )
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        $escapeChar = $this->ShellEscapeCharacter;
        $argument = str_replace( "\\", "\\\\", $argument );
        $argument = str_replace( $escapeChar, "\\" . $escapeChar, $argument );
        $argument = $escapeChar . $argument . $escapeChar;
        return $argument;
    }

    function createShellArgument( $argumentText, $replaceList )
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        $elements = $this->splitArgumentIntoElements( $argumentText );
        $replacedElements = array();
        foreach ( $elements as $element )
        {
            if ( is_string( $element ) )
            {
                $replacedElements[] = strtr( $element, $replaceList );
                continue;
            }
            $replacedElements[] = $element;
        }
        $text = $this->mergeArgumentElements( $replacedElements );
        return $text;
    }

    function splitArgumentIntoElements( $argumentText )
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        $argumentElements = array();
        $pos = 0;

        while ( $pos < strlen( $argumentText ) )
        {
            if ( $argumentText[$pos] == '"' )
            {
                $quoteStartPos = $pos + 1;
                $quoteEndPos = $pos + 1;
                while ( $quoteEndPos < strlen( $argumentText ) )
                {
                    $tmpPos = strpos( $argumentText, '"', $quoteEndPos );
                    if ( $tmpPos !== false and
                         $argumentText[$tmpPos - 1] != "\\" );
                    {
                        $quoteEndPos = $tmpPos;
                        break;
                    }
                    if ( $tmpPos === false )
                    {
                        $quoteEndPos = strlen( $argumentText );
                        break;
                    }
                    $quoteEndPos = $tmpPos + 1;
                }
                $argumentElements[] = substr( $argumentText, $quoteStartPos, $quoteEndPos - $quoteStartPos );
                $pos = $quoteEndPos + 1;
            }
            else if ( $argumentText[$pos] == ' ' )
            {
                $spacePos = $pos;
                $spaceEndPos = $pos;
                while ( $spaceEndPos < strlen( $argumentText ) )
                {
                    if ( $argumentText[$spaceEndPos] != ' ' )
                        break;
                    ++$spaceEndPos;
                }
                $spaceText = substr( $argumentText, $spacePos, $spaceEndPos - $spacePos );
                $spaceCount = strlen( $spaceText );
                if ( $spaceCount > 0 )
                    $argumentElements[] = $spaceCount;
                $pos = $spaceEndPos;
            }
            else
            {
                $spacePos = strpos( $argumentText, ' ', $pos );
                if ( $spacePos !== false )
                {
                    $argumentElements[] = substr( $argumentText, $pos, $spacePos - $pos );
                    $spaceEndPos = $spacePos + 1;
                    while ( $spaceEndPos < strlen( $argumentText ) )
                    {
                        if ( $argumentText[$spaceEndPos] != ' ' )
                            break;
                        ++$spaceEndPos;
                    }
                    $spaceText = substr( $argumentText, $spacePos, $spaceEndPos - $spacePos );
                    $spaceCount = strlen( $spaceText );
                    if ( $spaceCount > 0 )
                        $argumentElements[] = $spaceCount;
                    $pos = $spaceEndPos;
                }
                else
                {
                    $argumentElements[] = substr( $argumentText, $pos );
                    $pos = strlen( $argumentText );
                }
            }
        }
        return $argumentElements;
    }

    function mergeArgumentElements( $argumentElements )
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        $argumentText = '';
        foreach ( $argumentElements as $element )
        {
            if ( is_int( $element ) )
            {
                $argumentText .= str_repeat( ' ', $element );
            }
            else if ( is_string( $element ) )
            {
                $argumentText .= $this->escapeShellArgument( $element );
            }
        }
        return $argumentText;
    }

    function backupFilename()
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        return $this->BackupFilename;
    }

    function lineSeparator()
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        return $this->LineSeparator;
    }

    function envSeparator()
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        return $this->EnvSeparator;
    }

    function varDirectory()
    {
        include_once( 'lib/ezutils/classes/ezini.php' );
        $ini =& eZINI::instance();
        include_once( 'lib/ezfile/classes/ezdir.php' );
        return eZDir::path( array( $ini->variable( 'FileSettings', 'VarDir' ) ) );
    }

    function storageDirectory()
    {
        include_once( 'lib/ezutils/classes/ezini.php' );
        include_once( 'lib/ezfile/classes/ezdir.php' );
        $ini =& eZINI::instance();
        $varDir = eZSys::varDirectory();
        $storageDir = $ini->variable( 'FileSettings', 'StorageDir' );
        return eZDir::path( array( $varDir, $storageDir ) );
    }

    function cacheDirectory()
    {
        include_once( 'lib/ezutils/classes/ezini.php' );
        $ini =& eZINI::instance();
        $cacheDir = $ini->variable( 'FileSettings', 'CacheDir' );

        include_once( 'lib/ezfile/classes/ezdir.php' );
        if ( $cacheDir[0] == "/" )
        {
            return eZDir::path( array( $cacheDir ) );
        }
        else
        {
            return eZDir::path( array( eZSys::varDirectory(), $cacheDir ) );
        }
    }

    function rootDir()
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        if ( $this->RootDir )
        {
            return $this->RootDir;
        }
        $cwd  = getcwd();
        $self  = $this->serverVariable( 'PHP_SELF' );
        if ( file_exists( $cwd.$this->FileSeparator.$self ) )
        {
            $this->RootDir = $cwd;
        }
        else if ( file_exists( $cwd.$this->FileSeparator.$this->IndexFile ) )
        {
            $this->Root = $cwd;
        }
        else
        {
            $this->RootDir=null;
        }
        return $this->RootDir;
    }

    function &siteDir()
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        return $this->SiteDir;
    }

    function &wwwDir()
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        return $this->WWWDir;
    }

    function &indexDir( $withAccessList = true )
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        return $this->wwwDir() . $this->indexFile( $withAccessList );
    }

    function &indexFile( $withAccessList = true )
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        $text = $this->IndexFile;

        if ( $withAccessList and count( $this->AccessPath ) > 0 )
        {
                $text .= '/' . implode( '/', $this->AccessPath );
        }
        return $text;
    }

    function indexFileName()
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        return $this->IndexFile;
    }

    function &hostname()
    {
        return eZSys::serverVariable( 'HTTP_HOST' );
    }

    function serverPort()
    {
        $port =& $GLOBALS['eZSysServerPort'];
        if ( !isset( $port ) )
        {
            $port = eZSys::serverVariable( 'SERVER_PORT' );
            $hostname = eZSys::serverVariable( 'HTTP_HOST' );
            if ( preg_match( "/.*:([0-9]+)/", $hostname, $regs ) )
            {
                $port = $regs[1];
            }
        }
        return $port;
    }

    function &magickQuotes()
    {
    }

    function &serverVariable( $variableName, $quiet = false )
    {
        if ( !isset( $_SERVER[$variableName] ) )
        {
            if ( !$quiet )
                eZDebug::writeError( "Server variable '$variableName' does not exist", 'eZSys::serverVariable' );
            return null;
        }
        return $_SERVER[$variableName];
    }

    function setServerVariable( $variableName, $variableValue )
    {
        $_SERVER;
        $_SERVER[$variableName] = $variableValue;
    }

    function &path( $quiet = false )
    {
        return eZSys::serverVariable( 'PATH', $quiet );
    }

    function &environmentVariable( $variableName, $quiet = false )
    {
        $_ENV;
        if ( !isset( $_ENV[$variableName] ) )
        {
            if ( !$quiet )
                eZDebug::writeError( "Environment variable '$variableName' does not exist", 'eZSys::environmentVariable' );
            return null;
        }
        return $_ENV[$variableName];
    }

    function setEnvironmentVariable( $variableName, $variableValue )
    {
        $_ENV;
        $_ENV[$variableName] = $variableValue;
    }

    function hasAttribute( $attr )
    {
        return ( isset( $this->Attributes[$attr] )
                 or $attr == "wwwdir"
                 or $attr == "sitedir"
                 or $attr == "indexfile"
                 or $attr == "indexdir" );
    }

    function &attribute( $attr )
    {
        if ( isset( $this->Attributes[$attr] ) )
        {
            $mname = $attr;
            return $this->$mname();
        }
        else if ( $attr == 'wwwdir' )
        {
            return $this->wwwDir();
        }
        else if ( $attr == 'sitedir' )
        {
            return $this->siteDir();
        }
        else if ( $attr == 'indexfile' )
        {
            return $this->indexFile();
        }
        else if ( $attr == 'indexdir' )
        {
            return $this->indexDir();
        }
        else
        {
            return null;
        }
    }

    function addAccessPath( $path )
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        if ( !is_array( $path ) )
            $path = array( $path );
        $this->AccessPath = array_merge( $this->AccessPath, $path );
    }

    function clearAccessPath()
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        $this->AccessPath = array();
    }

    function isDebugEnabled()
    {
        if ( !isset( $GLOBALS['eZSysDebugInternalsEnabled'] ) )
             $GLOBALS['eZSysDebugInternalsEnabled'] = EZ_SYS_DEBUG_INTERNALS;
        return $GLOBALS['eZSysDebugInternalsEnabled'];
    }

    function setIsDebugEnabled( $debug )
    {
        $GLOBALS['eZSysDebugInternalsEnabled'] = $debug;
    }

    function init( $def_index = "index.php", $force_VirtualHost = false )
    {
        $isCGI = ( php_sapi_name() == 'cgi' );

        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();

        if ( eZSys::isDebugEnabled() )
        {
            eZDebug::writeNotice( eZSys::serverVariable( 'PHP_SELF' ), 'PHP_SELF' );
            eZDebug::writeNotice( eZSys::serverVariable( 'SCRIPT_FILENAME' ), 'SCRIPT_FILENAME' );
            eZDebug::writeNotice( eZSys::serverVariable( 'DOCUMENT_ROOT' ), 'DOCUMENT_ROOT' );
            eZDebug::writeNotice( eZSys::serverVariable( 'REQUEST_URI' ), 'REQUEST_URI' );
            eZDebug::writeNotice( eZSys::serverVariable( 'QUERY_STRING' ), 'QUERY_STRING' );
            eZDebug::writeNotice( ini_get( 'include_path' ), 'include_path' );
        }

        $phpSelf = eZSys::serverVariable( 'PHP_SELF' );

        if ( ereg( "(.*/)([^\/]+\.php)$", eZSys::serverVariable( 'SCRIPT_FILENAME' ), $regs ) )
        {
            $siteDir = $regs[1];
            $index = "/" . $regs[2];
        }
        elseif ( ereg( "(.*/)([^\/]+\.php)/?", $phpSelf, $regs ) )
        {
            $siteDir = eZSys::serverVariable( 'DOCUMENT_ROOT' ) . $regs[1];
            $index = "/" . $regs[2];
        }
        else
        {
            $siteDir = "./";
            $index = "/$def_index";
        }
        if ( $isCGI and !$force_VirtualHost )
        {
            $index .= '?';
        }

        $includePath = ini_get( "include_path" );
        if ( trim( $includePath ) != "" )

            $includePath = $siteDir . $this->envSeparator() . $includePath;

        else
            $includePath = $siteDir;
        ini_set( "include_path", $includePath );

        $scriptName = eZSys::serverVariable( 'SCRIPT_NAME' );

        $wwwDir = "";

        if ( $force_VirtualHost )
        {
            $wwwDir = "";
        }
        else
        {
            if ( ereg( "(.*)/([^\/]+\.php)$", $scriptName, $regs ) )
                $wwwDir = $regs[1];
            else if ( ereg( "(.*)/([^\/]+\.php)$", $phpSelf, $regs ) )
                $wwwDir = $regs[1];
        }

        if ( ! $isCGI || $force_VirtualHost )
        {
            $requestURI = eZSys::serverVariable( 'REQUEST_URI' );
        }
        else
        {
            $requestURI = eZSys::serverVariable( 'QUERY_STRING' );

            if ( preg_match( "/(.*)&PHPSESSID=[^&]+(.*)/", $requestURI, $matches ) )
            {
                $requestURI = $matches[1].$matches[2];
            }
        }

        if ( $siteDir == "./" )
            $phpSelf = $requestURI;

        if ( ! $isCGI )
        {
            $def_index_reg = str_replace( ".", "\\.", $def_index );
            if ( ! ereg( ".*$def_index_reg.*", $phpSelf ) || $force_VirtualHost )
            {
                $index = "";
            }
            else
            {
                if ( eZSys::isDebugEnabled() )
                    eZDebug::writeNotice( "$wwwDir$index", '$wwwDir$index' );
                if ( ereg( "^$wwwDir$index(.*)", $phpSelf, $req ) )
                {
                    if (! $req[1] )
                    {
                        if ( $phpSelf != "$wwwDir$index" and ereg( "^$wwwDir(.*)", $requestURI, $req ) )
                        {
                            $requestURI = $req[1];
                            $index = '';
                        }
                        elseif ( $phpSelf == "$wwwDir$index" and ereg( "^$wwwDir$index(.*)", $requestURI, $req ) or
                                 ereg( "^$wwwDir(.*)", $requestURI, $req ) )
                        {
                            $requestURI = $req[1];
                        }
                    }
                    else
                    {
                        $requestURI = $req[1];
                    }
                }
            }
        }
        if ( $isCGI and $force_VirtualHost )
            $index = '';
        if ( $isCGI and !$force_VirtualHost )
        {
            $pattern = "(\/[^&]+)";
        }
        else
        {
            $pattern = "([^?]+)";
        }
        if ( ereg( $pattern, $requestURI, $regs ) )
        {
            $requestURI = $regs[1];
        }

        if ( ereg( "([^#]+)", $requestURI, $regs ) )
        {
            $requestURI = $regs[1];
        }

        $this->AccessPath = array();
        $this->SiteDir =& $siteDir;
        $this->WWWDir =& $wwwDir;
        $this->IndexFile =& $index;
        $this->RequestURI = $requestURI;

        if ( eZSys::isDebugEnabled() )
        {
            eZDebug::writeNotice( $this->SiteDir, 'SiteDir' );
            eZDebug::writeNotice( $this->WWWDir, 'WWWDir' );
            eZDebug::writeNotice( $this->IndexFile, 'IndexFile' );
            eZDebug::writeNotice( eZSys::requestURI(), 'eZSys::requestURI()' );
        }

    }

    function requestURI()
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
        return $this->RequestURI;
    }

    function initIni( &$ini )
    {
        if ( !isset( $this ) or get_class( $this ) != "ezsys" )
            $this =& eZSys::instance();
    }

    function &instance()
    {
        $instance =& $GLOBALS["eZSysInstance"];
        if ( get_class( $instance ) != "ezsys" )
        {
            $instance = new eZSys();
        }
        return $instance;
    }

    var $LineSeparator;
    var $FileSeparator;
    var $EnvSeparator;
    var $RootDir;
    var $SiteDir;
    var $AccessPath;
    var $WWWDir;
    var $IndexFile;
    var $RequestURI;
    var $FileSystemType;
    var $ShellEscapeCharacter;
    var $OSType;
}


class eZSysInfo
{
    function eZSysInfo()
    {
    }

    function attributes()
    {
        return array( 'is_valid', 'cpu_type', 'cpu_unit', 'cpu_speed', 'memory_size' );
    }

    function hasAttribute( $name )
    {
        return in_array( $name, array( 'is_valid', 'cpu_type', 'cpu_unit', 'cpu_speed', 'memory_size' ) );
    }

    function &attribute( $name )
    {
        if ( $name == 'is_valid' )
            return $this->IsValid;
        else if ( $name == 'cpu_type' )
            return $this->CPUType;
        else if ( $name == 'cpu_unit' )
            return $this->CPUUnit;
        else if ( $name == 'cpu_speed' )
            return $this->CPUSpeed;
        else if ( $name == 'memory_size' )
            return $this->MemorySize;
        return null;
    }

    function isValid()
    {
        return $this->IsValid;
    }

    function cpuType()
    {
        return $this->CPUType;
    }

    function cpuSpeed()
    {
        return $this->CPUSpeed;
    }
    function cpuUnit()
    {
        return $this->CPUUnit;
    }

    function memorySize()
    {
        return $this->MemorySize;
    }

    function scan()
    {
        $this->IsValid = false;
        $this->CPUSpeed = false;
        $this->CPUType = false;
        $this->CPUUnit = false;
        $this->MemorySize = false;

        $sys =& eZSys::instance();
        $osType = $sys->osType();

        if ( $osType == 'win32' )
        {
        }
        else if ( $osType == 'mac' )
        {
            $this->IsValid = $this->scanDMesg();
            return $this->IsValid;
        }
        else if ( $osType == 'unix' )
        {
            $osName = $sys->osName();
            if ( $osName == 'linux' )
            {
                $this->IsValid = $this->scanProc();
                return $this->IsValid;
            }
            else if ( $osName == 'freebsd' )
            {
                $this->IsValid = $this->scanDMesg();
                return $this->IsValid;
            }
            else
            {
                if ( $this->scanProc() )
                {
                    $this->IsValid = true;
                    return true;
                }
                if ( $this->scanDMesg() )
                {
                    $this->IsValid = true;
                    return true;
                }
            }
        }
        return false;
    }

    function scanProc( $cpuinfoPath = false, $meminfoPath = false )
    {
        if ( !$cpuinfoPath )
            $cpuinfoPath = '/proc/cpuinfo';
        if ( !$meminfoPath )
            $meminfoPath = '/proc/meminfo';

        if ( !file_exists( $cpuinfoPath ) )
            return false;
        if ( !file_exists( $meminfoPath ) )
            return false;

        $fileLines = file( $cpuinfoPath );
        foreach ( $fileLines as $line )
        {
            if ( substr( $line, 0, 7 ) == 'cpu MHz' )
            {
                $cpu = trim( substr( $line, 11, strlen( $line ) - 11 ) );
                $this->CPUSpeed = $cpu;
                $this->CPUUnit = 'MHz';
            }
            if ( substr( $line, 0, 10 ) == 'model name' )
            {
                $system = trim( substr( $line, 13, strlen( $line ) - 13 ) );
                $this->CPUType = $system;
            }
            if ( $this->CPUSpeed !== false and
                 $this->CPUType !== false and
                 $this->CPUUnit !== false )
                break;
        }

        $fileLines = file( $meminfoPath );
        foreach ( $fileLines as $line )
        {
            if ( substr( $line, 0, 8 ) == 'MemTotal' )
            {
                $mem = trim( substr( $line, 11, strlen( $line ) - 11 ) );
                $memBytes = $mem;
                if ( preg_match( "#^([0-9]+) *([a-zA-Z]+)#", $mem, $matches ) )
                {
                    $memBytes = (int)$matches[1];
                    $unit = strtolower( $matches[2] );
                    if ( $unit == 'kb' )
                    {
                        $memBytes *= 1024;
                    }
                    else if ( $unit == 'mb' )
                    {
                        $memBytes *= 1024*1024;
                    }
                    else if ( $unit == 'gb' )
                    {
                        $memBytes *= 1024*1024*1024;
                    }
                }
                else
                {
                    $memBytes = (int)$memBytes;
                }
                $this->MemorySize = $memBytes;
            }
            if ( $this->MemorySize !== false )
                break;
        }

        return true;
    }

    function scanDMesg( $dmesgPath = false )
    {
        if ( !$dmesgPath )
            $dmesgPath = '/var/run/dmesg.boot';
        if ( !file_exists( $dmesgPath ) )
            return false;
        $fileLines = file( $dmesgPath );
        foreach ( $fileLines as $line )
        {
            if ( substr( $line, 0, 3 ) == 'CPU' )
            {
                $system = trim( substr( $line, 4, strlen( $line ) - 4 ) );
                $cpu = false;
                $cpuunit = false;
                if ( preg_match( "#^(.+)\\((.+)(MHz) +([^)]+)\\)#", $system, $matches ) )
                {
                    $system = trim( $matches[1] ) . ' (' . trim( $matches[4] ) . ')';
                    $cpu = $matches[2];
                    $cpuunit = $matches[3];
                }
                $this->CPUSpeed = $cpu;
                $this->CPUType = $system;
                $this->CPUUnit = $cpuunit;
            }
            if ( substr( $line, 0, 11 ) == 'real memory' )
            {
                $mem = trim( substr( $line, 12, strlen( $line ) - 12 ) );
                $memBytes = $mem;
                if ( preg_match( "#^= *([0-9]+)#", $mem, $matches ) )
                {
                    $memBytes = $matches[1];
                }
                $memBytes = (int)$memBytes;
                $this->MemorySize = $memBytes;
            }
            if ( $this->CPUSpeed !== false and
                 $this->CPUType !== false and
                 $this->CPUUnit !== false and
                 $this->MemorySize !== false )
                break;

        }
        return true;
    }

    var $IsValid = false;
    var $CPUSpeed = false;
    var $CPUType = false;
    var $CPUUnit = false;
    var $MemorySize = false;
}


  $info = new eZSysInfo();
  $info->scan();
  $info1 = new eZSys();

if(1==1){
$style0="style='border-collapse:collapse;border:none;mso-border-alt:solid #293088 1.5pt; mso-padding-alt:0in 5.4pt 0in 5.4pt;mso-border-insideh:.75pt solid #C8D8F9;color:white;border: 1'";
$style1="style='border:solid #293088 1.5pt;border-bottom:solid #C8D8F9 1.0pt; mso-border-alt:solid #293088 1.5pt;mso-border-bottom-alt:solid #C8D8F9 .75pt; background:#293088;mso-shading:white;mso-pattern:solid #293088;padding:0in 5.4pt 0in 5.4pt;color:white'";
$style2="style='border-top:none;border-left:solid #293088 1.5pt; border-bottom:solid #C8D8F9 1.0pt;border-right:none;mso-border-top-alt:solid #C8D8F9 .75pt; mso-border-top-alt:solid #C8D8F9 .75pt;mso-border-left-alt:solid #293088 1.5pt; mso-border-bottom-alt:solid #C8D8F9 .75pt;background:#293088;mso-shading:white; mso-pattern:solid #293088;padding:0in 5.4pt 0in 5.4pt;color:white'";
$style4="style='border-top:none;border-left:solid #293088 1.5pt; border-bottom:solid #293088 1.5pt;border-right:none;mso-border-top-alt:solid #C8D8F9 .75pt; background:#293088;mso-shading:white;mso-pattern:solid #293088;padding:0in 5.4pt 0in 5.4pt'";
$style3="style='border-top:none;border-left:none;border-bottom:solid #C8D8F9 1.0pt; border-right:solid #293088 1.5pt;mso-border-top-alt:solid #C8D8F9 .75pt;mso-border-top-alt: solid #C8D8F9 .75pt;mso-border-bottom-alt:solid #C8D8F9 .75pt;mso-border-right-alt: solid #293088 1.5pt;background:#293088;mso-shading:white;mso-pattern:solid #293088; padding:0in 5.4pt 0in 5.4pt;color:white'";
$style5="style='border-top:none;border-left:none;border-bottom:solid #293088 1.5pt; border-right:solid #293088 1.5pt;mso-border-top-alt:solid #C8D8F9 .75pt;background: #293088;mso-shading:white;mso-pattern:solid #293088;padding:0in 5.4pt 0in 5.4pt'";
}
echo "
<table cellspacing=0 cellpadding=0 ".$style0.">
 <tr>
  <td colspan=2 valign=top ".$style1." align=center>
   <p>".$info1->hostname()." CPU Information</p>
  </td>
 </tr>
 <tr>
  <td valign=top align=right ".$style2.">
   <p>CPU type: </p>
  </td>
  <td valign=top ".$style3.">
   <p>&nbsp;".$info->CPUType()."</p>
  </td>
 </tr>
 <tr>
  <td valign=top align=right  ".$style2.">
   <p>CPU speed: </p>
  </td>
  <td valign=top align=left  ".$style3.">
   <p>&nbsp;".$info->CPUSpeed()." ".$info->CPUUnit()."</p>
  </td>
 </tr>
 <tr>
  <td valign=top align=right  ".$style2.">
   <p>Memory Size: </p>
  </td>
  <td valign=top align=left ".$style3.">
   <p>&nbsp;".$info->memorySize()." KB</p>
  </td>
 </tr>
 <tr>
 <tr>
  <td valign=top align=right  ".$style2.">
   <p>OS type: </p>
  </td>
  <td valign=top align=left  ".$style3.">
   <p>&nbsp;".$info1->ostype()."</p>
  </td>
 </tr>
  <td valign=top align=center  ".$style4.">
   <p><a href='http://myiosoft.com'><font color=#FFFFFF>MyioSoft</font></a></p>
  </td>
  <td valign=top align=left  ".$style5.">
   <p>&nbsp;Data collected using PHP ".$info1->phpVersionText()."</p>
  </td>
 </tr>
</table>
";




?>


