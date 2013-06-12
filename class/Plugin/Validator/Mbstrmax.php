<?php
// vim: foldmethod=marker
/**
 *  Mbstrmax.php
 *
 *  @author     Yoshinari Takaoka <takaoka@beatcraft.com>
 *  @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *  @package    Ethna
 *  @version    $Id$
 */

// {{{ Ethna_Plugin_Validator_Mbstrmax
/**
 *  �����ͥ����å��ץ饰���� (�ޥ���Х���ʸ������)
 *
 *  NOTE:
 *      - mbstring ��ͭ���ˤ��Ƥ���ɬ�פ�����ޤ���
 *      - ���顼��å������ϡ�����Ⱦ�Ѥ���̤��ޤ���
 * 
 *  @author     Yoshinari Takaoka <takaoka@beatcraft.com>
 *  @access     public
 *  @package    Ethna
 */
class Ethna_Plugin_Validator_Mbstrmax extends Ethna_Plugin_Validator
{
    /** @var    bool    ����������뤫�ե饰 */
    var $accept_array = false;

    /**
     *  �����ͤΥ����å���Ԥ�
     *
     *  @access public
     *  @param  string  $name       �ե������̾��
     *  @param  mixed   $var        �ե��������
     *  @param  array   $params     �ץ饰����Υѥ�᡼��
     *  @return true: ����  Ethna_Error: ���顼
     */
    function validate($name, $var, $params)
    {
        $true = true;
        $type = $this->getFormType($name);
        if (isset($params['mbstrmax']) == false || $this->isEmpty($var, $type)) {
            return $true;
        }

        if ($type == VAR_TYPE_STRING) {
            $max_param = $params['mbstrmax'];
            if (mb_strlen($var) > $max_param) {
                if (isset($params['error'])) {
                    $msg = $params['error'];
                } else {
                    $msg = _et('Please input less than %d characters to {form}.');
                }
                return Ethna::raiseNotice($msg, E_FORM_MAX_STRING,
                        array($max_param));
            }
        }

        return $true;
    }
}
// }}}

