<?php echo $this->fetch('header.html'); ?>


<?php if ($this->_var['wap_ads']['1']): ?>
<script type="text/javascript" src="<?php echo $this->res_base . "/" . 'js/swipe.js'; ?>" charset="utf-8"></script>
<div id="banner_box" class="box_swipe">
    <ul>
        <?php $_from = $this->_var['wap_ads']['1']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'wap_ad');$this->_foreach['fe_wap_ad'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['fe_wap_ad']['total'] > 0):
    foreach ($_from AS $this->_var['wap_ad']):
        $this->_foreach['fe_wap_ad']['iteration']++;
?>
        <li><a href="<?php echo $this->_var['wap_ad']['ad_link']; ?>"><img src="<?php echo $this->_var['wap_ad']['ad_logo']; ?>" /></a></li>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </ul>
    <ol>
        <?php $_from = $this->_var['wap_ads']['1']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'wap_ad');$this->_foreach['fe_wap_ad'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['fe_wap_ad']['total'] > 0):
    foreach ($_from AS $this->_var['wap_ad']):
        $this->_foreach['fe_wap_ad']['iteration']++;
?>
        <li <?php if (($this->_foreach['fe_wap_ad']['iteration'] <= 1)): ?>class="on"<?php endif; ?>></li>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </ol>
    <script>
        $(function () {
            new Swipe(document.getElementById('banner_box'), {
                speed: 500,
                auto: 3000,
                callback: function () {
                    var lis = $(this.element).next("ol").children();
                    lis.removeClass("on").eq(this.index).addClass("on");
                }
            });
        });
    </script>
</div>
<?php endif; ?>


<section class="guide">
    <ul>
        <li>
            <a href="<?php echo url('app=category'); ?>">
                <em class="iconfont" style="color: #c91e31;">&#xe601;</em>
                <span class="title">商品分类</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=cart'); ?>">
                <em class="iconfont" style="color: #c91e31;">&#xe603;</em>
                <span class="title">购物车</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=member'); ?>">
                <em class="iconfont" style="color: #c91e31;">&#xe608;</em>
                <span class="title">个人中心</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=paycenter'); ?>">
                <em class="iconfont" style="color: #c91e31;">&#xe605;</em>
                <span class="title">财富中心</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=my_integral_log'); ?>">
                <em class="iconfont" style="color: #c91e31;">&#xe606;</em>
                <span class="title">白积分</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=refer'); ?>">
                <em class="iconfont" style="color: #c91e31;">&#xe607;</em>
                <span class="title">二维码</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=refer&act=all_refer'); ?>">
                <em class="iconfont" style="color: #c91e31;">&#xe602;</em>
                <span class="title">推荐成员</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('app=epay&act=withdraw'); ?>">
                <em class="iconfont" style="color: #c91e31;">&#xe604;</em>
                <span class="title">提现申请</span>
            </a>
        </li>

    </ul>
</section>




<div class="jd2015_floor">
   <h2 class="title">得来惠商学院</h2>
   <div class="hot-link">
       <!--<a href="<?php echo $this->_var['wap_ads']['3']['0']['ad_link']; ?>"><img src="<?php echo $this->_var['wap_ads']['3']['0']['ad_logo']; ?>" width="100%"></a>-->
       <img src="<?php echo $this->_var['wap_ads']['3']['0']['ad_logo']; ?>" width="100%">

   </div>
</div>
<div class="jd2015_floor">
    <h2 class="title">购车消费全返</h2>
    <div class="row1">
        <div class="col1"><a href="<?php echo $this->_var['wap_ads']['4']['0']['ad_link']; ?>"><img src="<?php echo $this->_var['wap_ads']['4']['0']['ad_logo']; ?>" width="100%" ></a></div>
        <div class="col2"><a href="<?php echo $this->_var['wap_ads']['4']['1']['ad_link']; ?>"><img src="<?php echo $this->_var['wap_ads']['4']['1']['ad_logo']; ?>" width="100%" ></a></div>
        <div class="col3"><a href="<?php echo $this->_var['wap_ads']['4']['2']['ad_link']; ?>"><img src="<?php echo $this->_var['wap_ads']['4']['2']['ad_logo']; ?>" width="100%" ></a></div>
    </div>
</div>
<div class="jd2015_floor">
    <h2 class="title">服装鞋包</h2>
    <div class="row2">
        <div class="col1"><a href="<?php echo $this->_var['wap_ads']['5']['0']['ad_link']; ?>"><img src="<?php echo $this->_var['wap_ads']['5']['0']['ad_logo']; ?>" width="100%" ></a></div>
        <div class="col2"><a href="<?php echo $this->_var['wap_ads']['5']['1']['ad_link']; ?>"><img src="<?php echo $this->_var['wap_ads']['5']['1']['ad_logo']; ?>" width="100%" ></a></div>
        <div class="col3"><a href="<?php echo $this->_var['wap_ads']['5']['2']['ad_link']; ?>"><img src="<?php echo $this->_var['wap_ads']['5']['2']['ad_logo']; ?>" width="100%" ></a></div>
    </div>
</div>

