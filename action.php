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
            'date'   => '2012-11-26',
            'name'   => 'indexreference',
            'desc'   => 'Change tokens',
            'url'    => 'http://www.d-scribe.de/',
        );
    }

    /**
     * Register the handlers with the dokuwiki's event controller
     */
    function register(&$controller) {
        $controller->register_hook('PARSER_IDXNUM_OPEN', 'AFTER',  $this, 'storeNumber');
        $controller->register_hook('PARSER_HANDLER_DONE', 'AFTER',  $this, 'setNumbers');
        //$controller->register_hook('TPL_CONTENT_DISPLAY', 'BEFORE',  $this, '_output_field');
    }

    function storeNumber(&$event) {

    }

    function setNumbers(&$event) {
        //echo "<pre>";print_r($event);die();
        $indexnumbers = array();
        $indexreferences = array();
        foreach($event->data->calls as $idx => $call) {
            if($call[0] != 'plugin') {
                continue;
            }
            switch($call[1][0]) {
                case 'indexnumber':
                    $data = $call[1][1];
                    if(!empty($data[3]) && $data[0] == DOKU_LEXER_ENTER) // If indexnumber has an id and is opening tag
                        $indexnumbers[$data[1]][$data[3]] = $data[2];
                    break;
                case "indexreference":
                    $data = $call[1][1];
                    $indexreferences[$data[1]][$data[2]] = $idx;
                    break;
            }
        }
        foreach($indexreferences as $indexkey => $idxcombo) {
            foreach($idxcombo as $id => $callId) {
                if(isset($indexnumbers[$indexkey][$id])) {
                    // Add real index number
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