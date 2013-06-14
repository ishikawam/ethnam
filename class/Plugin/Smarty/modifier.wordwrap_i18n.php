<?php
/**
 *  smarty modifier:文字列のwordwrap処理
 *
 *  [現在EUC-JP対応はEUC-JPのみ対応]
 *
 *  sample:
 *  <code>
 *  {"あいうaえaおaかきaaaくけこ"|wordrap_i18n:8}
 *  </code>
 *  <code>
 *  あいうa
 *  えaおaか
 *  きaaaく
 *  けこ
 *  </code>
 *
 *  @param  string  $string wordwrapする文字列
 *  @param  string  $break  改行文字
 *  @param  int     $width  wordwrap幅(半角$width文字でwordwrapする)
 *  @param  int     $indent インデント幅(半角$indent文字)
 *  @return string  wordwrap処理された文字列
 */
function smarty_modifier_wordwrap_i18n($string, $width, $break = "\n", $indent = 0)
{
    $r = "";
    $i = "$break" . str_repeat(" ", $indent);
    $tmp = $string;
    do {
        $n = strpos($tmp, $break);
        if ($n !== false && $n < $width) {
            $s = substr($tmp, 0, $n);
            $r .= $s . $i;
            $tmp = substr($tmp, strlen($s) + strlen($break));
            continue;
        }

        $s = mb_strimwidth($tmp, 0, $width, "", "EUC-JP");

        // EUC-JPのみ対応
        $n = strlen($s);
        if ($n >= $width && $tmp{$n} != "" && $tmp{$n} != " ") {
            while ((ord($s{$n-1}) & 0x80) == 0) {
                if ($s{$n-1} == " " || $n == 0) {
                    break;
                }
                $n--;
            }
        }
        $s = substr($s, 0, $n);

        $r .= $s . $i;
        $tmp = substr($tmp, strlen($s));
    } while (strlen($s) > 0);

    $r = preg_replace('/\s+$/', '', $r);

    return $r;
}
