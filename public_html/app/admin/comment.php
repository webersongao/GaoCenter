<?php
/*
+--------------------------------------------------------------------------
|   WeCenter [#RELEASE_VERSION#]
|   ========================================
|   by WeCenter Software
|   © 2011 - 2014 WeCenter. All Rights Reserved
|   http://www.wecenter.com
|   ========================================
|   Support: WeCenter@qq.com
|
+---------------------------------------------------------------------------
*/


if (!defined('IN_ANWSION')) {
    die;
}

class comment extends AWS_ADMIN_CONTROLLER
{
    public function setup()
    {
        if (!$this->user_info['permission']['is_administortar']) {
            H::redirect_msg(AWS_APP::lang()->_t('你没有访问权限, 请重新登录'));
        }

        @set_time_limit(0);
    }


    public function question_comment_list_action()
    {
        // TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(902));
        TPL::assign('menu_list',  $this->fetch_menu_list());

        if ($this->is_post()) {
            foreach ($_POST as $key => $val) {
                if ($key == 'start_date' OR $key == 'end_date') {
                    $val = base64_encode($val);
                }

                if ($key == 'keyword' OR $key == 'user_name') {
                    $val = rawurlencode($val);
                }

                $param[] = $key . '-' . $val;
            }

            H::ajax_json_output(AWS_APP::RSM(array(
                'url' => get_js_url('/admin/comment/question_comment_list/' . implode('__', $param))
            ), 1, null));
        }

        $where = array();

        if ($_GET['keyword']) {
            $where[] = "message like '%" . $_GET['keyword'] . "%'";
        }

        if (base64_decode($_GET['start_date'])) {
            $where[] = 'time >= ' . strtotime(base64_decode($_GET['start_date']));
        }

        if (base64_decode($_GET['end_date'])) {
            $where[] = 'time <= ' . strtotime('+1 day', strtotime(base64_decode($_GET['end_date'])));
        }

        if ($_GET['user_name']) {
            $user_info = $this->model('account')->get_user_info_by_username($_GET['user_name']);
            $where[] = 'uid = ' . intval($user_info['uid']);
        }
        $this->remove_question($where);
        $where[] = 'is_del = 0';
        if ($_GET['cur'] == 'list') {
            $page = $_GET['page'];
        } else {
            $page = 1;
        }
        if ($comment_list = $this->model('question')->fetch_page('question_comments', implode(' AND ', $where), 'id DESC', $page, $this->per_page)) {
            $total_rows = $this->model('question')->count('question_comments', implode(' AND ', $where));

            foreach ($comment_list AS $key => $val) {
                $comment_list_uids[$val['uid']] = $val['uid'];
            }

            if ($comment_list_uids) {
                $comment_list_user_infos = $this->model('account')->get_user_info_by_uids($comment_list_uids);
            }

            foreach ($comment_list AS $key => $val) {
                $comment_list[$key]['user_info'] = $comment_list_user_infos[$val['uid']];
            }
        }

        $url_param = array();

        foreach ($_GET as $key => $val) {
            if (!in_array($key, array('app', 'c', 'act', 'page'))) {
                $url_param[] = $key . '-' . $val;
            }
        }
        $url_param[] = 'cur-list';
        TPL::assign('pagination', AWS_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/admin/comment/question_comment_list/') . implode('__', $url_param),
            'total_rows' => $total_rows,
            'per_page' => $this->per_page
        ))->create_links());

        $this->crumb(AWS_APP::lang()->_t('问题评论管理'), 'admin/comment/question_comment_list/');

        TPL::assign('comment_count', $total_rows);
        TPL::assign('keyword', $_GET['keyword']);
        TPL::assign('list', $comment_list);
        TPL::assign('cur', $_GET['cur']);
        TPL::output('admin/comment/question_comment_list');
    }

    /**
     * 删除answer comment
     * @return [type] [description]
     */
    public function question_comment_manage_action()
    {
        if (!$_POST['ids']) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请选择问题进行操作')));
        }
        switch ($_POST['action']) {
            case 'remove':
                foreach ($_POST['ids'] AS $comment_id) {
                    $comment = $this->model('question')->get_comment_by_id($comment_id);
                    $this->model('question')->remove_comment(intval($comment_id));
                    $this->model('question')->update_question_comments_count(intval($comment['question_id']));
                }
                H::ajax_json_output(AWS_APP::RSM(null, 1, null));
                break;
            case 'recover':
                foreach ($_POST['ids'] AS $comment_id) {
                    $comment = $this->model('question')->get_comment_by_id($comment_id);
                    $this->model('question')->recover_comment($comment_id);
                    $this->model('question')->update_question_comments_count(intval($comment['question_id']));
                }
                H::ajax_json_output(AWS_APP::RSM(null, 1, null));
                break;
        }
    }

    public function answer_comment_list_action()
    {
        // TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(903));
        TPL::assign('menu_list',  $this->fetch_menu_list());

        if ($this->is_post()) {
            foreach ($_POST as $key => $val) {
                if ($key == 'start_date' OR $key == 'end_date') {
                    $val = base64_encode($val);
                }

                if ($key == 'keyword' OR $key == 'user_name') {
                    $val = rawurlencode($val);
                }

                $param[] = $key . '-' . $val;
            }

            H::ajax_json_output(AWS_APP::RSM(array(
                'url' => get_js_url('/admin/comment/answer_comment_list/' . implode('__', $param))
            ), 1, null));
        }

        $where = array();

        if ($_GET['keyword']) {
            $where[] = "message like '%" . $_GET['keyword'] . "%'";
        }

        if (base64_decode($_GET['start_date'])) {
            $where[] = 'time >= ' . strtotime(base64_decode($_GET['start_date']));
        }

        if (base64_decode($_GET['end_date'])) {
            $where[] = 'time <= ' . strtotime('+1 day', strtotime(base64_decode($_GET['end_date'])));
        }

        if ($_GET['user_name']) {
            $user_info = $this->model('account')->get_user_info_by_username($_GET['user_name']);
            $where[] = 'uid = ' . intval($user_info['uid']);
        }
        $this->remove_answer_comment($where);
        $where[] = 'is_del = 0';
        if ($comment_list = $this->model('answer')->fetch_page('answer_comments', implode(' AND ', $where), 'id DESC', $_GET['page'], $this->per_page)) {
            $total_rows = $this->model('answer')->count('answer_comments', implode(' AND ', $where));

            foreach ($comment_list AS $key => $val) {
                $comment_list_uids[$val['uid']] = $val['uid'];
            }

            if ($comment_list_uids) {
                $comment_list_user_infos = $this->model('account')->get_user_info_by_uids($comment_list_uids);
            }

            foreach ($comment_list AS $key => $val) {
                $comment_list[$key]['user_info'] = $comment_list_user_infos[$val['uid']];
            }
            $url_param = array();

            foreach ($_GET as $key => $val) {
                if (!in_array($key, array('app', 'c', 'act', 'page'))) {
                    $url_param[] = $key . '-' . $val;
                }
            }
            $url_param[] = 'cur-list';

            TPL::assign('pagination', AWS_APP::pagination()->initialize(array(
                'base_url' => get_js_url('/admin/comment/answer_comment_list/') . implode('__', $url_param),
                'total_rows' => $total_rows,
                'per_page' => $this->per_page
            ))->create_links());
        }


        $this->crumb(AWS_APP::lang()->_t('回复评论管理'), 'admin/comment/answer_comment_list/');

        TPL::assign('comment_count', $total_rows);
        TPL::assign('keyword', $_GET['keyword']);
        TPL::assign('list', $comment_list);

        TPL::assign('cur', $_GET['cur']);
        TPL::output('admin/comment/answer_comment_list');
    }

    /**
     * 删除answer comment
     * @return [type] [description]
     */
    public function answer_comment_manage_action()
    {
        if (!$_POST['ids']) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请选择问题进行操作')));
        }
        switch ($_POST['action']) {
            case 'remove':
                foreach ($_POST['ids'] AS $comment_id) {
                    $this->model('answer')->remove_comment(intval($comment_id));
                }
                H::ajax_json_output(AWS_APP::RSM(null, 1, null));
                break;
            case 'recover':
                foreach ($_POST['ids'] AS $comment_id) {
                    $this->model('answer')->recover_comment_answer(intval($comment_id));
                }
                H::ajax_json_output(AWS_APP::RSM(null, 1, null));
                break;
        }
    }

    public function answer_list_action()
    {
        // TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(901));
        TPL::assign('menu_list',  $this->fetch_menu_list());

        if ($this->is_post()) {
            foreach ($_POST as $key => $val) {
                if ($key == 'start_date' OR $key == 'end_date') {
                    $val = base64_encode($val);
                }

                if ($key == 'keyword' OR $key == 'user_name') {
                    $val = rawurlencode($val);
                }

                $param[] = $key . '-' . $val;
            }

            H::ajax_json_output(AWS_APP::RSM(array(
                'url' => get_js_url('/admin/comment/answer_list/' . implode('__', $param))
            ), 1, null));
        }

        $where = array();

        if ($_GET['keyword']) {
            $where[] = "answer_content like '%" . $_GET['keyword'] . "%'";
        }

        if (base64_decode($_GET['start_date'])) {
            $where[] = 'add_time >= ' . strtotime(base64_decode($_GET['start_date']));
        }

        if (base64_decode($_GET['end_date'])) {
            $where[] = 'add_time <= ' . strtotime('+1 day', strtotime(base64_decode($_GET['end_date'])));
        }

        if ($_GET['user_name']) {
            $user_info = $this->model('account')->get_user_info_by_username($_GET['user_name']);
            $where[] = 'uid = ' . intval($user_info['uid']);
        }
        $this->remove_answer($where);
        $where[] = 'is_del = 0';

        if ($_GET['cur'] == 'list') {
            $page = $_GET['page'];
        } else {
            $page = 1;
        }
        if ($comment_list = $this->model('answer')->fetch_page('answer', implode(' AND ', $where), 'answer_id DESC', $page, $this->per_page)) {
            $total_rows = $this->model('answer')->count('answer', implode(' AND ', $where));

            foreach ($comment_list AS $key => $val) {
                $comment_list_uids[$val['uid']] = $val['uid'];
            }

            if ($comment_list_uids) {
                $comment_list_user_infos = $this->model('account')->get_user_info_by_uids($comment_list_uids);
            }

            foreach ($comment_list AS $key => $val) {
                $comment_list[$key]['user_info'] = $comment_list_user_infos[$val['uid']];
            }

            $url_param = array();

            foreach ($_GET as $key => $val) {
                if (!in_array($key, array('app', 'c', 'act', 'page'))) {
                    $url_param[] = $key . '-' . $val;
                }
            }
            $url_param[] = 'cur-list';
            TPL::assign('pagination', AWS_APP::pagination()->initialize(array(
                'base_url' => get_js_url('/admin/comment/answer_list/') . implode('__', $url_param),
                'total_rows' => $total_rows,
                'per_page' => $this->per_page
            ))->create_links());
        }


        $this->crumb(AWS_APP::lang()->_t('回复管理'), 'admin/comment/answer_list/');

        TPL::assign('comment_count', $total_rows);
        TPL::assign('keyword', $_GET['keyword']);
        TPL::assign('list', $comment_list);
        TPL::assign('cur', $_GET['cur']);

        TPL::output('admin/comment/answer_list');
    }

    public function answer_manage_action()
    {
        if (!$_POST['answer_ids']) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请选择要删除的信息进行操作')));
        }
        switch ($_POST['action']) {
            case 'remove':
                foreach ($_POST['answer_ids'] AS $comment_id) {
                    $this->model('answer')->remove_answer(intval($comment_id));
                }
                H::ajax_json_output(AWS_APP::RSM(null, 1, null));
                break;
            case 'recover':
                foreach ($_POST['answer_ids'] AS $comment_id) {
                    $this->model('answer')->recover_answer($comment_id);
                }
                H::ajax_json_output(AWS_APP::RSM(null, 1, null));
                break;
            case 'remove_complain':
                foreach ($_POST['answer_ids'] AS $comment_id) {
                    $this->model('question')->remove_complain($comment_id);
                }
                H::ajax_json_output(AWS_APP::RSM(null, 1, null));
                break;
        }
    }

    /**
     * @description [已删除回答]
     * @param $where
     * @author Laushow
     * @datatime 2018/7/18 17:23
     */
    private function remove_answer($where)
    {
        $where[] = 'is_del =1 ';
        if ($comment_list = $this->model('answer')->fetch_page('answer', implode(' AND ', $where), 'answer_id DESC', $_GET['page'], $this->per_page)) {
            $total_rows = $this->model('answer')->count('answer', implode(' AND ', $where));

            foreach ($comment_list AS $key => $val) {
                $comment_list_uids[$val['uid']] = $val['uid'];
            }

            if ($comment_list_uids) {
                $comment_list_user_infos = $this->model('account')->get_user_info_by_uids($comment_list_uids);
            }

            foreach ($comment_list AS $key => $val) {
                $comment_list[$key]['user_info'] = $comment_list_user_infos[$val['uid']];
            }
            $url_param = array();
            foreach ($_GET as $key => $val) {
                if (!in_array($key, array('app', 'c', 'act', 'page'))) {
                    $url_param[] = $key . '-' . $val;
                }
            }
            TPL::assign('remove_pagination', AWS_APP::pagination()->initialize(array(
                'base_url' => get_js_url('/admin/comment/answer_list/') . implode('__', $url_param),
                'total_rows' => $total_rows,
                'per_page' => $this->per_page
            ))->create_links());
        }
        TPL::assign('remove_list', $comment_list);
    }

    /**
     * @description [已删除评论]
     * @param $where
     * @author Laushow
     * @datatime 2018/7/18 17:45
     */
    private function remove_question($where)
    {
        $where[] = 'is_del = 1';
        if ($_GET['cur'] == 'remove') {
            $page = $_GET['page'];
        } else {
            $page = 1;
        }
        if ($comment_list = $this->model('question')->fetch_page('question_comments', implode(' AND ', $where), 'id DESC', $page, $this->per_page)) {
            $total_rows = $this->model('question')->count('question_comments', implode(' AND ', $where));
            foreach ($comment_list AS $key => $val) {
                $comment_list_uids[$val['uid']] = $val['uid'];
            }
            if ($comment_list_uids) {
                $comment_list_user_infos = $this->model('account')->get_user_info_by_uids($comment_list_uids);
            }
            foreach ($comment_list AS $key => $val) {
                $comment_list[$key]['user_info'] = $comment_list_user_infos[$val['uid']];
            }

            $url_param = array();
            foreach ($_GET as $key => $val) {
                if (!in_array($key, array('app', 'c', 'act', 'page'))) {
                    $url_param[] = $key . '-' . $val;
                }
            }
            $url_param[] = 'cur-remove';
            TPL::assign('remove_pagination', AWS_APP::pagination()->initialize(array(
                'base_url' => get_js_url('/admin/comment/question_comment_list/') . implode('__', $url_param),
                'total_rows' => $total_rows,
                'per_page' => $this->per_page
            ))->create_links());
        }
        TPL::assign('remove_list', $comment_list);
    }

    /**
     * @description [已回复 删除评论]
     * @param $where
     * @author Laushow
     * @datatime 2018/7/18 18:14
     */
    private function remove_answer_comment($where)
    {
        $where[] = 'is_del = 1';
        if ($comment_list = $this->model('answer')->fetch_page('answer_comments', implode(' AND ', $where), 'id DESC', $_GET['page'], $this->per_page)) {
            $total_rows = $this->model('answer')->count('answer_comments', implode(' AND ', $where));
            foreach ($comment_list AS $key => $val) {
                $comment_list_uids[$val['uid']] = $val['uid'];
            }
            if ($comment_list_uids) {
                $comment_list_user_infos = $this->model('account')->get_user_info_by_uids($comment_list_uids);
            }
            foreach ($comment_list AS $key => $val) {
                $comment_list[$key]['user_info'] = $comment_list_user_infos[$val['uid']];
            }
            $url_param = array();
            foreach ($_GET as $key => $val) {
                if (!in_array($key, array('app', 'c', 'act', 'page'))) {
                    $url_param[] = $key . '-' . $val;
                }
            }
            $url_param[] = 'cur-remove';
            TPL::assign('remove_pagination', AWS_APP::pagination()->initialize(array(
                'base_url' => get_js_url('/admin/comment/answer_comment_list/') . implode('__', $url_param),
                'total_rows' => $total_rows,
                'per_page' => $this->per_page
            ))->create_links());
        }

        TPL::assign('remove_list', $comment_list);
    }

    //投诉列表
    public function complain_list_action()
    {
        // TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(899));
        TPL::assign('menu_list',  $this->fetch_menu_list());

        $where = array();
        $where[] = 'hide = 0';
        if ($comment_list = $this->model('answer')->fetch_page('question_complain', implode(' AND ', $where), 'id DESC,stat ASC', $_GET['page'], $this->per_page)) {
            $total_rows = $this->model('answer')->count('question_complain', implode(' AND ', $where));
            print_r($total_rows);

            foreach ($comment_list AS $key => $val) {
                $comment_list_uids[$val['uid']] = $val['uid'];
            }

            if ($comment_list_uids) {
                $comment_list_user_infos = $this->model('account')->get_user_info_by_uids($comment_list_uids);
            }

            foreach ($comment_list AS $key => $val) {
                $comment_list[$key]['user_info'] = $comment_list_user_infos[$val['uid']];
                $comment_list[$key]['question_info'] = $this->model('question')->get_question_info_by_id($val['question_id']);
            }
        }

        TPL::assign('pagination', AWS_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/admin/comment/complain_list/'),
            'total_rows' => $total_rows,
            'per_page' => $this->per_page
        ))->create_links());

        $this->crumb(AWS_APP::lang()->_t('投诉管理'), 'admin/comment/complain_list/');

        TPL::assign('comment_count', $total_rows);
        TPL::assign('keyword', $_GET['keyword']);
        TPL::assign('list', $comment_list);

        TPL::output('admin/comment/complain_list');
    }

    public function complain_edit_action()
    {
        // TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(899));
        TPL::assign('menu_list',  $this->fetch_menu_list());
        $this->crumb(AWS_APP::lang()->_t('投诉处理'), 'admin/comment/complain_edit/');

        if (!$complain = $this->model('answer')->fetch_row('question_complain', 'id = ' . intval($_GET['id']))) {
            H::redirect_msg(AWS_APP::lang()->_t('该条投诉信息不存在'), '/admin/comment/complain_list/');
        }

        $answer = $this->model('answer')->get_answer_by_id($complain['answer_id']);
        $question = $this->model('question')->get_question_info_by_id($complain['question_id']);

        TPL::assign('question', $question);
        TPL::assign('answer', $answer);
        TPL::assign('complain', $complain);

        TPL::output('admin/comment/complain_edit');
    }

    public function save_complain_action()
    {
        if (!$complain = $this->model('answer')->fetch_row('question_complain', 'id = ' . intval($_POST['id']))) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('该条投诉信息不存在')));
        }

        if (trim($_POST['remarks']) == '') {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请填写备注')));
        }

        $re = $this->model('question')->update_complain_attrib_fields(array(
            'stat' => intval($_POST['stat']),
            'remarks' => htmlspecialchars($_POST['remarks']),
            'answer_id' => $complain['answer_id'],
            'question_id' => $complain['question_id'],
        ), $complain['id']);
        if ($re) {
            if (intval($_POST['stat']) == 1) {
                $this->model('notify')->send(0, $complain['uid'], notify_class::TYPE_BEST_ANSWER_WARN, notify_class::CATEGORY_QUESTION, $complain['question_id'], array('question_id' => $complain['question_id']));
            }
            if (intval($_POST['stat']) == 2) {
                $this->model('notify')->send(0, $complain['uid'], notify_class::TYPE_BEST_ANSWER_WARN_ClOSED, notify_class::CATEGORY_QUESTION, $complain['question_id'], array('question_id' => $complain['question_id']));
            }
            H::ajax_json_output(AWS_APP::RSM(array(
                'url' => get_js_url('/admin/comment/complain_list/')
            ), 1, AWS_APP::lang()->_t('处理成功')));
        } else {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('处理失败')));
        }

    }
}
