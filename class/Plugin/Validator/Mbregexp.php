<?php
// {{{ Ethna_Plugin_Validator_Mbegexp
/**
 *  �ޥ���Х����б�����ɽ���ˤ��Х�ǡ����ץ饰����
 *
 *  @author     Yoshinari Takaoka <takaoka@beatcraft.com>
 *  @access     public
 *  @package    Ethna
 */
class Ethna_Plugin_Validator_Mbregexp extends Ethna_Plugin_Validator
{
    /** @var    bool    ����������뤫�ե饰 */
    var $accept_array = false;

    /**
     *  ����ɽ���ˤ��ե������ͤΥ����å���Ԥ�(�ޥ���Х����б���
     *
     *  @access public
     *  @param  string  $name       �ե������̾��
     *  @param  mixed   $var        �ե��������
     *  @param  array   $params     �ץ饰����Υѥ�᡼��
     */
    function validate($name, $var, $params)
    {
        $true = true;
        $type = $this->getFormType($name);
        if (isset($params['mbregexp']) == false
            || $type == VAR_TYPE_FILE || $this->isEmpty($var, $type)) {
            return $true;
        }

        $encoding = (isset($params['encoding']))
                  ? $params['encoding']
                  : 'EUC-JP';
        mb_regex_encoding($encoding);

        if (mb_ereg($params['mbregexp'], $var) !== 1) {
            if (isset($params['error'])) {
                $msg = $params['error'];
            } else {
                $msg = _et('Please input {form} properly.');
            }
            return Ethna::raiseNotice($msg, E_FORM_REGEXP);
        }

        return $true;
    }
}
// }}}

