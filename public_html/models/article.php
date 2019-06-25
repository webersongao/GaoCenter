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

class article_class extends AWS_MODEL
{
	public function get_article_info_by_id($article_id)
	{
		$article_id = intval($article_id);
		if (!is_digits($article_id))
		{
			return false;
		}

		static $articles;

		if (!$articles[$article_id])
		{
			$articles[$article_id] = $this->fetch_row('article', 'id = ' . intval($article_id));
		}
		return $articles[$article_id];
	}

	public function get_article_info_by_ids($article_ids)
	{
		if (!is_array($article_ids) OR sizeof($article_ids) == 0)
		{
			return false;
		}

		array_walk_recursive($article_ids, 'intval_string');

        #is_del 逻辑删除
		if ($articles_list = $this->fetch_all('article', 'is_del = 0 and id IN(' . implode(',', $article_ids) . ')'))
		{
			foreach ($articles_list AS $key => $val)
			{
				$result[$val['id']] = $val;
			}
		}

		return $result;
	}

    public function get_last_article_by_uid($uid)
    {
        $article = $this->fetch_row('article','uid = ' . intval($uid), 'add_time DESC');

        return $article;
    }

	public function get_comment_by_id($comment_id)
	{
		if ($comment = $this->fetch_row('article_comments', 'id = ' . intval($comment_id)))
		{
			$comment_user_infos = $this->model('account')->get_user_info_by_uids(array(
				$comment['uid'],
				$comment['at_uid']
			));

			$comment['user_info'] = $comment_user_infos[$comment['uid']];
			$comment['at_user_info'] = $comment_user_infos[$comment['at_uid']];
		}

		return $comment;
	}

	public function get_comments_by_ids($comment_ids)
	{
		if (!is_array($comment_ids) OR !$comment_ids)
		{
			return false;
		}

		array_walk_recursive($comment_ids, 'intval_string');

		if ($comments = $this->fetch_all('article_comments', 'is_del = 0 and id IN (' . implode(',', $comment_ids) . ')'))
		{
			foreach ($comments AS $key => $val)
			{
				$article_comments[$val['id']] = $val;
			}
		}

		return $article_comments;
	}

	public function get_comments($article_id, $page, $per_page)
	{
		if ($comments = $this->fetch_page('article_comments', 'is_del = 0 and article_id = ' . intval($article_id), 'add_time ASC', $page, $per_page))
		{
			foreach ($comments AS $key => $val)
			{
				$comment_uids[$val['uid']] = $val['uid'];

				if ($val['at_uid'])
				{
					$comment_uids[$val['at_uid']] = $val['at_uid'];
				}
			}

			if ($comment_uids)
			{
				$comment_user_infos = $this->model('account')->get_user_info_by_uids($comment_uids);
			}

			foreach ($comments AS $key => $val)
			{
				$comments[$key]['user_info'] = $comment_user_infos[$val['uid']];
				$comments[$key]['at_user_info'] = $comment_user_infos[$val['at_uid']];
			}
		}

		return $comments;
	}

    /**
     * @description [真实删除]
     * @param $article_id
     * @return bool|int
     * @throws Zend_Exception
     * @author Laushow
     * @datatime 2018/7/18 14:38
     */
	public function remove_article_phy($article_id)
	{
		if (!$article_info = $this->get_article_info_by_id($article_id))
		{
			return false;
		}

		$this->delete('article_comments', "article_id = " . intval($article_id)); // 删除关联的回复内容

		$this->delete('topic_relation', "`type` = 'article' AND item_id = " . intval($article_id));		// 删除话题关联

		ACTION_LOG::delete_action_history('associate_type = ' . ACTION_LOG::CATEGORY_QUESTION . ' AND associate_action IN(' . ACTION_LOG::ADD_ARTICLE . ', ' . ACTION_LOG::ADD_AGREE_ARTICLE . ', ' . ACTION_LOG::ADD_COMMENT_ARTICLE . ') AND associate_id = ' . intval($article_id));	// 删除动作

		// 删除附件
		if ($attachs = $this->model('publish')->get_attach('article', $article_id))
		{
			foreach ($attachs as $key => $val)
			{
				$this->model('publish')->remove_attach($val['id'], $val['access_key']);
			}
		}

		$this->model('notify')->delete_notify('model_type = 8 AND source_id = ' . intval($article_id));	// 删除相关的通知

		$this->model('posts')->remove_posts_index($article_id, 'article');

		$this->shutdown_update('users', array(
			'article_count' => $this->count('article', 'uid = ' . intval($uid))
		), 'uid = ' . intval($uid));

		return $this->delete('article', 'id = ' . intval($article_id));
	}

