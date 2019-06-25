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

if (! defined('IN_ANWSION'))
{
	die();
}

class article extends AWS_ADMIN_CONTROLLER
{
	public function setup()
	{
		// TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(309));
        TPL::assign('menu_list',  $this->fetch_menu_list());
	}

	public function list_action()
	{
		if ($this->is_post())
		{
			foreach ($_POST as $key => $val)
			{
				if ($key == 'start_date' OR $key == 'end_date')
				{
					$val = base64_encode($val);
				}

				if ($key == 'keyword' OR $key == 'user_name')
				{
					$val = rawurlencode($val);
				}

				$param[] = $key . '-' . $val;
			}

			H::ajax_json_output(AWS_APP::RSM(array(
				'url' => get_js_url('/admin/article/list/' . implode('__', $param))
			), 1, null));
		}


		$where = array();

		if ($_GET['keyword'])
		{
			$where[] = "(`title` LIKE '%" . $this->model('article')->quote($_GET['keyword']) . "%')";
		}

		if ($_GET['start_date'])
		{
			$where[] = 'add_time >= ' . strtotime(base64_decode($_GET['start_date']));
		}

		if ($_GET['end_date'])
		{
			$where[] = 'add_time <= ' . strtotime('+1 day', strtotime(base64_decode($_GET['end_date'])));
		}

		if ($_GET['user_name'])
		{
			$user_info = $this->model('account')->get_user_info_by_username($_GET['user_name']);

			$where[] = 'uid = ' . intval($user_info['uid']);
		}

		if ($_GET['comment_count_min'])
		{
			$where[] = 'comments >= ' . intval($_GET['comment_count_min']);
		}

		if ($_GET['answer_count_max'])
		{
			$where[] = 'comments <= ' . intval($_GET['comment_count_max']);
		}

		$this->remove_article($where);
        $where[] = 'is_del = 0';
        if($_GET['cur'] == 'list'){
            $page = $_GET['page'];
        } else {
            $page = 1;
        }
		if ($articles_list = $this->model('article')->fetch_page('article', implode(' AND ', $where), 'id DESC', $page, $this->per_page))
		{
			$search_articles_total = $this->model('article')->found_rows();
		}

		if ($articles_list)
		{
			foreach ($articles_list AS $key => $val)
			{
				$articles_list_uids[$val['uid']] = $val['uid'];
			}

			if ($articles_list_uids)
			{
				$articles_list_user_infos = $this->model('account')->get_user_info_by_uids($articles_list_uids);
			}

			foreach ($articles_list AS $key => $val)
			{
				$articles_list[$key]['user_info'] = $articles_list_user_infos[$val['uid']];
			}
        	$url_param[] =  'cur-list';

			TPL::assign('pagination', AWS_APP::pagination()->initialize(array(
                'base_url' => get_js_url('/admin/article/list/') . implode('__', $url_param),
                'total_rows' => $search_articles_total,
                'per_page' => $this->per_page
            ))->create_links());
		}
		$this->crumb(AWS_APP::lang()->_t('文章管理'), 'admin/article/list/');

        TPL::assign('cur', $_GET['cur']);
		TPL::assign('articles_count', $search_articles_total);
		TPL::assign('list', $articles_list);

		TPL::output('admin/article/list');
	}


