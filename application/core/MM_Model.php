<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class MY_Model extends CI_Model {

	protected $table_name;

	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	/** 新增数据
	 * @param array $data
	 * @return int = insert_id
	 */
	function insert($data)
	{
		$data['cdate'] = date('Y-m-d H:i:s');
		$this->db->insert($this->table_name, $data);
		$id = $this->db->insert_id();
		$affect = $this->db->affected_rows();
		if(empty($id)){
			if($affect==1){
				return 1;
			}
		}
		return $id;
	}

	/** 批量插入数据 (一般用于数据导入)
	 * @param array $data
	 * @return int = insert_id
	 */
	public function insert_batch($data) {
		return $this->db->insert_batch($this->table_name,$data);
	}

	/**
	 * 更新数据
	 * @param array $data, int $id
	 * @return bool
	 */
	function update($data,$id)
	{
		$data['edate'] = date('Y-m-d H:i:s');
		$this->db->where('id', $id);
		return $this->db->update($this->table_name, $data);
		//return $this->db->affected_rows();
	}


	/**
	 * 根据指定条件更新数据
	 * @param array $data, array $where
	 * @return bool
	 */
	function updateWhere($data,$where)
	{
		return $this->db->update($this->table_name, $data, $where);
		//return $this->db->affected_rows();
	}

	/**
	 * 删除数据 (标记删除)
	 * @param int $id
	 * @return bool
	 */
	public function del($id)
	{
		$data = array();
		$data['status'] = '-1';
		$data['edate'] = date('Y-m-d H:i:s');
		$this->db->where('id', $id);
		return $this->db->update($this->table_name, $data);
		//return $this->db->affected_rows();
	}

	/**
	 * 恢复删除 (标记恢复)
	 * @param int $id
	 * @return bool
	 */
	public function res($id)
	{
		$data = array();
		$data['status'] = '0';
		$data['edate'] = date('Y-m-d H:i:s');
		$this->db->where('id', $id);
		return $this->db->update($this->table_name, $data);
		//return $this->db->affected_rows();
	}

	/**
	 * 删除数据 (物理删除)
	 * @param int $id
	 * @return int
	 */
	public function realDel($id)
	{
		$this->db->where('id', $id);
		return $this->db->delete($this->table_name);
	}

	/**
	 * 根据指定条件删除数据 (物理删除)
	 * @param int $id
	 * @return int
	 */
	public function delWhere($where)
	{
		return $this->db->delete($this->table_name, $where);
	}

	/**
	 * 查询单个数据
	 * @param array $params
	 * @return array row
	 */
	public function getOne($params = array(), $data = '*')
	{
		$q = $this->db->select($data,false)->where($params)->get($this->table_name);
		return $q->row_array();
	}

	/**
	 * 查询记录条数
	 * Enter description here ...
	 * @param array $params
	 */

	public function getCount($params = array(), $like = array()) {
		if(!isset($where['status'])) {
			if (isset($where['status_in'])) {
				unset($where['status_in']);
			} else {
				$where['status !='] = '-1';
			}
		}
		return $this->db->where($params)->from($this->table_name)->count_all_results();
	}

	/**
	 * 查询列表
	 * @param array $params
	 * @param int $start
	 * @param int $perpage
	 */

	public function getList($where = array(), $data = '*', $start = '0', $perpage='0', $order = 'cdate', $sort = 'desc', $group = '', $key=array()) {
		
		if(!isset($where['status'])) {
			if (isset($where['status_in'])) {
				unset($where['status_in']);
			} else {
				$where['status !='] = '-1';
			}
		}
		if($perpage == '0'){
			$q = $this->db->select($data,false)->where($where)->like($key)->group_by($group)->order_by($order,$sort)->get($this->table_name);
		}else{
			$q = $this->db->select($data,false)->where($where)->like($key)->group_by($group)->order_by($order,$sort)->limit($perpage,$start)->get($this->table_name);
		}

		return $q->result_array();
	}

	/**
	 * 查询列表 (模糊查询)
	 *     注：本方法基本类同 用于迁移架构
	 * @param array $like
	 * @param array $params
	 * @param string $data
	 * @param int $start
	 * @param int $perpage
	 * @param string $order
	 * @param string $sort
	 * @return array array
	 */
	public function getLike($like = array(), $params = array(), $data = '*', $start = '0',$perpage='0',	$order = 'cdate', $sort = 'desc')
	{
		if(!isset($where['status'])) {
			if (isset($where['status_in'])) {
				unset($where['status_in']);
			} else {
				$where['status !='] = '-1';
			}
		}

		if($perpage == '0'){
			$q = $this->db->select($data,false)->where($params)->like($like)->order_by($order,$sort)->get($this->table_name);
		}else{
			$q = $this->db->select($data,false)->where($params)->like($like)->order_by($order,$sort)->limit($perpage,$start)->get($this->table_name);
		}
		return $q->result_array();
	}

	/**
	 * 组定义 SQL 查询
	 * Enter description here ...
	 * @param string $sql
	 * @return array
	 */
	public function query($sql) {
		return $this->db->query($sql)->result_array();
	}

	public function getIn($field='*',$infield,$inArr,$where = array()){
		$q = $this->db->select($field,false)->where($where)->where_in($infield, $inArr, true)->get($this->table_name);
		return $q->result_array();
	}
}
