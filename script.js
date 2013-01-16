/* 
 * Toolbar button for indexreferences
 */

if(window.toolbar!=undefined){
    toolbar[toolbar.length] = {
        "type":"indexreferencepicker",
        "title":LANG.plugins.indexreference.buttontext,
        "icon":"../../plugins/indexreference/indexref_icon.png"
    };
}

function createIndexreferencePicker(pickerid) {
    var picker, btn, i, body;
    picker               = document.createElement('div');
    picker.className         = 'picker';
    picker.id                = pickerid;
    picker.style.position    = 'absolute';
    picker.style.marginLeft  = '-10000px';
    body = document.getElementsByTagName('body')[0];
    body.appendChild(picker);
    return picker;
}

function updateIndexreferencePicker(pickerid, edid) {
    
    function getInsertHandler(idxname, idxnum) {
        return function(){  
            insertTags(edid,'<idxref '+idxname.replace(/^\s*(.*?)\s*$/, "$1") + ' ' + idxnum + '>', '', '');
            pickerClose();
        };
    }
    
    var picker, i, text, rx_indexnum, search_result;
    picker = document.getElementById(pickerid);
    while (picker.firstChild) {
        picker.removeChild(picker.firstChild);
    }
    rx_indexnum = /<idxnum\s*([^#]+)#(\d+)([^>]*)>/g;
    text = document.getElementById(edid).value;
    i = 0;
    while((search_result = rx_indexnum.exec(text)) !== null ) {
        btn = document.createElement('button');
        btn.className = 'pickerbutton';
        btn.textContent = search_result[1]+" #"+search_result[2];
        if(search_result[3]) {
            btn.title = search_result[3];
        }
        addEvent(btn,'click', getInsertHandler(search_result[1], search_result[2]));
        picker.appendChild(btn); 
        i++;
    }
    if(!i) {
        picker.textContent = LANG.plugins.indexreference.notfound;
    }
}

/**
 * Add button action for picker buttons and create picker element
 *
 * @param  DOMElement btn   Button element to add the action to 
 * @param  array      props Associative array of button properties
 * @param  string     edid  ID of the editor textarea
 * @param  int        id    Unique number of the picker
 * @return boolean    If button should be appended
 * @author Gabriel Birke <gb@birke-software.de>
 */
function addBtnActionIndexreferencepicker(btn, props, edid, id)
{
    var pickerid = 'picker'+(pickercounter++);
    createIndexreferencePicker(pickerid );
    addEvent(btn, 'click', function(){
        updateIndexreferencePicker(pickerid, edid)
        pickerToggle(pickerid, btn);
        return false;
    });
    return true;
}

