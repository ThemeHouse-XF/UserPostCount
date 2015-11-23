<?php

class ThemeHouse_UserPostCount_Listener_LoadClass extends ThemeHouse_Listener_LoadClass
{

    protected function _getExtendedClasses()
    {
        return array(
            'ThemeHouse_UserPostCount' => array(
                'controller' => array(
                    'XenForo_ControllerPublic_Thread'
                ), /* END 'controller' */
            ), /* END 'ThemeHouse_UserPostCount' */
        );
    } /* END _getExtendedClasses */

    public static function loadClassController($class, array &$extend)
    {
        $extend = self::createAndRun('ThemeHouse_UserPostCount_Listener_LoadClass', $class, $extend, 'controller');
    } /* END loadClassController */
}