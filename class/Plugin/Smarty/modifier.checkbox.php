<?php
/**
 *  smarty modifier:チェックボックス用フィルタ
 *
 *  sample:
 *  <code>
 *  <input type="checkbox" name="test" {""|checkbox}>
 *  <input type="checkbox" name="test" {"1"|checkbox}>
 *  </code>
 *  <code>
 *  <input type="checkbox" name="test">
 *  <input type="checkbox" name="test" checkbox>
 *  </code>
 *
 *  @param  string  $string チェックボックスに渡されたフォーム値
 *  @return string  $stringが空文字列あるいは0以外の場合は"checked"
 */
function smarty_modifier_checkbox($string)
{
    if ($string != "" && $string != 0) {
        return "checked";
    }
}
