<?php

/**
 *
 * @see XenForo_ControllerPublic_Thread
 */
class ThemeHouse_UserPostCount_Extend_XenForo_ControllerPublic_Thread extends XFCP_ThemeHouse_UserPostCount_Extend_XenForo_ControllerPublic_Thread
{

    /**
     *
     * @see XenForo_ControllerPublic_Thread::actionIndex()
     */
    public function actionIndex()
    {
        $response = parent::actionIndex();
        if ($response instanceof XenForo_ControllerResponse_View) {
            $allowInThread = XenForo_Application::get('options')->th_userPostCount_allowInThread;
            if (XenForo_Visitor::getInstance()->hasPermission('forum', 'thViewPostsSummary')) {
                $response->params['canViewUserThreadSummary'] = true;
                if ($allowInThread) {
                    $threadId = $response->params['thread']['thread_id'];
                    $userPostCounts = $this->_getUserPostCountInThread($threadId);
                    if ($userPostCounts) {
                        $response->params['userPostCountData'] = array(
                            'limit' => count($userPostCounts),
                            'records' => $userPostCounts
                        );
                    }
                }
            }
        }
        return $response;
    } /* END actionIndex */

    public function actionThreadPosters()
    {
        $threadId = $this->_input->filterSingle('thread_id', XenForo_Input::UINT);
        
        if (! $threadId) {
            return $this->responseError('no_thread_found');
        }
        
        /* @var $visitor XenForo_Visitor */
        $visitor = XenForo_Visitor::getInstance();
        $thread = $this->_getThreadModel()->getThreadById($threadId);
        
        if (! $visitor->hasPermission('forum', 'thViewPostsSummary')) {
            return $this->responseNoPermission();
        }
        
        $userPostCounts = $this->_getUserPostCountInThread($threadId);
        if (! $userPostCounts) {
            return $this->responseError('no_thread_found');
        }
        
        $viewParams = array(
            'thread' => $thread,
            'records' => $userPostCounts
        );
        
        return $this->responseView('ThemeHouse_UserPostCount_ViewPublic_Thread_ThreadPosters', 'th_thread_posters_userpostcount', $viewParams);
    } /* END actionThreadPosters */

    protected function _getUserPostCountInThread($threadId)
    {
        $db = XenForo_Application::getDb();
        return $db->fetchAll('SELECT user.*, tup.post_count
            FROM xf_thread_user_post as tup
            LEFT JOIN xf_user AS user ON (user.user_id = tup.user_id)
            WHERE tup.thread_id = ?
            ORDER BY tup.post_count DESC', $threadId);
    } /* END _getUserPostCountInThread */
}