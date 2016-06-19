
<footer id="copyright">
    <section class="footer-t">
        <div class="fl" id="is_login">
            <?php if (! $this->_var['visitor']['user_id']): ?>
            <a href="<?php echo url('app=member&act=login&ret_url=' . $this->_var['ret_url']. ''); ?>">请登录</a><a href="<?php echo url('app=member&act=register&ret_url=' . $this->_var['ret_url']. ''); ?>">注册</a>
            <?php else: ?>
            <span class="mr10"><?php echo htmlspecialchars($this->_var['visitor']['user_name']); ?></span><a href="<?php echo url('app=member&act=logout'); ?>">退出</a>
            <?php endif; ?> 
        </div>
        <a href="#" class="retum">回到顶部<b></b></a>
    </section>
    <section class="footer-c">
        <div>© <?php echo $this->_var['icp_number']; ?>
            <?php echo $this->_var['statistics_code']; ?>
        </div>
    </section>
    <?php echo $this->_var['async_sendmail']; ?>
</footer>

<div id="footer_nav" style="opacity: 0.9;">
    <ul>
        <li>
            <a href="<?php echo url('app=default'); ?>">
                <span class="iconfont">&#xe609;</span>
                <br/>
                首页
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=category'); ?>"><span class="iconfont">&#xe60b;</span><br/>分类</a>
        </li>
        <li>
            <a href="tel:<?php echo $this->_var['site_phone_tel']; ?>"><span class="iconfont">&#xe60a;</span><br/>电话</a>
        </li>
        <li>
            <a href="<?php echo url('app=cart'); ?>"><span class="iconfont">&#xe60d;</span><br/>购物车</a>
        </li>
        <li>
            <a href="<?php echo url('app=member'); ?>"><span class="iconfont">&#xe60c;</span><br/>个人中心</a>
        </li>
    </ul>
</div>

</html>