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


if (!defined('IN_ANWSION'))
{
	die;
}

class main extends AWS_CONTROLLER
{
	public function get_access_rule()
	{
		$rule_action['rule_type'] = 'white'; //黑名单,黑名单中的检查  'white'白名单,白名单以外的检查
		$rule_action['actions'] = array();
		return $rule_action;
	}

	public function setup()
	{
		$this->crumb(AWS_APP::lang()->_t('发布'), '/publish/');
	}

	public function index_action()
	{
		if ($_GET['id'])
		{
			if (!$question_info = $this->model('question')->get_question_info_by_id($_GET['id']))
			{
				H::redirect_msg(AWS_APP::lang()->_t('指定问题不存在'));
			}

			if (!$this->user_info['permission']['is_administortar'] AND !$this->user_info['permission']['is_moderator'] AND !$this->user_info['permission']['edit_question'] AND $question_info['published_uid'] != $this->user_id)
			{
				H::redirect_msg(AWS_APP::lang()->_t('你没有权限编辑这个问题'), '/question/' . $question_info['question_id']);
			}
		}
		else if (!$this->user_info['permission']['publish_question'])
		{
			H::redirect_msg(AWS_APP::lang()->_t('你所在用户组没有权限发布问题'));
		}
		else if ($this->is_post() AND $_POST['question_detail'])
		{
			$question_info = array(
				'question_content' => htmlspecialchars($_POST['question_content']),
				'question_detail' => htmlspecialchars($_POST['question_detail']),
				'category_id' => intval($_POST['category_id'])
			);
		}
		else
		{
			// $draft_content = $this->model('draft')->get_data(1, 'question', $this->user_id);
            if ($this->user_id && !$question_info['question_content'])
	        {   
	            $add_draft_type = 'question';

	            if (HTTP::get_cookie('taobihu_add_draft_'.$add_draft_type.'_'.$this->user_id))
	            {
	                $draft_content = HTTP::get_cookie('taobihu_add_draft_'.$add_draft_type.'_'.$this->user_id);
	            }

	        } 

			$question_info = array(
				'question_content' => htmlspecialchars($_POST['question_content']),
				// 'question_detail' => htmlspecialchars($draft_content['message'])
				'question_detail' => htmlspecialchars($draft_content)

			);
		}

		if ($this->user_info['integral'] < 0 AND get_setting('integral_system_enabled') == 'Y' AND !$_GET['id'])
		{
			H::redirect_msg(AWS_APP::lang()->_t('你的剩余积分已经不足以进行此操作'));
		}

		if (($this->user_info['permission']['is_administortar'] OR $this->user_info['permission']['is_moderator'] OR $question_info['published_uid'] == $this->user_id AND $_GET['id']) OR !$_GET['id'])
		{
			TPL::assign('attach_access_key', md5($this->user_id . time()));
		}

		if (!$question_info['category_id'])
		{
			$question_info['category_id'] = ($_GET['category_id']) ? intval($_GET['category_id']) : 0;
		}

		if (get_setting('category_enable') == 'Y')
		{
			TPL::assign('question_category_list', $this->model('system')->build_category_html('question', 0, $question_info['category_id']));
		}

		if ($modify_reason = $this->model('question')->get_modify_reason())
		{
			TPL::assign('modify_reason', $modify_reason);
		}

		TPL::assign('human_valid', human_valid('question_valid_hour'));

		TPL::import_js('js/app/publish.js');

		if (get_setting('advanced_editor_enable') == 'Y')
		{
			import_editor_static_files();
		}

		if (get_setting('upload_enable') == 'Y')
		{
			// fileupload
			TPL::import_js('js/fileupload.js');
		}

		TPL::assign('category_reward', $_GET['category']);
		TPL::assign('question_info', $question_info);

		TPL::assign('recent_topics', @unserialize($this->user_info['recent_topics']));

		TPL::output('publish/index');
	}

