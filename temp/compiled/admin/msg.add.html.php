<?php echo $this->fetch('header.html'); ?>
<style type="text/css">
    .info th{width:80px;}
</style>
<div id="rightTop">
    <ul class="subnav" style="margin-left:0px;">
        <li><a class="btn1" href="index.php?app=msg">发送记录</a></li>
        <li><a class="btn1" href="index.php?app=msg&act=user">短信用户</a></li>
        <li><span>增加短信</span></li>
        <li><a class="btn1" href="index.php?app=msg&act=send">短信发送</a></li>
        <li><a class="btn1" href="index.php?app=msg&act=setting">设置接口</a></li>
    </ul>
</div>

<div class="info">
    <table class="infoTable">
        <form method="post">
            <tr>
                <th class="paddingT15">用户名:</th>
                <td class="paddingT15 wordSpacing5">
                    <input name="user_name" type="text" value="<?php $_from = $this->_var['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'val');if (count($_from)):
    foreach ($_from AS $this->_var['val']):
?><?php echo $this->_var['val']['user_name']; ?><?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>" size="20">
                </td>
            </tr>
            <tr>
                <th class="paddingT15">增加/减少</th>
                <td class="paddingT15 wordSpacing5">
                    <input name="num" type="text"  value="" size="10">
                    条
                </td>
            </tr>
            <tr>
                <th class="paddingT15">操作类型:</th>
                <td class="paddingT15 wordSpacing5"><input name="jia_or_jian" type="radio" value="jia" checked="CHECKED" />增加&nbsp;&nbsp;
                    <input type="radio" name="jia_or_jian" value="jian" />减少
                    <font color="#FF0000">注意选择!</font>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">记录流水:</th>		
                <td class="paddingT15 wordSpacing5">
                    <input name="log_text" type="text" id="log_text" value="<?php echo $this->_var['visitor']['user_name']; ?>手工操作短信数量" size="60">
                </td>
            </tr>

            <tr>
                <th></th>
                <td class="ptb20">
                    <input class="formbtn" type="submit" name="Submit" value="提交" />
                    <input class="formbtn" type="reset" name="Submit2" value="重置" />
                </td>
            </tr>
        </form>


    </table>	
</div>
<?php echo $this->fetch('footer.html'); ?>