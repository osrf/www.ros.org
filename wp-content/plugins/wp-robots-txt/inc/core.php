<?php
/**
 * WP Robots Txt
 *
 * Copyright 2013 Christopher Davis <http://christopherdavis.me>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category    WordPress
 * @package     WPRobotsTxt
 * @copyright   2013 Christopher Davis
 * @license     http://opensource.org/licenses/GPL-2.0 GPL-2.0+
 */

/**
 * Makes the magic happen.  Get our saved robots.txt file and, if there's
 * something there replace WP's default robots file.
 * 
 * @since   1.0
 * @uses    get_option
 * @uses    esc_attr
 * @return  string
 */
function cd_rdte_filter_robots($rv, $public)
{
    $content = get_option('cd_rdte_content');
    if ($content) {
        $rv = esc_attr(strip_tags($content));
    }

    return $rv;
}

/**
 * Deactivation hook. Deletes our option containing the robots.txt content
 * 
 * @since   1.0
 * @uses    delete_option
 * @return  void
 */
function cd_rdte_deactivation()
{
    delete_option('cd_rdte_content');
}

/**
 * Activation hook.  Adds the option we'll be uses
 * 
 * @since   1.0
 * @uses    add_option
 * @return  void
 */
function cd_rdte_activation()
{
    add_option('cd_rdte_content', false);
}
