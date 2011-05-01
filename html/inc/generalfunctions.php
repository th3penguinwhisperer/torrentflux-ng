<?php

/**
 * Returns a string in format of TB, GB, MB, or kB depending on the size
 *
 * @param $inBytes
 * @return string
 */
function formatBytesTokBMBGBTB($inBytes) {
	if(!is_numeric($inBytes)) return "";
	if ($inBytes > 1099511627776)
		return round($inBytes / 1099511627776, 2) . " TB";
	elseif ($inBytes > 1073741824)
		return round($inBytes / 1073741824, 2) . " GB";
	elseif ($inBytes > 1048576)
		return round($inBytes / 1048576, 1) . " MB";
	elseif ($inBytes > 1024)
		return round($inBytes / 1024, 1) . " kB";
	else
		return $inBytes . " B";
}

?>
