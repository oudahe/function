<?php
header("content-type:text/html;charset=utf-8");
//封装一个类
/*
掌握满足单例模式的必要条件
(1)私有的构造方法-为了防止在类外使用new关键字实例化对象
(2)私有的成员属性-为了防止在类外引入这个存放对象的属性
(3)私有的克隆方法-为了防止在类外通过clone成生另一个对象
(4)公有的静态方法-为了让用户进行实例化对象的操作

调用如下
mysql测试
$db=db::getIntance();
var_dump($db);
$sql="select * from acticle";
$list=$db->getAll($sql);
$db->p($list);
$sql="select * from acticle where acticle_id=95";
$list=$db->getRow($sql);
$db->p($list);

$sql="select title from acticle";
$list=$db->getOne($sql);
$db->p($list);
//$list=$db->insert("users",$_POST);
//$del=$db->deleteOne("users","id=26");
//$del=$db->deleteAll("users","id in(23,24)");
//$up=$db->update("users",$_POST,"id=27");
//$id=$db->getInsertid();
//print_R($id);
*/
class db{
  //三私一共
  //私有的静态属性
  private static $dbcon=false;
  //私有的构造方法
  private function __construct(){
  $dbcon=@mysql_connect("localhost","root","root");
   mysql_select_db("small2",$dbcon) or die("mysql_connect error");
   mysql_query("set names utf8");
  }
  //私有的克隆方法
  private function __clone(){}
  //公用的静态方法
  public static function getIntance(){
   if(self::$dbcon==false){
    self::$dbcon=new self;
   }
   return self::$dbcon;
  }
  //打印数据
  public function p($arr){
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
  }
  public function v($arr){
  echo "<pre>";
    var_dump($arr);
    echo "</pre>";
  }
  //执行语句
  public function query($sql){
  $query=mysql_query($sql);
   return $query;
  }
  /**
  * 查询某个字段
  * @param
  * @return string or int
  */
  public function getOne($sql){
   $query=$this->query($sql);
    return mysql_result($query,0);
  }
  //获取一行记录,return array 一维数组
  public function getRow($sql,$type="assoc"){
   $query=$this->query($sql);
   if(!in_array($type,array("assoc",'array',"row"))){
     die("mysql_query error");
   }
   $funcname="mysql_fetch_".$type;
   return $funcname($query);
  }
  //获取一条记录,前置条件通过资源获取一条记录
  public function getFormSource($query,$type="assoc"){
  if(!in_array($type,array("assoc","array","row")))
  {
    die("mysql_query error");
  }
  $funcname="mysql_fetch_".$type;
  return $funcname($query);
  }
  //获取多条数据，二维数组
  public function getAll($sql){
   $query=$this->query($sql);
   $list=array();
   while ($r=$this->getFormSource($query)) {
    $list[]=$r;
   }
   return $list;
  }
  //获得最后一条记录id
  public function getInsertid(){
   return mysql_insert_id();
  }
   /**
   * 定义添加数据的方法
   * @param string $table 表名
   * @param string orarray $data [数据]
   * @return int 最新添加的id
   */
   public function insert($table,$data){
   //遍历数组，得到每一个字段和字段的值
   $key_str='';
   $v_str='';
   foreach($data as $key=>$v){
    if(empty($v)){
     die("error");
   }
      //$key的值是每一个字段s一个字段所对应的值
      $key_str.=$key.',';
      $v_str.="'$v',";
   }
   $key_str=trim($key_str,',');
   $v_str=trim($v_str,',');
   //判断数据是否为空
   $sql="insert into $table ($key_str) values ($v_str)";
   $this->query($sql);
 //返回上一次增加操做产生ID值
   return mysql_insert_id();
 }
 /*
  * 删除一条数据方法
  * @param1 $table, $where=array('id'=>'1') 表名 条件
  * @return 受影响的行数
  */
  public function deleteOne($table, $where){
    if(is_array($where)){
      foreach ($where as $key => $val) {
        $condition = $key.'='.$val;
      }
    } else {
      $condition = $where;
    }
    $sql = "delete from $table where $condition";
    $this->query($sql);
    //返回受影响的行数
    return mysql_affected_rows();
  }
  /*
  * 删除多条数据方法
  * @param1 $table, $where 表名 条件
  * @return 受影响的行数
  */
  public function deleteAll($table, $where){
    if(is_array($where)){
      foreach ($where as $key => $val) {
        if(is_array($val)){
          $condition = $key.' in ('.implode(',', $val) .')';
        } else {
          $condition = $key. '=' .$val;
        }
      }
    } else {
      $condition = $where;
    }
    $sql = "delete from $table where $condition";
    $this->query($sql);
    //返回受影响的行数
    return mysql_affected_rows();
  }
 /**
  * [修改操作description]
  * @param [type] $table [表名]
  * @param [type] $data [数据]
  * @param [type] $where [条件]
  * @return [type]
  */
 public function update($table,$data,$where){
   //遍历数组，得到每一个字段和字段的值
   $str='';
  foreach($data as $key=>$v){
   $str.="$key='$v',";
  }
  $str=rtrim($str,',');
  //修改SQL语句
  $sql="update $table set $str where $where";
  $this->query($sql);
  //返回受影响的行数
  return mysql_affected_rows();
 }
}
?>
