<?php
/*
 * @系统参数探测 LINUX
 * @Edit www.jbxue.com
 * @date 2013/5/8
 */
function sys_linux() {
	// CPU
	if (false === ($str = @file ( "/proc/cpuinfo" )))
		return false;
	$str = implode ( "", $str );
	@preg_match_all ( "/model\s+name\s{0,}\:+\s{0,}([\w\s\)\(.]+)[\r\n]+/", $str, $model );
	// @preg_match_all("/cpu\s+MHz\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $mhz);
	@preg_match_all ( "/cache\s+size\s{0,}\:+\s{0,}([\d\.]+\s{0,}[A-Z]+[\r\n]+)/", $str, $cache );
	if (false !== is_array ( $model [1] )) {
		$res ['cpu'] ['num'] = sizeof ( $model [1] );
		for($i = 0; $i < $res ['cpu'] ['num']; $i ++) {
			$res ['cpu'] ['detail'] [] = "类型：" . $model [1] [$i] . " 缓存：" . $cache [1] [$i];
		}
		if (false !== is_array ( $res ['cpu'] ['detail'] ))
			$res ['cpu'] ['detail'] = implode ( "
", $res ['cpu'] ['detail'] );
	}
	
	// UPTIME
	if (false === ($str = @file ( "/proc/uptime" )))
		return false;
	$str = explode ( " ", implode ( "", $str ) );
	$str = trim ( $str [0] );
	$min = $str / 60;
	$hours = $min / 60;
	$days = floor ( $hours / 24 );
	$hours = floor ( $hours - ($days * 24) );
	$min = floor ( $min - ($days * 60 * 24) - ($hours * 60) );
	if ($days !== 0)
		$res ['uptime'] = $days . "天";
	if ($hours !== 0)
		$res ['uptime'] .= $hours . "小时";
	$res ['uptime'] .= $min . "分钟";
	
	// MEMORY
	if (false === ($str = @file ( "/proc/meminfo" )))
		return false;
	$str = implode ( "", $str );
	preg_match_all ( "/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buf );
	
	$res ['memTotal'] = round ( $buf [1] [0] / 1024, 2 );
	$res ['memFree'] = round ( $buf [2] [0] / 1024, 2 );
	$res ['memUsed'] = ($res ['memTotal'] - $res ['memFree']);
	$res ['memPercent'] = (floatval ( $res ['memTotal'] ) != 0) ? round ( ($res ['memUsed'] / $res ['memTotal']) * 90, 2 ) : 0;
	
	$res ['swapTotal'] = round ( $buf [3] [0] / 1024, 2 );
	$res ['swapFree'] = round ( $buf [4] [0] / 1024, 2 );
	$res ['swapUsed'] = ($res ['swapTotal'] - $res ['swapFree']);
	$res ['swapPercent'] = (floatval ( $res ['swapTotal'] ) != 0) ? round ( ($res ['swapUsed'] / $res ['swapTotal']) * 90, 2 ) : 0;
	
	// LOAD AVG
	if (false === ($str = @file ( "/proc/loadavg" )))
		return false;
	$str = explode ( " ", implode ( "", $str ) );
	$str = array_chunk ( $str, 3 );
	$res ['loadAvg'] = implode ( " ", $str [0] );
	
	return $res;
}
/*
 * 系统参数探测 FreeBSD
 */
function sys_freebsd() {
	// CPU
	if (false === ($res ['cpu'] ['num'] = get_key ( "hw.ncpu" )))
		return false;
	$res ['cpu'] ['detail'] = get_key ( "hw.model" );
	
	// LOAD AVG
	if (false === ($res ['loadAvg'] = get_key ( "vm.loadavg" )))
		return false;
	$res ['loadAvg'] = str_replace ( "{", "", $res ['loadAvg'] );
	$res ['loadAvg'] = str_replace ( "}", "", $res ['loadAvg'] );
	
	// UPTIME
	if (false === ($buf = get_key ( "kern.boottime" )))
		return false;
	$buf = explode ( ' ', $buf );
	$sys_ticks = time () - intval ( $buf [3] );
	$min = $sys_ticks / 60;
	$hours = $min / 60;
	$days = floor ( $hours / 24 );
	$hours = floor ( $hours - ($days * 24) );
	$min = floor ( $min - ($days * 60 * 24) - ($hours * 60) );
	if ($days !== 0)
		$res ['uptime'] = $days . "天";
	if ($hours !== 0)
		$res ['uptime'] .= $hours . "小时";
	$res ['uptime'] .= $min . "分钟";
	
	// MEMORY
	if (false === ($buf = get_key ( "hw.physmem" )))
		return false;
	$res ['memTotal'] = round ( $buf / 1024 / 1024, 2 );
	$buf = explode ( "\n", do_command ( "vmstat", "" ) );
	$buf = explode ( " ", trim ( $buf [2] ) );
	
	$res ['memFree'] = round ( $buf [5] / 1024, 2 );
	$res ['memUsed'] = ($res ['memTotal'] - $res ['memFree']);
	$res ['memPercent'] = (floatval ( $res ['memTotal'] ) != 0) ? round ( ($res ['memUsed'] / $res ['memTotal']) * 90, 2 ) : 0;
	
	$buf = explode ( "\n", do_command ( "swapinfo", "-k" ) );
	$buf = $buf [1];
	preg_match_all ( "/([0-9]+)\s+([0-9]+)\s+([0-9]+)/", $buf, $bufArr );
	$res ['swapTotal'] = round ( $bufArr [1] [0] / 1024, 2 );
	$res ['swapUsed'] = round ( $bufArr [2] [0] / 1024, 2 );
	$res ['swapFree'] = round ( $bufArr [3] [0] / 1024, 2 );
	$res ['swapPercent'] = (floatval ( $res ['swapTotal'] ) != 0) ? round ( ($res ['swapUsed'] / $res ['swapTotal']) * 90, 2 ) : 0;
	
	return $res;
}

/*
 * 取得参数值 FreeBSD
 */
function get_key($keyName) {
	return do_command ( 'sysctl', "-n $keyName" );
}

/*
 * 确定执行文件位置 FreeBSD
 */
function find_command($commandName) {
	$path = array (
			'/bin',
			'/sbin',
			'/usr/bin',
			'/usr/sbin',
			'/usr/local/bin',
			'/usr/local/sbin' 
	);
	foreach ( $path as $p ) {
		if (@is_executable ( "$p/$commandName" ))
			return "$p/$commandName";
	}
	return false;
}

/*
 * 执行系统命令 FreeBSD
 */
function do_command($commandName, $args) {
	$buffer = "";
	if (false === ($command = find_command ( $commandName )))
		return false;
	if ($fp = @popen ( "$command $args", 'r' )) {
		while ( ! @feof ( $fp ) ) {
			$buffer .= @fgets ( $fp, 4096 );
		}
		return trim ( $buffer );
	}
	return false;
}
?>