<?php
	class clicaptcha{
		public $text;
		public $imagePath;
		private $fontPath;
		function __construct(){
			header("Content-type: text/html; charset=utf-8");
			session_start();
			error_reporting(E_ERROR | E_WARNING | E_PARSE);
			$this->fontPath = 'font/songtiGBK.TTF';
		}
		public function GetSmallImg()
		{		$fontPath = $this->fontPath;
				$imagePath = $this->imagePath;
			  foreach($this->text as $v){
					$fontSize = 50;
					//字符串文本框宽度和长度
					$fontarea  = imagettfbbox($fontSize, 0, $fontPath, $v);
					$textWidth = $fontarea[2] - $fontarea[0];
					$textHeight = $fontarea[1] - $fontarea[7];
					$tmp['text'] = $v;
					$tmp['size'] = $fontSize;
					$tmp['width'] = $textWidth;
					$tmp['height'] = $textHeight;
					$textArr['text'][] = $tmp;
				}
				//图片宽高和类型
				list($imageWidth, $imageHeight, $imageType) = getimagesize($imagePath);
				$textArr['width'] = $imageWidth;
				$textArr['height'] = $imageHeight;
				$array = array(array(0,$imageHeight/2),array(($imageWidth/4)*1,$imageHeight/2),array(($imageWidth/4)*2,$imageHeight/2),array(($imageWidth/4)*3,$imageHeight/2));
				//随机生成汉字位置
				foreach($textArr['text'] as $k => &$v){
					list($x, $y) = $array[$k];
					$v['x'] = $x;
					$v['y'] = $y;
					$text[] = $v['text'];
				}
				unset($v);
				//创建图片的实例
				$image = imagecreatefromstring(file_get_contents($imagePath));
				foreach($textArr['text'] as $v){
					list($r, $g, $b) = $this->getImageColor($imagePath, $v['x'] + $v['width'] / 2, $v['y'] - $v['height'] / 2);
					//字体颜色
					$r = $r > 127 ? 40 : 220;
					$g = $g > 127 ? 40 : 220;
					$b = $b > 127 ? 40 : 220;
					$color = imagecolorallocate($image, $r, $g, $b);
					//阴影字体颜色
					// $r = $r > 127 ? 40 : 200;
					// $g = $g > 127 ? 40 : 200;
					// $b = $b > 127 ? 40 : 200;
					$shadowColor = imagecolorallocate($image, $r, $g, $b);
					//绘画阴影
					imagettftext($image, $v['size'], 0, $v['x'] + 1, $v['y'], $shadowColor, $fontPath, $v['text']);
					imagettftext($image, $v['size'], 0, $v['x'], $v['y'] + 1, $shadowColor, $fontPath, $v['text']);
					imagettftext($image, $v['size'], 0, $v['x'] - 1, $v['y'], $shadowColor, $fontPath, $v['text']);
					imagettftext($image, $v['size'], 0, $v['x'], $v['y'] - 1, $shadowColor, $fontPath, $v['text']);
					imagettftext($image, $v['size'], 0, $v['x'] + 1, $v['y'] + 1, $shadowColor, $fontPath, $v['text']);
					imagettftext($image, $v['size'], 0, $v['x'] + 1, $v['y'] - 1, $shadowColor, $fontPath, $v['text']);
					imagettftext($image, $v['size'], 0, $v['x'] - 1, $v['y'] - 1, $shadowColor, $fontPath, $v['text']);
					imagettftext($image, $v['size'], 0, $v['x'] - 1, $v['y'] + 1, $shadowColor, $fontPath, $v['text']);
					//绘画文字
					imagettftext($image, $v['size'], 0, $v['x'], $v['y'], $color, $fontPath, $v['text']);
				}
				//生成图片
				switch($imageType){
					case 1://GIF
						header('Content-Type: image/gif');
						imagegif($image);
						break;
					case 2://JPG
						header('Content-Type: image/jpeg');
						imagejpeg($image);
						break;
					case 3://PNG
						header('Content-Type: image/png');
						imagepng($image);
						break;
					default:
						break;
				}
				imagedestroy($image);
		}
		public function creat(){
			$fontPath = $this->fontPath;
			$imagePath = $this->imagePath;
			foreach($this->text as $v){
				$fontSize = rand(25, 30);
				//字符串文本框宽度和长度
				$fontarea  = imagettfbbox($fontSize, 0, $fontPath, $v);
				$textWidth = $fontarea[2] - $fontarea[0];
				$textHeight = $fontarea[1] - $fontarea[7];
				$tmp['text'] = $v;
				$tmp['size'] = $fontSize;
				$tmp['width'] = $textWidth;
				$tmp['height'] = $textHeight;
				$textArr['text'][] = $tmp;
			}
			//图片宽高和类型
			list($imageWidth, $imageHeight, $imageType) = getimagesize($imagePath);
			$textArr['width'] = $imageWidth;
			$textArr['height'] = $imageHeight;
			//随机生成汉字位置
			foreach($textArr['text'] as &$v){
				list($x, $y) = $this->randPosition($textArr['text'], $imageWidth, $imageHeight, $v['width'], $v['height']);
				$v['x'] = $x;
				$v['y'] = $y;
				$text[] = $v['text'];
			}
			unset($v);
			$_SESSION['clicaptcha_text'] = $textArr;
			setcookie('clicaptcha_text', implode(',', $text), time() + 3600, '/');
			//创建图片的实例
			$image = imagecreatefromstring(file_get_contents($imagePath));
			foreach($textArr['text'] as $v){
				list($r, $g, $b) = $this->getImageColorBig($imagePath, $v['x'] + $v['width'] / 2, $v['y'] - $v['height'] / 2);
				//字体颜色
				$r = $r > 127 ? 40 : 220;
				$g = $g > 127 ? 40 : 220;
				$b = $b > 127 ? 40 : 220;
				$color = imagecolorallocate($image, $r, $g, $b);
				//阴影字体颜色
				// $r = $r > 127 ? 40 : 220;
				// $g = $g > 127 ? 40 : 220;
				// $b = $b > 127 ? 40 : 220;
				$shadowColor = imagecolorallocate($image, $r, $g, $b);
				$angle = rand(0,5);
				//绘画阴影
				imagettftext($image, $v['size'], $angle, $v['x'] + 1, $v['y'], $shadowColor, $fontPath, $v['text']);
				imagettftext($image, $v['size'], $angle, $v['x'], $v['y'] + 1, $shadowColor, $fontPath, $v['text']);
				imagettftext($image, $v['size'], $angle, $v['x'] - 1, $v['y'], $shadowColor, $fontPath, $v['text']);
				imagettftext($image, $v['size'], $angle, $v['x'], $v['y'] - 1, $shadowColor, $fontPath, $v['text']);
				imagettftext($image, $v['size'], $angle, $v['x'] + 1, $v['y'] + 1, $shadowColor, $fontPath, $v['text']);
				imagettftext($image, $v['size'], $angle, $v['x'] + 1, $v['y'] - 1, $shadowColor, $fontPath, $v['text']);
				imagettftext($image, $v['size'], $angle, $v['x'] - 1, $v['y'] - 1, $shadowColor, $fontPath, $v['text']);
				imagettftext($image, $v['size'], $angle, $v['x'] - 1, $v['y'] + 1, $shadowColor, $fontPath, $v['text']);
				//绘画文字
				imagettftext($image, $v['size'], $angle, $v['x'], $v['y'], $color, $fontPath, $v['text']);
			}
			//生成图片
			switch($imageType){
				case 1://GIF
					header('Content-Type: image/gif');
					imagegif($image);
					break;
				case 2://JPG
					header('Content-Type: image/jpeg');
					imagejpeg($image);
					break;
				case 3://PNG
					header('Content-Type: image/png');
					imagepng($image);
					break;
				default:
					break;
			}
			imagedestroy($image);
		}
		
		public function check($info, $unset = true){
			$flag = true;
			if(isset($_SESSION['clicaptcha_text'])){
				$textArr = $_SESSION['clicaptcha_text'];
				list($xy, $w, $h) = explode(';', $info);
				$xyArr = explode('-', $xy);
				$xpro = $w / $textArr['width'];//宽度比例
				$ypro = $h / $textArr['height'];//高度比例
				foreach($xyArr as $k => $v){
					$xy = explode(',', $v);
					$x = $xy[0];
					$y = $xy[1];
					if($x / $xpro < $textArr['text'][$k]['x'] || $x / $xpro > $textArr['text'][$k]['x'] + $textArr['text'][$k]['width']){
						$flag = false;
						break;
					}
					if($y / $ypro < $textArr['text'][$k]['y'] - $textArr['text'][$k]['height'] || $y / $ypro > $textArr['text'][$k]['y']){
						$flag = false;
						break;
					}
				}
				if($unset){
					unset($_SESSION['clicaptcha_text']);
				}
			}else{
				$flag = false;
			}
			return $flag;
		}
		//随机生成中文汉字
		public function randChars($length = 4){
			/**
			 * 字符串截取，支持中文和其他编码
			 * @static
			 * @access public
			 * @param string $str 需要转换的字符串
			 * @param string $start 开始位置
			 * @param string $length 截取长度
			 * @param string $charset 编码格式
			 * @param string $suffix 截断显示字符
			 * @return string
			 */
			function msubstr($str, $start = 0, $length, $charset = 'utf-8', $suffix = true){
				if(function_exists('mb_substr')){
					$slice = mb_substr($str, $start, $length, $charset);
				}else if(function_exists('iconv_substr')){
					$slice = iconv_substr($str, $start, $length, $charset);
				}else{
					$re['utf-8']  = '/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/';
					$re['gb2312'] = '/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/';
					$re['gbk']    = '/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/';
					$re['big5']   = '/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/';
					preg_match_all($re[$charset], $str, $match);
					$slice = join('', array_slice($match[0], $start, $length));
				}
				return $suffix ? $slice.'...' : $slice;
			}
			$chars = '首页形象爆品转化企业介绍证书认证视频播放产品展示产品评价设备展示案例展示客户服务自动水印网站搜索在线招聘招商加盟在线反馈电子地图全网营销微信名片微信海报静态系统热词部署缓存加速网站地图网址收录热词标签媒体分享友情链接邮件营销在线客服批量处理文件下载新闻发布自动发布会员管理在线购物安全防护流量监控客户展示搜索优化运行管理竞争策略品牌定位竞争网站优势提炼文案撰写形象策划招商策划加盟策划基础配置功能项目域名语言空间流量主机类型程序语言拍照处理网站备案实施负责优秀设计技术团队沟通模式技术支持制作周期确认上传';
			$str = msubstr($chars, floor(mt_rand(0, (mb_strlen($chars, 'utf-8')/4)-1)*4), 4, 'utf-8', false);
			for($i = 0; $i < $length; $i++){
				$return[] = msubstr($str, $i, 1, 'utf-8', false);
			}
			return $return;
		}
		//随机生成位置布局
		private function randPosition($textArr, $imgW, $imgH, $fontW, $fontH){
			$return = array();
			$x = rand(0, $imgW - $fontW);
			$y = rand($fontH, $imgH);
			//碰撞验证
			if(!$this->checkPosition($textArr, $x, $y, $fontW, $fontH)){
				$return = $this->randPosition($textArr, $imgW, $imgH, $fontW, $fontH);
			}else{
				$return = array($x, $y);
			}
			return $return;
		}
		private function checkPosition($textArr, $x, $y, $w, $h){
			$flag = true;
			foreach($textArr as $v){
				if(isset($v['x']) && isset($v['y'])){
					//分别判断X和Y是否都有交集，如果都有交集，则判断为覆盖
					$flagX = true;
					if($v['x'] > $x){
						if($x + $w > $v['x']){
							$flagX = false;
						}
					}else if($x > $v['x']){
						if($v['x'] + $v['width'] > $x){
							$flagX = false;
						}
					}else{
						$flagX = false;
					}
					$flagY = true;
					if($v['y'] > $y){
						if($y + $h > $v['y']){
							$flagY = false;
						}
					}else if($y > $v['y']){
						if($v['y'] + $v['height'] > $y){
							$flagY = false;
						}
					}else{
						$flagY = false;
					}
					if(!$flagX && !$flagY){
						$flag = false;
					}
				}
			}
			return $flag;
		}
		//获取图片某个定点上的主要色
		private function getImageColor($img, $x, $y){
			list($imageWidth, $imageHeight, $imageType) = getimagesize($img);
			switch($imageType){
				case 1://GIF
					$im = imagecreatefromgif($img);
					break;
				case 2://JPG
					$im = imagecreatefromjpeg($img);
					break;
				case 3://PNG
					$im = imagecreatefrompng($img);
					break;
			}
			$rgb1 = imagecolorat($im, $x, $y);
			$all = 0;
			for ($i=1; $i < 16; $i++) { 
				$rgb1 = imagecolorat($im, $x-$i, $y);
				$rgb2 = imagecolorat($im, $x-$i, $y-$i);
				$rgb3 = imagecolorat($im, $x, $y-$i);
				$rgb4 = imagecolorat($im, $x+$i, $y);
				$rgb5 = imagecolorat($im, $x+$i, $y+$i);
				$rgb6 = imagecolorat($im, $x, $y+$i);
				$rgb7 = imagecolorat($im, $x-$i, $y+$i);
				$rgb8 = imagecolorat($im, $x+$i, $y-$i);
				$all += ($rgb1+$rgb2+$rgb3+$rgb4+$rgb5+$rgb6+$rgb7+$rgb8);
			}
			$rgb = ($rgb1+$all)/129;
			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;
			return array($r, $g, $b);
		}

		//获取图片某个定点上的主要色
		private function getImageColorBig($img, $x, $y){
			list($imageWidth, $imageHeight, $imageType) = getimagesize($img);
			switch($imageType){
				case 1://GIF
					$im = imagecreatefromgif($img);
					break;
				case 2://JPG
					$im = imagecreatefromjpeg($img);
					break;
				case 3://PNG
					$im = imagecreatefrompng($img);
					break;
			}
			$rgb1 = imagecolorat($im, $x, $y);
			$all = 0;
			for ($i=1; $i < 50; $i++) {
				$rgb1 = imagecolorat($im, $x-$i, $y);
				$rgb2 = imagecolorat($im, $x-$i, $y-$i);
				$rgb3 = imagecolorat($im, $x, $y-$i);
				$rgb4 = imagecolorat($im, $x+$i, $y);
				$rgb5 = imagecolorat($im, $x+$i, $y+$i);
				$rgb6 = imagecolorat($im, $x, $y+$i);
				$rgb7 = imagecolorat($im, $x-$i, $y+$i);
				$rgb8 = imagecolorat($im, $x+$i, $y-$i);
				$all += ($rgb1+$rgb2+$rgb3+$rgb4+$rgb5+$rgb6+$rgb7+$rgb8);
			}
			$rgb = ($rgb1+$all)/401;
			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;
			return array($r, $g, $b);
		}
	}
?>
