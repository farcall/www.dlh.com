<div class="box-footer clearfix">
    <ul class="pagination pagination-sm no-margin pull-right">
        <li>
            <?php if ($this->_var['page_info']['prev_link']): ?>
            <a class="page_link" href="<?php echo $this->_var['page_info']['prev_link']; ?>">&laquo;</a>
            <?php endif; ?>
        </li>

        <li>
            <?php $_from = $this->_var['page_info']['page_links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('page', 'link');if (count($_from)):
    foreach ($_from AS $this->_var['page'] => $this->_var['link']):
?>
            <?php if ($this->_var['page_info']['curr_page'] == $this->_var['page']): ?>
            <a class="page_hover" href="<?php echo $this->_var['link']; ?>"><?php echo $this->_var['page']; ?></a>
            <?php else: ?>
            <a class="page_link" href="<?php echo $this->_var['link']; ?>"><?php echo $this->_var['page']; ?></a>
            <?php endif; ?>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </li>

        <li>
            <?php if ($this->_var['page_info']['next_link']): ?>
            <a class="down" href="<?php echo $this->_var['page_info']['next_link']; ?>">&raquo;</a>
            <?php endif; ?>
        </li>
    </ul>
</div>
