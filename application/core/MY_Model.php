<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 *******************************************************************************
 * DB 操作原型类
 * Model文件继承此类，即可使用通用DAO方法
 * 继承类请在构造函数最后加上：
 *     $this->table_name = '?';
 *******************************************************************************
 * @version 0.6.2 - 0.2.0
 */
class MY_Model extends CI_Model
{

	protected $table_name;

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	/** 
	 * 新增数据
	 *
	 * @param 	array 	$data
	 * @return 	int
	 */
	public function insert($data)
	{
		if ( ! isset($data['cdate'])) {
			$data['cdate'] = date('Y-m-d H:i:s');
		}
		$this->db->insert($this->table_name, $data);
		$id = $this->db->insert_id();
		if ($id) {
			return $id;
		} else {
			return $this->db->affected_rows();
		}
	}

	/** 
	 * 批量新增数据 (一般用于数据导入)
	 * 传入二维数组
	 *
	 * @param 	array 	$data
	 * @return 	bool
	 */
	public function insert_batch($data)
	{
		return $bool = $this->db->insert_batch($this->table_name, $data);
	}

	/**
	 * 更新数据
	 * 传入单个id, 或者一个数组条件
	 *
	 * @param 	array 	$data
	 * @param 	fixed 	$id
	 * @return 	bool
	 */
	public function update($data, $id)
	{
		if ( ! isset($data['edate'])) {			
			$data['edate'] = date('Y-m-d H:i:s');
		}
		if (is_array($id)) {
			$this->db->where($id, null, false);
		} else {
			$this->db->where('id', $id);
		}
		return $bool = $this->db->update($this->table_name, $data);
	}

	/**
	 * 删除数据, $real true 时 真删除
	 *
	 * @param 	fixed 	$id
	 * @param 	bool 	$real
	 * @return 	bool
	 */
	public function delete($id, $real = false)
	{
		if ($real) {
			return $bool = $this->_realDelete($id);
		} else {
			$data = array(
				'status' => '-1',
				'edate'  =>	date('Y-m-d H:i:s')
			);
			return $bool = $this->update($data, $id);
		}
	}

	/**
	 * 删除数据 (真删除)
	 *     注：此方法变更为私有方法，请调用 del($id, true) 实现同功能
	 *
	 * @param 	fixed 	$id
	 * @return 	int
	 */
	private function _realDelete($id)
	{
		if (is_array($id)) {
			$this->db->where($id, null, false);
		} else {
			$this->db->where('id', $id);
		}
		return $bool = $this->db->delete($this->table_name);
	}

	/**
	 * 查询单个数据
	 *
	 * @param 	fixed 	$params
	 * @param 	string 	$fields
	 * @return 	array
	 */
	public function getOne($params = array(), $fields = '*')
	{		
		// if ( ! isset($params['status'])) {
		// 	$params['status !='] = '-1';
		// }
		$q = $this->db->select($fields, false)->where($params)->get($this->table_name);
		return $row = $q->row_array();
	}

	/**
	 * 查询记录条数
	 *
	 * @param 	fixed 	$params
	 * @param 	array 	$like
	 * @return 	int
	 */
	public function getCount($params = array(), $like = array())
	{
		if ( ! isset($params['status'])) {
			$params['status !='] = '-1';
		}
		return $count = $this->db->where($params)->or_like($like)->from($this->table_name)
								 ->count_all_results();
	}

	/**
	 * 查询列表
	 *
	 * @param 	array 	$params
	 * @param 	string 	$data
	 * @param 	int 	$start
	 * @param 	int 	$perpage
	 * @param 	string 	$order
	 * @param 	string 	$sort
	 * @param 	array 	$like
	 * @return 	array
	 */
	public function getList($params = array(), $fields = '*', $start = 0, $perpage = 0, 
							$order 	= '', $sort = '', $like = array(), $group = array())
	{
		if ( ! isset($params['status'])) {
			$params['status !='] = '-1';
		}
		if ($perpage) {
			$this->db->limit($perpage, $start);
		}
		if ($order && $sort) {
			$this->db->order_by($order, $sort);
		}
		if (!empty($group) && count($group) > 0) {
			$this->db->group_by($group);
		}		
		$q = $this->db->select($fields, false)->where($params)->or_like($like)->get($this->table_name);
		return $list = $q->result_array();
	}

	/**
	 * 模糊查询
	 *     注：此版本变更为通用方法，转调List方法
	 *
	 * @param 	array 	$like
	 * @param 	array 	$params
	 * @param 	string 	$data
	 * @param 	int 	$start
	 * @param 	int 	$perpage
	 * @param 	string 	$order
	 * @param 	string 	$sort
	 * @return 	array 	array
	 */
	public function getLike($like = array(), $params = array(), $fields = '*', 
							$start = 0, $perpage = 0, $order = '', $sort = '')
	{
		return $list = $this->getList($params, $fields, $start, $perpage, $order, $sort, $like);
	}

	/**
	 * In 查询
	 *
	 * @param 	string 	$inField
	 * @param 	array 	$inArray
	 * @param 	array 	$params
	 * @param 	string 	$data
	 * @param 	int 	$start
	 * @param 	int 	$perpage
	 * @param 	string 	$order
	 * @param 	string 	$sort
	 * @return 	array 	array
	 */
	public function getIn($inField, $inArray, $params = array(), $fields = '*', 
						  $start = 0, $perpage = 0, $order = '', $sort = '')
	{
		if ( empty($inArray) ) {
			return array();
		}
		$inString = implode(',', $inArray);
		$params["$inField in ($inString)"] = null;
		return $list = $this->getList($params, $fields, $start, $perpage, $order, $sort);
	}

	/**
	 * 查询In记录条数
	 *
	 * @param 	string 	$inField
	 * @param 	array 	$inArray
	 * @param 	array 	$params
	 * @return 	int
	 */
	public function getInCount($inField, $inArray, $params = array())
	{
		if ( empty($inArray) ) {
			return 0;
		}
		$list = $this->getIn($inField, $inArray, $params, $fields=' count(*) as num ');
		if (isset($list[0]['num'])) {
			return $count = intval($list[0]['num']);
		} else {
			return 0;
		}
	}

	/**
	 * 组定义 SQL 查询
	 *
	 * @param string $sql
	 * @return array
	 */
	public function getQuery($sql)
	{
		return $list = $this->db->query($sql)->result_array();
	}
}

/* End of file: MY_Model.php */