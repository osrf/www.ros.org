<?php
/**
 * Copyright (c) 2013 iControlWP <support@icontrolwp.com>
 * All rights reserved.
 * 
 * Version: 2013-08-27-B
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

if ( !class_exists('ICWP_BaseProcessor_WPTB') ):

class ICWP_BaseProcessor_WPTB {
	
	const PcreDelimiter = '/';
	const LOG_MESSAGE_LEVEL_INFO = 0;
	const LOG_MESSAGE_LEVEL_WARNING = 1;
	const LOG_MESSAGE_LEVEL_CRITICAL = 2;

	const LOG_CATEGORY_DEFAULT = 0;
	const LOG_CATEGORY_FIREWALL = 1;
	const LOG_CATEGORY_LOGINPROTECT = 2;

	/**
	 * @var array
	 */
	protected $m_aLog;
	/**
	 * @var array
	 */
	protected $m_aLogMessages;
	
	/**
	 * @var long
	 */
	protected $m_nRequestIp;

	/**
	 * @var boolean
	 */
	protected $m_fLoggingEnabled;
	
	/**
	 * @var ICWP_EmailProcessor
	 */
	protected $m_oEmailHandler;

	public function __construct() {	}
	
	/**
	 * Resets the object values to be re-used anew
	 */
	public function reset() {
		$this->m_nRequestIp = self::GetVisitorIpAddress();
		$this->resetLog();
	}
	
	/**
	 * Resets the log
	 */
	public function resetLog() {
		$this->m_aLogMessages = array();
	}
	
	/**
	 * @param boolean $infEnableLogging
	 */
	public function setLogging( $infEnableLogging = true ) {
		$this->m_fLoggingEnabled = $infEnableLogging;
	}
	
	/**
	 * Builds and returns the full log.
	 * 
	 * @return array (associative)
	 */
	public function getLogData() {
		
		if ( $this->m_fLoggingEnabled  ) {
			$this->m_aLog = array(
				'messages'			=> serialize( $this->m_aLogMessages ),
			);
		}
		else {
			$this->m_aLog = false;
		}
		
		return $this->m_aLog;
	}
	
	/**
	 * @param string $insLogMessage
	 * @param string $insMessageType
	 */
	public function writeLog( $insLogMessage = '', $insMessageType = self::LOG_MESSAGE_LEVEL_INFO ) {
		if ( !is_array( $this->m_aLogMessages ) ) {
			$this->resetLog();
		}
		$this->m_aLogMessages[] = array( $insMessageType, $insLogMessage );
	}
	/**
	 * @param string $insLogMessage
	 */
	public function logInfo( $insLogMessage ) {
		$this->writeLog( $insLogMessage, self::LOG_MESSAGE_LEVEL_INFO );
	}
	/**
	 * @param string $insLogMessage
	 */
	public function logWarning( $insLogMessage ) {
		$this->writeLog( $insLogMessage, self::LOG_MESSAGE_LEVEL_WARNING );
	}
	/**
	 * @param string $insLogMessage
	 */
	public function logCritical( $insLogMessage ) {
		$this->writeLog( $insLogMessage, self::LOG_MESSAGE_LEVEL_CRITICAL );
	}

	/**
	 * Cloudflare compatible.
	 * 
	 * @return number - visitor IP Address as IP2Long
	 */
	public static function GetVisitorIpAddress( $infAsLong = true ) {
	
		$aAddressSourceOptions = array(
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_FORWARDED',
			'REMOTE_ADDR'
		);
		
		$fCanUseFilter = function_exists( 'filter_var' ) && defined( 'FILTER_FLAG_NO_PRIV_RANGE' ) && defined( 'FILTER_FLAG_IPV4' );
		
		$aIpAddresses = array();
		foreach( $aAddressSourceOptions as $sOption ) {
			$sIpAddressToTest = $_SERVER[ $sOption ];
			if ( empty( $sIpAddressToTest ) ) {
				continue;
			}
			
			$aIpAddresses = explode( ',', $sIpAddressToTest ); //sometimes a comma-separated list is returned
			foreach( $aIpAddresses as $sIpAddress ) {
				
				if ( $fCanUseFilter && !self::IsAddressInPublicIpRange( $sIpAddress ) ) {
					continue;
				}
				else {
					return $infAsLong? ip2long( $sIpAddress ) : $sIpAddress;
				}
			}
		}
		return false;
	}
	
	/**
	 * Assumes a valid IPv4 address is provided as we're only testing for a whether the IP is public or not.
	 * 
	 * @param string $insIpAddress
	 * @uses filter_var
	 */
	public static function IsAddressInPublicIpRange( $insIpAddress ) {
		return filter_var( $insIpAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE );
	}

	/**
	 * @param array $inaIpList
	 * @param integer $innIpAddress
	 * @return boolean
	 */
	public function isIpOnlist( $inaIpList, $innIpAddress = '', &$outsLabel = '' ) {
	
		if ( empty( $innIpAddress ) || !isset( $inaIpList['ips'] ) ) {
			return false;
		}
	
		$outsLabel = '';
		foreach( $inaIpList['ips'] as $mWhitelistAddress ) {
			
			$aIps = $this->parseIpAddress( $mWhitelistAddress );
			if ( count( $aIps ) === 1 ) { //not a range
				if ( $innIpAddress == $aIps[0] ) {
					$outsLabel = $inaIpList['meta'][ md5( $mWhitelistAddress ) ];
					return true;
				}
			}
			else if ( count( $aIps ) == 2 ) {
				if ( $aIps[0] <= $innIpAddress && $innIpAddress <= $aIps[1] ) {
					$outsLabel = $inaIpList['meta'][ md5( $mWhitelistAddress ) ];
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * @param string $insIpAddress	- an IP or IP address range in LONG format.
	 * @return array				- with 1 ip address, or 2 addresses if it is a range.
	 */
	protected function parseIpAddress( $insIpAddress ) {
		
		$aIps = array();
		
		if ( empty($insIpAddress) ) {
			return $aIps;
		}
		
		// offset=1 in the case that it's a range and the first number is negative on 32-bit systems
		$mPos = strpos( $sAddress, '-', 1 );

		if ( $mPos === false ) { //plain IP address
			$aIps[] = $sAddress;
		}
		else {
			//we remove the first character in case this is '-'
			$aParts = array( substr( $sAddress, 0, 1 ), substr( $sAddress, 1 ) );
			list( $sStart, $sEnd ) = explode( '-', $aParts[1], 2 );
			$aIps[] = $aParts[0].$sStart;
			$aIps[] = $sEnd;
		}
		return $aIps;
	}
	
	/**
	 * We force PHP to pass by reference in case of older versions of PHP (?)
	 * 
	 * @param ICWP_EmailProcessor $inoEmailHandler
	 */
	public function setEmailHandler( ICWP_EmailProcessor &$inoEmailHandler ) {
		$this->m_oEmailHandler = $inoEmailHandler;
	}
	
	/**
	 * @param string $insEmailSubject	- message subject
	 * @param array $inaMessage			- message content
	 * @return boolean					- message sending success (remember that if throttled, returns true)
	 */
	public function sendEmail( $insEmailSubject, $inaMessage ) {
		return $this->m_oEmailHandler->sendEmail( $insEmailSubject, $inaMessage );
	}
	
	/**
	 * @param string $insEmailAddress	- message recipient
	 * @param string $insEmailSubject	- message subject
	 * @param array $inaMessage			- message content
	 * @return boolean					- message sending success (remember that if throttled, returns true)
	 */
	public function sendEmailTo( $insEmailAddress, $insEmailSubject, $inaMessage ) {
		return $this->m_oEmailHandler->sendEmailTo( $insEmailAddress, $insEmailSubject, $inaMessage );
	}

	/**
	 * Checks the $inaData contains valid key values as laid out in $inaChecks
	 *
	 * @param array $inaData
	 * @param array $inaChecks
	 * @return boolean
	 */
	protected function validateParameters( $inaData, $inaChecks ) {
	
		if ( !is_array( $inaData ) ) {
			return false;
		}
	
		foreach( $inaChecks as $sCheck ) {
			if ( !array_key_exists( $sCheck, $inaData ) || empty( $inaData[ $sCheck ] ) ) {
				return false;
			}
		}
		return true;
	}
}

endif;