	public function comment_action(){
        // TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(904));
        TPL::assign('menu_list',  $this->fetch_menu_list());
        if ($this->is_post())
        {
            foreach ($_POST as $key => $val)
            {
                if ($key == 'start_date' OR $key == 'end_date')
                {
                    $val = base64_encode($val);
                }

                if ($key == 'keyword' OR $key == 'user_name')
                {
                    $val = rawurlencode($val);
                }

                $param[] = $key . '-' . $val;
            }

            H::ajax_json_output(AWS_APP::RSM(array(
                'url' => get_js_url('/admin/article/comment/' . implode('__', $param))
            ), 1, null));
        }


        $where = array();

        if ($_GET['keyword'])
        {
            $where[] = "(`message` LIKE '%" . $this->model('article')->quote($_GET['keyword']) . "%')";
        }

        if ($_GET['start_date'])
        {
            $where[] = 'add_time >= ' . strtotime(base64_decode($_GET['start_date']));
        }

        if ($_GET['end_date'])
        {
            $where[] = 'add_time <= ' . strtotime('+1 day', strtotime(base64_decode($_GET['end_date'])));
        }

        if ($_GET['user_name'])
        {
            $user_info = $this->model('account')->get_user_info_by_username($_GET['user_name']);

            $where[] = 'uid = ' . intval($user_info['uid']);
        }

        $this->remove_comment_article($where);
        $where[] = 'is_del = 0';
        if($_GET['cur'] == 'list'){
            $page = $_GET['page'];
        } else {
            $page = 1;
        }
        if ($articles_list = $this->model('article')->fetch_page('article_comments', implode(' AND ', $where), 'id DESC', $page, $this->per_page))
        {
            $search_articles_total = $this->model('article')->found_rows();
        }

        if ($articles_list)
        {
            foreach ($articles_list AS $key => $val)
            {
                $articles_list_uids[$val['uid']] = $val['uid'];
            }

            if ($articles_list_uids)
            {
                $articles_list_user_infos = $this->model('account')->get_user_info_by_uids($articles_list_uids);
            }

            foreach ($articles_list AS $key => $val)
            {
                $articles_list[$key]['user_info'] = $articles_list_user_infos[$val['uid']];
            }
            $url_param[] =  'cur-list';

            TPL::assign('pagination', AWS_APP::pagination()->initialize(array(
                'base_url' => get_js_url('/admin/article/comment/') . implode('__', $url_param),
                'total_rows' => $search_articles_total,
                'per_page' => $this->per_page
            ))->create_links());
        }

        $this->crumb(AWS_APP::lang()->_t('文章评论管理'), 'admin/article/comment/');
        TPL::assign('cur', $_GET['cur']);
        TPL::assign('articles_count', $search_articles_total);
        TPL::assign('list', $articles_list);
        TPL::output('admin/article/comment');
    }
    /**
     * @description [删除的文章]
     * @author Laushow
     * @datatime 2018/7/18 14:58
     */
	private function remove_article(){
        $where[] = 'is_del = 1';
        if($_GET['cur'] == 'remove'){
            $page = $_GET['page'];
        } else {
            $page = 1;
        }
        if ($articles_list = $this->model('article')->fetch_page('article', implode(' AND ', $where), 'id DESC', $page, $this->per_page))
        {
            $search_articles_total = $this->model('article')->found_rows();
        }
        if ($articles_list)
        {
            foreach ($articles_list AS $key => $val)
            {
                $articles_list_uids[$val['uid']] = $val['uid'];
            }

            if ($articles_list_uids)
            {
                $articles_list_user_infos = $this->model('account')->get_user_info_by_uids($articles_list_uids);
            }

            foreach ($articles_list AS $key => $val)
            {
                $articles_list[$key]['user_info'] = $articles_list_user_infos[$val['uid']];
            }

            $url_param[] = 'cur-remove';
            TPL::assign('remove_pagination', AWS_APP::pagination()->initialize(array(
                'base_url' => get_js_url('/admin/article/list/') . implode('__', $url_param),
                'total_rows' => $search_articles_total,
                'per_page' => $this->per_page
            ))->create_links());
        }
        TPL::assign('remove_articles_count', $search_articles_total);
        TPL::assign('remove_list', $articles_list);
    }

    /**
     * @description [删除的文章评论]
     * @author Laushow
     * @datatime 2018/7/18 14:58
     */
    private function remove_comment_article()
    {
        $where[] = 'is_del = 1';
        if($_GET['cur'] == 'remove'){
            $page = $_GET['page'];
        } else {
            $page = 1;
        }
        if ($articles_list = $this->model('article')->fetch_page('article_comments', implode(' AND ', $where), 'id DESC', $page, $this->per_page)) {
            $search_articles_total = $this->model('article')->found_rows();
        }

        if ($articles_list) {
            foreach ($articles_list AS $key => $val) {
                $articles_list_uids[$val['uid']] = $val['uid'];
            }

            if ($articles_list_uids) {
                $articles_list_user_infos = $this->model('account')->get_user_info_by_uids($articles_list_uids);
            }

            foreach ($articles_list AS $key => $val) {
                $articles_list[$key]['user_info'] = $articles_list_user_infos[$val['uid']];
            }
            $url_param[] = 'cur-remove';
            TPL::assign('remove_pagination', AWS_APP::pagination()->initialize(array(
                'base_url' => get_js_url('/admin/article/comment/') . implode('__', $url_param),
                'total_rows' => $search_articles_total,
                'per_page' => $this->per_page
            ))->create_links());
        }
        TPL::assign('remove_list', $articles_list);
    }
}