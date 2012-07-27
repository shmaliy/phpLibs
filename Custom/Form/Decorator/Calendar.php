<?php
class Custom_Form_Decorator_Calendar extends Zend_Form_Decorator_Abstract
{
    /**
     * Получение строк подключения Javacript и CSS для календаря
     * Статическая переменная $jsAndCss отвечает за то, чтобу подключение
     * осуществлялось только один раз
     *
     * @return string
     */
    private function _getJsAndCss()
    {
        static $jsAndCss = null;
        
        if($jsAndCss === null) {

            $jsAndCss = '
            <style type="text/css">@import url(/js/calendar/skins/aqua/theme.css);</style>
            <script type="text/javascript" src="/js/calendar/calendar.js"></script>
            <script type="text/javascript" src="/js/calendar/lang/calendar-ru.js"></script>
            <script type="text/javascript" src="/js/calendar/calendar-setup.js"></script>
            ';
            
            //return $jsAndCss;
        }
        return '';
    }
    
    
    /**
     * Получение кода ссылки и изображения каледаря. Настройка календаря
     *
     * @return string
     */
    private function _getCalendarLink()
    {
        $calendarLink = '
            <a href="#" id="' . $this->getElement()->getName() . '_calendar">
                <img class="calendar-image" src = "/js/calendar/calendar.gif">
            </a>
    
            <script type="text/javascript">
                Calendar.setup(
                  {
                    inputField  : "' . $this->getElement()->getName() . '",
                    ifFormat    : "%d.%m.%Y",
                    button      : "' . $this->getElement()->getName() . '_calendar",
                    firstDay    : 1
                  }
                );
            </script>
        ';
        
        return $calendarLink;
    }
    
    
    /**
     * Рендеринг декоратора
     *
     * @param string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        if (!$element instanceof Zend_Form_Element) {
            return $content;
        }        
        
        $view = $element->getView();
        if (null === $view) {
            return $content;
        }
        
        $name = $element->getName() . '-calendar';
        $val = $element->getValue();
        if (is_null($val) || empty($val) || $val == 0) {
            $year    = '0000';
            $month   = '00';
            $day     = '00';
            $hour    = '00';
            $minute  = '00';
            $secound = '00';
        } else {
            $year    = date('Y', $val);
            $month   = date('m', $val);
            $day     = date('d', $val);
            $hour    = date('H', $val);
            $minute  = date('i', $val);
            $secound = date('s', $val);
        }
        
        
        $xhtml = $view->formText($name . 'Year', $year, array('size' => 4, 'maxlength' => 4, 'class' => 'calendarYear')) . ' - '
               . $view->formText($name . 'Month', $month, array('size' => 2, 'maxlength' => 2, 'class' => 'calendarMonth')) . ' - '
               . $view->formText($name . 'Day', $day, array('size' => 2, 'maxlength' => 2, 'class' => 'calendarDay')) . ' &nbsp '
               . $view->formText($name . 'Hour', $hour, array('size' => 2, 'maxlength' => 2, 'class' => 'calendarHour')) . ' : '
               . $view->formText($name . 'Minute', $minute, array('size' => 3, 'maxlength' => 2, 'class' => 'calendarMinute')) . ' : '
               . $view->formText($name . 'Secound', $secound, array('size' => 2, 'maxlength' => 2, 'class' => 'calendarSecound')) . ' '
               . $view->formButton($name . 'Icon', '...');
        
        return $this->_getJsAndCss() . '<div class="' . $name . '" style="display:none">'
                                     . $content . '</div>' . $xhtml;
    }
}