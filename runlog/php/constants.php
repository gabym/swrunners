<?php
	define('BC_OK', "1");
	define('BC_ERR', "0");

	define('LOGGER_ERR', 0);
	define('LOGGER_DBG', 1);

    define('AUTH_SECRET_KEY', 'ettiEiner#1');
    define('AUTH_SESSION_KEY_NAME', 'auth_member');
    define('AUTH_COOKIE_NAME', 'tlog-number');
    define('AUTH_COOKIE_DURATION', 2592000); // 30 days in seconds

    define('MEMBER_NAME_SESSION_KEY_NAME', 'member_name');

	define('tl_members', 'members');
	define('tl_events', 'events');
	define('tl_shoes', 'shoes');
	define('tl_course', 'courses');
	define('tl_logger', 'tl_logger');

	define('MIN_WARMUP_DISTANCE', 0);
	define('MAX_WARMUP_DISTANCE', 9);
	define('MIN_RUN_DISTANCE', 0);
	define('MAX_RUN_DISTANCE', 99);
	define('MIN_COOLDOWN_DISTANCE', 0);
	define('MAX_COOLDOWN_DISTANCE', 9);
	define('MIN_WARMUP_TIME', 0);
	define('MAX_WARMUP_TIME', 3599);
	define('MIN_RUN_TIME', 0);
	define('MAX_RUN_TIME', 35999);
	define('MIN_COOLDOWN_TIME', 0);
	define('MAX_COOLDOWN_TIME', 3599);

	define('JS_VERSION', 9);
	define('CSS_VERSION', 1);