<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:63:"G:\www.shop.com\public/../application/admin\view\goods\add.html";i:1533278721;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>无标题文档</title>
    <link href="<?php echo config('admin_static'); ?>/css/style.css" rel="stylesheet" type="text/css" />
    <script language="JavaScript" src="<?php echo config('admin_static'); ?>/js/jquery.js"></script>
    <script type="text/javascript" charset="utf-8" src="/static/plugins/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/static/plugins/ueditor/ueditor.all.min.js"> </script>
    <!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
    <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
    <script type="text/javascript" charset="utf-8" src="/static/plugins/ueditor/lang/zh-cn/zh-cn.js"></script>
    <style>
        .active{
            border-bottom: solid 3px #66c9f3;
        }
    </style>
</head>

<body>
    <div class="place">
        <span>位置：</span>
        <ul class="placeul">
            <li><a href="#">首页</a></li>
            <li><a href="#">表单</a></li>
        </ul>
    </div>
    <div class="formbody">
        <div class="formtitle">
            <span class="active">基本信息</span>
            <span>商品属性信息</span>
            <span>商品相册</span>
            <span>商品描述</span>

        </div>
        <form action="" method="post" enctype="multipart/form-data">
            <ul class="forminfo">
                <li>
                    <label>商品名称</label>
                    <input name="goods_name" placeholder="请输入商品名称" type="text" class="dfinput" />
                </li>
                <li>
                    <label>商品价格</label>
                    <input name="goods_price" placeholder="请输入商品价格" type="text" class="dfinput" /><i></i>
                </li>
                <li>
                    <label>商品库存</label>
                    <input name="goods_number" placeholder="请输入商品库存" type="text" class="dfinput" />
                </li>
                <li>
                    <label>商品分类</label>
                    <select name='cat_id' class="dfinput">
                        <option value=''>请选择商品分类</option>
                        <?php if(is_array($categorys) || $categorys instanceof \think\Collection || $categorys instanceof \think\Paginator): if( count($categorys)==0 ) : echo "" ;else: foreach($categorys as $key=>$cat): ?>
                            <option value="<?php echo $cat['cat_id']; ?>"><?php echo str_repeat('&nbsp;',$cat['level']*3); ?><?php echo $cat['cat_name']; ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </li>
                <li>
                   <label>回收站</label>
                   <input type="radio" name="is_delete" value='0' checked="checked">否 &nbsp;&nbsp;&nbsp;
                   <input type="radio" name="is_delete" value='1'>是
                </li>
                <li>
                   <label>是否上架</label>
                   <input type="radio" name="is_sale" value='0' >否 &nbsp;&nbsp;&nbsp;
                   <input type="radio" name="is_sale" value='1' checked="checked">是
                </li>
                <li>
                   <label>是否新品</label>
                   <input type="radio" name="is_new" value='0'>否 &nbsp;&nbsp;&nbsp;
                   <input type="radio" name="is_new" value='1'  checked="checked">是
                </li>
                <li>
                   <label>是否热卖</label>
                   <input type="radio" name="is_hot" value='0' >否 &nbsp;&nbsp;&nbsp;
                   <input type="radio" name="is_hot" value='1' checked="checked">是
                </li>
                <li>
                   <label>是否推荐</label>
                   <input type="radio" name="is_best" value='0' >否 &nbsp;&nbsp;&nbsp;
                   <input type="radio" name="is_best" value='1' checked="checked">是
                </li>
            </ul>

            <ul class="forminfo">
                <li>
                    <label>选择商品类型</label>
                    <select name="type_id"  class="dfinput">
                        <option value=''>请选择商品类型</option>
                        <?php if(is_array($types) || $types instanceof \think\Collection || $types instanceof \think\Paginator): if( count($types)==0 ) : echo "" ;else: foreach($types as $key=>$type): ?>
                            <option value="<?php echo $type['type_id']; ?>"><?php echo $type['type_name']; ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                    <!-- 存放生成属性的容器 -->
                    <li id='attrContains'>
                    
                    </li>
                </li>
            </ul>

            <ul class="forminfo">
                <li>
                    <a href="javascript:;" onclick="cloneImg(this)">[+]</a>&nbsp;&nbsp;<input name="img[]" type='file' />
                </li>
            </ul>

            <ul class="forminfo">
                <li>
                    <label>商品详情</label>
                    <textarea name='goods_desc' id="goods_desc"></textarea>
                </li>
            </ul>

			<li>
                <label>&nbsp;</label>
                <input name="" id="btnSubmit" type="submit" class="btn" value="确认保存" />
             </li>
        </form>
    </div>
