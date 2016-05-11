<?php
/**
 * Git class
 *
 * @package      Mooti
 * @subpackage   Framework
 * @author       Ken Lalobo <ken@mooti.io>
 */

namespace Mooti\Framework\Util;
    
class Git
{
	/**
     * Clone a git repo
     *     
     * @param string $url  The git url
     * @param string $path The local path to clone to
     */
    public function cloneRepo($url, $path)
    {
        return shell_exec('git clone '.$url.' '.$path);
    }

    /**
     * pull in the latest remote changes
     *
     */
    public function pull()
    {
        return shell_exec('git pull');
    }

    /**
     * Get the git version string
     *
     * @return string The git version string
     */
    public function getVersion()
    {
    	return shell_exec('git --version');	
    }
}
