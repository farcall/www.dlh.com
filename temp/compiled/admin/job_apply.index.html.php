<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">
    <p>在线应聘</p>
    <ul class="subnav">
        <li><span>管理</span></li>
    </ul>
</div>
<div class="mrightTop">
    <div class="fontl">
        <form method="get">
            <div class="left">
                <input type="hidden" name="app" value="job_apply" />
                <input type="hidden" name="act" value="index" />
                状态:
                <select class="querySelect" name="state">
                    <?php echo $this->html_options(array('options'=>$this->_var['states'],'selected'=>$this->_var['query']['state'])); ?>
                </select>
                应聘职位:
                <select class="querySelect" name="job_id">
                    <?php $_from = $this->_var['jobs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'job');if (count($_from)):
    foreach ($_from AS $this->_var['job']):
?>
                    <option value="<?php echo $this->_var['job']['job_id']; ?>" <?php if ($this->_var['job']['job_id'] == $_GET['job_id']): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['job']['position']; ?></option>    
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </select>
                <input type="submit" class="formbtn" value="查询" />
            </div>
            <?php if ($this->_var['filtered']): ?>
            <a class="left formbtn1" href="index.php?app=job_apply}">撤销检索</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="fontr">
        <?php if ($this->_var['job_applys']): ?><?php echo $this->fetch('page.top.html'); ?><?php endif; ?>
    </div>
</div>
<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable">
        <?php if ($this->_var['job_applys']): ?>
        <tr class="tatr1">
            <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
            <td align="center">应聘职位</td>
            <td align="center">姓名</td>
            <td align="center">联系电话</td>
            <td align="center">学历</td>
            <td align="center">专业</td>
            <td align="center">申请时间</td>
            <td class="handler">操作</td>
        </tr>
        <?php endif; ?>
        <?php $_from = $this->_var['job_applys']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'job_apply');if (count($_from)):
    foreach ($_from AS $this->_var['job_apply']):
?>
        <tr class="tatr2">
            <td class="firstCell"><input value="<?php echo $this->_var['job_apply']['id']; ?>" class='checkitem' type="checkbox" /></td>
            <td align="center"><?php echo (htmlspecialchars($this->_var['job_apply']['position']) == '') ? '未填写' : htmlspecialchars($this->_var['job_apply']['position']); ?></td>
            <td align="center"><?php echo (htmlspecialchars($this->_var['job_apply']['name']) == '') ? '未填写' : htmlspecialchars($this->_var['job_apply']['name']); ?></td>
            <td align="center"><?php echo (htmlspecialchars($this->_var['job_apply']['telephone']) == '') ? '未填写' : htmlspecialchars($this->_var['job_apply']['telephone']); ?></td>
            <td align="center"><?php echo (htmlspecialchars($this->_var['job_apply']['education']) == '') ? '未填写' : htmlspecialchars($this->_var['job_apply']['education']); ?></td>
            <td align="center"><?php echo (htmlspecialchars($this->_var['job_apply']['professional']) == '') ? '未填写' : htmlspecialchars($this->_var['job_apply']['professional']); ?></td>
            <td align="center"><?php echo local_date("Y-m-d",$this->_var['job_apply']['add_time']); ?></td>
            <td class="handler">
                <a href="index.php?app=job_apply&amp;act=view&amp;id=<?php echo $this->_var['job_apply']['id']; ?>">查看</a>  |  
                <a name="drop" href="javascript:drop_confirm('您确定要删除它吗？', 'index.php?app=job_apply&amp;act=drop&amp;id=<?php echo $this->_var['job_apply']['id']; ?>');">删除</a>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="7">没有符合条件的记录</td>
        </tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </table>
    <?php if ($this->_var['job_applys']): ?>
    <div id="dataFuncs">
        <div id="batchAction" class="left paddingT15">
            &nbsp;&nbsp;
            <input class="formbtn batchButton" type="button" value="删除" name="id" uri="index.php?app=job_apply&act=drop" presubmit="confirm('您确定要删除它吗？');" />
        </div>
        <div class="pageLinks">
            <?php if ($this->_var['job_applys']): ?><?php echo $this->fetch('page.bottom.html'); ?><?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <div class="clear"></div>
</div>
<?php echo $this->fetch('footer.html'); ?>