</body>
<script>
    //切换类型，获取属性
    $("select[name='type_id']").on('change',function(){
        //获取到选择的类型type_id的值
        var type_id = $(this).val();
        if(type_id == ''){
            //清空属性容器
             $("#attrContains").html('');
            //不发无用请求
            return false;
        }
        var html = "";
        //发送ajax，获取指定类型的所有的属性
        $.get("<?php echo url('/admin/goods/getTypeAttr'); ?>",{"type_id":type_id},function(res){
            //console.log(res); return false;
           html += "<ul>";
           //循环拼接li标签
           for(var i=0,length = res.length; i<length; i++){
                //每个属性都是一个li标签
                html += "<li>";
                    //1.单选属性(1)前面有个[+]号
                    if(res[i].attr_type == 1){
                        html += "<a href='javascript:;' onclick='cloneAttr(this)'>[+]</a>";
                    }
                    //2.拼接属性名称
                    html += res[i].attr_name+'&nbsp;&nbsp;';
                    //3.判断属性值的录入方式
                    //单选属性表单name后面需要拼接[],唯一属不需要
                    var hasManyValue = res[i].attr_type == 1 ? "[]" :'';
                    if(res[i].attr_input_type == 0){
                        //手工输入
                        html += "<input type='text' name='goodsAttrValue["+res[i].attr_id+"]"+hasManyValue+"' class='dfinput' placeholder='请输入属性值'/>";
                    }else{
                        //列表选择 attr_values => 黑色|土豪金
                        var attr_values = res[i].attr_values;
                        //炸开属性 ['黑色','土豪金','玫瑰金']
                        var arr_values = attr_values.split('|'); 
                        console.log(arr_values);
                        html += "<select class='dfinput' name='goodsAttrValue["+res[i].attr_id+"]"+hasManyValue+"'>";
                        html += "<option value=''>请选择</option>";
                        //循环创建option标签
                        for(var j=0; j<arr_values.length; j++){
                            html += "<option value='"+arr_values[j]+"'>"+arr_values[j]+"</option>";
                        }
                        html += "</select>";
                    }
                    //4.单选属性需要拼接价格的input框
                    //价格只针对于单选属性。name后面必须加[]
                    if(res[i].attr_type == 1){
                        html += "属性价格：<input type='text' name='goodsAttrPrice["+res[i].attr_id+"][]' class='dfinput' placeholder='请输入价格'/>";
                    }
                html += "</li>";
           }
           html += "</ul>";
           //把最终拼接好的ul放在属性容器里面
           $("#attrContains").html(html);
        },'json');

    })


    function cloneAttr(ele){
        //获取a标签的内容
        var text = $(ele).html();
        if(text == '[+]'){
            //值是[+]
            //克隆父元素li,并把a标签的[+]变为[-]
            var newLi = $(ele).parent().clone();
            newLi.children('a').html('[-]');
            //清空已上传的文件
            newLi.children('input').val('');
            //把newLi追加到当前元素的父元素后面
            $(ele).parent().after(newLi);
        }else{
            //值是[-]把当前父元素li给移除
            $(ele).parent().remove();
        }
    }


    //克隆图片（多文件上传）
    function cloneImg(ele){
        //获取a标签的内容
        var text = $(ele).html();
        if(text == '[+]'){
            //值是[+]
            //克隆父元素li,并把a标签的[+]变为[-]
            var newLi = $(ele).parent().clone();
            newLi.children('a').html('[-]');
            //清空已上传的文件
            newLi.children('input').val('');
            //把newLi追加到当前元素的父元素后面
            $(ele).parent().after(newLi);
        }else{
            //值是[-]把当前父元素li给移除
            $(ele).parent().remove();
        }
    }
    //初始化富文本编辑器
    //实例化编辑器
    //建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例
    var ue = UE.getEditor('goods_desc');

    $(".formtitle span").click(function(event){
        $(this).addClass('active').siblings("span").removeClass('active') ;
        var index = $(this).index();
        $("ul.forminfo").eq(index).show().siblings(".forminfo").hide();
    });
     $(".formtitle span").eq(0).click();
</script>

</html>
