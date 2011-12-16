<?php
class DB{static $q;public$i='`',$c;function DB($a){$this->c=$a;}function column($a,$b=NULL,$c=0){if($d=$this->query($a,$b))return$d->fetchColumn($c);}function row($a,$b=NULL){if($c=$this->query($a,$b))return$c->fetch();}function fetch($a,$b=NULL){if($c=$this->query($a,$b))return$c->fetchAll();}function query($a,$b=NULL){$c=$this->c->prepare(self::$d[]=strtr($a,'`',$this->i));$c->execute($b);return$c;}function insert($a,$b){$c=$this;$d="INSERT INTO `$a`(`".implode('`,`',array_keys($b)).'`)VALUES('.rtrim(str_repeat('?,',count($b=array_values($b))),',').')';return$c->p?$c->column($d.'RETURNING `id`',$b):($c->query($d,$b)?$c->c->lastInsertId():NULL);}function update($a,$b,$c){$d="UPDATE `$a` SET `".implode('`=?,`',array_keys($b)).'`=? WHERE '.(is_array($c)?$this->where($c,$b):$c);if($e=$this->query($d,array_values($b)))return$e->rowCount();}function delete($a,$b){$c;if($d=$this->query("DELETE FROM `$a` WHERE ".(is_array($b)?$this->where($b,$c):$b),$c))return$d->rowCount();}function where($a,&$b){$c;foreach($a as$d=>$e){$c[]="`$d`=?";$b[]=$e;}return join(' AND ',$c);}}class _{static function convert($a,$b=0,$c=1){if(function_exists('mb_detect_encoding'))$b=mb_detect_encoding($a,'auto');if(($a=@iconv(!$b?'UTF-8':$b,'UTF-8//IGNORE',$a))!==false){return$c?preg_replace('~\p{C}+~u','',$a):preg_replace(array('~\r\n?~','~[^\P{C}\t\n]+~u'),array("\n",''),$a);}}static function date($a=0,$b=IntlDateFormatter::MEDIUM,$c=IntlDateFormatter::SHORT,$d=NULL){returnnew IntlDateFormatter($a?:setlocale(LC_ALL,0),$b,$c,$d);}static function format($a,array$b=NULL){return msgfmt_format_message(setlocale(LC_ALL,0),$a,$b);}static function normalize($a,$b=Normalizer::FORM_D){return normalizer_normalize($a,$b);}static function unaccent($a){if(strpos($a=htmlentities($a,ENT_QUOTES,'UTF-8'),'&')!==false)return html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i','$1',$a),ENT_QUOTES,'UTF-8');}static function slug($a,$b='-',$c=null){return strtolower(trim(preg_replace('~[^0-9a-z'.preg_quote($c,'~').']+~i',$b,self::unaccent($a)),$b));}}class I{publicstatic function __callStatic($a,$b){$c=array('session'=>'_SESSION','post'=>'_POST','get'=>'_GET','server'=>'_SERVER','files'=>'_FILES','cookie'=>'_COOKIE','env'=>'_ENV','request'=>'_REQUEST');$a=$c[$a];if(isset($d[$a][$b[0]])){return$d[$a][$b[0]];}returnisset($b[1])?$b[1]:NULL;}}class View{public$__v;publicfunction __construct($a,$b=__DIR__){$this->__v="$b/view/$a.php";}publicfunction e($a){return htmlspecialchars($a,ENT_QUOTES,'UTF-8');}publicfunction d($a){return htmlspecialchars_decode($a,ENT_QUOTES,'UTF-8');}publicfunction __call($a,$b){$this->$a=$b[0];return$this;}publicfunction set($a){foreach($a as$b=>$c)$this->$b=$c;return$this;}publicfunction __toString(){try{ob_start();extract((array)$this);require$a;return ob_get_clean();}catch(\Exception$b){return''.$b;}}}class Validation{public$s='Array';function __set($a,$b){$this->s[$a]=$b;}function __get($a){return$this->s[$a]($this);}function validate($a){$b;foreach($this->s as$c=>$d){if($e=$d(isset($a[$c])?$a[$c]:NULL,$c)){$b[$c]=$e;}}return$b;}}