    /**
     * @description [物理删除]
     * @param $comment_id
     * @return bool
     * @throws Zend_Exception
     * @author Laushow
     * @datatime 2018/9/10 18:16
     */
	public function remove_comment_phy($comment_id)
	{
		$comment_info = $this->get_comment_by_id($comment_id);

		if (!$comment_info)
		{
			return false;
		}

		$this->delete('article_comments', 'id = ' . $comment_info['id']);

		$this->update('article', array(
			'comments' => $this->count('article_comments', 'article_id = ' . $comment_info['article_id'])
		), 'id = ' . $comment_info['article_id']);

		return true;
	}

	public function update_article($article_id, $uid, $title,$logo_img, $message, $topics, $category_id,$column_id, $create_topic)
	{
		if (!$article_info = $this->model('article')->get_article_info_by_id($article_id))
		{
			return false;
		}

		$this->delete('topic_relation', 'item_id = ' . intval($article_id) . " AND `type` = 'article'");

		if (is_array($topics))
		{
			foreach ($topics as $key => $topic_title)
			{
				$topic_id = $this->model('topic')->save_topic($topic_title, $uid, $create_topic);

				$this->model('topic')->save_topic_relation($uid, $topic_id, $article_id, 'article');
			}
		}

		$this->model('search_fulltext')->push_index('article', htmlspecialchars($title), $article_info['id']);

		$this->update('article', array(
			'title' => htmlspecialchars($title),
			'article_img'=>$logo_img,
			'message' => htmlspecialchars($message),
			'category_id' => intval($category_id),
			'column_id' => intval($column_id),
		), 'id = ' . intval($article_id));

		$this->model('posts')->set_posts_index($article_id, 'article');

		return true;
	}

	public function get_articles_list($category_id, $page, $per_page, $order_by, $day = null ,$uid = 0 , $filter = '')
	{
		$where = array();

		if ($category_id)
		{
			$where[] = 'category_id = ' . intval($category_id);
		}

		if ($day)
		{
			$where[] = 'add_time > ' . (time() - $day * 24 * 60 * 60);
		}

		if ($uid)
		{
			$where[] = 'uid = ' . $uid;
		}
             
		$where[] = 'is_del = 0';

        $category_ids = $this->model('column')->check_suggest();
              
        if(!empty($category_ids)){

        	  $where[] = 'category_id not in ('.implode(',',$category_ids) .')';
        	  
        }

		return $this->fetch_page('article', implode(' AND ', $where), $order_by, $page, $per_page);
	}

