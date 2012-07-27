<?php

require_once 'Zend/Form/Decorator/HtmlTag.php';

class Custom_Form_Decorator_CustomHtmlTag extends Zend_Form_Decorator_HtmlTag
{
    /**
     * Convert options to tag attributes (modified)
     *
     * @return string
     */
    protected function _htmlAttribs(array $attribs)
    {
        $xhtml = '';
        $enc   = $this->_getEncoding();
        foreach ((array) $attribs as $key => $val) {
            $key = htmlspecialchars($key, ENT_COMPAT, $enc);
            if (is_array($val)) {
                if (array_key_exists('callback', $val)
                    && (is_callable($val['callback']) || is_string($val['callback']))) {
                    $callback = $val['callback'];
                    if (is_string($val['callback'])) {
                        eval('$val = ' . $callback);
                    } else {
                        $val = $callback($this);
                    }
                } else {
                    $val = implode(' ', $val);
                }
            }
            $val    = htmlspecialchars($val, ENT_COMPAT, $enc);
            $xhtml .= " $key=\"$val\"";
        }
        return $xhtml;
    }
}
