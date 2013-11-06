<?php

/**
 * Copyright (c) 2013 iControlWP <support@icontrolwp.com>
 * All rights reserved.
 * 
 * "WordPress Twitter Bootstrap CSS" is distributed under the GNU General Public License, Version 2,
 * June 1991. Copyright (C) 1989, 1991 Free Software Foundation, Inc., 51 Franklin
 * St, Fifth Floor, Boston, MA 02110, USA
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

if ( !class_exists('HLT_DashboardRssWidget') ):

class HLT_DashboardRssWidget {

	protected $m_aFeeds;

	public function __construct() {
		$this->m_aFeeds = array();
		
		$this->addFeed( 'hlt', 'http://feeds.feedburner.com/hostliketoast/' );
		$this->addFeed( 'icontrolwp', 'http://feeds.feedburner.com/icontrolwp/' );
		
		add_action( 'wp_dashboard_setup', array( $this, 'addNewsWidget' ) );
	}

	public function HLT_DashboardRssWidget() {
		$this->__construct();
	}
	
	public function addFeed( $insReference, $insUrl ) {
		$this->m_aFeeds[$insReference] = $insUrl;
	}

	public function addNewsWidget() {
		add_meta_box( 'hlt_news_widget', __( 'The iControlWP Blog', 'hlt-wordpress-bootstrap-css' ), array( $this, 'renderNewsWidget' ), 'dashboard', 'normal', 'low' );
	}

	public function renderNewsWidget() {
		
		$aItems = array();
		
		$oRss = fetch_feed( $this->m_aFeeds['hlt'] );
		
		if ( !is_wp_error( $oRss ) ) {
			$nMaxItems = $oRss->get_item_quantity( 3 );
			$aItems = $oRss->get_items( 0, $nMaxItems );
		}
		
		$oRss = fetch_feed( $this->m_aFeeds['icontrolwp'] );
		
		if ( !is_wp_error( $oRss ) ) {
			$nMaxItems = $oRss->get_item_quantity( 3 );
			$aItems = array_merge( $oRss->get_items( 0, $nMaxItems ), $aItems );
		}
		
		$sRssWidget = '
			<style>
				.hlt_rss_widget {
					font-family: verdana;
					font-size: 9px;
				}
				.hlt_rss_date {
					font-size: smaller;
				}
				.hlt_rss_link {
					font-size: 11px;
					font-family: verdana;
				}
				.hlt_rss_link:hover {
					color: #333333;
				}
			</style>
		';
		
		$sRssWidget .= '<div class="hlt_rss_widget"><ul>';
		
		if ( !empty( $aItems ) ) {
			$sDateFormat = get_option( 'date_format' );
			
			foreach ( $aItems as $oItem ) {
				$sRssWidget .= '
					<li class="hlt_rss_listitem">
						<a class="hlt_rss_link"
							target="_blank"
							href="'.esc_url( $oItem->get_permalink() ).'"
							title="'.esc_attr( $oItem->get_description() ).'">'.esc_attr( $oItem->get_title() ).'</a>
						<span class="hlt_rss_date">('.esc_attr( $oItem->get_date( $sDateFormat ) ).')</span>
					</li>';
			}
		}
		else {
			$sRssWidget .= '<li><a href="'.$this->m_aFeeds['icontrolwp'].'">'.__('Check out The iControlWP Blog', 'hlt-wordpress-bootstrap-css').'</a></li>';
		}

		$sRssWidget .= '</ul>';
		
		$sRssWidget .= '<p>You can turn off this news widget from the <a href="admin.php?page=worpit-wtb-bootstrap-css">Options Page</a>, but we don\'t recommend that because you\'ll miss our latest news ;)</p></div>';
		
		echo $sRssWidget;
	}
}//class HLT_DashboardRssWidget

endif;
