<?php
/**
 * Copyright 2016 Google Inc.
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc., 51
 * Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/gcs-media-plugin')
;

return PhpCsFixer\Config::create()
    ->setRules(array(
        '@PSR2' => true,
        'concat_space' => array('spacing' => 'one'),
        'no_unused_imports' => true,
        'no_trailing_whitespace' => true,
	'no_whitespace_in_blank_line' => true,
        'indentation_type' => true
    ))
    ->setFinder($finder)
;
