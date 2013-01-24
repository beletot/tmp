<?php


class JFile {
	/**
	 * Gets the extension of a file name
	 *
	 * @param string $file The file name
	 * @return string The file extension
	 * @since 1.5
	 */
	function getExt($file) 
	{
		$dot = strrpos($file, '.') + 1;
		return substr($file, $dot);
	}
	/**
     * Strips the last extension off a file name
     *
     * @param string $file The file name
     * @return string The file name without the extension
     * @since 1.5
     */
    function stripExt($file) {
        return preg_replace('#\.[^.]*$#', '', $file);
    }
	function write($file, $buffer)
	{
        $file = JPath::clean($file);
        $ret = file_put_contents($file, $buffer);
        return $ret;
	}
}	
?>