	public function get_articles_list_by_topic_ids($page, $per_page, $order_by, $topic_ids)
	{
		if (!$topic_ids)
		{
			return false;
		}

		if (!is_array($topic_ids))
		{
			$topic_ids = array(
				$topic_ids
			);
		}

		array_walk_recursive($topic_ids, 'intval_string');

		$result_cache_key = 'article_list_by_topic_ids_' . md5(implode('_', $topic_ids) . $order_by . $page . $per_page);

		$found_rows_cache_key = 'article_list_by_topic_ids_found_rows_' . md5(implode('_', $topic_ids) . $order_by . $page . $per_page);

		if (!$result = AWS_APP::cache()->get($result_cache_key) OR $found_rows = AWS_APP::cache()->get($found_rows_cache_key))
		{
			$topic_relation_where[] = '`topic_id` IN(' . implode(',', $topic_ids) . ')';
			$topic_relation_where[] = "`type` = 'article'";

			if ($topic_relation_query = $this->query_all("SELECT item_id FROM " . get_table('topic_relation') . " WHERE " . implode(' AND ', $topic_relation_where)))
			{
				foreach ($topic_relation_query AS $key => $val)
				{
					$article_ids[$val['item_id']] = $val['item_id'];
				}
			}

			if (!$article_ids)
			{
				return false;
			}

			$where[] = "id IN (" . implode(',', $article_ids) . ")";
		}


		if (!$result)
		{
			$result = $this->fetch_page('article', implode(' AND ', $where), $order_by, $page, $per_page);

			AWS_APP::cache()->set($result_cache_key, $result, get_setting('cache_level_high'));
		}


		if (!$found_rows)
		{
			$found_rows = $this->found_rows();

			AWS_APP::cache()->set($found_rows_cache_key, $found_rows, get_setting('cache_level_high'));
		}

		$this->article_list_total = $found_rows;

		return $result;
	}

	public function lock_article($article_id, $lock_status = true)
	{
		return $this->update('article', array(
			'lock' => intval($lock_status)
		), 'id = ' . intval($article_id));
	}

	public function article_vote($type, $item_id, $rating, $uid, $reputation_factor, $item_uid)
	{
		$this->delete('article_vote', "`type` = '" . $this->quote($type) . "' AND item_id = " . intval($item_id) . ' AND uid = ' . intval($uid));

		if ($rating)
		{
			if ($article_vote = $this->fetch_row('article_vote', "`type` = '" . $this->quote($type) . "' AND item_id = " . intval($item_id) . " AND rating = " . intval($rating) . ' AND uid = ' . intval($uid)))
			{
				$this->update('article_vote', array(
					'rating' => intval($rating),
					'time' => time(),
					'reputation_factor' => $reputation_factor
				), 'id = ' . intval($article_vote['id']));
			}
			else
			{
				$this->insert('article_vote', array(
					'type' => $type,
					'item_id' => intval($item_id),
					'rating' => intval($rating),
					'time' => time(),
					'uid' => intval($uid),
					'item_uid' => intval($item_uid),
					'reputation_factor' => $reputation_factor
				));
			}
		}

		switch ($type)
		{
			case 'article':
				$this->update('article', array(
					'votes' => $this->count('article_vote', "`type` = '" . $this->quote($type) . "' AND item_id = " . intval($item_id) . " AND rating = 1")
				), 'id = ' . intval($item_id));
			if($rating == 1 AND $article_vote['rating'] != 1 AND $article_vote['uid'] != $uid)
			$this->model('notify')->send($uid, $item_uid, notify_class::TYPE_ARTICLE_VOTES, notify_class::CATEGORY_ARTICLE, $item_id, array(
				'from_uid' => $uid,
				'article_id' => $item_id,
			));
				switch ($rating)
				{
					case 1:
						ACTION_LOG::save_action($uid, $item_id, ACTION_LOG::CATEGORY_QUESTION, ACTION_LOG::ADD_AGREE_ARTICLE);
					break;

					case -1:
						ACTION_LOG::delete_action_history('associate_type = ' . ACTION_LOG::CATEGORY_QUESTION . ' AND associate_action = ' . ACTION_LOG::ADD_AGREE_ARTICLE . ' AND uid = ' . intval($uid) . ' AND associate_id = ' . intval($item_id));
					break;
				}
			break;

			case 'comment':
				$this->update('article_comments', array(
					'votes' => $this->count('article_vote', "`type` = '" . $this->quote($type) . "' AND item_id = " . intval($item_id) . " AND rating = 1")
				), 'id = ' . intval($item_id));
				$comms=$this->get_comment_by_id($item_id);
				// $arts=$this->get_article_info_by_id($comms['article_id']);
			$this->model('notify')->send($uid, $item_uid, notify_class::TYPE_ARTICLE_COMMENT_VOTES, notify_class::CATEGORY_ARTICLE, $item_id, array(
				'from_uid' => $uid,
				'article_id' => $comms['article_id'],
			));
			break;
		}

		$this->model('account')->sum_user_agree_count($item_uid);

		return true;
	}

