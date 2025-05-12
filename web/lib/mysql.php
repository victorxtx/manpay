<?php
if (!defined('VALID')){
?>
<!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="refresh" content="15;URL=<?php echo "https://www.mps.gov.cn/";?>" />
		<title>傻逼来了?</title>
		<link rel="stylesheet" type="text/css" href="style/remind.css" />
	</head>
	<body>
		<div class="notice">
			<div style="margin:20px auto;text-align:center;">
				<h1 style="font-size:40px">滚，你妈生你的时候逼眼儿被我的鸡巴堵死了，你是从屁眼儿拉出来的！</h1>
				<h1 style="font-size:40px">拉出来的时候全身都是屎！味道现在还没洗干净！</h1>
			</div>
		</div>
	</body>
</html>
<?php
exit();
}

//1.连接数据库
function connect($host = MYSQL_HOST, $user = MYSQL_USER, $pass = MYSQL_PASS, $name = MYSQL_NAME, $port = MYSQL_PORT, $sock = MYSQL_SOCK){
	$conn = new mysqli($host, $user, $pass, $name, $port, $sock);
	if($conn->connect_errno){
		exit($conn->connect_errno);
	}
	$e=$conn->errno;
	$conn->set_charset('utf8');
	return $conn;
}

//2.执行一条查询,获取结果（针对“仅查询”，返回result）
function execute($conn, $query){
	$result = @$conn->query($query);
	if($conn->errno){
		return $conn->error;
	}
	return $result;
}
//3. 执行一条增删改,获取成功与否（默认不返回result，若要取结果，用store_result和use_result
function execute_bool($conn,$query){
	$bool=@$conn->real_query($query);
	if ($conn->errno)	{
		return $conn->error;
	}
	return $bool;
}
//4. 执行多条语句,
function execute_multi($conn, $arr_sqls, &$error){
	$sqls=implode(';',$arr_sqls).';';
	if($conn->multi_query($sqls)){
		$data=[];
		$i = 0;//计数
		do{ //把数据放入临时变量
			if(($result = $conn->store_result()) !== false){ //执行总结果在$conn中,从$conn提取表数组,把表一个一个放进临时变量$result,$result一次只能存一个表
				$data[$i]=mysqli_fetch_all($result);	//然后把当次取出来的表放进$data[$i];
				mysqli_free_result($result);					//因为一次放一个,临时变量每次清空
			}
			else{
				$data[$i]=null;
			}
			$i++;
			if(!$conn->more_results()){
				break;
			}
		}
		while ($conn->next_result());				//把$conn内部表指针+1
		if($i == count($arr_sqls)){
			return $data;
		}
		else{
			$error="sql语句执行失败：<br />&nbsp;数组下标为{$i}的语句:{$arr_sqls[$i]}执行错误<br />&nbsp;错误原因：".mysqli_error($conn);
			return false;
		}
	}
	else{
		$error='执行失败！请检查首条语句是否正确！<br />可能的错误原因：'.mysqli_error($conn);
		return false;
	}
}
//5. 入库前字符串递归转义,整理 mysqli_real
function escape($conn, $data){
	if(is_string($data)){
		return $conn->real_escape_string($data);	//返回转义后字符串
	}
	if(is_array($data)){
		foreach ($data as $key => $val){
			$data[$key] = escape($conn, $val);
		}
		return $data;
	}
	else{
		return false;
	}
}

//7关闭连接
function close($conn){
	mysqli_close($conn);
}
?>