	public function article_action()
	{
		if ($_GET['id'])
		{
			if (!$article_info = $this->model('article')->get_article_info_by_id($_GET['id']))
			{
				H::redirect_msg(AWS_APP::lang()->_t('指定文章不存在'));
			}

			if (!$this->user_info['permission']['is_administortar'] AND !$this->user_info['permission']['is_moderator'] AND !$this->user_info['permission']['edit_article'] AND $article_info['uid'] != $this->user_id)
			{
				H::redirect_msg(AWS_APP::lang()->_t('你没有权限编辑这个文章'), '/article/' . $article_info['id']);
			}

			//判断是否为建议 如果是则跳转
			if($this->model('category')->check_is_suggest($article_info['category_id']))
			{   
				 H::redirect_msg(AWS_APP::lang()->_t('跳转至建议编辑'), '/publish/suggest/' . $article_info['id']);
			}
            $draft_content = $this->model('draft')->get_data($article_info['id'], 'article', $this->user_id);
			if($draft_content){
                $article_info['message'] = htmlspecialchars($draft_content['message']);
            }
			TPL::assign('article_topics', $this->model('topic')->get_topics_by_item_id($article_info['id'], 'article'));
		}
		else if (!$this->user_info['permission']['publish_article'])
		{
			H::redirect_msg(AWS_APP::lang()->_t('你所在用户组没有权限发布文章'));
		}
		else if ($this->is_post() AND $_POST['message'])
		{
			$article_info = array(
				'title' => htmlspecialchars($_POST['title']),
				'message' => htmlspecialchars($_POST['message']),
				'category_id' => intval($_POST['category_id'])
			);
		}
		else
		{
			$draft_content = $this->model('draft')->get_data(0, 'article', $this->user_id);

            if ($this->user_id && !$question_info['question_content'])
	        {   
	            $add_draft_type = 'article';

	            if (HTTP::get_cookie('taobihu_add_draft_'.$add_draft_type.'_'.$this->user_id))
	            {
	                $draft_content = HTTP::get_cookie('taobihu_add_draft_'.$add_draft_type.'_'.$this->user_id);
	            }    
			}

			$article_info =  array(
				'title' => htmlspecialchars($_POST['title']),
				// 'message' => htmlspecialchars($draft_content['message'])
				'message' => htmlspecialchars($draft_content)
			);
		}

		if (($this->user_info['permission']['is_administortar'] OR $this->user_info['permission']['is_moderator'] OR $article_info['uid'] == $this->user_id AND $_GET['id']) OR !$_GET['id'])
		{
			TPL::assign('attach_access_key', md5($this->user_id . time()));
		}

		if (!$article_info['category_id'])
		{
			$article_info['category_id'] = ($_GET['category_id']) ? intval($_GET['category_id']) : 0;
		}
        if (!$article_info['column_id'])
        {
            $article_info['column_id'] = ($_GET['column_id']) ? intval($_GET['column_id']) : 0;
        }

		if (get_setting('category_enable') == 'Y')
		{
			TPL::assign('article_category_list', $this->model('system')->build_category_html('article', 0, $article_info['category_id']));
		}
        TPL::assign('column_list', $this->model('column')->get_column_by_uid($this->user_id));
		TPL::assign('human_valid', human_valid('question_valid_hour'));

		TPL::import_js('js/app/publish.js');

		if (get_setting('advanced_editor_enable') == 'Y')
		{
			import_editor_static_files();
		}
// 
		// if (get_setting('upload_enable') == 'Y')
		// {
			// fileupload 因为文章必须上传封面图片原因，默认开启
			TPL::import_js('js/fileupload.js');
		// }

		TPL::assign('recent_topics', @unserialize($this->user_info['recent_topics']));

		TPL::assign('article_info', $article_info);

		TPL::output('publish/article');
	}


	public function suggest_action()
	{
		if ($_GET['id'])
		{
			if (!$suggest_info = $this->model('article')->get_article_info_by_id($_GET['id']))
			{
				H::redirect_msg(AWS_APP::lang()->_t('指定建议不存在'));
			}

			if (!$this->user_info['permission']['is_administortar'] AND !$this->user_info['permission']['is_moderator'] AND !$this->user_info['permission']['edit_article'] AND $suggest_info['uid'] != $this->user_id)
			{
				H::redirect_msg(AWS_APP::lang()->_t('你没有权限编辑这条建议'), '/article/' . $suggest_info['id']);
			}

			TPL::assign('article_topics', $this->model('topic')->get_topics_by_item_id($suggest_info['id'], 'article'));
		}
		else if (!$this->user_info['permission']['publish_article'])
		{
			H::redirect_msg(AWS_APP::lang()->_t('你所在用户组没有权限发布建议'));
		}
		else if ($this->is_post() AND $_POST['message'])
		{
			$suggest_info = array(
				'title' => htmlspecialchars($_POST['title']),
				'message' => htmlspecialchars($_POST['message']),
				'category_id' => intval($_POST['category_id'])
			);
		}
		else
		{
			$draft_content = $this->model('draft')->get_data(1, 'article', $this->user_id);

			$suggest_info =  array(
				'title' => htmlspecialchars($_POST['title']),
				'message' => htmlspecialchars($draft_content['message'])
			);
		}

		if (($this->user_info['permission']['is_administortar'] OR $this->user_info['permission']['is_moderator'] OR $suggest_info['uid'] == $this->user_id AND $_GET['id']) OR !$_GET['id'])
		{
			TPL::assign('attach_access_key', md5($this->user_id . time()));
		}

		if (!$suggest_info['category_id'])
		{
			$suggest_info['category_id'] = ($_GET['category_id']) ? intval($_GET['category_id']) : 0;
		}
		if (get_setting('category_enable') == 'Y')
		{    
			TPL::assign('suggest_category_list', $this->model('system')->build_category_html('question', 0, $suggest_info['category_id'],'', true , 1 ));
		}
        TPL::assign('column_list', $this->model('column')->get_column_by_uid($this->user_id));
		TPL::assign('human_valid', human_valid('question_valid_hour'));

		TPL::import_js('js/app/publish.js');

		if (get_setting('advanced_editor_enable') == 'Y')
		{
			import_editor_static_files();
		}

		if (get_setting('upload_enable') == 'Y')
		{
			// fileupload
			TPL::import_js('js/fileupload.js');
		}

		TPL::assign('recent_topics', @unserialize($this->user_info['recent_topics']));

		TPL::assign('suggest_info', $suggest_info);

		TPL::output('publish/suggest');
	}

	public function wait_approval_action()
	{
		if ($_GET['question_id'])
		{
			if ($_GET['_is_mobile'])
			{
				$url = '/m/question/' . $_GET['question_id'];
			}
			else
			{
				$url = '/question/' . $_GET['question_id'];
			}
		}
		else
		{
			$url = '/';
		}

		H::redirect_msg(AWS_APP::lang()->_t('发布成功, 请等待管理员审核...'), $url);
	}
		//新增项 友情链接
	public function link_wait_approval_action()
	{
		$url = '/';
		H::redirect_msg(AWS_APP::lang()->_t('友链申请成功, 请等待管理员审核...'), $url);
	}
	//新增项 友情链接


}