	public function get_article_vote_by_id($type, $item_id, $rating = null, $uid = null)
	{
		if ($article_vote = $this->get_article_vote_by_ids($type, array(
			$item_id
		), $rating, $uid))
		{
			return end($article_vote[$item_id]);
		}
	}

	public function get_article_vote_by_ids($type, $item_ids, $rating = null, $uid = null)
	{
		if (!is_array($item_ids))
		{
			return false;
		}

		if (sizeof($item_ids) == 0)
		{
			return false;
		}

		array_walk_recursive($item_ids, 'intval_string');

		$where[] = "`type` = '" . $this->quote($type) . "'";
		$where[] = 'item_id IN(' . implode(',', $item_ids) . ')';

		if ($rating)
		{
			$where[] = 'rating = ' . intval($rating);
		}

		if ($uid)
		{
			$where[] = 'uid = ' . intval($uid);
		}

		if ($article_votes = $this->fetch_all('article_vote', implode(' AND ', $where)))
		{
			foreach ($article_votes AS $key => $val)
			{
				$result[$val['item_id']][] = $val;
			}
		}

		return $result;
	}

	public function get_article_vote_users_by_id($type, $item_id, $rating = null, $limit = null)
	{
		$where[] = "`type` = '" . $this->quote($type) . "'";
		$where[] = 'item_id = ' . intval($item_id);

		if ($rating)
		{
			$where[] = 'rating = ' . intval($rating);
		}

		if ($article_votes = $this->fetch_all('article_vote', implode(' AND ', $where)))
		{
			foreach ($article_votes AS $key => $val)
			{
				$uids[$val['uid']] = $val['uid'];
			}

			return $this->model('account')->get_user_info_by_uids($uids);
		}
	}

	public function get_article_vote_users_by_ids($type, $item_ids, $rating = null, $limit = null)
	{
		if (! is_array($item_ids))
		{
			return false;
		}

		if (sizeof($item_ids) == 0)
		{
			return false;
		}

		array_walk_recursive($item_ids, 'intval_string');

		$where[] = "`type` = '" . $this->quote($type) . "'";
		$where[] = 'item_id IN(' . implode(',', $item_ids) . ')';

		if ($rating)
		{
			$where[] = 'rating = ' . intval($rating);
		}

		if ($article_votes = $this->fetch_all('article_vote', implode(' AND ', $where)))
		{
			foreach ($article_votes AS $key => $val)
			{
				$uids[$val['uid']] = $val['uid'];
			}

			$users_info = $this->model('account')->get_user_info_by_uids($uids);

			foreach ($article_votes AS $key => $val)
			{
				$vote_users[$val['item_id']][$val['uid']] = $users_info[$val['uid']];
			}

			return $vote_users;
		}
	}

	public function update_views($article_id)
	{
		if (AWS_APP::cache()->get('update_views_article_' . md5(session_id()) . '_' . intval($article_id)))
		{
			return false;
		}

		AWS_APP::cache()->set('update_views_article_' . md5(session_id()) . '_' . intval($article_id), time(), 60);

		$this->shutdown_query("UPDATE " . $this->get_table('article') . " SET views = views + 1 WHERE id = " . intval($article_id));

		return true;
	}

	public function set_recommend($article_id)
	{
		$this->update('article', array(
			'is_recommend' => 1
		), 'id = ' . intval($article_id));

		$this->model('posts')->set_posts_index($article_id, 'article');
	}

