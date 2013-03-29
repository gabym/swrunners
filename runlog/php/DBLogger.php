<?php

/*
    This class is responsible for logging into DB 
    Typical usage example:
    if("we find err") {
    	$backtrace = debug_backtrace();
    	DBLogger::log(LOGGER_ERR,$backtrace,"There is an error with the second argument...");
    }
*/
require_once 'constants.php';
require_once 'utils.php';

class DBLogger {

	/**
	 * $level - integer from the set {LOGGER_ERR,LOGGER_DBG}
	 * $info - array that was returned from a call to debug_backtrace
	 * 		   see: http://php.net/manual/en/function.debug-backtrace.php
	 * $msg - free text
	 */
	public static function log($level, $info, $msg) {
		$levelStr = null;
		$shouldLog = false;
		switch ($level) {
			case LOGGER_ERR :
				$levelStr = "ERR";
				$shouldLog = true;
				break;
			case LOGGER_DBG :
				$levelStr = "DBG";
				$shouldLog = self::isDebugEnabled() == true;
				break;
			default :
				$shouldLog = false;
		}
		if ($shouldLog) {
			self::_log($levelStr, $info, $msg);
		}

	}

	/**
	 * A boolean method that tells if debug msg will be written to DB
	 */
	public static function isDebugEnabled() {
		return (isset($_SESSION['debug']) && $_SESSION['debug']);
	}
	
	/**
	 * Store the 'debug' flag in the session. Create a session if needed
	 */
	public static function setDebugEnabled($debugIsEnabled) {
		$_SESSION['debug'] = $debugIsEnabled;
	}

	/**
	 * Does the actual writing to DB
	 */
	private static function _log($levelStr, $info, $msg) {
		try {
			$conn = getConnection();
			$sql = "INSERT INTO " . tl_logger . " (level,msg,function,file,qs,line,agent,ip) VALUES (:level,:msg,:function,:file,:qs,:line,:agent,:ip)";
			$q = $conn->prepare($sql);

			$fileName = basename($_SERVER['PHP_SELF']);
            $requestParameters = getRequestParameters();
            $function = '';
            $line = '';
            if ($levelStr == 'ERR')
            {
                $function = $info[0]["function"];
                $line = $info[0]["line"];
            }

            $q->execute(array (
				':level' => $levelStr,
				':msg' => $msg, 
				':function' => $function,
				':file' => $fileName, 
				':qs' => $requestParameters,
				':line' => $line,
				':agent' => $_SERVER['HTTP_USER_AGENT'],
				':ip' => $_SERVER['REMOTE_ADDR'] 
			));
			$conn = null;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	/*
	 * CREATE TABLE tl_logger 
	 * 				(
	 * 				ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	 * 				level char(5),
	 * 				msg text,
	 * 				function varchar(40),
	 * 				file varchar(140),
	 * 				line integer,
	 * 				agent varchar(300),
	 *              ip char(15)  
	 * 				);
	 * 
	 */
}