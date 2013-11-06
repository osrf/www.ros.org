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

if ( !class_exists('ICWP_DataProcessor') ):

class ICWP_DataProcessor {

	const OldIpRangeSeparator	= '-';
	const IpRangeSeparator		= '-';
	
	public static $fUseFilter = false;
	
	static public function ExtractIpAddresses( $insAddresses = '' ) {
		
		$aRawAddresses = array();
		
		if ( empty( $insAddresses ) ) {
			return $aRawAddresses;
		}
		$aRawList = array_map( 'trim', explode( "\n", $insAddresses ) );

		self::$fUseFilter = function_exists('filter_var') && defined( FILTER_VALIDATE_IP );

		foreach( $aRawList as $sKey => $sRawAddressLine ) {
			
			if ( empty( $sRawAddressLine ) ) {
				continue;
			}
			
			// Each line can have a Label which is the IP separated with a space.
			$aParts = explode( ' ', $sRawAddressLine, 2 );
			if ( count( $aParts ) == 1 ) {
				$aParts[] = '';
			}
			$aRawAddresses[ $aParts[0] ] = trim( $aParts[1] );
		}
		return self::Add_New_Raw_Ips( array(), $aRawAddresses );
	}
	
	static public function ExtractCommaSeparatedList( $insRawList = '' ) {
		
		$aRawList = array();
		if ( empty( $insRawList ) ) {
			return $aRawList;
		}

		$aRawList = array_map( 'trim', preg_split( '/\r\n|\r|\n/', $insRawList ) );
		$aNewList = array();
		$fHadStar = false;
		foreach( $aRawList as $sKey => $sRawLine ) {
			
			if ( empty( $sRawLine ) ) {
				continue;
			}
			$sRawLine = str_replace( ' ', '', $sRawLine );
			$aParts = explode( ',', $sRawLine, 2 );
			// we only permit 1x line beginning with *
			if ( $aParts[0] == '*' ) {
				if ( $fHadStar ) {
					continue;
				}
				$fHadStar = true;
			}
			else {
				//If there's only 1 item on the line, we assume it to be a global
				// parameter rule
				if ( count( $aParts ) == 1 || empty( $aParts[1] ) ) { // there was no comma in this line in the first place
					array_unshift( $aParts, '*' );
				}
			}
			
			$aParams = empty( $aParts[1] )? array() : explode( ',', $aParts[1] );
			$aNewList[ $aParts[0] ] = $aParams;
		}
		return $aNewList;
	}
	
	/**
	 * Given a list of new IPv4 address ($inaNewRawAddresses) it'll add them to the existing list
	 * ($inaCurrent) where they're not already found
	 * 
	 * @param array $inaCurrent			- the list to which to add the new addresses
	 * @param array $inaNewRawAddresses	- the new IPv4 addresses
	 * @param int $outnNewAdded		- the count of newly added IPs
	 * @return unknown|Ambigous <multitype:multitype: , string>
	 */
	public static function Add_New_Raw_Ips( $inaCurrent, $inaNewRawAddresses, &$outnNewAdded = 0 ) {
		
		if ( empty( $inaNewRawAddresses ) ) {
			return $inaCurrent;
		}
		
		if ( !array_key_exists( 'ips', $inaCurrent ) ) {
			$inaCurrent['ips'] = array();
		}
		if ( !array_key_exists( 'meta', $inaCurrent ) ) {
			$inaCurrent['meta'] = array();
		}

		foreach( $inaNewRawAddresses as $sRawIpAddress => $sLabel ) {
			$mVerifiedIp = self::Verify_Ip( $sRawIpAddress );
			if ( $mVerifiedIp !== false && !in_array( $mVerifiedIp, $inaCurrent['ips'] ) ) {
				$inaCurrent['ips'][] = $mVerifiedIp;
				if ( empty($sLabel) ) {
					$sLabel = 'no label';
				}
				$inaCurrent['meta'][ md5( $mVerifiedIp ) ] = $sLabel;
				$outnNewAdded++;
			}
		}
		return $inaCurrent;
	}
	
	/**
	 * @param array $inaCurrent
	 * @param array $inaRawAddresses - should be a plain numerical array of IPv4 addresses
	 * @return array:
	 */
	public static function Remove_Raw_Ips( $inaCurrent, $inaRawAddresses ) {
		if ( empty( $inaRawAddresses ) ) {
			return $inaCurrent;
		}
		
		if ( !array_key_exists( 'ips', $inaCurrent ) ) {
			$inaCurrent['ips'] = array();
		}
		if ( !array_key_exists( 'meta', $inaCurrent ) ) {
			$inaCurrent['meta'] = array();
		}
		
		foreach( $inaRawAddresses as $sRawIpAddress ) {
			$mVerifiedIp = self::Verify_Ip( $sRawIpAddress );
			if ( $mVerifiedIp === false ) {
				continue;
			}
			$mKey = array_search( $mVerifiedIp, $inaCurrent['ips'] );
			if ( $mKey !== false ) {
				unset( $inaCurrent['ips'][$mKey] );
				unset( $inaCurrent['meta'][ md5( $mVerifiedIp ) ] );
			}
		}
		return $inaCurrent;
	}
	
	public static function Verify_Ip( $insIpAddress ) {
		
		$sAddress = self::Clean_Ip( $insIpAddress );
		
		// Now, determine if this is an IP range, or just a plain IP address.
		if ( strpos( $sAddress, '-' ) === false ) { //plain IP address
			return self::Verify_Ip_Address( $sAddress );
		}
		else {
			return self::Verify_Ip_Range( $sAddress );
		}
	}

	public static function Clean_Ip( $insRawAddress ) {
		$insRawAddress = preg_replace( '/[a-z\s]/i', '', $insRawAddress );
		$insRawAddress = str_replace( '.', 'PERIOD', $insRawAddress );
		$insRawAddress = str_replace( '-', 'HYPEN', $insRawAddress );
		$insRawAddress = preg_replace( '/[^a-z0-9]/i', '', $insRawAddress );
		$insRawAddress = str_replace( 'PERIOD', '.', $insRawAddress );
		$insRawAddress = str_replace( 'HYPEN', '-', $insRawAddress );
		return $insRawAddress;
	}
	
	public static function Verify_Ip_Address( $insIpAddress ) {
		if ( self::$fUseFilter ) {
			if ( filter_var( $insIpAddress, FILTER_VALIDATE_IP ) ) {
				return ip2long( $insIpAddress );
			}
		}
		else {
			if ( preg_match( '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $insIpAddress ) ) { //It's a valid IPv4 format, now check components
				$aParts = explode( '.', $insIpAddress );
				foreach ( $aParts as $sPart ) {
					$sPart = intval( $sPart );
					if ( $sPart < 0 || $sPart > 255 ) {
						return false;
					}
				}
				return ip2long( $insIpAddress );
			}
		}
		return false;
	}
	
	/**
	 * The only ranges currently accepted are a.b.c.d-f.g.h.j
	 * @param string $insIpAddressRange
	 * @return string|boolean
	 */
	public static function Verify_Ip_Range( $insIpAddressRange ) {
		
		list( $sIpRangeStart, $sIpRangeEnd ) = explode( '-', $insIpAddressRange, 2 );
		
		if ( self::Verify_Ip_Address( $sIpRangeStart ) && self::Verify_Ip_Address( $sIpRangeEnd ) ) {
			return ip2long( $sIpRangeStart ). self::IpRangeSeparator .ip2long( $sIpRangeEnd );
		}
		return false;
	}
	
}

endif;