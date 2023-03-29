<?php
/**
.---------------------------------------------------------------------------.
|  Software: PHP class Botprotection                                        |
|   Version: 1.0                                                            |
|   Date   : 2023-03-26                                                     |
|      Site: http:                                                          |
| ------------------------------------------------------------------------- |
| Copyright (c) 2023, Peter Junk. All Rights Reserved.                      |
| ------------------------------------------------------------------------- |
|   License: Distributed under the Lesser General Public License (LGPL)     |
|            http://www.gnu.org/copyleft/lesser.html                        |
| This program is distributed in the hope that it will be useful - WITHOUT  |
| ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or     |
| FITNESS FOR A PARTICULAR PURPOSE.                                         |
'---------------------------------------------------------------------------'
*/
class Botprotection {
    const SESSKEY = 'Botprotection_v1';
    //min + max. Zeit fuer Formular 
    
    private $minInputTime = 5;  //min time in seconds for formular input
    private $maxInputTime = 1200; //max time in seconds for formular input

    /*
     * Class constructor
     * @throws Exception
     */
    public function __construct() {
        //Verification of a valid SESSION exists
        if(!session_id()) {
            $msg = 'This class needs a session_start() in advance';
            throw new \Exception($msg);
        }
        if(!array_key_exists(self::SESSKEY,$_SESSION)){
            $_SESSION[self::SESSKEY] = [];
        }
    } 

    /*
     * Set min input time in seconds
     * @param int $minSeconds, defdault 5
     * @return $this
     */
    public function setMinInputTime(int $minSeconds = 5) : self {
        $this->minInputTime = $minSeconds;
        return $this;
    }

    /*
     * Set max input time in seconds
     * @param int $maxSeconds, defdault 1200 (20 Minutes)
     * @return $this
     */
    public function setMaxInputTime(int $maxSeconds = 1200) : self {
        $this->maxInputTime = $maxSeconds;
        return $this;
    }

    /*
     * create ptrotection input element
     * @param string $name
     * @return string html
     */
    public function protectionInput(string $name) : string {
        $uid = strtr(uniqid("k",true),["." => ""]);
        $rnd = dechex(rand(4096,65535)); //random string
        $_SESSION[self::SESSKEY][$name] = [
            'id' => $uid,
            'time' => microtime(true),
            'token' => $rnd
        ];
        $html = '<label for="Id_'.$name.'_0" id="Id_'.$name.'_2" > Ihre Eingabe';
        $html .= '<input name="'.$name.'[0]" id="Id_'.$name.'_0" type="text" value=""></label>';
        $html .= '<input name="'.$name.'['.$uid.']" id="Id_'.$name.'_1" type="text" value="">';
        $html .= '<script>
            (function() {
                var el0 = document.getElementById("Id_'.$name.'_0"); el0.style.display="none";
                var el1 = document.getElementById("Id_'.$name.'_1"); el1.style.display="none";el1.value="'.$rnd.'";
                document.getElementById("Id_'.$name.'_2").style.display="none";
            })();
        </script>';
        return $html;
    }

    /*
     * check status
     * @param string $name: The same name must be used here as when calling protectionInput
     * @param bool $setIdInvalid, sets entry from name to invalid, default true,
     * @return int : status
     */
    public function status(string $name, bool $setNameInvalid = true) : int {
        if(empty($_POST)) return -1; //No form submitted yet
        //Status 1: $name has expired, the name is unknown, or there is a session error
        if(!array_key_exists($name,$_SESSION[self::SESSKEY])) return 1;
        $sessInfo = $_SESSION[self::SESSKEY][$name];
        if($setNameInvalid) {
            unset($_SESSION[self::SESSKEY][$name]);
        }
        //Status 2: $name not exists in $_POST
        if(!array_key_exists($name,$_POST)) return 2; 
        $postInfo = $_POST[$name];
        //Status 3: $_POST[$name] is not a array
        if(!is_array($postInfo)) return 3;
        //Status 4: Keys not 0 and TokenId
        if(array_keys($postInfo) != [0,$sessInfo['id']]) return 4;
        //Status 5: Honeypot not empty
        if($postInfo[0] !== "") return 5;
        //Status 6: invalid Token
        if($postInfo[$sessInfo['id']] !== $sessInfo['token']) return 6;
        //Time-based check
        $reaactionTime = microtime(true) - $sessInfo['time'];
        //Status 7: Time < minInputTime
        if($reaactionTime < $this->minInputTime) return 7;
        //Status 8: Time > maxInputTime
        if($reaactionTime > $this->maxInputTime) return 8;
        //Ok
        return 0;
    }

    /*
     * check if is a bot
     * @param string $name: The same name must be used here as when calling protectionInput
     * @param bool $setIdInvalid, sets entry from name to invalid, default true,
     * @return bool
     */
    public function isBot(string $name, bool $setIdInvalid = true) : bool {
        return $this->status($name,$setIdInvalid) > 0;
    }


   
    
    

}