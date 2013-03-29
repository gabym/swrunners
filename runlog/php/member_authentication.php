<?php

require_once 'constants.php';
require_once 'DBLogger.php';

/**
 * Manages the aspects of authenticating a member
 */
class memberAuthentication
{
    /**
     * Tests if current member is authenticated according to this order:
     * 1. If an authentication session value exists
     * 2. If an authentication cookie exists
     */
    public function isMemberAuthenticated()
    {
        session_start();
        if (isset($_SESSION[AUTH_SESSION_KEY_NAME]))
        {
            return true;
        }
        else
        {
            $cookieAuth = $this->getCookieAuth();
            if (empty($cookieAuth))
            {
                // authentication cookie is not valid
                return false;
            }
            else
            {
                $memberId = $cookieAuth[0];
                $this->setSessionAuth($memberId);
                return true;
            }
        }
    }

    /**
     * Returns the authenticated member id if member is authenticated, null otherwise. The value is
     * taken from a session variable which currently only holds the member id but could hold more
     * stuff in the future
     *
     * @return string the authenticated member id if member is authenticated, null otherwise
     */
    public function getMemberId()
    {
        session_start();
        if (isset($_SESSION[AUTH_SESSION_KEY_NAME]))
        {
            return $_SESSION[AUTH_SESSION_KEY_NAME];
        }
        else
        {
            return '';
        }
    }

    public function getMemberName()
    {
        session_start();
        if (isset($_SESSION[MEMBER_NAME_SESSION_KEY_NAME]))
        {
            return $_SESSION[MEMBER_NAME_SESSION_KEY_NAME];
        }
        else
        {
            $conn = getConnection();
            $sql = "SELECT member_name FROM tl_runners WHERE tl_runners.id = '{$this->getMemberId()}'";
            $stmt = $conn->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($result))
            {
                // no member with such an id
                return '';
            }

            $memberName = $result[0]['member_name'];
            $_SESSION[MEMBER_NAME_SESSION_KEY_NAME] = $memberName;

            return $memberName;
        }
    }

    public function redirectToLoginPage()
    {
        if (DBLogger::isDebugEnabled())
        {
            DBLogger::log(LOGGER_DBG, debug_backtrace(), 'Unauthenticated user. Redirect to login page');
        }
        header("Location: login.php");
      	exit();
    }

    public function login($memberEmail, $memberPassword, $rememberMe)
    {
        $conn = getConnection();
        $sql = "SELECT * FROM tl_runners WHERE tl_runners.email = '$memberEmail' AND tl_runners.member_num = '$memberPassword'";
        $stmt = $conn->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result))
        {
            // no member with such an email or password
            return false;
        }

        $memberId = $result[0]['id'];
        $this->setSessionAuth($memberId);
        if ($rememberMe)
        {
            $this->setCookieAuth($memberId, $memberPassword);
        }

        return true;
    }

    public function logout()
    {
        setcookie(AUTH_COOKIE_NAME, '', time() - AUTH_COOKIE_DURATION);
        session_destroy();
    }

    private function getCookieAuth()
    {
        if (!isset($_COOKIE[AUTH_COOKIE_NAME]))
        {
            // no auth cookie sent from client
            return null;
        }

        $cookieAuth = explode('-', openssl_decrypt($_COOKIE[AUTH_COOKIE_NAME], "AES-256-CBC", AUTH_SECRET_KEY));
        if (!is_array($cookieAuth) || count($cookieAuth) != 2)
        {
            // cookie auth structure is not valid
            return null;
        }

        $memberId = $cookieAuth[0];
        if (!is_numeric($memberId) || intval($memberId) < 0)
        {
            // member id is not a positive integer
            return null;
        }
        $memberId = intval($memberId);

        $conn = getConnection();
        $sql = "SELECT tl_runners.id as member_id, tl_runners.member_num as member_password FROM tl_runners WHERE tl_runners.id = $memberId";
        $stmt = $conn->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result))
        {
            // no member with such an id exists
            return null;
        }

        $memberHash = $cookieAuth[1];
        if ($memberHash != md5($memberId . '-' . $result[0]['member_password']))
        {
            // cookie hashed info is not valid
            return null;
        }

        return $cookieAuth;
    }

    private function setCookieAuth($memberId, $memberPassword)
    {
        $memberHash = md5("$memberId-$memberPassword");
        $cookieAuth = openssl_encrypt("$memberId-$memberHash", "AES-256-CBC", AUTH_SECRET_KEY);
        setcookie(AUTH_COOKIE_NAME, $cookieAuth, time() + AUTH_COOKIE_DURATION);
    }

    private function setSessionAuth($memberId)
    {
        session_start();
        $_SESSION[AUTH_SESSION_KEY_NAME] = $memberId;
    }
}