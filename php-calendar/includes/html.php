<?php
/*
   Copyright 2002 - 2005 Sean Proctor, Nathan Poiro

   This file is part of PHP-Calendar.

   PHP-Calendar is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   PHP-Calendar is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with PHP-Calendar; if not, write to the Free Software
   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if ( !defined('IN_PHPC') ) {
       die("Hacking attempt");
}

$HtmlInline = array('a', 'strong');

class Html {
        var $tagName;
        var $attributeList;
        var $childElements;

        function Html() {
                $args = func_get_args();
                return call_user_func_array(array(&$this, '__construct'),
                                $args);
        }

        function __construct() {
                $args = func_get_args();
                $this->tagName = array_shift($args);
                $this->attributeList = NULL;
                $this->childElements = NULL;

                $arg = array_shift($args);
                if($arg == NULL) return;

                if(is_a($arg, 'AttributeList')) {
                        $this->attributeList = $arg;
                        $arg = array_shift($args);
                }

                do {
                        if(is_object($arg) && !is_a($arg, 'Html')) {
                                soft_error(_('Invalid class') . ': '
                                                . get_class($arg));
                        }
                        if($this->childElements == NULL) {
                                $this->childElements = array();
                        }
                        $this->childElements[] = $arg;
                        $arg = array_shift($args);
                } while($arg !== NULL);
        }

        function add($htmlElement) {
                if($this->childElements == NULL) {
                        $this->childElements = array();
                }
                if(is_array($htmlElement)) {
                        foreach($htmlElement as $element) {
                                if(is_object($element)
                                                && !is_a($element, 'Html')) {
                                        soft_error(_('Invalid class') . ': '
                                                        . get_class($element));
                                }
                        }
                        $htmlElement = array_merge($this->childElements,
                                        $htmlElement);
                } elseif(is_object($element) && !is_a($element, 'Html')) {
                        soft_error(_('Invalid class') . ': '
                                        . get_class($element));
                }
                $this->childElements[] = $htmlElement;
        }

        function prepend($htmlElement) {
                if($this->childElements == NULL) {
                        $this->childElements = array();
                }
                if(is_array($htmlElement)) {
                        foreach($htmlElement as $element) {
                                if(is_object($element)
                                                && !is_a($element, 'Html')) {
                                        soft_error(_('Invalid class') . ': '
                                                        . get_class($element));
                                }
                        }
                        $htmlElement = array_merge($this->childElements,
                                        $htmlElement);
                } elseif(is_object($element) && !is_a($element, 'Html')) {
                        soft_error(_('Invalid class') . ': '
                                        . get_class($element));
                }
                $this->childElements = array_merge($htmlElement,
                                $this->childElements);
        }

        function toString() {
                global $HtmlInline;

                /*echo "<pre>";
                print_r($this);
                echo "</pre>";
                die();*/
                $output = "<{$this->tagName}";

                if($this->attributeList != NULL) {
                        $output .= ' ' . $this->attributeList->toString();
                }

                if($this->childElements == NULL) {
                        $output .= " />\n";
                        return $output;
                }

                $output .= ">";

                foreach($this->childElements as $child) {
                        if(is_object($child)) {
                                if(is_a($child, 'Html')) {
                                        $output .= $child->toString();
                                } else {
                                        soft_error(_('Invalid class') . ': '
                                                        . get_class($child));
                                }
                        } else {
                                $output .= $child;
                        }
                }

                $output .= "</{$this->tagName}>";

                if(!in_array($this->tagName, $HtmlInline)) {
                        $output .= "\n";
                }
                return $output;
        }
}

class AttributeList {
        var $list;

        function AttributeList() {
                $args = func_get_args();
                return call_user_func_array(array(&$this, '__construct'),
                                $args);
        }

        function __construct() {
                $this->list = array();
                $args = func_get_args();
                foreach($args as $arg) {
                        $this->list = array_merge($this->list, $arg);
                }
        }

        function add($str) {
                $this->list[] = $str;
        }

        function toString() {
                return implode(' ', $this->list);
        }
}

function tag()
{
        $args = func_get_args();
        $html = new Html();
        call_user_func_array(array(&$html, '__construct'), $args);
        return $html;
}

function attributes($args)
{
        $args = func_get_args();
        $attrs = new AttributeList();
        call_user_func_array(array(&$attrs, '__construct'), $args);
        return $attrs;
}

?>
