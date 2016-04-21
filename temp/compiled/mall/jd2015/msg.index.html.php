<?php echo $this->fetch('member.header.html'); ?>
<style type="text/css">
    .table .line td{border:none;}
    .float_right {float: right;}
    .line{border-bottom:1px solid #E2E2E2}
</style>
<div class="content">
    <?php echo $this->fetch('member.menu.html'); ?>
    <div id="right">
        <?php echo $this->fetch('member.submenu.html'); ?>
        <div class="wrap">
            <div class="public table">
                <table>
                    <?php if ($this->_var['msglogs']): ?>
                    <tr class="line tr_bgcolor">
                        <td align='left' width='20%'>手机号码</td>
                        <td align='left' width='20%'>时间</td>
                        <td align='left' width='50%'>内容</td>
                        <td align='left' width='10%'>状态</td>
                    </tr>
                    <?php endif; ?>
                    <?php $_from = $this->_var['msglogs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'msglog');$this->_foreach['v'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['v']['total'] > 0):
    foreach ($_from AS $this->_var['msglog']):
        $this->_foreach['v']['iteration']++;
?>
                    <?php if (($this->_foreach['v']['iteration'] == $this->_foreach['v']['total'])): ?><tr class="line_bold"><?php else: ?><tr class="line"><?php endif; ?>
                        <td align='left'><?php echo htmlspecialchars($this->_var['msglog']['to_mobile']); ?></td>
                        <td align='left'><?php echo local_date("Y-m-d H:i:s",$this->_var['msglog']['time']); ?></td>
                        <td align='left'><?php echo htmlspecialchars($this->_var['msglog']['content']); ?></td>
                        <td align='left'><?php echo $this->_var['msglog']['state']; ?></td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="5" class="member_no_records padding6"><?php echo $this->_var['lang'][$_GET['act']]; ?>没有符合条件的记录</td>
                    </tr>
                    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </table>
                <?php echo $this->fetch('member.page.bottom.html'); ?>
            </div>
            <div class="wrap_bottom"></div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<div class="clear"></div>
<?php echo $this->fetch('footer.html'); ?>