	public function unset_recommend($article_id)
	{
		$this->update('article', array(
			'is_recommend' => 0
		), 'id = ' . intval($article_id));

		$this->model('posts')->set_posts_index($article_id, 'article');
	}


	public function get_user_article_views($uid){
        return $this->sum('article','views','uid = '.$uid);
	}

	public function get_by_like($q, $page = 1, $limit = 20, $is_recommend = false)
    {
        $offset = ($page-1) * $limit;
		// 数据库表名 写死
        $sql = "select * from " . $this->get_table('article') . " where title regexp'".$this->quote(implode('|', $q))."' or message regexp'".$this->quote(implode('|', $q))."' order by add_time desc limit ".calc_page_limit($page,$limit);
        $ret = $this->query_all($sql);
        
        return $ret;
    }


	/**
     * 更新文章表字段
     *
     * @param array
     * @param uid
     * @return int
     */
    public function update_article_fields($update_data, $id)
    {
        return $this->update('article', $update_data, 'id = ' . intval($id));
    }

    /**
     * 获取封面图文件路径
     *
     * 举个例子：$id=12345，那么封面图路径很可能(根据您部署的上传文件夹而定)会被存储为/uploads/article/cover/000/01/23/45_article_cover_min.jpg
     *
     * @param  int
     * @param  string
     * @param  int
     * @return string
     */
    public function get_cover($id, $size = 'min', $return_type = 0)
    {
        $size = in_array($size, array(
            'max',
            'mid',
            'min',
            '50',
            '150',
            'big',
            'middle',
            'small'
        )) ? $size : 'real';

        $id = abs(intval($id));
        $id = sprintf('%\'09d', $id);
        $dir1 = substr($id, 0, 3);
        $dir2 = substr($id, 3, 2);
        $dir3 = substr($id, 5, 2);

        if ($return_type == 1)
        {
            return $dir1 . '/' . $dir2 . '/' . $dir3 . '/';
        }

        if ($return_type == 2)
        {
            return substr($id, -2) . '_article_cover_' . $size . '.jpg';
        }

        return $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($id, -2) . '_article_cover_' . $size . '.jpg';
    }


	/**
     * 获取相关文章列表
     */
    public function get_related_article_list($article_id, $article_title, $limit = 10)
	{
		$cache_key = 'article_related_list_' . md5($article_title) . '_' . $limit;

		if ($article_related_list = AWS_APP::cache()->get($cache_key))
		{
			return $article_related_list;
		}

		if ($article_keywords = $this->model('system')->analysis_keyword($article_title))
		{
			if (sizeof($article_keywords) <= 1)
			{
				return false;
			}

			if ($article_list = $this->query_all($this->model('search_fulltext')->bulid_query('article', 'title', $article_keywords), 2000))
			{
				$article_list = aasort($article_list, 'score', 'DESC');
				$article_list = aasort($article_list, 'comments', 'DESC');

				$article_list = array_slice($article_list, 0, ($limit + 1));

				foreach ($article_list as $key => $val)
				{
					if ($val['id'] == $article_id)
					{
						unset($article_list[$key]);
					}
					else
					{
						if (! isset($article_related[$val['id']]))
						{
							$article_related[$val['id']] = $val['title'];

							$article_info[$val['id']] = $val;
						}
					}
				}
			}
		}

		if ($article_related)
		{
			foreach ($article_related as $key => $title)
			{
				$article_related_list[] = array(
					'article_id' => $key,
					'article_title' => $title,
				);
			}
		}

		if (sizeof($article_related) > $limit)
		{
			array_pop($article_related);
		}

		AWS_APP::cache()->set($cache_key, $article_related_list, get_setting('cache_level_low'));

		return $article_related_list;
	}

