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

class draft_class extends AWS_MODEL
{
	public function save_draft($item_id, $type, $uid, $data)
	{
		if (!$data)
		{
			return false;
		}

		if ($draft = $this->get_draft($item_id, $type, $uid))
		{
			$this->update('draft', array(
				'data' => serialize($data)
			), 'id = ' . intval($draft['id']));

			return $draft['id'];
		}
		else
		{
			$draft_id = $this->insert('draft', array(
				'uid' => intval($uid),
				'item_id' => intval($item_id),
				'type' => $type,
				'data' => serialize($data),
				'time' => time()
			));

			$this->shutdown_update('users', array(
				'draft_count' => $this->get_draft_count('answer', $uid)
			), 'uid = ' . intval($uid));

			return $draft_id;
		}
	}

	public function get_draft($item_id, $type, $uid)
	{
		$draft = $this->fetch_row('draft', "item_id = " . intval($item_id) . " AND uid = " . intval($uid) . " AND `type` = '" . $this->quote($type) . "'");

		if ($draft['data'])
		{
			$draft['data'] = unserialize($draft['data']);
		}

		return $draft;
	}

	public function get_draft_count($type, $uid)
	{
		return $this->count('draft', "uid = " . intval($uid) . " AND `type` = '" . $this->quote($type) . "'");
	}

	public function delete_draft($item_id, $type, $uid)
	{
		$this->delete('draft', "item_id = " . intval($item_id) . " AND uid = " . intval($uid) . " AND `type` = '" . $this->quote($type) . "'");

		$this->shutdown_update('users', array(
			'draft_count' => $this->get_draft_count('answer', $uid)
		), 'uid = ' . intval($uid));

		return true;
	}
	
	public function clean_draft($uid)
	{
		$this->delete('draft', "uid = " . intval($uid));

		$this->shutdown_update('users', array(
			'draft_count' => 0
		), 'uid = ' . intval($uid));

		return true;
	}

	public function get_data($item_id, $type, $uid)
	{
		$draft = $this->get_draft($item_id, $type, $uid);

		return $draft['data'];
	}

	public function get_all($type, $uid, $page = null)
	{
		if ($draft = $this->fetch_all('draft', "uid = " . intval($uid) . " AND `type` = '" . $this->quote($type) . "'", 'time DESC', $page))
		{
			foreach ($draft AS $key => $val)
			{
				if ($val['data'])
				{
					$draft[$key]['data'] = unserialize($val['data']);
				}
			}
		}

		return $draft;
	}
	    /**
     * @description [获取列表]
     * @author Laushow
     * @datatime 2018/10/10 15:17
     */
	public function get_darft_list($uid,$page = null){
        $sql = '(select d.*,q.question_content as title from '.$this->get_table('draft').' as d left join '.$this->get_table('question').' as q on q.question_id = d.item_id where d.`type` = "question" and d.uid = ' . intval($uid).')';
        $sql .= ' union all (select d.*,a.title from '.$this->get_table('draft').' as d left join '.$this->get_table('article').' as a on a.id = d.item_id where d.`type` = "article" and d.uid = ' . intval($uid).')';
        $sql .= ' union all (select d.*,q.question_content as title from '.$this->get_table('draft').' as d left join '.$this->get_table('question').' as q on q.question_id = d.item_id where d.`type` = "answer" and d.uid = ' . intval($uid).')';
        $sql .= ' limit '.$page;
        return $this->query_all($sql);
    }
}