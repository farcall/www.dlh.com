<?php echo $this->fetch('member.header.html'); ?>
<div class="mb-head">
    <a href="<?php echo url('app=default'); ?>" class="l_b">首页</a>
    <div class="tit">个人中心</div>
    <a href="javascript" class="r_b"></a>
</div>


<div class="user_header">
    <div class="user_photo">
        <a href="<?php echo url('app=member'); ?>"><img src="<?php echo $this->_var['user']['portrait']; ?>" /></a>
    </div>
    <span class="user_name">
        您好,欢迎 <?php if ($this->_var['paycenter']['vip'] == '0'): ?>免费会员<?php else: ?>VIP会员<?php endif; ?><?php echo htmlspecialchars($this->_var['visitor']['user_name']); ?>。<a href="index.php?app=member&act=logout">退出</a>
    </span>
</div>

<section class="member_index">
    <div class="info">

        <dl>
            <dd>积分赠送券</dd>
            <dt><?php echo $this->_var['paycenter']['integral_power']; ?></dt>
        </dl>
        <dl>
            <dd>现金账户</dd>
            <dt><?php echo $this->_var['paycenter']['money']; ?></dt>
        </dl>
        <dl>
            <dd>白积分</dd>
            <dt><?php echo $this->_var['paycenter']['integral_white']; ?></dt>
        </dl>
        <dl>
            <dd>冻结资金</dd>
            <dt><?php echo $this->_var['paycenter']['money_dj']; ?></dt>
        </dl>

        <dl>
            <dd>红积分</dd>
            <dt><?php echo $this->_var['paycenter']['integral_red']; ?></dt>
        </dl>
        <dl>
            <dd>税费资金</dd>
            <dt><?php echo $this->_var['paycenter']['money_tax']; ?></dt>
        </dl>
        <div class="clear"></div>
    </div>
    <ul>
        <li>
            <a href="<?php echo url('app=paycenter'); ?>">
                <span class="ico" style="background:#993399"><em class="iconfont">&#xe605;</em></span>
                <span class="title">财富中心</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=refer'); ?>">
                <span class="ico" style="background:#663366"><em class="iconfont">&#xe607;</em></span>
                <span class="title">二维码推广</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=epay&act=withdraw'); ?>">
                <span class="ico" style="background:#99CC66"><em class="iconfont">&#xe604;</em></span>
                <span class="title">提现</span>
            </a>
        </li>

        <li>
            <a href="<?php echo url('app=my_integral_log'); ?>">
                <span class="ico" style="background:#663366"><em class="iconfont">&#xe606;</em></span>
                <span class="title">白积分明细</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=my_favorite'); ?>">
                <span class="ico" style="background:#993399"><em class="iconfont">&#xe628;</em></span>
                <span class="title">商品收藏</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=my_favorite&type=store'); ?>">
                <span class="ico" style="background:#99CC66"><em class="iconfont">&#xe627;</em></span>
                <span class="title">店铺收藏</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=my_address'); ?>">
                <span class="ico" style="background:#FF99CC"><em class="iconfont">&#xe623;</em></span>
                <span class="title">收货地址</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=buyer_order'); ?>">
                <span class="ico" style="background:#FF6666"><em class="iconfont">&#xe628;</em></span>
                <span class="title">我的订单</span>
            </a>
        </li>
        <li>
            <a href="http://m.kuaidi100.com/">
                <span class="ico" style="background:#CC6699"><em class="iconfont">&#xe626;</em></span>
                <span class="title">物流查询</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=member&act=password'); ?>">
                <span class="ico" style="background:#339999"><em class="iconfont">&#xe624;</em></span>
                <span class="title">修改密码</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=member&act=profile'); ?>">
                <span class="ico" style="background:#FF99CC"><em class="iconfont">&#xe61a;</em></span>
                <span class="title">修改信息</span>
            </a>
        </li>



    </ul>
</section>


<section class="member_index">
    <?php if ($this->_var['store']): ?>
    <ul>
        <li>
            <a href="<?php echo url('app=my_store'); ?>">
                <span class="ico" style="background:#CC6699"><em class="iconfont">&#xe613;</em></span>
                <span class="title">店铺设置</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=my_goods'); ?>">
                <span class="ico" style="background:#003399"><em class="iconfont">&#xe60b;</em></span>
                <span class="title">商品管理</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=seller_order'); ?>">
                <span class="ico" style="background:#66CC99"><em class="iconfont">&#xe611;</em></span>
                <span class="title">订单管理</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=my_payment'); ?>">
                <span class="ico" style="background:#CC6699"><em class="iconfont">&#xf00ae;</em></span>
                <span class="title">支付管理</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=my_shipping'); ?>">
                <span class="ico" style="background:#FF6666"><em class="iconfont">&#xe608;</em></span>
                <span class="title">配送管理</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=store&id=' . $this->_var['store']['store_id']. ''); ?>">
                <span class="ico" style="background:#FF6666"><em class="iconfont">&#xe609;</em></span>
                <span class="title">我的店铺</span>
            </a>
        </li>
    </ul>

    <?php else: ?>
    <ul>
        <li>
            <a href="<?php echo url('app=apply'); ?>">
                <span class="ico" style="background:#993366"><em class="iconfont">&#xf00a0;</em></span>
                <span class="title">申请开店</span>
            </a>
        </li>
    </ul>
    <?php endif; ?>

</section>


<?php echo $this->fetch('member.footer.html'); ?>