<div class="jd2015_floor">
    <h2 class="title">美容化妆</h2>
    <div class="row1">
        <div class="col1"><a href="<?php echo $this->_var['wap_ads']['6']['0']['ad_link']; ?>"><img src="<?php echo $this->_var['wap_ads']['6']['0']['ad_logo']; ?>" width="100%" ></a></div>
        <div class="col2"><a href="<?php echo $this->_var['wap_ads']['6']['1']['ad_link']; ?>"><img src="<?php echo $this->_var['wap_ads']['6']['1']['ad_logo']; ?>" width="100%" ></a></div>
        <div class="col3"><a href="<?php echo $this->_var['wap_ads']['6']['2']['ad_link']; ?>"><img src="<?php echo $this->_var['wap_ads']['6']['2']['ad_logo']; ?>" width="100%" ></a></div>
    </div>
</div>

<div class="jd2015_floor">
    <h2 class="title">手机数码</h2>
    <div class="row2">
        <div class="col1"><a href="<?php echo $this->_var['wap_ads']['7']['0']['ad_link']; ?>"><img src="<?php echo $this->_var['wap_ads']['7']['0']['ad_logo']; ?>" width="100%" ></a></div>
        <div class="col2"><a href="<?php echo $this->_var['wap_ads']['7']['1']['ad_link']; ?>"><img src="<?php echo $this->_var['wap_ads']['7']['1']['ad_logo']; ?>" width="100%" ></a></div>
        <div class="col3"><a href="<?php echo $this->_var['wap_ads']['7']['2']['ad_link']; ?>"><img src="<?php echo $this->_var['wap_ads']['7']['2']['ad_logo']; ?>" width="100%" ></a></div>
    </div>
</div>

<div class="jd2015_floor">
    <h2 class="title">家用电器</h2>
    <div class="row1">
        <div class="col1"><a href="<?php echo $this->_var['wap_ads']['8']['0']['ad_link']; ?>"><img src="<?php echo $this->_var['wap_ads']['8']['0']['ad_logo']; ?>" width="100%" ></a></div>
        <div class="col2"><a href="<?php echo $this->_var['wap_ads']['8']['1']['ad_link']; ?>"><img src="<?php echo $this->_var['wap_ads']['8']['1']['ad_logo']; ?>" width="100%" ></a></div>
        <div class="col3"><a href="<?php echo $this->_var['wap_ads']['8']['2']['ad_link']; ?>"><img src="<?php echo $this->_var['wap_ads']['8']['2']['ad_logo']; ?>" width="100%" ></a></div>
    </div>
</div>


<div class="jd2015_floor">
    <h2 class="title">热门活动</h2>
    <ul class="row3">
        <?php $_from = $this->_var['wap_ads']['10']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'wap_ad');$this->_foreach['fe_wap_ad'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['fe_wap_ad']['total'] > 0):
    foreach ($_from AS $this->_var['wap_ad']):
        $this->_foreach['fe_wap_ad']['iteration']++;
?>
        <li>
            <a href="<?php echo $this->_var['wap_ad']['ad_link']; ?>"><img src="<?php echo $this->_var['wap_ad']['ad_logo']; ?>" width="100%"></a>
        </li>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </ul>
    <div class="clear"></div>
</div>



<div class="jd2015_floor">
    <h2 class="title">推荐产品</h2>
    <ul class="row4">
        <?php $_from = $this->_var['wap_recommended_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');$this->_foreach['fe_goods'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['fe_goods']['total'] > 0):
    foreach ($_from AS $this->_var['goods']):
        $this->_foreach['fe_goods']['iteration']++;
?>
        <a href="<?php echo url('app=goods&id=' . $this->_var['goods']['goods_id']. ''); ?>">
        <li>
            <div class="p_img">
                <img src="<?php echo $this->_var['goods']['default_image']; ?>" width="100%" height="100%">
            </div>
            <div class="p_title">
                <span><?php echo htmlspecialchars($this->_var['goods']['goods_name']); ?></span>
            </div>
            <div class="p_bottom">
                <span class="price"><?php echo price_format($this->_var['goods']['price']); ?></span>
                <span class="sales">已售：<em><?php echo htmlspecialchars($this->_var['goods']['sales']); ?></em></span>
            </div>
        </li>
        </a>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </ul>
    <div class="clear"></div>
</div>


<section id="recommended_store">
    <h3>推荐店铺</h3>
    <div class="row">
        <ul>
            <?php $_from = $this->_var['wap_recommended_stores']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'store');if (count($_from)):
    foreach ($_from AS $this->_var['store']):
?>
            <a href="<?php echo url('app=store&id=' . $this->_var['store']['store_id']. ''); ?>">
                <li>
                    <img src="<?php echo $this->_var['store']['store_logo']; ?>" alt="<?php echo htmlspecialchars($this->_var['store']['store_name']); ?>" width="88" height="88"/>
                    <span class="store_name"><?php echo htmlspecialchars($this->_var['store']['store_name']); ?></span><br/>
                    <span class="user_name"><?php echo htmlspecialchars($this->_var['store']['user_name']); ?></span><br/>
                    <span class="region_name">地址：<?php echo htmlspecialchars($this->_var['store']['region_name']); ?></span><br/>
                    <span class="credit_image"><img src="<?php echo $this->_var['store']['credit_image']; ?>"/></span><br/>
                </li>
            </a>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
    </div>
</section>

<?php echo $this->fetch('footer.html'); ?>