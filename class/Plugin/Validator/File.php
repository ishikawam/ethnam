<?php
// vim: foldmethod=marker
/**
 *  Ethna_Plugin_Validator_File.php
 *
 *  @author     ICHII Takashi <ichii386@schweetheart.jp>
 *  @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *  @package    Ethna
 *  @version    $Id$
 */

// UPLOAD_ERR_* ��̤����ξ��
if (defined('UPLOAD_ERR_OK') == false) { // PHP 4.3.0
    define('UPLOAD_ERR_OK', 0);
}
if (defined('UPLOAD_ERR_INI_SIZE') == false) { // PHP 4.3.0
    define('UPLOAD_ERR_INI_SIZE', 1);
}
if (defined('UPLOAD_ERR_FORM_SIZE') == false) { // PHP 4.3.0
    define('UPLOAD_ERR_FORM_SIZE', 2);
}
if (defined('UPLOAD_ERR_PARTIAL') == false) { // PHP 4.3.0
    define('UPLOAD_ERR_PARTIAL', 3);
}
if (defined('UPLOAD_ERR_NO_FILE') == false) { // PHP 4.3.0
    define('UPLOAD_ERR_NO_FILE', 4);
}
if (defined('UPLOAD_ERR_NO_TMP_DIR') == false) { // PHP 4.3.10, 5.0.3
    define('UPLOAD_ERR_NO_TMP_DIR', 6);
}
if (defined('UPLOAD_ERR_CANT_WRITE') == false) { // PHP 5.1.0
    define('UPLOAD_ERR_CANT_WRITE', 7);
}

// {{{ Ethna_Plugin_Validator_File
/**
 *  �ե���������å��ץ饰����
 *
 *  @author     ICHII Takashi <ichii386@schweetheart.jp>
 *  @access     public
 *  @package    Ethna
 */
class Ethna_Plugin_Validator_File extends Ethna_Plugin_Validator
{
    /** @var    bool    ����������뤫�ե饰 */
    var $accept_array = false;

    /**
     *  ���åץ��ɤ��줿�ե�����Υ����å���Ԥ�
     *  XXX: �ץ饰����Υ��顼�����ɤ�������
     *
     *  @access public
     *  @param  string  $name       �ե������̾��
     *  @param  mixed   $var        �ե��������
     *  @param  array   $params     �ץ饰����Υѥ�᡼��
     */
    function validate($name, $var, $params)
    {
        $true = true;
        if ($this->getFormType($name) != VAR_TYPE_FILE) {
            return $true;
        }

        // ���⤽�⥢�åץ��ɤ���Ƥ��ʤ����ϥ����å�
        if ($var['error'] == UPLOAD_ERR_NO_FILE) {
            return $true;
        }


        // ���顼�����ɤθ���
        $msg = '';
        switch ($var['error']) {
        case UPLOAD_ERR_INI_SIZE: 
            $msg = _et("Uploaded file size exceeds php.ini's upload_max_filesize directive.");
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $msg = _et('Uploaded File size exceeds MAX_FILE_SIZE specified in HTML Form.');
            break;
        case UPLOAD_ERR_PARTIAL:
            $msg= _et('File was only uploaded patially.');
            break;
        case UPLOAD_ERR_NO_FILE:
            $msg = _et('File was not uploaded.');
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $msg = _et('Temporary folder was not found.');
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $msg= _et('Could not write uploaded file to disk.');
            break;
        }
        if ($msg != '') {
            if (isset($params['error'])) {
                $msg = $params['error'];
            }
            return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FILE);
        }


        // tmp_name �θ���
        if (isset($var['tmp_name']) == false || is_uploaded_file($var['tmp_name']) == false) {
            if (isset($params['error'])) {
                $msg = $params['error'];
            } else {
                $msg = _et('invalid tmp_name.');
            }
            return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FILE);
        }


        // size �θ���
        if (isset($params['size_max'])) {
            $st = stat($var['tmp_name']);
            if ($st[7] > $this->_getSizeAsBytes($params['size_max'])) {
                if (isset($params['error'])) {
                    $msg = $params['error'];
                } else {
                    $msg = _et('Uploaded file size must be less than %s.');
                }
                return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FILE, array($params['size_max']));
            }
        }
        if (isset($params['size_min'])) {
            $st = stat($var['tmp_name']);
            if ($st[7] < $this->_getSizeAsBytes($params['size_min'])) {
                if (isset($params['error'])) {
                    $msg = $params['error'];
                } else {
                    $msg = _et('Uploaded file size must be more than %s.');
                }
                return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FILE, array($params['size_min']));
            }
        }


        // type �θ���
        if (isset($params['type'])) {
            $type_list = to_array($params['type']);
            $posted_mime = explode('/', $var['type'], 2);
            foreach ($type_list as $type) {
                $wanted_mime = explode('/', $type, 2);
                $test = (count($wanted_mime) == 1)
                        ? (strcasecmp($wanted_mime[0], $posted_mime[0]) == 0)
                : (strcasecmp($type, $var['type']) == 0);  
                if ($test == true) {
                    break;
                }
            }
            if ($test == false) {
                if (isset($params['error'])) {
                    $msg = $params['error'];
                } else {
                    $msg = _et('Invalid file type.');
                }
                return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FILE);
            }
        }

        // name(�ե�����̾)�θ���
        if (isset($params['name'])) {
            $test = ($params['name']{0} == '/')
                ? preg_match($params['name'], $var['name'])
                : (strcmp($params['name'], $var['name']) == 0);
            if ($test == false) {
                if (isset($params['error'])) {
                    $msg = $params['error'];
                } else {
                    $msg = _et('Invalid file name.');
                }
                return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FILE);
            }
        }

        return $true;
    }


    function _getSizeAsBytes($size)
    {
        $unit = 1;
        if (preg_match('/^([0-9]+)([mk])?(b(ytes?)?)?$/i', trim($size), $matches)) {
            if (isset($matches[1])) {
                $size = $matches[1];
            }
            if (isset($matches[2])) {
                if (strtolower($matches[2]) === 'm') {
                    $unit = 1048576;
                } else if (strtolower($matches[2]) === 'k') {
                    $unit = 1024;
                }
            }
        }
        return intval($matches[1]) * $unit;
    }
}
// }}}

