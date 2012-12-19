<?php

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

class action_plugin_indexreference extends DokuWiki_Action_Plugin {

    protected $indexnumbers = array();

    /**
     * return some info
     */
    function getInfo(){
        return array(
            'author' => 'Gabriel Birke',
            'email'  => 'gb@birke-software.de',
            'date'   => '2012-12-20',
            'name'   => 'indexreference',
            'desc'   => 'Display references to index numbers',
            'url'    => 'http://www.birke-software.de/',
        );
    }

    /**
     * Register the handlers with the dokuwiki's event controller
     */
    function register(&$controller) {
        $controller->register_hook('PARSER_HANDLER_DONE', 'AFTER',  $this, 'setNumbers');
    }

    /**
     * Analyze the parsed data and add the actual counter numbers to each index reference.
     * 
     * @param object $event
     */
    function setNumbers(&$event) {
        // $indexnumbers stores id => value pairs for each counter
        $indexnumbers = array();
        // indexreferences stores id => array(callids) pairs for each counter
        // "callid" is the call id from $event->data->calls
        $indexreferences = array();
        // Build array content
        foreach($event->data->calls as $idx => $call) {
            if($call[0] != 'plugin') {
                continue;
            }
            switch($call[1][0]) {
                case 'indexnumber':
                    $data = $call[1][1];
                    if(!empty($data[3]) && $data[0] == DOKU_LEXER_ENTER) { // If indexnumber has an id and is opening tag
                        $indexnumbers[$data[1]][$data[3]] = $data[2];
                    }
                    break;
                case "indexreference":
                    $data = $call[1][1];
                    $indexreferences[$data[1]][$data[2]][] = $idx;
                    break;
            }
        }
        // Assign counter values to indexreference calls in event data
        // Either the counter value is set, or the reference type is changed to signal
        // an invalid reference
        foreach($indexreferences as $indexkey => $idxcombo) {
            foreach($idxcombo as $id => $callIds) {
                foreach($callIds as $callId) {
                    if(isset($indexnumbers[$indexkey][$id])) {
                        // Add real index number as third parameter
                        $event->data->calls[$callId][1][1][3] = $indexnumbers[$indexkey][$id];
                    }
                    elseif(!isset($indexnumbers[$indexkey])) {
                        $event->data->calls[$callId][1][1][0] = syntax_plugin_indexreference::TYPE_WRONG_IDX;
                    }
                    else {
                        $event->data->calls[$callId][1][1][0] = syntax_plugin_indexreference::TYPE_WRONG_REF;
                    }
                }
            }
        }
    }

}