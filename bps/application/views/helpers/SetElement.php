<?php
class Zend_View_Helper_SetElement
{
    function setElement($params = array())
    {
        $result  = '';
        $checked = (isset($params['checked'])) ? $params['checked'] : '';

        $id  = (isset($params['id'])) ? $params['id'] : $params['name'];
        $ext = (isset($params['ext'])) ? $params['ext'] : '';
		$style = (isset($params['style'])) ? $params['style'] : '';
        switch ($params['type']) {
            case 'text':
                $result = "<input type=\"text\" name=\"{$params['name']}\" style='{$style}' id=\"{$id}\"" .
                          " value=\"{$params['value']}\" {$ext} />\n";
                break;
            case 'password':
                $result = "<input type=\"password\" name=\"{$params['name']}\" style='{$style}' id=\"{$id}\"" .
                          " value=\"{$params['value']}\" {$ext} />\n";
                break;
            case 'select':
                $result  = "<select name=\"{$params['name']}\" style='{$style}'  id=\"{$id}\" {$ext}>\n";
                foreach ($params['options'] as $value => $label) {
                    $selected = ($value === $params['value']) ? 'selected="selected"' : '';
                    $result .= "<option value=\"{$value}\" {$selected}>{$label}</option>\n";
                }
                $result .= "</select>\n";
                break;
            case 'checkbox':
                $checked = ($params['checked']) ? 'checked="checked"' : '';
                $result = "<input type=\"checkbox\" name=\"{$params['name']}\" style='{$style}'  id=\"{$id}\"" .
                          " value=\"{$params['value']}\" {$checked} {$ext} />\n";
                break;
            case 'radio':
                $checked = ($params['checked']) ? 'checked="checked"' : '';
                $result = "<input type=\"radio\" name=\"{$params['name']}\" style='{$style}'  id=\"{$id}\"" .
                          " value=\"{$params['value']}\" {$checked} {$ext} />\n";
                break;
            case 'textarea':
                $result = "<textarea name=\"{$params['name']}\" style='{$style}'  id=\"{$id}\" {$ext}>" .
                          "{$params['value']}" .
                          "</textarea>\n";
                break;
            case 'hidden':
                $result = "<input type=\"hidden\" name=\"{$params['name']}\" style='{$style}'  id=\"{$id}\"" .
                          " value=\"{$params['value']}\" />\n";
                break;
        }

        return $result;
   }
}
?>