    /**
     * @description [逻辑删除文章]
     * @param $article_id
     * @return bool|int
     * @throws Zend_Exception
     * @author Laushow
     * @datatime 2018/7/18 14:39
     */
    public function remove_article($article_id,$whereDel = false)
    {
        if (!$article_info = $this->get_article_info_by_id($article_id))
        {
            return false;
        }
        $delWhere = null;
        $isdel = 1;
        if($whereDel !== false){
            $delWhere = ' and is_del != 1';
            $isdel = $whereDel;
        }
        $this->update('topic_relation', ['is_del'=>$isdel],"`type` = 'article' AND item_id = " . intval($article_id).$delWhere);// 删除话题关联
        $this->update('posts_index', ['is_del'=>$isdel],'post_type="article" and post_id = ' . intval($article_id).$delWhere);
        $this->shutdown_update('users', array(
            'article_count' => $this->count('article', 'is_del=0 and uid = ' . intval($article_info['uid']))
        ), 'uid = ' . intval($article_info['uid']));
        $this->model('topic')->update_discuss_count(3);
        AWS_APP::cache()->clean();
        return $this->update('article', ['is_del'=>$isdel],'id = ' . intval($article_id).$delWhere);
    }

    /**
     * @description [恢复]
     * @author Laushow
     * @datatime 2018/7/18 15:02
     */
    public function recover_question($article_id,$whereDel = false){
        if (!$article_info = $this->get_article_info_by_id($article_id))
        {
            return false;
        }
        $delWhere = null;
        if($whereDel !== false){
            $delWhere = ' and is_del = '.$whereDel;
        }
        $this->update('topic_relation', ['is_del'=>0],"`type` = 'article' AND item_id = " . intval($article_id).$delWhere);// 删除话题关联
        $this->update('posts_index', ['is_del'=>0],'post_type="article" and post_id = ' . intval($article_id).$delWhere);
        $this->shutdown_update('users', array(
            'article_count' => $this->count('article', 'is_del=0 and uid = ' . intval($article_info['uid']))
        ), 'uid = ' . intval($article_info['uid']));
        $this->model('topic')->update_discuss_count(3);
        AWS_APP::cache()->clean();
        return $this->update('article', ['is_del'=>0],'id = ' . intval($article_id).$delWhere);
    }

    /**
     * @description [删除文章评论]
     * @param $comment_id
     * @return bool
     * @throws Zend_Exception
     * @author Laushow
     * @datatime 2018/7/19 10:40
     */
    public function remove_comment($comment_id,$whereDel = false)
    {
        $comment_info = $this->get_comment_by_id($comment_id);
        if (!$comment_info) {
            return false;
        }
        $delWhere = null;
        $isdel = 1;
        if($whereDel !== false){
            $delWhere = ' and is_del != 1';
            $isdel = $whereDel;
        }
        $this->update('article_comments', ['is_del' => $isdel],'id = ' . $comment_info['id'].$delWhere);
        $this->update('article', array(
            'comments' => $this->count('article_comments', 'is_del = 0 and article_id = ' . $comment_info['article_id'])
        ), 'id = ' . $comment_info['article_id']);
        return true;
    }

    /**
     * @description [删除文章评论恢复]
     * @param $comment_id
     * @return bool
     * @throws Zend_Exception
     * @author Laushow
     * @datatime 2018/7/19 10:40
     */
    public function recover_comment($comment_id,$whereDel = false)
    {
        $comment_info = $this->get_comment_by_id($comment_id);
        if (!$comment_info) {
            return false;
        }
        $delWhere = null;
        if($whereDel !== false){
            $delWhere = ' and is_del = '.$whereDel;
        }
        $this->update('article_comments', ['is_del' => 0],'id = ' . $comment_info['id'].$delWhere);
        $this->update('article', array(
            'comments' => $this->count('article_comments', 'is_del = 0 and article_id = ' . $comment_info['article_id'])
        ), 'id = ' . $comment_info['article_id']);
        return true;
    }

}