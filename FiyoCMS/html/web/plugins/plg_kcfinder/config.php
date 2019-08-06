<?php

/** This file is part of KCFinder project
  *
  *      @desc Base configuration file
  *   @package KCFinder
  *   @version 2.51
  *    @author Pavel Tzonkov <pavelc@users.sourceforge.net>
  * @copyright 2010, 2011 KCFinder Project
  *   @license http://www.opensource.org/licenses/gpl-2.0.php GPLv2
  *   @license http://www.opensource.org/licenses/lgpl-2.1.php LGPLv2
  *      @link http://kcfinder.sunhater.com
  */

// IMPORTANT!!! Do not remove uncommented settings in this file even if
// you are using session configuration.
// See http://kcfinder.sunhater.com/install for setting descriptions
if(empty($_SESSION['media_theme']))	$_SESSION['media_theme']="oxygen";
$_SESSION['media_dir'] = "media";
$_CONFIG = array(
    'disabled' => false,
    'denyZipDownload' => false,
    'denyUpdateCheck' => true,
    'denyExtensionRename' => false,
    'theme' => "$_SESSION[media_theme]",
    'uploadURL' => "../../$_SESSION[media_dir]",
    'uploadDir' => "../../$_SESSION[media_dir]",

    'dirPerms' => 0755,
    'filePerms' => 0644,
    'access' => array(

        'files' => array(
            'upload' => true,
            'delete' => true,
            'copy' => true,
            'move' => true,
            'rename' => true
        ),

        'dirs' => array(
            'create' => true,
            'delete' => true,
            'rename' => true
        )
    ),

    'deniedExts' => "exe com msi bat php phps phtml php3 php4 cgi pl",

    'types' => array(

        // CKEditor & FCKEditor types
		
        'images'  =>  "*img",
        'files'   =>  "",
        'flash'   =>  "swf",
        'video'   =>  "flv avi mpg mpeg mov wmv webm mp4",

        // TinyMCE types
        'file'    =>  "",
        'media'   =>  "swf flv avi mpg mpeg qt mov wmv asf rm zip rar iso",
        'image'   =>  "*img",
    ),

    'filenameChangeChars' => array(/*
        ' ' => "_",
        ':' => "."
    */),

    'dirnameChangeChars' => array(/*
        ' ' => "_",
        ':' => "."
    */),

    'mime_magic' => "",

    'maxImageWidth' => 0,
    'maxImageHeight' => 0,

    'thumbWidth' => 300,
    'thumbHeight' => 300,

    'thumbsDir' => ".thumbs",

    'jpegQuality' => 90,

    'cookieDomain' => "",
    'cookiePath' => "",
    'cookiePrefix' => 'KCFINDER_',

    // THE FOLLOWING SETTINGS CANNOT BE OVERRIDED WITH SESSION CONFIGURATION
    '_check4htaccess' => true,

    '_sessionVar' => &$_SESSION['KCFINDER'],
);

?>