<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <ul class="subnav" style="margin-left:0px;">
        <li><a class="btn1" href="index.php?app=msg">发送记录</a></li>
        <li><span>短信用户</span></li>
        <li><a class="btn1" href="index.php?app=msg&act=add">增加短信</a></li>
        <li><a class="btn1" href="index.php?app=msg&act=send">短信发送</a></li>
        <li><a class="btn1" href="index.php?app=msg&act=setting">设置接口</a></li>
    </ul>
</div>

<div class="mrightTop">
    <div class="fontl">
        <form method="get">
            <div class="left">
                <input name="app" type="hidden" value="msg" />
                <input name="act" type="hidden" value="user" />
                用户名:
                <input class="queryInput" type="text" name="user_name" value="<?php echo htmlspecialchars($this->_var['query']['user_name']); ?>" />
                <input type="submit" class="formbtn" value="查询" />
            </div>
        </form>
    </div>
    <div class="fontr">
        <?php echo $this->fetch('page.top.html'); ?>
    </div>
</div>

<div class="tdare">
    <table width="100%" cellspacing="0">

        <tr class="tatr1">
            <td width="50">id</td>
            <td width="200">用户名</td>
            <td width="200">绑定手机</td>
            <td >开启功能</td>
            <td width="80">可用短信</td>
            <td width="80">状态</td>
            <td width="80">管理操作</td>
        </tr>

        <?php $_from = $this->_var['user']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'val');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['val']):
?>
        <tr class="tatr2">
            <td><b><?php echo $this->_var['val']['user_id']; ?></b></td>
            <td><?php echo $this->_var['val']['user_name']; ?></td>
            <td><?php echo $this->_var['val']['mobile']; ?></td>
            <td>
                <?php if ($this->_var['functions']): ?>
                <?php $_from = $this->_var['functions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'func');if (count($_from)):
    foreach ($_from AS $this->_var['func']):
?>
                <input type="checkbox"  readonly="readonly" <?php if ($this->_var['checked_functions'][$this->_var['func']]): ?> checked<?php endif; ?> id="function_<?php echo $this->_var['func']; ?>_<?php echo $this->_var['val']['user_id']; ?>" /><label for="function_<?php echo $this->_var['func']; ?>_<?php echo $this->_var['val']['user_id']; ?>">&nbsp;<?php echo $this->_var['lang'][$this->_var['func']]; ?></label>&nbsp;&nbsp;
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                <?php endif; ?>
            </td>
            <td><?php echo $this->_var['val']['num']; ?></td>
            <td><?php echo $this->_var['val']['state']; ?></td>
            <td><a href="index.php?app=msg&act=add&user_id=<?php echo $this->_var['val']['user_id']; ?>&user_name=<?php echo $this->_var['val']['user_name']; ?>">增加短信</a></td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="5">没有符合条件的记录</td>
        </tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </table>
    <?php if ($this->_var['index']): ?>
    <div id="dataFuncs">
        <div class="pageLinks"><?php echo $this->fetch('page.bottom.html'); ?></div>
        <div class="clear"></div>
    </div>
    <?php endif; ?>
</div>
<?php echo $this->fetch('footer.html'); ?>