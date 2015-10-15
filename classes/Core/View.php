<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 15.10.15
 * Time: 21:29
 */

namespace Core;


class View
{
    private $vidgetViews;

    /**
     * View constructor.
     * @param $vidgetViews
     */
    public function __construct(array $vidgetViews)
    {
        $this->vidgetViews = $vidgetViews;
    }

    public function renderState($state, array $appData, array $templateMap, Registry $registry)
    {
        $out = '';
        if (!is_null($templateMap[$state])) {{
            //$templateContent = file_get_contents(TEMPLATE_ROOT . "/" . $this->templateMap[$this->state]);
            $templateContent = (new \Utility\Template())->parse($templateMap[$state], array('site_root' => $registry->get(REG_SITE_ROOT)));

            $foundVidgetPlaceCount = preg_match_all('<[\w\s="-;]*data-vidgets="([\w,\s]+)"[\w\s="-;]*>', $templateContent, $matches, PREG_OFFSET_CAPTURE);
            //error_log('matches:' . print_r($matches, true), 3, 'my_errors.txt');
            $prevPos = 0;
            for($i = 0; $i < $foundVidgetPlaceCount; $i++) {
                $vidgetContent = $this->getVidgetSectionContent($matches[1][$i][0], $appData, $registry);
                $tagLength = strlen($matches[0][$i][0]) + 1;
                $out .= substr($templateContent, $prevPos, $matches[0][$i][1] + $tagLength - $prevPos);
                $out .= $vidgetContent;
                $prevPos = $matches[0][$i][1] + $tagLength;
            };
            $out .= substr($templateContent, $prevPos);
        }}
        //error_log('out:' . print_r($out, true), 3, 'my_errors.txt');
        return $out;
    }

    /**
     * Returns rendered content of the vidget collection, put to section
     * @param $vidgetString string kind of 'Vidget1,Vidget2', found in data-vidgets attribute of the tag
     * @return string rendered content
     */
    private function getVidgetSectionContent($vidgetString, array $appData, Registry $registry)
    {
        $vidgetList = explode(',', $vidgetString);
        $out = '';
        foreach($vidgetList as $vidgetName) {
            $out .= $this->getVidgetContent(trim($vidgetName), $appData, $registry);
        }
        return $out;
    }

    /**
     * Calls vidget and return its rendered output
     * @param $vidgetName string name of the vidget to load
     * @return string
     */
    private function getVidgetContent($vidgetName, array $appData, Registry $registry)
    {
        $className = VIDGET_NAMESPACE . '\\' . $vidgetName;
        $vidgetTemplateName = $this->vidgetViews[$vidgetName];
        return (new $className())->render($appData, $vidgetTemplateName, $registry);
    }


}